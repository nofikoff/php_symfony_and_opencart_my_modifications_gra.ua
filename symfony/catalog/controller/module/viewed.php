<?php

class ControllerModuleViewed extends Controller
{
    protected function index()
    {
        $this->language->load('module/special');
        $this->load->model('catalog/product');
        $this->load->model('tool/seo_url');

        $this->data['text_viewed'] = $this->language->get('text_viewed');
        $this->data['text_no_viewed'] = $this->language->get('text_no_viewed');
        $this->data['button_add_to_cart'] = $this->language->get('button_add_to_cart');
        $this->data['heading_title'] = $this->language->get('heading_title');

//        //by Novikov
//        //$this->data['stock_status'] = html_entity_decode($this->model_catalog_product['stock_status']);
//
//        if ($_SERVER["HTTP_CF_CONNECTING_IP"] == '195.69.221.201') {
//            $product_info = $this->model_catalog_product->getProduct($product_id);
//            print_r($this->model_catalog_product);
//            exit;
//        }

        $url = isset($this->request->get['path']) ? '&path=' . $this->request->get['path'] : '';
        $this->data['viewed_products'] = array();


        $results = $this->model_catalog_product->getViewedProducts($this->request->get['product_id']);
        $this->data['viewed_products'] = $this->model_catalog_product->formatProductModuleList($results, $url);

        $this->id = 'viewed';
        $this->template = $this->config->get('config_template') . '/template/module/viewed.tpl';
        $this->render();
    }

}

?>