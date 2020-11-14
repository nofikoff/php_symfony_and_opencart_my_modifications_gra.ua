<?php  
class ControllerCommonFooter extends Controller {
	protected function index() {
		$this->language->load('common/footer');
		
		$this->data['text_powered_by'] = sprintf($this->language->get('text_powered_by'), $this->config->get('config_name'), date('Y', time()));
                    
        if (!$this->config->get('config_store_id')) {
            $this->data['footer_contact'] = html_entity_decode($this->config->get('config_footer_contact_' . $this->config->get('config_language_id')), ENT_QUOTES, 'UTF-8');
            $this->data['footer_partner'] = html_entity_decode($this->config->get('config_footer_partner_' . $this->config->get('config_language_id')), ENT_QUOTES, 'UTF-8');
            $footer_block_left = html_entity_decode($this->config->get('config_footer_block_left_' . $this->config->get('config_language_id')), ENT_QUOTES, 'UTF-8');
            $this->data['footer_block_right'] = html_entity_decode($this->config->get('config_footer_block_right_' . $this->config->get('config_language_id')), ENT_QUOTES, 'UTF-8');
            
            
                } else {
			$store_info = $this->model_setting_store->getStore($this->config->get('config_store_id'));

			if ($store_info) {
                $this->data['footer_contact'] = html_entity_decode($store_info['description'], ENT_QUOTES, 'UTF-8');
                $this->data['footer_partner'] = html_entity_decode($store_info['description'], ENT_QUOTES, 'UTF-8');
                $footer_block_left = html_entity_decode($store_info['description'], ENT_QUOTES, 'UTF-8');
                $this->data['footer_block_right'] = html_entity_decode($store_info['description'], ENT_QUOTES, 'UTF-8');
			} else {
                $this->data['footer_contact'] = '';
                $this->data['footer_partner'] = '';
                $this->data['footer_block_left'] = '';
                $this->data['footer_block_right'] = '';

			}
		}
		
		
		
		
		
		
		
		// REMARKETING
		$data['remarketing_code'] = '';
		$data['tag_params'] = '';

		if (1||$this->config->get('config_remarketing_code')) {
		
		if (isset($this->request->get['route'])) {
			$route = $this->request->get['route'];
		} else {
			$route = '';
		}
		$this->load->model('catalog/product');
		$this->load->model('checkout/order');	
		
		$this->data['dynx_itemid'] = array();
		$this->data['dynx_totalvalue'] = '';
	
		switch($route) {
			case '':			
			case 'common/home':	
				$this->data['dynx_pagetype'] = 'home';
		break;	
			case 'product/category':
			case 'product/search':
			case 'product/special':
			case 'product/manufacturer/info':
				$this->data['dynx_pagetype'] = false;
				break;		
			case 'product/product':
			//	$this->data['dynx_pagetype'] = 'product';
			//	$product_info = $this->model_catalog_product->getProduct($this->request->get['product_id']);
			//	$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			//	if ((float)$product_info['special']) {
			//		$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			//	} else {
			//		$special = false;
			//	}  
			//	$price_formatted = trim(preg_replace('/[^\d.]/','', ($special ? $special : $price)), '.');
			//	$this->data['dynx_itemid'][] = $product_info['product_id'];
			//	$this->data['dynx_totalvalue'] = $price_formatted;
			//	$this->data['product_sku'] = $product_info['sku'];
			//	$this->data['product_price'] = $price_formatted;
			//	$this->data['product_name'] = $product_info['name'];
			//	$this->data['product_category'] = $this->model_catalog_product->getProductMainCategory($this->request->get['product_id']);
				
				break;	
			case 'checkout/cart':
			case 'checkout/simplecheckout':
			case 'checkout/checkout':
			case 'checkout/unicheckout':
			case 'checkout/green_checkout':
			case 'checkout/revcheckout':
			case 'checkout/oct_fastorder':
			case 'checkout/symple_order':
				$this->data['dynx_pagetype'] = 'checkout';
				$products = $this->cart->getProducts();
				
				foreach ($products as $product) {
					$this->data['dynx_itemid'][] = $product['model'];			
				} 
				$this->data['dynx_totalvalue'] = trim(preg_replace('/[^\d.]/','', $this->currency->format($this->cart->getTotal(), $this->session->data['currency'])),'.'); 
				
		$this->data['checkout_ecommerce'] = '';
		$this->data['checkout_ecommerce'] .= '<script type="text/javascript">'."\n";
		$this->data['checkout_ecommerce'] .= 'window.dataLayer = window.dataLayer || [];'."\n";
		$this->data['checkout_ecommerce'] .= 'dataLayer.push({'."\n";
		$this->data['checkout_ecommerce'] .= "'ecommerce': {"."\n";
		$this->data['checkout_ecommerce'] .= "'currencyCode': '" . $this->session->data['currency']. "',"."\n";
		$this->data['checkout_ecommerce'] .= "'checkout': {"."\n";
		$this->data['checkout_ecommerce'] .= "'actionField': {'step': 1},"."\n";
		$this->data['checkout_ecommerce'] .= "'products': ["."\n";
		foreach($products as $product) {
			$this->data['checkout_ecommerce'] .= "{"."\n";
			$this->data['checkout_ecommerce'] .= "'name': '" . $product['name'] . "',"."\n";
			$this->data['checkout_ecommerce'] .= "'id': '" . $product['model'] . "',"."\n";
			$this->data['checkout_ecommerce'] .= "'price': '" . preg_replace('/[^\d.]/','', $product['price']) . "',"."\n";
			$this->data['checkout_ecommerce'] .= "'quantity': '" . $product['quantity'] . "'},"."\n";
		}
		$this->data['checkout_ecommerce'] = rtrim($this->data['checkout_ecommerce'], ',');
		$this->data['checkout_ecommerce'] .= "]}},\n";
		$this->data['checkout_ecommerce'] .= "'event': 'gtm-ee-event',
		'gtm-ee-event-category': 'Enhanced Ecommerce',
		'gtm-ee-event-action': 'Checkout Step 1',
		'gtm-ee-event-non-interaction': 'False'";
		$this->data['checkout_ecommerce'] .= '});'."\n</script>\n";
				
		break;	
			case 'checkout/success':
			case 'checkout/green_success':
			case 'checkout/order_success':
		
				$this->data['dynx_pagetype'] = 'purchase';
				if (isset($this->session->data['order_id'])) {
				$order_info = $this->getOrderRemarketing($this->session->data['order_id']);
				if ($order_info) {
					$this->data['dynx_itemid'] = $order_info['products'];
					$this->data['dynx_totalvalue'] = trim(preg_replace('/[^\d.]/','', $this->currency->format($order_info['total'], $this->session->data['currency'])),'.');
				}
                /*
		$order_info_e = $this->getOrderEcommerce($this->session->data['order_id']);		
		$this->data['checkout_ecommerce'] = '';
		$this->data['checkout_ecommerce'] .= '<script type="text/javascript">'."\n";
		$this->data['checkout_ecommerce'] .= 'window.dataLayer = window.dataLayer || [];'."\n";
		$this->data['checkout_ecommerce'] .= 'dataLayer.push({'."\n";
		$this->data['checkout_ecommerce'] .= "'ecommerce': {"."\n";
		$this->data['checkout_ecommerce'] .= "'currencyCode': '" . $this->session->data['currency']. "',"."\n";
		$this->data['checkout_ecommerce'] .= "'purchase': {"."\n";
		$this->data['checkout_ecommerce'] .= "'actionField': {'id': ". $this->session->data['order_id'] . ","."\n";
		$this->data['checkout_ecommerce'] .= "'affiliation': '". $order_info_e['store_name'] . "',"."\n";
		$this->data['checkout_ecommerce'] .= "'revenue': '". $order_info_e['total'] . "'\n},"."\n";
		
		$this->data['checkout_ecommerce'] .= "'products': ["."\n";
		foreach($order_info_e['products'] as $product) {
			$this->data['checkout_ecommerce'] .= "{"."\n";
			$this->data['checkout_ecommerce'] .= "'name': '" . $product['name'] . "',"."\n";
			$this->data['checkout_ecommerce'] .= "'id': '" . $product['model'] . "',"."\n";
			$this->data['checkout_ecommerce'] .= "'price': '" . preg_replace('/[^\d.]/','', $product['price']) . "',"."\n";
			$this->data['checkout_ecommerce'] .= "'quantity': '" . $product['quantity'] . "'},"."\n";
		}
		$this->data['checkout_ecommerce'] = rtrim($this->data['checkout_ecommerce'], ',');
		$this->data['checkout_ecommerce'] .= "]}},\n";
		$this->data['checkout_ecommerce'] .= "'event': 'gtm-ee-event',
		'gtm-ee-event-category': 'Enhanced Ecommerce',
		'gtm-ee-event-action': 'Purchase',
		'gtm-ee-event-non-interaction': 'False'";
		$this->data['checkout_ecommerce'] .= '});'."\n</script>\n";
        */
			
		unset($this->session->data['order_id']); 
				} else {
				$this->data['dynx_pagetype'] = 'other';
				}
				break;	 
			default:
				$this->data['dynx_pagetype'] = 'other';
				break;
		}
		if (count($this->data['dynx_itemid']) > 1){
			$dynx_itemid =  implode(',', $this->data['dynx_itemid']);
		} elseif (!empty($this->data['dynx_itemid'])) {
			$dynx_itemid = $this->data['dynx_itemid'][0];
		} else {
			$dynx_itemid = '';	
		}
		if ($this->data['dynx_pagetype']) {
		
		$this->data['tag_params'] .= '<script type="text/javascript">'."\n";
		$this->data['tag_params'] .= 'window.dataLayer = window.dataLayer || [];'."\n";
		$this->data['tag_params'] .= 'dataLayer.push({'."\n";
		if (!empty($dynx_itemid)) $this->data['tag_params'] .= "'productID': " . "'" . $dynx_itemid . "',"."\n";
		$this->data['tag_params'] .= "'pageType': '" . $this->data['dynx_pagetype'] ."'," . "\n";
		if ($this->data['dynx_pagetype'] == 'product') {
			$this->data['tag_params'] .= "'productSCU': '" . $this->data['product_sku'] ."'," . "\n";
			$this->data['tag_params'] .= "'productPrice': '" . $this->data['product_price'] ."'," . "\n";
			$this->data['tag_params'] .= "'productName': '" . $this->data['product_name'] ."'," . "\n";
			if ($this->data['product_category']) $this->data['tag_params'] .= "'productCategory': '" . $this->data['product_category'] ."'," . "\n";
		}
		if (!empty($this->data['dynx_totalvalue']) && ($this->data['dynx_pagetype'] == 'purchase' || $this->data['dynx_pagetype'] == 'checkout')) $this->data['tag_params'] .= "'totalValue': '". $this->data['dynx_totalvalue'] . "'\n";
		$this->data['tag_params'] .= '});'."\n</script>\n";
		
		$this->document->setDatalayer($this->data['tag_params']);
		
		}
		}
		
		
		
      
                $this->data['feedback_href'] = $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=information/feedback');
                $this->data['sitemap_href'] = $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=information/sitemap');
                $this->data['feedback_tittle'] = 'Оставьте отзыв'; 
                
		$this->id = 'footer';

        $this->children = array(
             'module/information',
             'module/information_article',
             'module/special'
            );
        
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/footer.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/common/footer.tpl';
		} else {
			$this->template = 'default/template/common/footer.tpl';
		}
		
