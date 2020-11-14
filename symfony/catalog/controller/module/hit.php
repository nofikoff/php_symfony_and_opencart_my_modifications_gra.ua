<?php
class ControllerModuleHit extends Controller {
	protected function index() {
		$this->language->load('module/hit');

      	$this->data['heading_title'] = $this->language->get('heading_title');
        if (isset($this->request->get['path'])) {
            $parts = explode('_', $this->request->get['path']);
            $category_id = array_pop($parts);
        } else {
            $category_id = 0;
            
        }
              
		$this->load->model('catalog/product');
                $this->data['button_add_to_cart'] = $this->language->get('button_add_to_cart');
		$results = $this->model_catalog_product->getHitProducts($this->config->get('hit_limit'), $category_id);
                $this->data['products'] = $this->model_catalog_product->formatProductModuleList($results);

		$this->id = 'hit';

                if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/hit.tpl')) {
                        $this->template = $this->config->get('config_template') . '/template/module/hit.tpl';
                } else {
                        $this->template = 'default/template/module/hit.tpl';
                }
		

		$this->render();
	}
}
?>