<?php 
class ControllerInformationInformationCategory extends Controller {
	public function index() {  
    	$this->language->load('information/information_category');
		
		$this->load->model('catalog/information');
        $this->load->model('tool/seo_url');
		
		if (isset($this->request->get['type'])) {
			$information_category_id = $this->request->get['type'];
		} else {
			$information_category_id = 0;
		}

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$this->document->breadcrumbs = array();

      	$this->document->breadcrumbs[] = array(
        	'href'      => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=common/home'),
        	'text'      => $this->language->get('text_home'),
        	'separator' => FALSE
      	);

		$information_category_info = $this->model_catalog_information->getInformationCategory($information_category_id); 

		if ($information_category_info) {

	  		$this->document->title = $information_category_info['name'] . ' | '. $this->language->get('title');

            $this->data['heading_title'] = $information_category_info['name'];

      		$this->document->breadcrumbs[] = array(
        		'href'      => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=information/information_category&type=' . $this->request->get['type'] . $url),
        		'text'      => $information_category_info['name'],
        		'separator' => $this->language->get('text_separator')
      		);		

        $this->data['category_informations'] = array();

		$data = array(
			'information_category_id'  => $information_category_id,
			'start' => ($page - 1) * $this->config->get('config_catalog_limit'),
			'limit' => $this->config->get('config_catalog_limit')
		);

        $information_total = $this->model_catalog_information->getTotalInformationsByCategoryId($information_category_id);

        $results = $this->model_catalog_information->getCategoryInformations($data);
      
		foreach ($results as $result) {
      		$this->data['category_informations'][] = array(
                'date_added' => ($this->config->get('news_information_category') == $result['information_category_id'] ) ? $result['date_added'] : '',
        		'title' => $result['title'],
                'short_description' => $result['short_description'],
	    		'href'  => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=information/information&information_id=' . $result['information_id'] . '&type=' . $result['information_category_id'])
      		);
    	}    

      		$this->data['button_continue'] = $this->language->get('button_continue');

            $pagination = new Pagination();
            $pagination->total = $information_total;
            $pagination->page = $page;
            $pagination->limit = $this->config->get('config_catalog_limit');
            $pagination->text = $this->language->get('text_pagination');
            $pagination->url = $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=information/information_category&type=' . $this->request->get['type'] . '&page={page}');
            
            $this->data['pagination'] = $pagination->render();  

			$this->data['continue'] = $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=common/home');

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/information/information_category.tpl')) {
				$this->template = $this->config->get('config_template') . '/template/information/information_category.tpl';
			} else {
				$this->template = 'default/template/information/information_category.tpl';
			}
			
			$this->children = array(
				'common/column_right',
				'common/footer',
				'common/column_left',
				'common/header'
			);		
			
	  		$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
    	} else {

//            $url = '';
//
//			if (isset($this->request->get['page'])) {
//				$url .= '&page=' . $this->request->get['page'];
//			}


      		$this->document->breadcrumbs[] = array(
        		'href'      => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=information/information_category&type=' . $this->request->get['type'] . $url),
        		'text'      => $this->language->get('text_error'),
        		'separator' => $this->language->get('text_separator')
      		);
				
	  		$this->document->title = $this->language->get('text_error');
			
      		$this->data['heading_title'] = $this->language->get('text_error');

      		$this->data['text_error'] = $this->language->get('text_error');

      		$this->data['button_continue'] = $this->language->get('button_continue');

      		$this->data['continue'] = $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=common/home');

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/error/not_found.tpl')) {
				$this->template = $this->config->get('config_template') . '/template/error/not_found.tpl';
			} else {
				$this->template = 'default/template/error/not_found.tpl';
			}
			
			$this->children = array(
				'common/column_right',
				'common/footer',
				'common/column_left',
				'common/header'
			);
		
	  		$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
    	}
  	}
}
?>