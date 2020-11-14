<?php
class ModelFeedUniXml extends Model {

  public $ext = 'unixml';

  //replace
  public function getReplaceNameList($feed){
    $replace = array();

    if($feed){
      $query = $this->db->query("SELECT name_from, name_to FROM " . DB_PREFIX . "unixml_replace_name WHERE feed = '" . $this->db->escape($feed) . "'");
      foreach($query->rows as $row){
        $replace[] = array(
          'name_from'  => $row['name_from'],
          'name_to'    => $row['name_to'],
        );
      }
    }

    return $replace;
  }
  //replace

  //attribute
  public function getAttributeList($feed){
    $attributes = array();
    $query = $this->db->query("SELECT attribute_id, xml_name FROM " . DB_PREFIX . "unixml_attributes WHERE feed = '" . $this->db->escape($feed) . "'");
    foreach($query->rows as $row){
      $attributes[] = array(
        'attribute_id'   => $row['attribute_id'],
        'xml_name'   => $row['xml_name']
      );
    }
    return $attributes;
  }
  //attribute

  //getCategories
  private function getPathByCategory($category_id) {
		static $path = null;
		$max_level = 10;

		$sql = "SELECT CONCAT_WS('_'";
		for ($i = $max_level-1; $i >= 0; --$i) {
			$sql .= ",t$i.category_id";
		}
		$sql .= ") AS path FROM " . DB_PREFIX . "category t0";
		for ($i = 1; $i < $max_level; ++$i) {
			$sql .= " LEFT JOIN " . DB_PREFIX . "category t$i ON (t$i.category_id = t" . ($i-1) . ".parent_id)";
		}
		$sql .= " WHERE t0.category_id = '" . (int)$category_id . "'";

		$query = $this->db->query($sql);

		$path[$category_id] = $query->num_rows ? $query->row['path'] : false;


		return $path[$category_id];
	}

  public function getCategories($allowed_categories, $lang_id, $category_match, $feed) {
    $replacename = $this->getReplaceNameList($feed);

    $category_match_array = array();
    if($category_match){ //если замена названий категорий
      $category_replace_query = $this->db->query("SELECT category_id, xml_name FROM " . DB_PREFIX . "unixml_category_match WHERE feed = '" . $this->db->escape($feed) . "'");
      foreach($category_replace_query->rows as $row){
        if($row['xml_name']){
          $category_match_array[$row['category_id']] = $row['xml_name'];
        }
      }
    }

    $categories = array();

    foreach(explode(',', $allowed_categories) as $cat_id){
      $categories_path = explode('_', $this->getPathByCategory($cat_id));

      foreach($categories_path as $level => $category_id){

        $category_info = $this->db->query("SELECT name FROM " . DB_PREFIX . "category_description WHERE category_id = '" . (int)$category_id . "' AND language_id = '" . (int)$lang_id . "'");
        $category_name = $category_info->row['name'];

        if($category_match){
          if(isset($category_match_array[$category_id])){
            $category_name = $category_match_array[$category_id];
          }
        }

        $categories[$category_id] = array(
          'category_id' => $category_id,
          'name'        => $this->ClearReplace($category_name, $replacename),
          'parent_id'   => $level?$categories_path[$level-1]:''
        );
      }
    }

    return $categories;
  }
  //getCategories

  //getCategoryCustomData
  private function getCategoryCustomData($feed){
    $category_data = array();
    $category_replace_query = $this->db->query("SELECT category_id, markup, custom FROM " . DB_PREFIX . "unixml_category_match WHERE feed = '" . $this->db->escape($feed) . "'");
    foreach($category_replace_query->rows as $row){
      if($row['markup'] or $row['custom']){
        $category_data[$row['category_id']] = array(
          'markup' => $row['markup'],
          'custom' => $row['custom']
        );
      }
    }

    return $category_data;
  }
  //getCategoryCustomData

  //markupCalc
  private function markupCalc($price, $markup){

    $number = 0;

    if(substr($markup, -1) == '%'){
      $number += $price * str_replace(array('%',',',' '), array('','.',''), $markup) / 100;
    }else{
      $number += (float)str_replace(',', '.', $markup);
    }

    return $number;
  }
  //markupCalc

  //checkFields
  private function checkFields(){
    $all_access_column = array();

    $fileds_query = $this->db->query("SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema = '" . $this->db->escape(DB_DATABASE) . "' and table_name = '" . DB_PREFIX ."product'");
    foreach($fileds_query->rows as $row){
      $all_access_column[] = 'p.' . $row['COLUMN_NAME'];
    }

    $fileds_query = $this->db->query("SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema = '" . $this->db->escape(DB_DATABASE) . "' and table_name = '" . DB_PREFIX ."product_description'");
    foreach($fileds_query->rows as $row){
      $all_access_column[] = 'pd.' . $row['COLUMN_NAME'];
    }

    return $all_access_column;
  }
  //checkFields

