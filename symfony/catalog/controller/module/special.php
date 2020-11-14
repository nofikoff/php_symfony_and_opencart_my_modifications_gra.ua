<?php
class ControllerModuleSpecial extends Controller {
	protected function index() {
		$this->language->load('module/special');
                $this->load->model('catalog/product');
		$this->load->model('tool/seo_url');

                $this->data['heading_title'] = $this->language->get('heading_title');
                $this->data['text_all_promotional_products'] = $this->language->get('text_all_promotional_products');
                $this->data['button_add_to_cart'] = $this->language->get('button_add_to_cart');
                $this->data['special_href'] = $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/special');
        
                if (isset($this->request->get['path'])) {
                    $parts = explode('_', $this->request->get['path']);
                    $category_id = array_pop($parts);
                } else {
                    $category_id = 0;

                }
                
		$results = $this->model_catalog_product->getSpecialModule($this->config->get('special_limit'), $category_id);
        $this->data['products'] =$this->model_catalog_product->formatProductModuleList($results);

		$this->id = 'special';
		
                if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/special.tpl')) {
                        $this->template = $this->config->get('config_template') . '/template/module/special.tpl';
                } else {
                        $this->template = 'default/template/module/special.tpl';
                }
		
		$this->render();
	}
}
?>