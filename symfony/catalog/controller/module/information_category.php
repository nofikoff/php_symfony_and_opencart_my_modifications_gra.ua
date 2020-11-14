<?php  
class ControllerModuleInformationCategory extends Controller {
	protected function index() {
		$this->language->load('module/information_category');
		
    	$this->data['heading_title'] = $this->language->get('heading_title');
    	
		$this->data['text_contact'] = $this->language->get('text_contact');
    	$this->data['text_sitemap'] = $this->language->get('text_sitemap');
        $this->data['text_teh'] = $this->language->get('text_teh');
		
		$this->load->model('catalog/information');
		$this->load->model('tool/seo_url');
		
		$this->data['information_categories'] = array();

		foreach ($this->model_catalog_information->getInformationCategories() as $result) {
      		$this->data['information_categories'][] = array(
        		'name' => $result['name'],
	    		'href'  => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=information/information_category&type=' . $result['information_category_id'])
      		);
    	}
		
		$this->id = 'information_category';

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/information_category.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/module/information_category.tpl';
		} else {
			$this->template = 'default/template/module/information_category.tpl';
		}
		
		$this->render();
	}
}
?>