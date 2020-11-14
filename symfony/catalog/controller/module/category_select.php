<?php  
class ControllerModuleCategorySelect extends Controller {
	protected function index() {
		$this->language->load('module/category_select');

      	$this->data['heading_title'] = $this->language->get('heading_title');
		
		$this->data['description'] = html_entity_decode($this->config->get('category_select_description'));

     	$this->id = 'category_select';

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/category_select.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/module/category_select.tpl';
		} else {
			$this->template = 'default/template/module/category_select.tpl';
		}
		
		$this->render();
	}
}
?>