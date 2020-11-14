<?php  
class ControllerModuleCategory extends Controller {
	protected $category_id = 0;
	protected $path = array();
	
	protected function index() {
		$this->language->load('module/category');
		
    	$this->data['heading_title'] = $this->language->get('heading_title');
         $this->data['text_price'] = $this->language->get('text_price');
        $this->data['text_in_stock'] = $this->language->get('text_in_stock');
        $this->data['text_buy'] = $this->language->get('text_buy');
        $this->data['text_read_more'] = $this->language->get('text_read_more');
        $this->data['text_all_categories'] = $this->language->get('text_all_categories');

		$this->load->model('catalog/category');
		$this->load->model('tool/seo_url');

		if (isset($this->request->get['path'])) {
			$this->path = explode('_', $this->request->get['path']);

			$this->category_id = end($this->path);
		}

        $this->id = 'category';

		if ($this->config->get('category_position') == 'home') {

		$this->data['category'] = $this->getHomeCategories(0);

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/category_home.tpl')) {
				$this->template = $this->config->get('config_template') . '/template/module/category_home.tpl';
			} else {
				$this->template = 'default/template/module/category_home.tpl';
			}
		} else {

            $this->data['category'] = $this->getCategories(0);

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/category.tpl')) {
				$this->template = $this->config->get('config_template') . '/template/module/category.tpl';
			} else {
				$this->template = 'default/template/module/category.tpl';
			}
		}
		
		$this->render();
  	}
	
	protected function getCategories($parent_id, $current_path = '') {
		$category_id = array_shift($this->path);
		
		$output = '';
		
		$results = $this->model_catalog_category->getCategories($parent_id);
		
		if ($results) { 
			$output .= '<ul>';
    	}
		
		foreach ($results as $result) {	
			if (!$current_path) {
				$new_path = $result['category_id'];
			} else {
				$new_path = $current_path . '_' . $result['category_id'];
			}
			
			$output .= '<li>';
			
			$children = '';
			
			if ($category_id == $result['category_id']) {
				$children = $this->getCategories($result['category_id'], $new_path);
			}
			
			if ($this->category_id == $result['category_id']) {
				$output .= '<a href="' . $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/category&amp;path=' . $new_path)  . '"><b>' . $result['name'] . '</b></a>';
			} else {
				$output .= '<a href="' . $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/category&amp;path=' . $new_path)  . '">' . $result['name'] . '</a>';
			}
			
        	$output .= $children;
        
        	$output .= '</li>'; 
		}
 
		if ($results) {
			$output .= '</ul>';
		}
		
		return $output;
	}

	protected function getHomeCategories($parent_id) {

		$output = '<div class="category_home_box">';

		$results = $this->model_catalog_category->getCategories($parent_id);
            $text_all_categories = $this->language->get('text_all_categories');

        if ($results) {
            foreach ($results as $result) {
                $output .= '<div><h3>' .  $result['name'] . '</h3>';
                $result_categories = $this->model_catalog_category->getCategories($result['category_id'], $this->config->get('category_limit'));

                if ($result_categories) {
                    $output .= '<ul>';
                }
                foreach ($result_categories as $result_category){

                    $path = $result['category_id'] . '_' . $result_category['category_id'];
                    $output .= '<li>';
                    $output .= '<a href="' . $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/category&amp;path=' . $path)  . '">' . $result_category['name'] . '</a>';
                    $output .= '</li>';    
                }
                if ($result_categories) {
                    $output .= '</ul>';
                    $output .= '<br /><a class="read_more_red" href="' . $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/category&amp;path=' .$result['category_id'])  . '">' . $text_all_categories . '</a>';
                    $output .= '</div>';
                }
            }
            $output .= '</div>';
        }

		return $output;
	}

}
?>