		if ($this->config->get('google_analytics_status')) {
			$this->data['google_analytics'] = html_entity_decode($this->config->get('google_analytics_code'), ENT_QUOTES, 'UTF-8');
		} else {
			$this->data['google_analytics'] = '';
		}
		
		$this->render();
	}
	
	public function getOrderEcommerce($order_id) {
			$order_query = $this->db->query("SELECT o.store_name, o.total FROM `" . DB_PREFIX . "order` o WHERE o.order_id = '" . (int)$order_id . "'");
			if ($order_query->num_rows) {
				$order_product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
				$products = array();
				foreach ($order_product_query->rows as $product) {
					$products[] =  array(
					'product_id' => $product['model'],
					'quantity' =>  $product['quantity'],
					'price' =>  $product['price'],
					'name' =>  $product['name']
					);
				}
				return array(
					'products'      => $products,
					'total'         => $order_query->row['total'],
					'store_name'    => $order_query->row['store_name'],
				);
			} else {
				return false;
			}  
		}
		 	public function getOrderRemarketing($order_id) {
			$order_query = $this->db->query("SELECT o.total FROM `" . DB_PREFIX . "order` o WHERE o.order_id = '" . (int)$order_id . "'");
			if ($order_query->num_rows) {
				$order_product_query = $this->db->query("SELECT model FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
				$products = array();
				foreach ($order_product_query->rows as $product) {
					$products[] =  $product['model'];
				}
				return array(
					'products'      => $products,
					'total'         => $order_query->row['total'],
				);
			} else {
				return false;
			}  
		}
}
?>