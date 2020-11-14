<?php

/* OpenCart 1.5 */

class ControllerModuleSalesdrive extends Controller
{
	private $_key;
	private $_url;
	private $handler;
	
	public function __construct($registry)
    {
		parent::__construct($registry);
		$this->_url = 'gra.salesdrive.me';
		$this->_key = '0qycwyKH1TOTciF1dSKwmVzSaywfZHpp7Gc9rfG17TUkdCebUMERZLw0D0Cxg-eXhYHaAvT3';
		$this->handler = 'https://'.$this->_url.'/handler/';
    }
    public function addOrder($order_id) {

		$handle = fopen(__DIR__."/salesdrive_debug.txt", "a");
		$date = date('m/d/Y h:i:s a', time());
		ob_start();
		print($date.". ".$_SERVER['REMOTE_ADDR']."\n");
		
		print("order_id: $order_id\n");

		
		$this->load->model('checkout/order');
		$this->load->model('account/order');
		$this->load->model('catalog/product');
		
		$order = $this->model_checkout_order->getOrder($order_id);
		print_r($order);

		$htmlStr = ob_get_contents()."\n";
		ob_end_clean(); 
		fwrite($handle,$htmlStr);
		/*
		$order_products = $this->model_account_order->getOrderProducts($order_id);

		$data = array();
		$data['externalId'] = $order_id;
		$data['fName'] = htmlspecialchars_decode($order['firstname']);
		$data['lName'] = htmlspecialchars_decode($order['lastname']);
		$data['phone'] = $order['telephone'];
		$data['email'] = $order['email'];
		$data['company'] = htmlspecialchars_decode($order['shipping_company']);
		$data['products'] = array();

		foreach ($order_products as $product) {
			$options = $this->model_account_order->getOrderOptions($order_id, $product['order_product_id']);

			// product data init
			$product_data = array(
				'id' => $product['product_id'],
				'name' => htmlspecialchars_decode($product['name']),
			);

			//generate virtual products by options
			$product_data[] = $product_data;

			$description = '';
			if($options) {
				foreach ($options as $option) {
					if ($option['product_option_value_id'] === '0') {
						$description .= htmlspecialchars_decode($option['name']).': '.htmlspecialchars_decode($option['value']).";\n";
					}
				}
			}
			//die('HERE');
			$data['products'][] = array(
				'id' => $product_data['id'],
				'name' => htmlspecialchars_decode($product_data['name']),
				'costPerItem' => $product['price'] * $order['currency_value'],
				'amount' => $product['quantity'],
				'description' => $description,
			);
		}
		if ($order['shipping_code'] == 'novaposhta.novaposhta') {
			$data['novaposhta'] = array(
				'city' => $order['shipping_zone'],
				'ServiceType' => 'WarehouseWarehouse',
				'WarehouseNumber' => $order['shipping_city'],
			);
		} 

		$shipping_address_2 = $order['shipping_address_2'] !='' ? ', '.htmlspecialchars_decode($order['shipping_address_2']): '';
		$data['shipping_address'] = htmlspecialchars_decode($order['shipping_address_1']). $shipping_address_2;

		$data['shipping_method'] = $order['shipping_method'];
		$data['payment_method'] = $order['payment_method'];
		$data['comment'] = htmlspecialchars_decode($order['comment']);

		$data['prodex24source_full'] = isset($_COOKIE['prodex24source_full']) ? $_COOKIE['prodex24source_full'] : '';
		$data['prodex24source'] = isset($_COOKIE['prodex24source']) ? $_COOKIE['prodex24source'] : '';
		$data['prodex24medium'] = isset($_COOKIE['prodex24medium']) ? $_COOKIE['prodex24medium'] : '';
		$data['prodex24campaign'] = isset($_COOKIE['prodex24campaign']) ? $_COOKIE['prodex24campaign'] : '';
		$data['prodex24content'] = isset($_COOKIE['prodex24content']) ? $_COOKIE['prodex24content'] : '';
		$data['prodex24term'] = isset($_COOKIE['prodex24term']) ? $_COOKIE['prodex24term'] : '';
		$data['prodex24page'] = $_SERVER['HTTP_REFERER'];
		
		//print_r($data);
		//$salesdrive = new Salesdrive($this->config->get('salesdrive_domain'), $this->config->get('salesdrive_key'));
		$this->addOrderSalesdrive($data);
		*/
    }
    
	/*
    private function getVirtualProductData($product_data, $options) {
        // product options
        $product_options = $this->model_catalog_product->getProductOptions($product_data['id']);
        usort($product_options, function ($a, $b)
        {
            if ($a["option_id"] == $b["option_id"]) {
                return 0;
            }
            return ($a["option_id"] < $b["option_id"]) ? -1 : 1;
        });
        
        foreach ($product_options as $option) {
			if(count($option['product_option_value']) > 0) {
				$id = $product_data['id'].'_'.$option['option_id'];

				foreach ($option['product_option_value'] as $k => $option_value) {
					if (in_array($option_value['product_option_value_id'], array_column($options, 'product_option_value_id'))) {
						$product_data = array(
							'id' => $id.'-'.$option_value['option_value_id'],
							'product_id' => $product_data['id'],
							'name' => $product_data['name'].' '.$option_value['name'],
						);
					}
				}
			}
        }
        
        return $product_data;
    }
    */
    private function addOrderSalesdrive($data)
    {
        $_values = $data;
        $_values['form'] = $this->_key;
        $this->execute($this->handler, $_values);
    }
	
	private function execute($actionUrl, $params = array())
	{
        //cURL POST
        $ch = curl_init($actionUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:application/json"));
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);			
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response);		
	}
	/*
	public function update() {
		$feed = $this->config->get('salesdrive_feed');
		$xml = file_get_contents($feed);
		$xml = new SimpleXMLElement($xml);
		$offers = $xml->shop->offers;
		$n = count($offers->offer);
		for ($i = 0; $i < $n; $i++) {
			$offer = $offers->offer[ $i ];
			$id = (string)$offer['id'];
			$qty = (int)$offer->quantity_in_stock;
			$ids = explode('_', $id);
			if(count($ids) == 1) {
				$this->db->query("UPDATE `" . DB_PREFIX . "product` SET `quantity` = '" . $qty . "' WHERE `product_id` = '" . (int)$ids[0] . "'");
			} elseif(count($ids) == 2) {
				$options = explode('-', $ids[1]);
				if(count($options) == 2) {
					$this->db->query("UPDATE `" . DB_PREFIX . "product_option_value` SET `quantity` = '" . $qty . "' WHERE `product_id` = '" . (int)$ids[0] . "' AND `option_id` = '" . (int)$options[0] . "' AND `option_value_id` = '" . (int)$options[1] . "'");
				}
			}
		}
		echo 'Total products in YML feed: ' . $n . '. Quantity successfully updated!';
    }
	*/
}