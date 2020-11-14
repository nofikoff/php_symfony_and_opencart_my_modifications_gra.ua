<?php

// ВНИМАНИЕ ЭТО ОБРАБОТЧИК ДЛЯ ЗАПИСИ В ТЕТРАДКУ
// ВНИМАНИЕ ЭТО ОБРАБОТЧИК ДЛЯ ЗАПИСИ В ТЕТРАДКУ
// ВНИМАНИЕ ЭТО ОБРАБОТЧИК ДЛЯ ЗАПИСИ В ТЕТРАДКУ
// ВНИМАНИЕ ЭТО ОБРАБОТЧИК ДЛЯ ЗАПИСИ В ТЕТРАДКУ



class ModelCheckoutSympleOrder extends Model
{

    // City search suggest
    public function getCitySuggest($keyword, $limit)
    {
        $query = $this->db->query("SELECT c.city_id, c.name as city, r.name as region FROM sf_city c INNER JOIN sf_region r ON r.region_id = c.region_id WHERE c.name LIKE '%" . $keyword . "%' LIMIT " . $limit);

        return $query->rows;
    }

    // Add new buyer
    public function addBuyer($name, $phone)
    {
        $name = $this->db->escape(trim($name));
        $phone = $this->db->escape(trim($phone));

        // ???? почем тут ЛАйк а не равенство
        $buyer = $this->db->query("SELECT b.buyer_id FROM sf_buyer b WHERE b.phone LIKE '" . $phone . "%'");

        if ($buyer->num_rows) {
            //by Novikov 2018 - задача скоректироватьи мя заказчика
            $this->db->query("UPDATE sf_buyer SET name = '" . $name . "' WHERE buyer_id = " . $buyer->row['buyer_id'] . ";");
            return $buyer->row['buyer_id'];
        } else {
            $this->db->query("INSERT INTO sf_buyer (name, phone, created_by, created_at, updated_by, updated_at) VALUES ('" . $name . "', '" . $phone . "', 'site', '" . date('Y-m-d H:i:s') . "', 'site', '" . date('Y-m-d H:i:s') . "')");
            return $this->db->getLastId();
        }
    }