  //findInText
  private function findInText($text, $marker, $mask){
    $finded = array();
    $posa = strpos($text, $marker);
    if($posa !== false){ //если есть атрибуты
      if(preg_match_all($mask, $text, $matches)){
        $finded = $matches[1];
      }
    }
    return $finded;
  }
  //findInText

  //getProducts
  public function getProducts($allowed_categories, $allowed_manufacturers, $custom_products, $products_mode, $lang, $quantity, $multiply_options_status, $option_multiplier_id, $genname, $gendesc, $gendesc_mode, $currency, $stock, $attribute_status, $andor, $images, $image, $markup, $fields, $seopro, $utm, $field_price, $field_id, $clear_desc, $only_product, $feed) {

    $seopro_exist = false;
    $seopro_query = $this->db->query("SHOW COLUMNS FROM " . DB_PREFIX . "product_to_category LIKE 'main_category'");
    if($seopro_query->num_rows){
      $seopro_exist = true;
    }

    $currency_query = $this->db->query("SELECT `value` FROM `" . DB_PREFIX . "currency` WHERE currency_id = '" . (int)$currency . "'");
    if(isset($currency_query->row['value'])){
      $currency = $currency_query->row['value'];
    }else{
      $currency = 1;
    }

    $all_access_column = $this->checkFields();
    $attributes_name = array();
    $options_name = array();
    $custom_name = array();
    $array_names = array();

    if($genname or $gendesc){
      $attributes_name = $this->findInText($genname . $gendesc, "{{", "/{{(.*?)}}/"); //attribute
      $options_name = $this->findInText($genname . $gendesc, "[[", "/\[\[(.*?)\]\]/"); //options
      $custom_name = $this->findInText($genname . $gendesc, "((", "/\(\((.*?)\)\)/"); //db
      $array_names = $this->findInText($genname . $gendesc, "{", "/{(.*?)}/"); //product array
    }

    $replacename = $this->getReplaceNameList($feed);
    $attributeList = $this->getAttributeList($feed);
    $category_data = $this->getCategoryCustomData($feed);

    $selected_vars = array(
      'p.product_id' => 'product_id',
      'p.image' => 'image',
      'p.stock_status_id' => 'stock_status_id',
      'p.quantity' => 'quantity',
      'model' => 'model',
      'p.price' => 'price',
      'pd.name' => 'name',
      'pd.description' => 'description',
      'm.name' => 'manufacturer'
    );

    if($seopro_exist && $seopro){
      $selected_vars['p2c.category_id'] = 'category_id';
    }

    if($fields){
      $fields = explode(',', $fields);
      foreach($fields as $field){
        $rep_from = array('','(',')','{','}','[',']');
        $field = str_replace($rep_from, '', $field);
        $selected_vars[$field] = str_replace(array('p.','pd.'), '', $field);
      }
    }

    $recuired_vars = $selected_vars;

    if($genname){unset($selected_vars['pd.name']);}
    if($gendesc){
      if(!$gendesc_mode){ //если заменяем в любом случае
        unset($selected_vars['pd.description']);
      }
    }

    if($custom_name){
      foreach($custom_name as $custom_item){
        if(!isset($selected_vars[$custom_item]) && in_array($custom_item, $all_access_column)){
          $selected_vars[$custom_item] = str_replace(array('p.','pd.'), '', $custom_item);
        }
      }
    }

    //custom param
    $custom_replace = array();
    $custom_params = array();
    $query_custom_attribute = $this->db->query("SELECT param_name, param_text FROM " . DB_PREFIX . "unixml_additional_params WHERE feed = '" . $this->db->escape($feed) . "'");

    foreach($query_custom_attribute->rows as $row){

      $custom_attributes_name = $this->findInText($row['param_text'], "{{", "/{{(.*?)}}/"); //attribute
      $custom_options_name = $this->findInText($row['param_text'], "[[", "/\[\[(.*?)\]\]/"); //options
      $custom_custom_name = $this->findInText($row['param_text'], "((", "/\(\((.*?)\)\)/"); //db
      $custom_array_names = $this->findInText($row['param_text'], "{", "/{(.*?)}/"); //product array

      //from to attribute
      if($custom_attributes_name){
        $attributes_name = array_merge($custom_attributes_name, $attributes_name);
      }
      //from to attribute

      //from to custom fileds
      if($custom_custom_name){
        foreach($custom_custom_name as $custom_item){
          if(!isset($selected_vars[$custom_item]) && in_array($custom_item, $all_access_column)){
            $selected_vars[$custom_item] = str_replace(array('p.','pd.'), '', $custom_item);
          }
          $custom_replace['db'][] = $custom_item;
        }
      }
      //from to custom fileds

      //from to field in product array
      if($custom_array_names){
        foreach($custom_array_names as $custom_item){
          $custom_replace['product_array'][] = $custom_item;
        }
      }
      //from to field in product array

      $substr_name = html_entity_decode(trim($row['param_text']), ENT_QUOTES, 'UTF-8');
      $text = $this->ClearReplace($row['param_text'], $replacename);
      if(substr($substr_name, 0, 1) == "<" && substr($substr_name, -1) == ">"){ //если полнотеговые значения - не фильтруем
        $text = $substr_name;
      }

      $custom_params[] = array(
        'name' => $row['param_name'],
        'custom_options_name' => $custom_options_name,
        'text' => $text
      );
    }

    $attributes_name = array_unique($attributes_name);

    //custom param

    //custom price
    if($field_price){
      if(isset($selected_vars['p.price'])){
        unset($selected_vars['p.price']);
      }
      $selected_vars[$field_price] = 'price';
    }
    //custom price

    //custom id
    if($field_id && $field_id != "p.product_id"){
      $selected_vars[$field_id] = 'alt_id';
    }
    //custom id

    $cat_id_name = array();

    $products = array();

    $sql = "SELECT";

    if(!isset($only_product['start']) && !isset($only_product['finish'])){
      $sql .= " COUNT(p.product_id) as total";
      $selected_vars = false;
    }

    if($selected_vars){
      $sql_plus = '';
      foreach($selected_vars as $selected_var => $selected_as){
        $selected_as_name = explode('.', $selected_var);
        if(isset($selected_as_name[1]) && $selected_as_name[1] != $selected_as){
          $sql_plus .= ", " . $selected_var . ' as ' . $selected_as;
        }else{
          $sql_plus .= ", " . $selected_var;
        }
      }
      $sql .= ltrim($sql_plus, ',');
    }

    if($selected_vars){
      if(!$seopro){
        $sql .= ", (SELECT MAX(sub2c.category_id) FROM " . DB_PREFIX . "product_to_category sub2c WHERE sub2c.product_id = p.product_id";
        if($allowed_categories){ //если заданы категории
          $sql .= " AND sub2c.category_id IN(" . $this->db->escape($allowed_categories) . ")";
        }
        $sql .= ") AS category_id";
      }
      $sql .= ", (SELECT ps.price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ps.date_start < NOW() AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) as special";
    }

    $sql .= " FROM " . DB_PREFIX . "product p
            LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)
            LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id)
            LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
            LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)";

    if($custom_products && !$products_mode){ //если есть товары выбиранные вручную + мод = 0, то есть только эти товары
      $sql .= " WHERE p.product_id IN (" . $this->db->escape($custom_products) . ")";
    }else{ //если нет товаров или же режим запрета некоторых товаров выполяем условия по категориям и брендам
      if($allowed_categories && $allowed_manufacturers){ //если заданы и категории и бренды
        $and_or = $andor?"OR":"AND";
        $sql .= " WHERE (p2c.category_id IN (" . $this->db->escape($allowed_categories) . ") " . $and_or . " p.manufacturer_id IN (" . $this->db->escape($allowed_manufacturers) . "))";
      }elseif($allowed_categories){ //если заданы категории
        $sql .= " WHERE p2c.category_id IN (" . $this->db->escape($allowed_categories) . ")";
      }elseif($allowed_manufacturers){ //если заданы бренды
        $sql .= " WHERE p.manufacturer_id IN (" . $this->db->escape($allowed_manufacturers) . ")";
      }else{ //если категории и бренды не заданы
        $sql .= " WHERE p.product_id > 0"; //пустышка для WHERE
      }
      if($custom_products && $products_mode){ //если заданы товары а также стоит режим запрета некоторых
        $sql .= "  AND p.product_id NOT IN (" . $this->db->escape($custom_products) . ")";
      }
    }

    $sql .= " AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
              AND pd.language_id = '" . (int)$lang . "'
              AND p.status = '1'";

    if($image){
      $sql .= " AND p.image != '' AND p.image != 'no_image.jpg' AND p.image != 'no_image.png' AND p.image != 'placeholder.jpg' AND p.image != 'placeholder.png'";
    }
    if($seopro_exist && $seopro){
      $sql .= " AND p2c.main_category = '1'";
    }
    if(!$quantity){
      $sql .= " AND p.quantity > '0'";
    }
    if($selected_vars){
      $sql .= " GROUP BY p.product_id";
    }
    $sql .= " ORDER BY p.product_id ASC";

    if(isset($only_product['start']) && isset($only_product['finish'])){
      $sql .= " LIMIT " . $only_product['start'] . ", " . $only_product['finish'];
    }

    $query = $this->db->query($sql);

    if($query->num_rows){

      if(!$selected_vars){ //если это инит
        return array('total' => $query->row['total']);
      }

      foreach($query->rows as $key=> $product){

        $product['product_original_id'] = $product['product_id'];

        if($image){
          if(!is_file(DIR_IMAGE . $product['image'])){
            continue; //Убираем с выгрузки товары в которых нет фото
          }
        }

        $product['product_option_id'] = false;
        $product['attributes'] = array();
        $product['from'] = array();
        $product['to'] = array();
        $product['custom_from'] = array();
        $product['custom_to'] = array();
        $product['clear'] = array();
        $product['images'] = array();

        if(!isset($cat_id_name[$product['category_id']])){
          $category_name = $this->db->query("SELECT `name` FROM " . DB_PREFIX . "category_description WHERE category_id = '" . (int)$product['category_id'] . "' AND language_id = '" . (int)$lang . "'");
          if(isset($category_name->row['name']) && $category_name->row['name']){
            $product['category'] = $category_name->row['name'];
            $cat_id_name[$product['category_id']] = $category_name->row['name'];
          }
        }else{
          $product['category'] = $cat_id_name[$product['category_id']];
        }

        //from to attributes
        if($attributes_name){
          foreach($attributes_name as $attribute_name){
            $attribute_text_query = $this->db->query("SELECT pa.text FROM " . DB_PREFIX . "product_attribute pa LEFT JOIN " . DB_PREFIX . "attribute_description ad ON(pa.attribute_id = ad.attribute_id AND pa.language_id = ad.language_id) WHERE ad.name = '" . $this->db->escape($attribute_name) . "' AND ad.language_id = '" . (int)$lang . "' AND pa.product_id = '" . (int)$product['product_original_id'] . "'");
            $product['from'][] = "{{" . $attribute_name . "}}";
            if(isset($attribute_text_query->row['text']) && !empty($attribute_text_query->row['text'])){
              $product['to'][] = trim($attribute_text_query->row['text']);
            }else{
              $product['to'][] = ""; //заменяем на пустое когда не найден атрибут
            }
          }
        }
        //from to attributes

        //from to custom fileds
        if($custom_name){
          foreach($custom_name as $custom_item){
            $product['from'][] = "((" . $custom_item . "))";
            $custom_item_data = explode('.', $custom_item);
            if(isset($custom_item_data[1]) && $custom_item_data[1] && !in_array($custom_item_data[1], $recuired_vars)){
              $product['clear'][] = $custom_item_data[1];
            }
            if(isset($custom_item_data[1]) && $custom_item_data[1] && isset($product[$custom_item_data[1]]) && $product[$custom_item_data[1]]){
              $product['to'][] = $product[$custom_item_data[1]];
            }else{
              $product['to'][] = "";
            }
          }
        }
        //from to custom fileds

        //all attributes
        if(!$attribute_status){ //если не скрыто
          if($attributeList){ //если есть списки атрибутов
            $attributes_sql = "SELECT pa.attribute_id,
                                      pa.text,
                                      ua.xml_name as name
                               FROM " . DB_PREFIX . "product_attribute pa
                               INNER JOIN " . DB_PREFIX . "unixml_attributes ua ON (ua.attribute_id = pa.attribute_id)
                               WHERE product_id ='" .(int) $product['product_original_id'] . "'
                               AND pa.language_id = '" . (int)$lang . "'
                               AND feed = '" . $feed . "'";
          }else{ //если нет - выгружаем все атрибуты
            $attributes_sql = "SELECT pa.attribute_id,
                                      pa.text,
                                      ad.name
                               FROM " . DB_PREFIX . "product_attribute pa
                               INNER JOIN " . DB_PREFIX . "attribute_description ad ON (ad.attribute_id = pa.attribute_id AND ad.language_id = pa.language_id)
                               WHERE product_id ='" . (int)$product['product_original_id'] . "'
                               AND ad.language_id = '" . (int)$lang . "'";
          }

          $product_attributes = $this->db->query($attributes_sql);

          foreach($product_attributes->rows as $p_attribute){
            $product['attributes'][] = array(
              'name'  => $p_attribute['name'],
              'text'  => $this->ClearReplace($p_attribute['text'], $replacename)
            );
          }
        }
        //attributes

        //custom from to
        $custom_from = array();
        $custom_to = array();
        if(isset($custom_replace['db'])){ //replaces db
          foreach($custom_replace['db'] as $custom_db){
            $custom_from[] = "((" . $custom_db . "))";
            $custom_item_data = explode('.', $custom_db);

            if(isset($custom_item_data[1]) && $custom_item_data[1] && !in_array($custom_item_data[1], $recuired_vars)){
              $product['clear'][] = $custom_item_data[1];
            }

            if(isset($custom_item_data[1]) && $custom_item_data[1] && isset($product[$custom_item_data[1]]) && $product[$custom_item_data[1]]){
              $custom_to[] = $product[$custom_item_data[1]];
            }else{
              $custom_to[] = "";
            }
          }
        }

        if(isset($custom_replace['product_array'])){ //replaces array
          foreach($custom_replace['product_array'] as $array_name){
            $custom_from[] = "{" . $array_name . "}";
            if(isset($product[$array_name])){
              $custom_to[] = $product[$array_name];
            }else{
              $custom_to[] = '';
            }
          }
        }

        if($custom_params){
          foreach($custom_params as $custom_item){
            $final_custom_text = $custom_item['text'];
            $final_custom_text = str_replace($product['from'], $product['to'], $final_custom_text);
            $final_custom_text = str_replace($custom_from, $custom_to, $final_custom_text);

            $product['attributes'][] = array(
              'name'  => $custom_item['name'],
              'custom_options_name'=>$custom_item['custom_options_name'],
              'text'  => $final_custom_text
            );
          }
        }
        //custom from to

        if($multiply_options_status && $option_multiplier_id) {

          $option_sql = "SELECT ovd.name as value, od.name, pov.price_prefix, pov.price, pov.quantity
                         FROM " . DB_PREFIX . "option_description od
                         INNER JOIN " . DB_PREFIX . "option_value_description " . "ovd ON (od.option_id = ovd.option_id)
                         INNER JOIN " . DB_PREFIX . "product_option_value pov ON (pov.option_value_id = ovd.option_value_id)
                         WHERE pov.product_id = '" . (int)$product['product_original_id'] . "'
                         AND ovd.language_id = '" . (int)$lang . "'
                         AND od.language_id = '" . (int)$lang . "'";
                         if(!$quantity){ //только то что в наличи
                           $option_sql .= " AND pov.quantity > 0";
                         }
                         $option_sql .= " AND pov.option_id IN (" . $option_multiplier_id  . ")";

          $options = $this->db->query($option_sql);

          //alt_id detect and replace original id
          if(isset($product['alt_id'])){
            $product['product_id'] = $product['alt_id'];
          }
          //alt_id detect and replace original id

          if($options->num_rows){

            //перемножать опцию на опцию
            if(true){ //Стоит true т.к необходимости в настройке нет из-за того что если опций 2 и больше то их просто перемножим.
              $multi_option_array = array();
              foreach($options->rows as $key => $row){
                $multi_option_array[$row['name']][] = $row;
              }

              $count_multi_option = count($multi_option_array);
              $multi_option_array = array_values($multi_option_array);

              if($count_multi_option > 1){ //если опций более чем 1 то обнуляем массив опций и обрабатываем
                $options->rows = array();
                $option_key = 0;
                foreach($multi_option_array[0] as $option_value){ //первый проход
                  if($count_multi_option == 3){ //если опций 3 то множим 1*2*3
                    foreach($multi_option_array[1] as $option_value1){
                      foreach($multi_option_array[2] as $option_value2){
                        $options->rows[$option_key]['from'] = array_merge(array("[[" . $option_value['name'] . "]]","[[" . $option_value1['name'] . "]]","[[" . $option_value2['name'] . "]]"), $product['from']);
                        $options->rows[$option_key]['to'] = array_merge(array(trim($option_value['value']),trim($option_value1['value']),trim($option_value2['value'])), $product['to']);
                        $options->rows[$option_key]['quantity'] = $option_value['quantity'] + $option_value1['quantity'] + $option_value2['quantity'];
                        $options->rows[$option_key]['price'] = $option_value2['price'];
                        $options->rows[$option_key]['price_prefix'] = $option_value2['price'];
                        $option_key++;
                      }
                    }
                  }elseif($count_multi_option == 2){ //если опций 2 то множим
                    foreach($multi_option_array[1] as $option_value1){
                      $options->rows[$option_key]['from'] = array_merge(array("[[" . $option_value['name'] . "]]","[[" . $option_value1['name'] . "]]"), $product['from']);
                      $options->rows[$option_key]['to'] = array_merge(array(trim($option_value['value']),trim($option_value1['value'])), $product['to']);
                      $options->rows[$option_key]['quantity'] = $option_value['quantity'] + $option_value1['quantity'];
                      $options->rows[$option_key]['price'] = $option_value1['price'];
                      $options->rows[$option_key]['price_prefix'] = $option_value1['price_prefix'];
                      $option_key++;
                    }
                  }
                } //первый проход
              }
            }
            //перемножать опцию на опцию

            $price_original = $product['price'];
            foreach($options->rows as $key => $row){
              $product['custom_from'] = array();
              $product['custom_to'] = array();

              if(!isset($row['from'])){ //если не множим опцию на опцию
                //from to options
                if($options_name){
                  $option_iter = 777;
                  foreach($options_name as $option_name){ //перебираем все опции в шаблоне
                    $product['from'][$option_iter] = "[[" . $option_name . "]]";
                    if($row['name'] == $option_name){
                      $product['to'][$option_iter] = trim($row['value']);
                    }else{
                      $product['to'][$option_iter] = ""; //заменяем на пустое когда опция не найдена
                    }
                    $option_iter++;
                  }
                }
                //from to options

                //replace in additional params
                foreach($product['attributes'] as $attribute_key => $attr_item){ //пепебираем доп параметры
                  if(isset($attr_item['custom_options_name']) && $attr_item['custom_options_name']){ //если есть опция в шаблоне
                    foreach($attr_item['custom_options_name'] as $custom_option_name){ //перебираем все найденные элементы для замены
                      $product['custom_from'][] = "[[" . $custom_option_name . "]]";
                      if($row['name'] == $custom_option_name){
                        $product['custom_to'][] = $row['value'];
                      }else{
                        $product['custom_to'][] = "";
                      }
                    }
                  }
                }
                //replace in additional params

              }else{ //если множим опцию на опцию
                $product['from'] = $row['from'];
                $product['to'] = $row['to'];
                $product['custom_from'] = $row['from'];
                $product['custom_to'] = $row['to'];
              }

              $option_price = 0;
              if($row['price_prefix'] == '+'){
                $option_price += $row['price'];
              } elseif($row['price_prefix'] == '-'){
                $option_price -= $row['price'];
              } elseif($row['price_prefix'] == '='){
                $option_price = $row['price'];
              }

              $product['price'] = $price_original + $option_price;
              $product['option_stock'] = $row['quantity'];
              $product['quantity'] = $row['quantity'];
              $product['product_option_id'] =  (($key+1) * 10000) . $product['product_id'];
              $product['model'] =  (($key+1) * 10000) . '-' . $product['product_id'];

              $products[] = $product;
            } //foreach option rows

          }else{
            foreach($options_name as $option_name){ //перебираем все опции в шаблоне
              $product['from'][] = "[[" . $option_name . "]]";
              $product['to'][] = "";
            }
            $products[] = $product;
          }
        }else{
          //alt_id detect and replace original id
          if(isset($product['alt_id'])){
            $product['product_id'] = $product['alt_id'];
          }
          //alt_id detect and replace original id
          $products[] = $product;
        }
      }
    }

    //prepare to final data
    $products_final = array();
    $from = array();
    $to = array();
    $product_id_for_image = '';

    foreach($products as $product){

      $attribute_foreach = $product['attributes'];
      $product['attributes'] = array();
      $product['attributes_full'] = array();
      foreach($attribute_foreach as $attribute){
        if($product['custom_from'] && $product['custom_to']){ //replace custom params
          $attribute['text'] = trim(str_replace($product['custom_from'], $product['custom_to'], $attribute['text']));
        }
        if($this->findInText($attribute['text'], "[[", "/\[\[(.*?)\]\]/")){ //options?
          $attribute['text'] = '';
        }

        if($attribute['text']){
          $substr_name = html_entity_decode(trim($attribute['name']), ENT_QUOTES, 'UTF-8');
          $first_element = substr($substr_name, 0, 1);
          $last_element = substr($substr_name, -1);
          if($first_element == "<" && $last_element == ">"){ //если полнотеговые
            $af_name = str_replace(array($first_element, $last_element), '', $substr_name);
            $product['attributes_full'][$af_name] = array(
              'name' => $af_name,
              'text' => $attribute['text'],
              'end'  => $af_name
            );
          }else{
            $product['attributes'][] = array(
              'name' => $attribute['name'],
              'text' => $attribute['text']
            );
          }
        }
      }

      if(isset($category_data[$product['category_id']]['custom']) && $category_data[$product['category_id']]['custom']){
        foreach(explode(PHP_EOL, $category_data[$product['category_id']]['custom']) as $custom_item){
          $custom_data = explode("==", $custom_item);
          if(isset($custom_data[0]) && isset($custom_data[1])){
            $af_name = html_entity_decode(trim($custom_data[0]), ENT_QUOTES, 'UTF-8');
            $end_data = explode(" ", $af_name);
            $product['attributes_full'][$af_name] = array(
              'name' => $af_name,
              'text' => str_replace($product['from'], $product['to'], trim($custom_data[1])),
              'end'  => $end_data[0]
            );
          }
        }
      }

      //generate name
      if($genname && $product['from'] && $product['to']){ //attributes and options
        $product['name'] = trim(str_replace($product['from'], $product['to'], $genname));
      }
      //generate name

      //generate description
      $gendesc_allow = true;
      if($gendesc_mode && $product['description']){ //если генерируем только если нет описания и если есть описание
        $gendesc_allow = false;
      }

      if($gendesc && $gendesc_allow && $product['from'] && $product['to']){ //attributes and options
        $product['description'] = trim(str_replace($product['from'], $product['to'], $gendesc));
      }
      //generate description

      $product['name'] = $this->ClearReplace($product['name'], $replacename);
      $product['model'] = $this->ClearReplace($product['model'], $replacename);
      $product['manufacturer'] = $this->ClearReplace($product['manufacturer'], $replacename);
      $product['description'] = $this->ClearReplace($product['description'], $replacename, $clear_desc);

      $product['url'] = $this->url->link('product/product', 'product_id=' . (int)$product['product_original_id']);
      $product['image'] = HTTPS_SERVER . 'image/' . $product['image'];
      $product['image'] = $this->ClearReplace($product['image'], $replacename);

      $product['price'] = $product['price'] * $currency;
      if($product['special']){
        $product['special'] = $product['special'] * $currency;
      }

      //markup
      if(isset($category_data[$product['category_id']]['markup']) && $category_data[$product['category_id']]['markup']){ //markup from category
        $product['price'] += $this->markupCalc($product['price'], $category_data[$product['category_id']]['markup']);
        if($product['special']){
          $product['special'] += $this->markupCalc($product['special'], $category_data[$product['category_id']]['markup']);
        }
      }else{
        if($markup && (float)$markup > 0){
          $product['price'] += $this->markupCalc($product['price'], $markup);
          if($product['special']){
            $product['special'] += $this->markupCalc($product['special'], $markup);
          }
        }
      }
      //markup

      $product['stock'] = false;
      if($quantity){ //не привязываемся и выгружаем то что нет в наличии
        if($product['quantity'] > 0){
          $product['stock'] = 'В наличии';
        }else{ //если количество 0
          if($product['stock_status_id'] == $stock){ //если статус товара тот при котором в наличии то проставляем
            $product['stock'] = 'В наличии';
          }
        }
      }else{ //если привязываемся то все товары в наличии
        $product['stock'] = 'В наличии';
      }
      if(isset($product['option_stock'])){
        $product['stock'] = $product['option_stock']?'В наличии':false;
      }

      if($utm){
        $url_from = array();
        $url_to = array();
        $array_url_replace = $this->findInText($utm, "{", "/{(.*?)}/");
        foreach($array_url_replace as $array_name){
          $url_from[] = "{" . $array_name . "}";
          if(isset($product[$array_name])){
            $url_to[] = $product[$array_name];
          }else{
            $url_to[] = '';
          }
        }
        if($url_from && $url_to){
          $product['url'] .= trim(str_replace($url_from, $url_to, $utm));
        }
      }

      //from to array fields - final replace
      if($array_names){
        foreach($array_names as $array_name){
          $from[] = "{" . $array_name . "}";
          if(isset($product[$array_name])){
            $to[] = $product[$array_name];
          }else{
            $to[] = '';
          }
        }
        if($genname && $from && $to){ //attributes and options
          $product['name'] = trim(str_replace($from, $to, $product['name']));
        }
        if($gendesc && $from && $to){ //attributes and options
          $product['description'] = trim(str_replace($from, $to, $product['description']));
        }
      }
      //from to array fields - final replace

//custom_code_xml_start

//custom_code_xml_end

      //clear old data
      foreach($product['clear'] as $clear_item){ //clear all tmp data
        if(isset($product[$clear_item])){
          unset($product[$clear_item]);
        }
      }

      unset($product['clear']);
      unset($product['from']);
      unset($product['to']);
      unset($product['custom_from']);
      unset($product['custom_to']);
      unset($product['option_stock']);
      //clear old data

      if($feed == 'prom'){ //get discount for prom
        $discounts_query = $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product['product_original_id'] . "' AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND quantity > 1 AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY quantity ASC, priority ASC, price ASC");
        if($discounts_query->num_rows){
          foreach($discounts_query->rows as $row){
            $product['prices'][] = array('price'=>$row['price'],'quantity'=>$row['quantity']);
          }
        }
      }

      if($images){ //если размечаем доп фото
        $product_id_for_image .= $product['product_original_id'] . ',';
      }

      if($product['product_option_id']){
        if(!isset($product_option_ids)){
          $product_option_ids = array();
        }
        $product_option_ids[$product['product_original_id']][] = $product['product_option_id'];
      }

      $products_final[$product['product_option_id']?$product['product_option_id']:$product['product_id']] = $product;

    } //end product foreach

    if($product_id_for_image){
      if($images){
        $image_query = $this->db->query("SELECT product_id, image FROM " . DB_PREFIX ."product_image WHERE product_id IN (" . $this->db->escape(rtrim($product_id_for_image, ",")) . ")");

        if($image_query->num_rows){
          foreach($image_query->rows as $image_item){
            if(isset($product_option_ids) && $product_option_ids){ //если опции
              if(isset($product_option_ids[$image_item['product_id']])){
                foreach($product_option_ids[$image_item['product_id']] as $product_option_id){
                  if(isset($products_final[$product_option_id]['product_id'])){
                    $products_final[$product_option_id]['images'][] = HTTPS_SERVER . 'image/' . $this->ClearReplace($image_item['image'], $replacename);
                  }
                }
              }
            }else{ //если без умножения
              if(isset($products_final[$image_item['product_id']]['product_id'])){
                $products_final[$image_item['product_id']]['images'][] = HTTPS_SERVER . 'image/' . $this->ClearReplace($image_item['image'], $replacename);
              }
            }
          }
        }
      }
    }

    unset($products);
    //prepare to final data

    return $products_final;
  }
  //getProducts

  //getCurrencyCode
  public function getCurrencyCode($currency_id){
    $currency_query = $this->db->query("SELECT `code` FROM " . DB_PREFIX . "currency WHERE currency_id = '" . (int)$currency_id . "'");
    if(isset($currency_query->row['code']) && $currency_query->row['code']){
      $currency = $currency_query->row['code'];
    }else{
      $currency = $this->config->get('config_currency');
    }

    return $currency;
  }
  //getCurrencyCode

  //getProductCategories
  public function getProductsCategories($products, $quantity){
    $unixml_categories = array();
    $unixml_categories_query_array = array();

    $sql = "SELECT p2c.category_id FROM " . DB_PREFIX . "product_to_category p2c LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id) WHERE p2c.product_id IN (" . $this->db->escape($products) . ")";
    if(!$quantity){
      $sql .= " AND p.quantity > '0'";
    }
    $sql .= " GROUP BY category_id";

    $unixml_categories_query = $this->db->query($sql);

    if($unixml_categories_query->num_rows){
      foreach($unixml_categories_query->rows as $unixml_categories_query_row){
        $unixml_categories_query_array[$unixml_categories_query_row['category_id']] = $unixml_categories_query_row['category_id'];
      }
      $unixml_categories = implode(",", $unixml_categories_query_array);
    }

    return $unixml_categories;
  }
  //getProductCategories

  //getProductsManufacturers
  public function getProductsManufacturers($manufacturers, $quantity){
    $unixml_categories = array();
    $unixml_categories_query_array = array();

    $sql = "SELECT p2c.category_id FROM " . DB_PREFIX . "product_to_category p2c LEFT JOIN " . DB_PREFIX . "product p ON(p2c.product_id = p.product_id) WHERE p.manufacturer_id IN (" . $this->db->escape($manufacturers) . ") AND p.status = 1 AND p2c.category_id > '0'";
    if(!$quantity){
      $sql .= " AND p.quantity > '0'";
    }
    $sql .= " GROUP BY category_id";

    $unixml_categories_query = $this->db->query($sql);

    if($unixml_categories_query->num_rows){
      foreach($unixml_categories_query->rows as $unixml_categories_query_row){
        $unixml_categories_query_array[$unixml_categories_query_row['category_id']] = $unixml_categories_query_row['category_id'];
      }
      $unixml_categories = implode(",", $unixml_categories_query_array);
    }

    return $unixml_categories;
  }
  //getProductsManufacturers

  //from System
  public function opencart_version($d){
		$opencart_version = explode(".", VERSION);
		return $opencart_version[$d];
	}

	public function module_info($key, $admin = false){
		$domen = explode("//", $admin?HTTP_CATALOG:HTTP_SERVER);
		$information = array(
			'main_host'	=> str_replace("/", "", $domen[1]),
			'engine' 	=> VERSION,
			'version' 	=> '5.1',
			'module' 	=> 'UniXml',
			'sys_key'	=> '327450',
			'sys_keyf'  => '7473'
		);
		return $information[$key];
	}

  public function getAllVars(){
    $fields = array('products','category_match');
    $unixml_dat = explode(PHP_EOL, file_get_contents($this->request->server['DOCUMENT_ROOT'] . '/price/' . $this->ext . '.dat'));
    foreach(explode(',', $unixml_dat[1]) as $field){
      if($field){
        $fields[] = trim($field);
      }
    }
    return $fields;
  }

  public function ClearReplace($text = '', $replace_array, $stop_clear_tags = false) {
		if(is_string($text) && $text){
			$text = html_entity_decode($text, ENT_QUOTES, 'UTF-8'); //переводим в теги
      if(!$stop_clear_tags){
        $text = str_replace('&amp;','&',$text);
        $text = str_replace('&','&amp;',$text);
  			$text = str_replace("><", "> <", $text); //что бы не слипался текст если есть пробел между тегами
  			$text = str_replace(array("<br />", "<br>"), " ", $text); //fix br
  			$text = strip_tags($text); //удаляем теги
  			$find = array(PHP_EOL, "\r\n", "\r", "\n", "\t", '  ', '  ', '    ', '    ', '"', "'", "\\", '&varr;', '&nbsp;', '&pound;', '&euro;', '&para;', '&sect;', '&copy;', '&reg;', '&trade;', '&deg;', '&plusmn;', '&frac14;', '&frac12;', '&frac34;', '&times;', '&divide;', '&fnof;', '&Alpha;', '&Beta;', '&Gamma;', '&Delta;', '&Epsilon;', '&Zeta;', '&Eta;', '&Theta;', '&Iota;', '&Kappa;', '&Lambda;', '&Mu;', '&Nu;', '&Xi;', '&Omicron;', '&Pi;', '&Rho;', '&Sigma;', '&Tau;', '&Upsilon;', '&Phi;', '&Chi;', '&Psi;', '&Omega;', '&alpha;', '&beta;', '&gamma;', '&delta;', '&epsilon;', '&zeta;', '&eta;', '&theta;', '&iota;', '&kappa;', '&lambda;', '&mu;', '&nu;', '&xi;', '&omicron;', '&pi;', '&rho;', '&sigmaf;', '&sigma;', '&tau;', '&upsilon;', '&phi;', '&chi;', '&psi;', '&omega;', '&larr;', '&uarr;', '&rarr;', '&darr;', '&harr;', '&spades;', '&clubs;', '&hearts;', '&diams;', '&quot;', '&amp;', '&lt;', '&gt;', '&hellip;', '&prime;', '&Prime;', '&ndash;', '&mdash;', '&lsquo;', '&rsquo;', '&sbquo;', '&ldquo;', '&rdquo;', '&bdquo;', '&laquo;', '&raquo;'); //что чистим
  			$text = str_replace($find, ' ', $text); //чистим текст
      }
		}

    //replaces
    if(!empty($replace_array)){
      foreach($replace_array as $replace_item){
        $text = str_replace($replace_item['name_from'], $replace_item['name_to'], $text);
      }
    }
    //replaces
    return trim($text);
	}

}
?>
