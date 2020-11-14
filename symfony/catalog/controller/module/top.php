<?php
class ControllerModuleTop extends Controller {
	protected function index() {
		$this->language->load('module/top');

      	$this->data['heading_title'] = $this->language->get('heading_title');

		$this->load->model('catalog/product');
                 $this->data['button_add_to_cart'] = $this->language->get('button_add_to_cart');
		$results = $this->model_catalog_product->getTopProducts();
                $this->data['products'] = $this->model_catalog_product->formatProductModuleList($results);

		$this->id = 'top';

		
                if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/top.tpl')) {
                        $this->template = $this->config->get('config_template') . '/template/module/top.tpl';
                } else {
                        $this->template = 'default/template/module/top.tpl';
                }
		

		$this->render();
	}
}
?>