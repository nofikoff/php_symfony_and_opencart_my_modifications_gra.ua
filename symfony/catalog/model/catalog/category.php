<?php

class ModelCatalogCategory extends Model
{

    public function getCategory($category_id)
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.category_id = '" . (int) $category_id . "' AND cd.language_id = '" . (int) $this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int) $this->config->get('config_store_id') . "' AND c.status = '1'");

        return $query->row;
    }

//    public function getCategories($parent_id = 0, $limit = false)
//    {
//
//        $sql = "SELECT * FROM " . DB_PREFIX . "category c 
//            LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) 
//            LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id)
//            WHERE c.parent_id = '" . (int) $parent_id . "' 
//                AND cd.language_id = '" . (int) $this->config->get('config_language_id') . "' 
//                AND c2s.store_id = '" . (int) $this->config->get('config_store_id') . "'  
//                AND c.status = '1' 
//                AND c.sort_order <> '-1' 
//                ORDER BY c.sort_order, LCASE(cd.name)";
//
//        if ($limit)
//            $sql .= " LIMIT " . (int) $limit;
//
//        $query = $this->db->query($sql);
//
//        $category_data = $query->rows;
//        return $category_data;
//    }
    
    public function getCategories($parent_id = 0) {

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category c 
            LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) 
            LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) 
            WHERE c.parent_id = '" . (int)$parent_id . "' 
                AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' 
                AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'  
                AND c.status = '1' 
                AND c.sort_order <> '-1' 
                ORDER BY c.sort_order, LCASE(cd.name)");
         $category_data=array();
        foreach ($query->rows as $item) {
            $count = $this->model_catalog_category->getCategoryProducts($item['category_id']);
            $sub = $this->getCategories($item['category_id']);
            if($count || $sub){
                $category_data[]=$item;
            }
        }
        return $category_data;
    }
    
    public function getCategoryProducts($category_id = 0) {

        $query = $this->db->query("SELECT count(p.product_id) AS count FROM " . DB_PREFIX . "product p 
            INNER JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)
            WHERE p2c.category_id = '" . (int) $category_id . "' 
                AND p.status = '1' ");

        return isset($query->row['count']) ? $query->row['count'] : 0;
    }

    public function getParametrCategories($selected_categorys = array(), $selected_pv = '', $url = '', $limit = false)
    {
        $this->load->model('tool/image');
        $this->load->model('tool/seo_url');

        $sql = "SELECT DISTINCT cd.name as name, c.parent_id, cd.category_id as category_id, pv.param_value_id, pv.param_id, pv.value, pv2i.image, pv2i.big_image  
            FROM " . DB_PREFIX . " category c 
            INNER JOIN " . DB_PREFIX . "category_description cd ON (cd.category_id = c.category_id)
            INNER JOIN " . DB_PREFIX . "param_to_category par2c ON (par2c.category_id = c.category_id)
            INNER JOIN " . DB_PREFIX . "product_to_category p2c ON (cd.category_id=p2c.category_id)
            INNER JOIN " . DB_PREFIX . "product_to_param_value p2pv ON (p2pv.product_id=p2c.product_id)
            INNER JOIN " . DB_PREFIX . "param_value pv ON(pv.param_value_id = p2pv.param_value_id)
            LEFT JOIN " . DB_PREFIX . "param_value_to_image pv2i ON(pv2i.param_value_id = p2pv.param_value_id)
            WHERE pv.is_active = '1' AND pv.param_id = '" . $this->config->get('config_grouping_param_id') . "' 
            AND cd.language_id = '" . (int) $this->config->get('config_language_id') . "' 
            AND c.status = '1' AND c.sort_order <> '-1' ORDER BY pv.sort_order ";

        if ($limit)
            $sql .= " LIMIT " . (int) $limit;
        $query = $this->db->query($sql);

        $data = $query->rows;

        $category_group = array();

        foreach ($data as $value) {
			
	
            if ($value['parent_id']) {
                $sql = "SELECT DISTINCT cd.name as name   
                FROM category_description cd 
                WHERE cd.category_id =" . $value['parent_id'];
                $parent_query = $this->db->query($sql);

                $parent_category = 0;
                if ($parent_query->num_rows) {
                    $parent_category = $parent_query->row['name'];
                }

                $url_cat = HTTPS_SERVER . 'index.php?route=product/group&pv=' . $value['param_value_id'];
                if (in_array($value['category_id'], $selected_categorys)) {
                    $without_this_value = implode(',', array_diff($selected_categorys, array($value['category_id'])));

                    if ($without_this_value)
                        $url_cat .= '&category=' . $without_this_value;
                } else
                    $url_cat .= '&category=' . $value['category_id'] . ($selected_categorys ? ',' . implode(',', $selected_categorys) : '');

                $image = $value['image'] ? $this->model_tool_image->resize($value['image'], 45, 45, FALSE) : '';
				
			
				
                $big_image = $value['big_image'] ? $this->model_tool_image->resize($value['big_image'], $this->config->get('config_image_category_width'), $this->config->get('config_image_category_height'), FALSE) : '';

                $category_group[$value['param_value_id']]['active'] = $selected_pv == $value['param_value_id'] ? TRUE : FALSE;
                $category_group[$value['param_value_id']]['value'] = $value['value'];
                $category_group[$value['param_value_id']]['image'] = $image;
                $category_group[$value['param_value_id']]['big_image'] = $big_image;
                $category_group[$value['param_value_id']]['href'] = $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/group&pv=' . $value['param_value_id'] . $url);
                $category_group[$value['param_value_id']]['categoty'][$parent_category][$value['category_id']]['name'] = $value['name'];
                $category_group[$value['param_value_id']]['categoty'][$parent_category][$value['category_id']]['href'] = $this->model_tool_seo_url->rewrite($url_cat . $url);
                $category_group[$value['param_value_id']]['categoty'][$parent_category][$value['category_id']]['active'] = in_array($value['category_id'], $selected_categorys);
            }
        }
        return $category_group;
    }

    public function getTotalCategoriesByCategoryId($parent_id = 0)
    {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.parent_id = '" . (int) $parent_id . "' AND c2s.store_id = '" . (int) $this->config->get('config_store_id') . "' AND c.status = '1' AND c.sort_order <> '-1'");

        return $query->row['total'];
    }

    public function getAllCategories()
    {
        $category_data = $this->cache->get('category.all.' . $this->config->get('config_language_id') . '.' . (int) $this->config->get('config_store_id'));

        if (!$category_data) {
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE cd.language_id = '" . (int) $this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int) $this->config->get('config_store_id') . "'  AND c.status = '1' ORDER BY c.parent_id, c.sort_order, LCASE(cd.name)");
            $category_data = $query->rows;
            $this->cache->set('category.all.' . $this->config->get('config_language_id') . '.' . (int) $this->config->get('config_store_id'), $category_data);
        }
        
        return $category_data;
    }

    public function getTotalCategoryProducts($category_id)
    {
        $query = $this->db->query("SELECT COUNT(p2c.product_id) AS total FROM " . DB_PREFIX . "product_to_category p2c LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id = p2c.product_id) WHERE p2c.category_id = '" . (int) $category_id . "' ");

        return $query->row['total'];
    }

}

?>