<?php  
class ControllerCommonColumnLeft extends Controller {
	protected function index() {
		$module_data = array();
		
		$this->load->model('checkout/extension');
                $this->load->model('catalog/category');
		
                if (!$this->config->get('config_store_id')) {
                    $footer_block_left = html_entity_decode($this->config->get('config_footer_block_left_' . $this->config->get('config_language_id')), ENT_QUOTES, 'UTF-8');
                } else {
                    $store_info = $this->model_setting_store->getStore($this->config->get('config_store_id'));

                    if ($store_info) {
                        $footer_block_left = html_entity_decode($store_info['description'], ENT_QUOTES, 'UTF-8');
                    } else {
                         $this->data['footer_block_left'] = '';
                    }
		}
                
                $this->load->model('catalog/information');
		$this->load->model('tool/seo_url');
		
		$this->data['informations'] = array();

		foreach ($this->model_catalog_information->getInformations($footer_block_left) as $result) {
      		$this->data['informations'][] = array(
        		'title' => $result['title'],
	    		'href'  => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=information/information&information_id=' . $result['information_id'])
      		);}
                
                
		$results = $this->model_checkout_extension->getExtensions('module');
                $this->children[] = 'module/search' ;
                                          
		foreach ($results as $result) {
			if ($this->config->get($result['key'] . '_status') && ($this->config->get($result['key'] . '_position') == 'left')) {
				$module_data[] = array(
					'code'       => $result['key'],
					'sort_order' => $this->config->get($result['key'] . '_sort_order')
				);
				
				$this->children[] = 'module/' . $result['key'];		
			}
		}

		$sort_order = array(); 
	  
		foreach ($module_data as $key => $value) {
      		$sort_order[$key] = $value['sort_order'];
    	}

    	array_multisort($sort_order, SORT_ASC, $module_data);			
		
		$this->data['modules'] = $module_data;
		
                $this->data['parametr_categories'] = $this->model_catalog_category->getParametrCategories();
                
                
		$this->id = 'column_left';

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/column_left.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/common/column_left.tpl';
		} else {
			$this->template = 'default/template/common/column_left.tpl';
		}
		
		$this->render();
	}
}
?>