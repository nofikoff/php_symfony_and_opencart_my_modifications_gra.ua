<?php

class ControllerCheckoutSympleOrder extends Controller
{

    private $error = array();
    private $ga_order_info = array();

    public function index()
    {
        $this->load->model('tool/seo_url');

        if ($this->request->server['REQUEST_METHOD'] == 'GET' && isset($this->request->get['product'])) {

            if (isset($this->request->get['option'])) {
                $option = $this->request->get['option'];
            } else {
                $option = array();
            }

            if (isset($this->request->get['quantity'])) {
                $quantity = $this->request->get['quantity'];
            } else {
                $quantity = 1;
            }

            unset($this->session->data['shipping_methods']);
            unset($this->session->data['shipping_method']);
            unset($this->session->data['payment_methods']);
            unset($this->session->data['payment_method']);

            $this->cart->add($this->request->get['product'], $quantity, $option);
            $this->redirect($this->model_tool_seo_url->rewrite(HTTPS_SERVER . 'index.php?route=checkout/symple_order'));
        } elseif (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            if (!isset($this->request->post['remove']) && !isset($this->request->post['qtt'])) {

                if ($this->validate() && !$this->session->data['order_id']) {

                    $order_id = $this->addOrderTetradka();

                    //$this->cart->clear();

                    $this->session->data['order_id'] = $order_id;

                    $this->session->data['ga_order_info'] = $this->ga_order_info;

                    $this->session->data['phone'] = $this->db->escape($this->request->post['phone']);

                    $this->redirect($this->model_tool_seo_url->rewrite(HTTPS_SERVER . 'index.php?route=checkout/order_success'));
                } else {
                    $this->redirect($this->model_tool_seo_url->rewrite(HTTPS_SERVER . 'index.php?route=checkout/order_success'));
                }
            } else {
                if (isset($this->request->post['quantity'])) {
                    if (!is_array($this->request->post['quantity'])) {
                        if (isset($this->request->post['option'])) {
                            $option = $this->request->post['option'];
                        } else {
                            $option = array();
                        }

                        $this->cart->add($this->request->post['product'], $this->request->post['quantity'], $option);
                    } else {
                        foreach ($this->request->post['quantity'] as $key => $value) {
                            $this->cart->update($key, $value);
                        }
                    }

                    unset($this->session->data['shipping_methods']);
                    unset($this->session->data['shipping_method']);
                    unset($this->session->data['payment_methods']);
                    unset($this->session->data['payment_method']);
                }

                if (isset($this->request->post['remove'])) {
                    $this->cart->remove($this->request->post['remove']);
                }

                if (isset($this->request->post['redirect'])) {
                    $this->session->data['redirect'] = $this->request->post['redirect'];
                }


                if (isset($this->request->post['quantity']) || isset($this->request->post['remove'])) {
                    unset($this->session->data['shipping_methods']);
                    unset($this->session->data['shipping_method']);
                    unset($this->session->data['payment_methods']);
                    unset($this->session->data['payment_method']);

                    $this->redirect(HTTPS_SERVER . 'index.php?route=checkout/symple_order');
                }
            }
        }

        // Load files
        $this->load->language('checkout/symple_order');
        $this->load->model('tool/seo_url');
        $this->load->model('tool/image');

        // Text
        $this->document->title = $this->language->get('heading_title') . ' - ' . $this->language->get('title');

        $this->data['heading_title'] = $this->language->get('heading_title');
        $this->data['text_products'] = $this->language->get('text_products');
        $this->data['text_info_contact'] = $this->language->get('text_info_contact');
        $this->data['text_info_delivery'] = $this->language->get('text_info_delivery');
        $this->data['text_info_add'] = $this->language->get('text_info_add');
        $this->data['text_name'] = $this->language->get('text_name');
        $this->data['text_phone'] = $this->language->get('text_phone');
        $this->data['text_city'] = $this->language->get('text_city');
        $this->data['text_address'] = $this->language->get('text_address');
        $this->data['text_confirm'] = $this->language->get('text_confirm');
        $this->data['text_basket'] = $this->language->get('text_basket');
        $this->data['column_image'] = $this->language->get('column_image');
        $this->data['column_name'] = $this->language->get('column_name');
        $this->data['column_model'] = $this->language->get('column_model');
        $this->data['column_quantity'] = $this->language->get('column_quantity');
        $this->data['column_price'] = $this->language->get('column_price');
        $this->data['column_total'] = $this->language->get('column_total');
        $this->data['button_checkout'] = $this->language->get('button_checkout');

        // Errors
        $this->data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';
        $this->data['error_product'] = isset($this->error['product']) ? $this->error['product'] : '';
        $this->data['error_name'] = isset($this->error['name']) ? $this->error['name'] : '';
        $this->data['error_phone'] = isset($this->error['phone']) ? $this->error['phone'] : '';

        $this->data['error_easy_nova_poshta_type'] = isset($this->error['easy_nova_poshta_type']) ? $this->error['easy_nova_poshta_type'] : '';
        $this->data['error_easy_nova_poshta_city'] = isset($this->error['easy_nova_poshta_city']) ? $this->error['easy_nova_poshta_city'] : '';

        if (isset($this->request->post['easy_nova_poshta_type']) && $this->request->post['easy_nova_poshta_type'] == 'address') {
            $this->data['error_easy_nova_poshta_address'] = isset($this->error['easy_nova_poshta_street']) ? $this->error['easy_nova_poshta_street'] : '';
            $this->data['error_easy_nova_poshta_buildingappartment'] = isset($this->error['easy_nova_poshta_buildingappartment']) ? $this->error['easy_nova_poshta_buildingappartment'] : '';
        } else if (isset($this->request->post['easy_nova_poshta_type']) && $this->request->post['easy_nova_poshta_type'] == 'office') {
            $this->data['error_easy_nova_poshta_office'] = isset($this->error['easy_nova_poshta_office']) ? $this->error['easy_nova_poshta_office'] : '';
        }

        $this->data['error_city'] = isset($this->error['city']) ? $this->error['city'] : '';

        // ADDRESS FIELD ===
        $this->data['error_address'] = isset($this->error['address']) ? $this->error['address'] : '';

        $this->data['error_comments'] = isset($this->error['comments']) ? $this->error['comments'] : '';

        // Form
        $this->data['name'] = isset($this->request->post['name']) ? $this->request->post['name'] : '';
        $this->data['phone'] = isset($this->request->post['phone']) ? $this->request->post['phone'] : '';

        $this->data['easy_nova_poshta_city'] = isset($this->request->post['easy_nova_poshta_city']) ? $this->request->post['easy_nova_poshta_city'] : '';

        if (isset($this->request->post['easy_nova_poshta_type']) && $this->request->post['easy_nova_poshta_type'] == 'address') {
            $this->data['easy_nova_poshta_street'] = isset($this->request->post['easy_nova_poshta_street']) ? $this->request->post['easy_nova_poshta_street'] : '';
            $this->data['easy_nova_poshta_buildingappartment'] = isset($this->request->post['easy_nova_poshta_buildingappartment']) ? $this->request->post['easy_nova_poshta_buildingappartment'] : '';
        } else if (isset($this->request->post['easy_nova_poshta_type']) && $this->request->post['easy_nova_poshta_type'] == 'office') {
            $this->data['easy_nova_poshta_office'] = isset($this->request->post['easy_nova_poshta_office']) ? $this->request->post['easy_nova_poshta_office'] : '';
        }

        $this->data['city_id'] = isset($this->request->post['city_id']) ? $this->request->post['city_id'] : '';
        $this->data['city_name'] = isset($this->request->post['city_name']) ? $this->request->post['city_name'] : '';

        // ADDRESS FIELD ===
        $this->data['address'] = isset($this->request->post['address']) ? $this->request->post['address'] : '';

        $this->data['comments'] = isset($this->request->post['comments']) ? $this->request->post['comments'] : '';

        $this->data['action'] = $this->model_tool_seo_url->rewrite(HTTPS_SERVER . 'index.php?route=checkout/symple_order');
        $this->data['suggest'] = $this->model_tool_seo_url->rewrite(HTTPS_SERVER . 'index.php?route=checkout/symple_order/suggest');

        if ($this->config->get('easy_novaposhta_status'))
            $this->data['easy_novaposhta_status'] = $this->config->get('easy_novaposhta_status');
        else
            $this->data['easy_novaposhta_status'] = '';

        if ($this->config->get('easy_novaposhta_api_key'))
            $this->data['easy_novaposhta_api_key'] = $this->config->get('easy_novaposhta_api_key');
        else
            $this->data['easy_novaposhta_api_key'] = '';

        if (isset($_SERVER["HTTP_REFERER"]))
            $this->data['cart_back'] = $_SERVER['HTTP_REFERER'];
        else
            $this->data['cart_back'] = $this->model_tool_seo_url->rewrite(HTTPS_SERVER . 'index.php?route=checkout/cart');

// Product list
        $this->data['products'] = array();

        $this->data['payment_info'] = html_entity_decode($this->config->get('config_payment_info_1'));

        $total_usd = 0;
        $tax_class_id = 0;
        foreach ($this->cart->getProducts() as $result) {
            $option_data = array();

            foreach ($result['option'] as $option) {
                $option_data[] = array(
                    'name' => $option['name'],
                    'value' => $option['value']
                );
            }

            $image = $result['image'] ? $result['image'] : 'no_image.jpg';

            $this->data['products'][] = array(
                'key' => $result['key'],
                'name' => $result['name'],
                'category_name' => $result['category_name'],
                'product_id' => $result['product_id'],
                'model' => $result['model'],
                'thumb' => $this->model_tool_image->resize($image, $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height')),
                'option' => $option_data,
                'quantity' => $result['quantity'],
                'stock' => $result['stock'],
                'price' => $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax'))),
                'total' => $this->currency->format($this->tax->calculate($result['total'], $result['tax_class_id'], $this->config->get('config_tax'))),
                'href' => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/product&product_id=' . $result['product_id'])
            );
            $total_usd += $result['total'];
            $tax_class_id = $result['tax_class_id'];
        }

        // Totals
        $this->data['total_usd'] = $this->currency->format($this->tax->calculate($total_usd, $tax_class_id, $this->config->get('config_tax')), "usd");
        $total_data = array();
        $total = 0;
        $taxes = $this->cart->getTaxes();
        $this->load->model('checkout/extension');
        $sort_order = array();
        $results = $this->model_checkout_extension->getExtensions('total');

        foreach ($results as $key => $value)
            $sort_order[$key] = $this->config->get($value['key'] . '_sort_order');

        array_multisort($sort_order, SORT_ASC, $results);

        foreach ($results as $result) {
            $this->load->model('total/' . $result['key']);
            $this->{'model_total_' . $result['key']}->getTotal($total_data, $total, $taxes);
        }

        $sort_order = array();
        foreach ($total_data as $key => $value)
            $sort_order[$key] = $value['sort_order'];

        array_multisort($sort_order, SORT_ASC, $total_data);
        $this->data['totals'] = $total_data;




if ($_SERVER['REMOTE_ADDR']=='195.211.136.9'){
        $this->template = 'default/template/checkout/symple_order_new.tpl';
}else{
        // Template
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/symple_order.tpl'))
            $this->template = $this->config->get('config_template') . '/template/checkout/symple_order.tpl';
        else
            $this->template = 'default/template/checkout/symple_order.tpl';
}

        $this->children = array(
            'common/column_right',
            'common/footer',
            'common/column_left',
            'common/header'
        );
        $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
    }

    private function validate()
    {
        $this->load->language('checkout/symple_order');

        if ($this->cart->hasProducts() < 1)
            $this->error['product'] = $this->language->get('error_product');

        // check name
        $strlen = strlen(utf8_decode($this->request->post['name']));
        if (!$strlen || $strlen > 100)
            $this->error['name'] = $this->language->get('error_name');

        // check city
        if ((int)$this->request->post['city_id'] < 1) {
            $this->error['city'] = $this->language->get('error_city');
            unset($this->request->post['city_id']);
            unset($this->request->post['city_name']);
        }

        if ($this->config->get('easy_novaposhta_status')) {

            if (!$this->request->post['easy_nova_poshta_type'])
                $this->error['easy_nova_poshta_type'] = $this->language->get('error_easy_nova_poshta_type');

            if (isset($this->request->post['easy_nova_poshta_type']) && $this->request->post['easy_nova_poshta_type'] == 'address') {

                if (!$this->request->post['easy_nova_poshta_city'])
                    $this->error['easy_nova_poshta_city'] = $this->language->get('error_easy_nova_poshta_city');

                if (!$this->request->post['easy_nova_poshta_street'])
                    $this->error['easy_nova_poshta_street'] = $this->language->get('error_easy_nova_poshta_address');

                if (!$this->request->post['easy_nova_poshta_buildingappartment'])
                    $this->error['easy_nova_poshta_buildingappartment'] = $this->language->get('error_easy_nova_poshta_buildingappartment');
            } else if (isset($this->request->post['easy_nova_poshta_type']) && $this->request->post['easy_nova_poshta_type'] == 'office') {

                if (!$this->request->post['easy_nova_poshta_city'])
                    $this->error['easy_nova_poshta_city'] = $this->language->get('error_easy_nova_poshta_city');

                if (!$this->request->post['easy_nova_poshta_office'])
                    $this->error['easy_nova_poshta_office'] = $this->language->get('error_easy_nova_poshta_office');
            }
        } else {
            if (!$this->request->post['address'])
                $this->error['address'] = $this->language->get('error_address');
        }

        $this->request->post['phone'] = str_replace('+', '', $this->request->post['phone']);
        $this->request->post['phone'] = str_replace('-', '', $this->request->post['phone']);
        $this->request->post['phone'] = str_replace(' ', '', $this->request->post['phone']);
        $this->request->post['phone'] = str_replace('(', '', $this->request->post['phone']);
        $this->request->post['phone'] = str_replace(')', '', $this->request->post['phone']);
        $this->request->post['phone'] = trim($this->request->post['phone'], '+ () -');
        //$this->request->post['phone'] = preg_replace('/^380/', '0', $this->request->post['phone']);

        if (!substr_count($this->request->post['phone'],'+')) {
        $this->request->post['phone'] = '+'.$this->request->post['phone'];
        }

        //        if (strpos($this->request->post['phone'], "38") === false) $this->request->post['phone'] = '38' . $this->request->post['phone'];

        /*
        if (!preg_match('/^\d{10}$/', $this->request->post['phone'])) {

            $this->error['phone'] = $this->language->get('error_phone') . ' Должно быть формат телефона : 0671234567';

        }
        */

        // важно + иначе тетрадка не пропустит
        //$this->request->post['phone'] = '+38' . $this->request->post['phone'];

        // if no errors -> true
        return empty($this->error);
    }

    public function suggest()
    {
        $output = '';

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->post['keyword'])) {

            $this->load->model('checkout/symple_order');
            $keyword = trim($this->request->post['keyword']);
            $results = $this->model_checkout_symple_order->getCitySuggest($keyword, 15);

            if ($results) {
                $output .= '<div class="suggest">';

                foreach ($results as $key => $result) {
                    $output .= '<a class="' . (!$key ? 'first' : '') . '" onclick="setCity(\'' . $result['city_id'] . '\', \'' . $result['city'] . ' (' . $result['region'] . ')\')">'
                        . '<span class="city">' . preg_replace('#' . $keyword . '#ui', '<b>' . $keyword . '</b>', $result['city']) . '</span>'
                        . '<span class="region">(' . $result['region'] . ')</span>'
                        . '</a>';
                }
                $output .= '</div>';
            }
        }

