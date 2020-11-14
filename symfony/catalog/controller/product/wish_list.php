<?php

class ControllerProductWishList extends Controller {

    public function index() {
        $this->language->load('product/wish_list');

        $this->load->model('catalog/product');

        $this->load->model('tool/image');
        $this->load->model('tool/seo_url');

        if (!isset($this->session->data['wish_list'])) {
            $this->session->data['wish_list'] = array();
        }

        if (isset($this->request->post['remove'])) {
            $key = array_search($this->request->post['remove'], $this->session->data['wish_list']);

            if ($key !== false) {
                unset($this->session->data['wish_list'][$key]);
            }
        }

        $this->document->setTitle($this->language->get('heading_title'));

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => HTTP_SERVER . 'index.php?route=common/home',
            'separator' => false
        );

        $url = '';

        if (isset($this->request->get['wish_list'])) {
            $url .= '?wish_list=' . $this->request->get['wish_list'];
        }

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => HTTP_SERVER . 'index.php?route=product/wish_list' . $url,
            'separator' => $this->language->get('text_separator')
        );

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_product'] = $this->language->get('text_product');
        $this->data['text_name'] = $this->language->get('text_name');
        $this->data['text_image'] = $this->language->get('text_image');
        $this->data['text_price'] = $this->language->get('text_price');
        $this->data['text_model'] = $this->language->get('text_model');
        $this->data['text_manufacturer'] = $this->language->get('text_manufacturer');
        $this->data['text_availability'] = $this->language->get('text_availability');
        $this->data['text_rating'] = $this->language->get('text_rating');
        $this->data['text_summary'] = $this->language->get('text_summary');
        $this->data['text_weight'] = $this->language->get('text_weight');
        $this->data['text_dimension'] = $this->language->get('text_dimension');
        $this->data['text_remove'] = $this->language->get('text_remove');
        $this->data['text_empty'] = $this->language->get('text_empty');

        $this->data['button_cart'] = $this->language->get('button_add_to_cart');
        $this->data['button_continue'] = $this->language->get('button_continue');

        $this->data['action'] = HTTP_SERVER . 'index.php?route=product/wish_list';

       
        $categories = $this->model_catalog_product->getProductsCategories($this->session->data['wish_list']);
        $this->data['categories'] = array();
        foreach ($categories as $category) {
            if($category['count']>0){
                $this->data['categories'][$category['category_id']]['id'] = $category['category_id'];
                $this->data['categories'][$category['category_id']]['name'] = $category['name'];
                $this->data['categories'][$category['category_id']]['count'] = $category['count'];
            }
        }
    
        foreach ($this->session->data['wish_list'] as $product_id) {
            
                
                $product_info = $this->model_catalog_product->getProduct($product_id);
                if ($product_info) {
                    if (key_exists($product_info['category_id'], $this->data['categories']) ) {
                      
//by Novikov исходники непрверяем картимнок
//                        if ($product_info['image'] && file_exists(DIR_IMAGE . $product_info['image']))
                        if ($product_info['image'])
                            $image = $product_info['image'];
                        else
                            $image = 'no_image.jpg';

                        if ($product_info['quantity'] <= 0) {
                            $availability = $product_info['stock_status'];
                        } elseif ($this->config->get('config_stock_display')) {
                            $availability = $product_info['quantity'];
                        } else {
                            $availability = $this->language->get('text_instock');
                        }

//                        $attribute_data = array();
//                        $attributes = array(); 
//                        $attributes = $this->model_catalog_product->getParamsDesc($product_id, $category_id);

                        $price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')));
            
                        
                         $this->data['categories'][$product_info['category_id']]['products'][$product_id] = array(
                            'product_id' => $product_info['product_id'],
                            'name' => $product_info['name'],
                            'thumb' => $this->model_tool_image->resize($image, $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height')),
                            'price' => $price,
                            'status' => $product_info['status'],
                            'special' => 0,
                            'description' => substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0, 200) . '..',
                            'model' => $product_info['model'],
                            'manufacturer' => $product_info['manufacturer'],
                            'availability' => $availability,
                            'add' => $this->model_tool_seo_url->rewrite(HTTPS_SERVER . 'index.php?route=checkout/symple_order&product=' . $product_id),
                            'href' => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/product&product_id=' . $product_id),
                            'remove' => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/product&product_id=' . $product_id)
                        );

                    }
                }
                
            
        }

        $this->data['continue'] =isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : HTTP_SERVER . 'index.php?route=common/home';


        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/wish_list.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/product/wish_list.tpl';
        } else {
            $this->template = 'default/template/product/wish_list.tpl';
        }

        $this->children = array(
            'common/column_right',
            'common/column_left',
            'common/footer',
            'common/header'
        );

        $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
    }

    public function update() {
        $this->language->load('product/wish_list');
        $this->load->model('tool/image');
        $this->load->model('tool/seo_url');
        $json = array();

        if (!isset($this->session->data['wish_list'])) {
            $this->session->data['wish_list'] = array();
        }

        if (isset($this->request->post['product_id'])) {

            $this->load->model('catalog/product');

            $product_info = $this->model_catalog_product->getProduct($this->request->post['product_id']);

            if ($product_info) {
                if (!in_array($this->request->post['product_id'], $this->session->data['wish_list'])) {
                    $this->session->data['wish_list'][] = $this->request->post['product_id'];
                }
                $this->load->helper('russian');
                $json['total'] = (isset($this->session->data['wish_list']) ? count($this->session->data['wish_list']) : 0) . ' ' . Russian::howMany(isset($this->session->data['wish_list']) ? count($this->session->data['wish_list']) : 0, 'товар', 'товара', 'товаров');
            }
        }
        if (isset($this->request->post['remove'])) {
            $key = array_search($this->request->post['remove'], $this->session->data['wish_list']);

            if ($key !== false) {
                unset($this->session->data['wish_list'][$key]);
            }
            $this->load->helper('russian');
            $json['total'] = (isset($this->session->data['wish_list']) ? count($this->session->data['wish_list']) : 0) . ' ' . Russian::howMany(isset($this->session->data['wish_list']) ? count($this->session->data['wish_list']) : 0, 'товар', 'товара', 'товаров');
        
            
        }

        $this->response->setOutput(json_encode($json));
    }

}

?>