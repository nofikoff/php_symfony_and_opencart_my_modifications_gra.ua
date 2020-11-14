<?php
class ControllerModuleRelated extends Controller {
	protected function index() {
        
		$this->language->load('module/related');
        $this->load->model('catalog/product');
		$this->load->model('tool/seo_url');

        $this->data['text_related'] = $this->language->get('text_related');
        $this->data['text_no_related'] = $this->language->get('text_no_related');

        $url = isset($this->request->get['path']) ? '&path=' . $this->request->get['path'] : '';

        $this->data['related_products'] = array();

        $results = $this->model_catalog_product->getProductRelated($this->request->get['product_id']);

        $this->data['related_products'] = $this->model_catalog_product->formatProductModuleList($results, $url);

      	$this->data['heading_title'] = $this->language->get('heading_title');

		$this->id = 'related';
		$this->template = $this->config->get('config_template') . '/template/module/related.tpl';
		$this->render();
	}
    
}
?>