        $this->response->setOutput($output, $this->config->get('config_compression'));
    }

    // Create order in TETRADKA
    private function addOrderTetradka()
    {
        // load model
        $this->load->model('checkout/symple_order');
        $this->load->model('localisation/currency');
        $total = $this->cart->getTotal();

        // get currencies
        $currency = $this->model_localisation_currency->getCurrencies();

        // get filial
        $filial_id = $this->model_checkout_symple_order->getFilialId($this->request->post['city_id']);

        if ($filial_id) {

            // add buyer
            $buyer_id = $this->model_checkout_symple_order->addBuyer($this->request->post['name'], $this->request->post['phone']);

            // order total
            $total = $this->cart->getTotal();

            if ($this->config->get('easy_novaposhta_status')) {
                if (isset($this->request->post['easy_nova_poshta_type']) && $this->request->post['easy_nova_poshta_type'] == 'address') {
                    $order_address = 'По адресу: '.$this->request->post['easy_nova_poshta_city'].', '.$this->request->post['easy_nova_poshta_street'].', '.$this->request->post['easy_nova_poshta_buildingappartment'];
                } else if (isset($this->request->post['easy_nova_poshta_type']) && $this->request->post['easy_nova_poshta_type'] == 'office') {
                    $order_address = 'Склад новая почта: '.$this->request->post['easy_nova_poshta_city'].', '.$this->request->post['easy_nova_poshta_office'];
                } else if (isset($this->request->post['easy_nova_poshta_type']) && $this->request->post['easy_nova_poshta_type'] == 'self') {
                    $order_address = 'Самовывоз магазин: г. Киев, ул. Михаила Бойчука (Киквидзе), 26, вход со двора 2-й подъезд, домофон 77в';
                }
            } else {
                $order_address = $this->request->post['city_name'] . ($this->request->post['address'] ? ', ' . $this->request->post['address'] : '');
            }

            // order info
            $order_data = array(
                'buyer_id' => $buyer_id,
                'status_id' => 'order_waiting',
                'filial_id' => $filial_id,
                'address' => $order_address,
                'comment' => $this->request->post['comments'] ? '<b>Покупатель:</b> ' . trim(htmlspecialchars(@strip_tags($this->request->post['comments']))) . '<br/>' : '',
                'discount' => 1,
                'is_direct' => 1,
                'amount_uah' => $total,
                'amount_usd' => $total * $currency['usd']['value'],
                'amount_eur' => $total * $currency['eur']['value'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'created_by' => 'site',
                'updated_by' => 'site',
                'byer_history' => $this->request->post['name'],
            );

            if ($this->config->get('easy_novaposhta_status')) {
                if (isset($this->request->post['easy_nova_poshta_type']) && $this->request->post['easy_nova_poshta_type'] == 'address') {
                    $this->session->data['address'] = 'По адресу: '.$this->request->post['easy_nova_poshta_city'].', '.$this->request->post['easy_nova_poshta_street'].', '.$this->request->post['easy_nova_poshta_buildingappartment'];
                } else if (isset($this->request->post['easy_nova_poshta_type']) && $this->request->post['easy_nova_poshta_type'] == 'office') {
                    $this->session->data['address'] = 'Склад новая почта: '.$this->request->post['easy_nova_poshta_city'].', '.$this->request->post['easy_nova_poshta_office'];
                } else if (isset($this->request->post['easy_nova_poshta_type']) && $this->request->post['easy_nova_poshta_type'] == 'self') {
                    $this->session->data['address'] = 'Самовывоз магазин: г. Киев, ул. Михаила Бойчука (Киквидзе), 26, вход со двора 2-й подъезд, домофон 77в';
                }
            } else {
                $this->session->data['address'] = $this->request->post['city_name'] . ($this->request->post['address'] ? ', ' . $this->request->post['address'] : '');
            }

            // ADDRESS FIELD ===
			$this->session->data['address'] = isset($this->request->post['address']) ? $this->request->post['address'] : '';

			$this->session->data['name'] = isset($this->request->post['name']) ? $this->request->post['name'] : '';
			$this->session->data['amount_uah'] = $total;
			$this->session->data['order_cart'] = $this->cart->getProducts();

            $types = array(
                'prepayment-to-the-card' => 'Оплата на карту',
                'payment-upon-receipt' => 'Оплата при получении',
            );

            $payment_type = '';
            if(isset($this->request->post['payment-type'])) {
                $payment_type = isset($types[$this->request->post['payment-type']]) ? $types[$this->request->post['payment-type']] : 'не указано';
            }

            // PAYMENT FIELD ===
            $this->session->data['payment-type'] = $payment_type;

            // add order
            $order_id = $this->model_checkout_symple_order->addOrder($order_data);

            // by Novikov 2018
            // отправка СМС
            // отправка СМС
            // отправка СМС
            // отправка СМС
            if ($_SERVER["HTTP_CF_CONNECTING_IP"] == '46.219.78.155' OR 1) {
                $txt = $this->request->post['name'] . ",\nваш заказ $order_id на сумму $total грн успешно оформлен";
                // Данные авторизации
                $auth = [
                    'login' => SMS_USER,
                    'password' => SMS_PASS
                ];
                try {
                    // полный код здесь
                    //https://turbosms.ua/soap.html
                    // Подключаемся к серверу
                    $client = new SoapClient(SMS_GATE);
                    $result = $client->Auth($auth);
//                    print_r($result);
                    $sms = [
                        'sender' => SMS_SENDER,
                        'destination' => $this->request->post['phone'],
                        'text' => $txt
                    ];
                    $result = $client->SendSMS($sms);
//                    print_r($result);
//                    exit;
                } catch (Exception $e) {
                    echo 'Ошибка: ' . $e->getMessage() . PHP_EOL;
                }
            }


            // склад транспортной компании по городу
            $warehouse_id = $this->model_checkout_symple_order->getWarehouseId($this->request->post['city_id']);

            // add order entry
            foreach ($this->cart->getProducts() as $key => $product) {
                $order_entry_data = array(
                    'order_id' => $order_id,
                    'stock_id' => '', //!
                    'product_id' => $product['product_id'],
                    'unit_id' => '', //!
                    'name' => $product['name'],
                    'quantity' => $product['quantity'],
                    'price_curr' => '', //!
                    'price_in' => '', //!
                    'price_out' => (int)$product['price'],
                    'code' => '', //!
                    'status_id' => 'order_entry_waiting',
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => 'site',
                );

                $this->model_checkout_symple_order->addOrderEntry($order_entry_data, $warehouse_id);

                $ga_products[] = array(
                    'order_id' => $order_id,
                    'product_id' => $product['product_id'],
                    'product_name' => $product['name'] . ' ' . $product['model'],
                    'category_name' => $product['category_name'],
                    'price' => (int)($product['price'] * 0.3),
                    'quantity' => (int)$product['quantity']
                );

            }

            $this->ga_order_info[] = array(
                'order_id' => $order_id,
                'total' => (int)($total * 0.3),
                'city' => (isset($this->request->post['city_name']) ? $this->request->post['city_name'] : ''), //$this->model_checkout_symple_order->getCity($this->request->post['city_id']),
                'products' => $ga_products,
            );

            return $order_id;
        } else {
            return 0;
        }
    }

}

?>