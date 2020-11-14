<?php
class ControllerModuleCart extends Controller {
    
	public function index() {
		$this->language->load('module/cart');

		$this->load->model('tool/seo_url');
                $this->load->model('tool/image');

                $this->data['heading_title'] = $this->language->get('heading_title');
		$this->data['text_subtotal'] = $this->language->get('text_subtotal');
		$this->data['text_empty'] = $this->language->get('text_empty');
		$this->data['text_remove'] = $this->language->get('text_remove');
		$this->data['text_confirm'] = $this->language->get('text_confirm');
		$this->data['text_view'] = $this->language->get('text_view');
		$this->data['text_checkout'] = $this->language->get('text_checkout');
                $this->data['text_total'] = $this->language->get('text_total');
                $this->data['text_cart_list'] = $this->language->get('text_cart_list');
                $this->data['text_cart_price'] = $this->language->get('text_cart_price');
       
		$this->data['view'] = $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=checkout/cart');
		$this->data['action_cart'] = $this->model_tool_seo_url->rewrite(HTTPS_SERVER . 'index.php?route=checkout/symple_order');
        $this->data['delete_action'] = $this->model_tool_seo_url->rewrite(HTTPS_SERVER . 'index.php?route=checkout/cart');
        $this->data['item'] = 0;
        $this->data['products'] = array();
       
    	foreach ($this->cart->getProducts() as $result) {
            $image = $result['image'] ? $result['image'] : 'no_image.jpg';
            $this->data['products'][] = array(
                'key'     => $result['key'],
                'quantity' => $result['quantity'],
                'name'     => $result['name'],
                'thumb'    => $this->model_tool_image->resize($image, 80, 50),
                'price'    => $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax'))),
                'href'     => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/product&product_id=' . $result['product_id'])
            );

            $this->data['item'] += $result['quantity'];
    	}

        $this->data['text_item'] = $this->formatItems($this->data['item']);

		$total_data = array();
		$total = 0;
		$taxes = $this->cart->getTaxes();
		$this->load->model('checkout/extension');
		$sort_order = array();
		$results = $this->model_checkout_extension->getExtensions('total');
		foreach ($results as $key => $value) {
			$sort_order[$key] = $this->config->get($value['key'] . '_sort_order');
		}
		array_multisort($sort_order, SORT_ASC, $results);
		foreach ($results as $result) {
			$this->load->model('total/' . $result['key']);

			$this->{'model_total_' . $result['key']}->getTotal($total_data, $total, $taxes);
		}
		$sort_order = array();
		foreach ($total_data as $key => $value) {
      		$sort_order[$key] = $value['sort_order'];
    	}
    	array_multisort($sort_order, SORT_ASC, $total_data);
        $total = end($total_data);
        $this->data['total'] = $total['text'];
        
		$this->data['ajax'] = $this->config->get('cart_ajax');

		$this->id = 'cart';
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/cart.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/module/cart.tpl';
		} else {
			$this->template = 'default/template/module/cart.tpl';
		}

		  $this->response->setOutput( $this->render(TRUE), $this->config->get('config_compression'));
                 
	}

    private function formatItems($item) {
            if(($item>=5) && ($item<=20)) $str = " товаров";
            else {
                $num = $item - (floor($item/10)*10);
                if($num == 1) { $str = " товар"; }
                elseif($num == 0) { $str = "Товаров";}
                elseif(($num>=2) && ($num<=4)) { $str = " товара"; }
                elseif(($num>=5) && ($num<=9)) { $str = " товаров"; }
            }
            return $str ;
    }

	public function callback() {
                $this->language->load('module/cart');
		$this->load->model('tool/seo_url');
                $this->load->model('tool/image');
                unset($this->session->data['shipping_methods']);
		unset($this->session->data['shipping_method']);
		unset($this->session->data['payment_methods']);
		unset($this->session->data['payment_method']);
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
                    if (isset($this->request->post['remove'])) {
                        $result = explode('_', $this->request->post['remove']);
                        $this->cart->remove(trim($result[0]));
                    } else {
                        if (isset($this->request->post['option'])) {
                                $option = $this->request->post['option'];
                        } else {
                                $option = array();
                        }
                        $this->cart->add($this->request->post['product_id'], $this->request->post['quantity'], $option);
                    }
                }
                $output = '';
                $products = array();
		if ($this->cart->getProducts()) {

                    $item = 0;
                    foreach ($this->cart->getProducts() as $result) {
                        $image = $result['image'] ? $result['image'] : 'no_image.jpg';
                        $products[] = array(
                            'key'     => $result['key'],
                            'quantity' => $result['quantity'],
                            'name'     => $result['name'],
                            'thumb'    => $this->model_tool_image->resize($image, 50, 50),
                            'price'    => $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax'))),
                            'href'     => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/product&product_id=' . $result['product_id'])
                        );
                        $item += $result['quantity'];
                    }
                    $total = 0;
                    $taxes = $this->cart->getTaxes();
                    $this->load->model('checkout/extension');
                    $sort_order = array();
                    $view = $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=checkout/cart');
                    $checkout = $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=checkout/shipping');
                    $results = $this->model_checkout_extension->getExtensions('total');
                    foreach ($results as $key => $value) {
                            $sort_order[$key] = $this->config->get($value['key'] . '_sort_order');
                    }
                    array_multisort($sort_order, SORT_ASC, $results);
                    foreach ($results as $result) {
                            $this->load->model('total/' . $result['key']);
                            $this->{'model_total_' . $result['key']}->getTotal($total_data, $total, $taxes);
                    }
                    $sort_order = array();
                    foreach ($total_data as $key => $value) {
                        $sort_order[$key] = $value['sort_order'];
                    }
                    
                    array_multisort($sort_order, SORT_ASC, $total_data);
                    $total = end($total_data);
                    $total = $total['text'];
                    $action_cart = $this->model_tool_seo_url->rewrite(HTTPS_SERVER . 'index.php?route=checkout/symple_order');

                    $output .= '<div class="cart_info">';
                    $output .= '<span class="total">';
                    $output .= $this->formatItems($item) . ' <span>' . $item . '</span> <br/>';
                    $output .= $this->language->get('text_cart_price') . ' <span>' . $total . '</span>';
                    $output .= '</span>';
                    $output .= '<a href="' . $action_cart . '"><span>' . $this->language->get('text_checkout') . '</span></a>';
                    $output .= '</div>';
                    $output .= '<div class="drop">';
                    foreach ($products as $product) {
                        $output .= '<div class="item" id="product' .$product['key'] .'">
                                    <a class="image" href="'.$product['href'].'"><img src="'.$product['thumb'].'" alt="'.$product['name'].'" /></a>
                                    <a class="name" href="'. $product['href'] .'">'.$product['name'].'</a>';

                        if($product['quantity'] > 1)
                            $output .= '('. $product['quantity'] . 'шт.)';

                        $output .= '<span class="price">'.$product['price'].'</span>
                                    <a class="icon delete cart_remove" onclick="cartRemove('.$product['key'].')"></a>
                                    </div>';
                    }
                    $output .= '<a class="button_grey" href="'.$action_cart.'"><span>'.$this->language->get('text_checkout').'</span></a>
                        </div>';
            } else {
                    $output .= '<div class="cart_info"><div class="empty">'. $this->language->get('text_empty') .'</div></div>';
            }
           
            $this->response->setOutput($output, $this->config->get('config_compression'));
        
	}
}
?>