    // Get filial id
    function getFilialId($city_id)
    {
        $filial = $this->db->query("SELECT r.filial_id FROM sf_region r 
            INNER JOIN sf_city c ON c.region_id = r.region_id 
            INNER JOIN sf_filial sf ON r.filial_id = sf.filial_id
            WHERE c.city_id = " . (int)$city_id . " LIMIT 1");

        if ($filial->num_rows)
            return $filial->row['filial_id'];
        else
            return 0;
    }

    // Get city name by ref
    function getCityNameByRef($city_ref)
    {
        $city = $this->db->query("SELECT `Description` FROM `oc_novaposhta_cities` WHERE `Ref` LIKE '".$city_ref."'");

        if ($city->num_rows)
            return $city->row['Description'];
        else
            return 0;
    }

    // Get warehouse id
    function getWarehouseId($city_id)
    {
        $warehouse = $this->db->query("SELECT r.warehouse_id as unit_id FROM sf_region r INNER JOIN sf_city c ON c.region_id = r.region_id WHERE c.city_id = " . (int)$city_id . " LIMIT 1");

        if ($warehouse->num_rows)
            return $warehouse->row['unit_id'];
        else
            return 0;
    }

    // Add new order
    function addOrder(array $order)
    {

        foreach ($order as $field => $value) {
            $fields[] = $field;
            $values[] = $this->db->escape($value);
        }

        $this->db->query("INSERT INTO sf_order (" . implode(', ', $fields) . ") VALUES ('" . implode("', '", $values) . "')");
		
		$order_id = $this->db->getLastId();

		/* SALESDRIVE */
		$session_data = $this->session->data;
		// LOG
		$handle = fopen("salesdrive_log_34rn34fddf.txt", "a");
		$date = date('m/d/Y h:i:s a', time());
		ob_start();
		print("MODEL-addOrder: ".$date.". ".$_SERVER['REMOTE_ADDR']."\n");
		print('post:');
		print_r($_POST);
		print('session-data: ');
		print_r($session_data);
		$htmlStr = ob_get_contents()."\n";
		ob_end_clean(); 
		fwrite($handle,$htmlStr);

        //$this->load->language('checkout/order_success');

        $order_id = $order_id;

		$products = [];
		$i=0;
		$session_products = isset($session_data['order_cart']) ? $session_data['order_cart'] : '';
		foreach($session_products as $session_product){
			$products[$i]["id"] = $session_product['model']; // id товара
			$products[$i]["name"] = $session_product['name']; // название товара
			$products[$i]["costPerItem"] = $session_product['price']; // цена
			$products[$i]["amount"] = $session_product['quantity']; // количество
			$i++;
		}

		$phone = isset($_POST['phone']) ? $_POST['phone'] : '';
		$lName = '';
		$fName = '';
		$mName = '';
		if(isset($_POST['name'])){
			$name = trim($_POST['name']);
			$name = explode(' ',$name,3);
			$lName = $name[0];
			$fName = isset($name[1]) ? $name[1] : '';
			$mName = isset($name[2]) ? $name[2] : '';
		}
		$shipping_method = isset($_POST["easy_nova_poshta_type"])?$_POST["easy_nova_poshta_type"]:"";
		$city_code = isset($_POST["easy_nova_poshta_city"])?$_POST["easy_nova_poshta_city"]:"";
		$city_id = isset($_POST["city_id"])?$_POST["city_id"]:"0";
		$city_name_by_id = $this->getFilialId($city_id);
		$city_name_by_ref = $this->getCityNameByRef($city_code);
		$branch = isset($_POST["easy_nova_poshta_office"])?$_POST["easy_nova_poshta_office"]:"";
		$street = isset($_POST["easy_nova_poshta_street"])?$_POST["easy_nova_poshta_street"]:"";
		$biulding = isset($_POST["easy_nova_poshta_buildingappartment"])?$_POST["easy_nova_poshta_buildingappartment"]:"";
		$novaposhta = [];
		$shipping_address = '';
		if($shipping_method == 'office'){
			$shipping_method = 'Нова Пошта';
			$novaposhta['ServiceType'] = 'WarehouseWarehouse';
			$novaposhta['city'] = $city_code;
			$novaposhta['WarehouseNumber'] = $branch;
			$shipping_address = $city_name_by_ref.', '.$branch;
		}
		if($shipping_method == 'self'){
			$shipping_method = 'Самовывоз';
		}
		if($shipping_method == 'address'){
			$shipping_method = 'Нова Пошта';
			$novaposhta['ServiceType'] = 'WarehouseDoors';
			$novaposhta['city'] = $city_name_by_id;
			$shipping_address = $city_name_by_id.', '.$street.', '.$biulding;
		}
		$comment = isset($_POST["comments"])?$_POST["comments"]:"";

		$_salesdrive_values = [
			"form" => "0qycwyKH1TOTciF1dSKwmVzSaywfZHpp7Gc9rfG17TUkdCebUMERZLw0D0Cxg-eXhYHaAvT3",
			"externalId"=>$order_id, // Заказ
			"lName"=>$lName, // Фамилия
			"fName"=>$fName, // Имя
			"mName"=>$mName, // Отчество
			"phone"=>$phone, // Телефон
			//"email"=>isset($_POST["email"])?$_POST["email"]:"", // Email
			"products"=>$products, //Товары/Услуги
			"comment"=>$comment, // Комментарий
			"shipping_address"=>$shipping_address,
			"shipping_method"=>$shipping_method,
			"sajt"=>'gra.ua',
			"novaposhta"=>$novaposhta,
			"prodex24source_full"=>isset($_COOKIE["prodex24source_full"])?$_COOKIE["prodex24source_full"]:"",
			"prodex24source"=>isset($_COOKIE["prodex24source"])?$_COOKIE["prodex24source"]:"",
			"prodex24medium"=>isset($_COOKIE["prodex24medium"])?$_COOKIE["prodex24medium"]:"",
			"prodex24campaign"=>isset($_COOKIE["prodex24campaign"])?$_COOKIE["prodex24campaign"]:"",
			"prodex24content"=>isset($_COOKIE["prodex24content"])?$_COOKIE["prodex24content"]:"",
			"prodex24term"=>isset($_COOKIE["prodex24term"])?$_COOKIE["prodex24term"]:"",
			"prodex24page"=>isset($_SERVER["HTTP_REFERER"])?$_SERVER["HTTP_REFERER"]:"",
		];

        if(isset($_POST['payment-type'])) {
            $types = array(
                'prepayment-to-the-card' => 'Оплата на карту',
                'payment-upon-receipt' => 'Оплата при получении',
            );

            $payment_type = '';
            if(isset($_POST['payment-type'])) {
                $payment_type = isset($types[$_POST['payment-type']]) ? $types[$_POST['payment-type']] : 'не указано';
            }

            $_salesdrive_values['payment_method'] = $payment_type;
        }

        //LOG
		/*
		ob_start();
		print('_salesdrive_values(MODEL):'); print_r($_salesdrive_values);
		$htmlStr = ob_get_contents()."\n";
		ob_end_clean(); 
		fwrite($handle,$htmlStr);
		*/
		
		$_salesdrive_url = "https://gra.salesdrive.me/handler/";
		$_salesdrive_ch = curl_init();
		curl_setopt($_salesdrive_ch, CURLOPT_URL, $_salesdrive_url);
		curl_setopt($_salesdrive_ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($_salesdrive_ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
		curl_setopt($_salesdrive_ch, CURLOPT_SAFE_UPLOAD, true);
		curl_setopt($_salesdrive_ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($_salesdrive_ch, CURLOPT_POST, 1);
		curl_setopt($_salesdrive_ch, CURLOPT_POSTFIELDS, json_encode($_salesdrive_values));
		curl_setopt($_salesdrive_ch, CURLOPT_TIMEOUT, 10);

		$_salesdrive_res = curl_exec($_salesdrive_ch);
		$_salesdriveerrno = curl_errno($_salesdrive_ch);
		$_salesdrive_error = 0;
		if ($_salesdriveerrno or $_salesdrive_res != "") {
			$_salesdrive_error = 1;
		}
		if ($_salesdrive_error) {
			//LOG
			//fwrite($handle,"Ошибка при отправке заявки! Заявка не отправлена.");
		}
		else{
			//LOG
			//fwrite($handle,"Ваша заявка успешно отправлена.");
		} 

		/* END SALESDRIVE */		
		
		return $order_id;
		
    }

    // Add order comment
    function addOrderComment($order_id, $comment)
    {
        $this->db->query("UPDATE sf_order SET comment = CONCAT(comment,'" . $comment . "') WHERE order_id=" . $order_id);
    }

    // Add order entry
    function addOrderEntry($entry, $warehouse_id)
    {

        $stock_product = $this->db->query("SELECT stock_id, unit_id, price_curr, price_in, code FROM sf_stock WHERE product_id = " . $entry['product_id'] . " AND price_out = " . $entry['price_out'] . " AND is_active = 1 LIMIT 1");

        if (!$stock_product->num_rows) {
            $stock_product = $this->db->query("SELECT stock_id, unit_id, price_curr, price_in, code FROM sf_stock WHERE product_id = " . $entry['product_id'] . " AND is_active = 1 LIMIT 1");
            if (!$stock_product->num_rows) {
                $comment = '<b>Ошибка заказа!: </b>№ товара: ' . $entry['product_id'] . ', товар: ' . $entry['name'] . ', кол-во: ' . $entry['quantity'] . ', цена: ' . $entry['price_out'] . '<br/>';
                $this->addOrderComment($entry['order_id'], $comment);
            }
        }

        if ($stock_product->num_rows) {
            $entry['stock_id'] = $stock_product->row['stock_id'];
            $entry['unit_id'] = mysql_real_escape_string($stock_product->row['unit_id']);
            $entry['price_curr'] = $stock_product->row['price_curr'];
            $entry['price_in'] = $stock_product->row['price_in'];
            $entry['code'] = $stock_product->row['code'];
            $entry['name'] = mysql_real_escape_string($entry['name']);

            $this->db->query("INSERT INTO sf_order_entry (" . implode(',', array_keys($entry)) . ") VALUES ('" . implode("','", array_values($entry)) . "')");

            // order to delivery - FROM
            $delivery_data = array(
                'order_id' => $entry['order_id'],
                'order_entry_id' => $this->db->getLastId(),
                'unit_id' => $warehouse_id,
                'sort_order' => -1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'created_by' => 'site',
                'updated_by' => 'site',
            );

            if ($stock_product->num_rows) {
                $this->db->query("INSERT INTO sf_order_to_delivery (" . implode(',', array_keys($delivery_data)) . ") VALUES ('" . implode("','", array_values($delivery_data)) . "')");
            }
        }
    }

}

?>