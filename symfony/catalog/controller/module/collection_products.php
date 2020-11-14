<?php

class ControllerModuleCollectionProducts extends Controller
{

    protected function index()
    {

        $this->load->model('catalog/product');
        $this->load->model('tool/seo_url');
        $this->language->load('product/product');

        $this->data['button_add_to_cart'] = $this->language->get('button_add_to_cart');

        $product_id = isset($this->request->get['product_id']) ? $this->request->get['product_id'] : 0;

        $collection_info = $this->model_catalog_product->getCollectionIdName($product_id);
        if ($collection_info) {
            $collection_id = $collection_info['collection_id'];
            $this->data['name'] = $collection_info['name'];
            $this->data['href'] = $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/brand&brand=' . $collection_info['param_value_id'] . '&collection_id=' . $collection_id);
            $results = $this->model_catalog_product->getCollectionProducts($collection_id, $product_id, 6);
            $this->data['products'] = $this->model_catalog_product->formatProductModuleList($results);

        } else {
            $this->data['products'] = array();
            $this->data['href'] = '';
            $this->data['name'] = '';
        }
        $this->id = 'collection_products';
        $this->template = $this->config->get('config_template') . '/template/module/collection_products.tpl';
        $this->render();
    }

}

?>