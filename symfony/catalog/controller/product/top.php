<?php 
class ControllerProductTop extends Controller { 	
	public function index() { 
    	$this->language->load('product/top');
        $this->load->model('catalog/product');
        $this->load->model('tool/seo_url');
	  	  
        if (isset($this->request->get['limit'])) {
            $limit = $this->request->get['limit'];
        } else {
            $limit = $this->config->get('config_catalog_limit');
        }
        $this->data['limit'] = $limit;
        
    	$this->document->title = $this->language->get('heading_title') . ' | '. $this->language->get('title');;

        $this->document->breadcrumbs = array();

      	$this->document->breadcrumbs[] = array(
        	'href'      => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=common/home'),
        	'text'      => $this->language->get('text_home'),
        	'separator' => FALSE
      	);
        
        $this->document->breadcrumbs[] = array(
   	    				'href'      => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/special'),
    	   				'text'      => 'товары Топ15',
        				'separator' => $this->language->get('text_separator')
        			);
        
    	$this->data['heading_title'] = $this->language->get('heading_title');
   		$this->data['text_sort'] = $this->language->get('text_sort');
        $this->data['text_brand'] = $this->language->get('text_brand');
        $this->data['button_add_to_cart'] = $this->language->get('button_add_to_cart');
        $this->data['text_price'] = $this->language->get('text_price');
        $this->data['text_in_stock'] = $this->language->get('text_in_stock');
        $this->data['text_buy'] = $this->language->get('text_buy');
        $this->data['text_read_more'] = $this->language->get('text_read_more');

        $product_total = $this->model_catalog_product->getTopProductsTotal();
     
		if ($product_total) {
            $results = $this->model_catalog_product->getTopProducts();
            $this->data['products'] = $this->model_catalog_product->formatProductList($results);

            $url = HTTP_SERVER . 'index.php?route=product/top';
             
            $this->data['sorts'] = $this->model_catalog_product->formatSorts($url);
            
            if (isset($this->request->get['sort']))
                $url .= '&sort=' . $this->request->get['sort'];
            if (isset($this->request->get['order']))
                $url .= '&order=' . $this->request->get['order'];
            
            $this->data['limits'] = array();
            
                $this->data['limits'][] = array(
                    'text' => $this->config->get('config_catalog_limit'),
                    'value' => $this->config->get('config_catalog_limit'),
                    'href' => $this->model_tool_seo_url->rewrite($url . '&limit=' . $this->config->get('config_catalog_limit'))
                );

                $this->data['limits'][] = array(
                    'text' => $this->config->get('config_catalog_limit') * 2,
                    'value' => $this->config->get('config_catalog_limit') * 2,
                    'href' => $this->model_tool_seo_url->rewrite($url. '&limit=' . $this->config->get('config_catalog_limit') * 2)
                );

                $this->data['limits'][] = array(
                    'text' => $this->config->get('config_catalog_limit') * 3,
                    'value' => $this->config->get('config_catalog_limit') * 3,
                    'href' => $this->model_tool_seo_url->rewrite($url . '&limit=' . $this->config->get('config_catalog_limit') * 3)
                ); 
            
            $this->data['pagination'] = $this->model_catalog_product->formatPagination($product_total, $url);
            
            
            
            $this->template = $this->config->get('config_template') . '/template/product/top.tpl';
			$this->children = array(
				'common/column_right',
				'common/column_left',
				'common/footer',
				'common/header'
			);
			$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));			
		}
        else {
      		$this->data['text_error'] = $this->language->get('text_empty');
      		$this->data['button_continue'] = $this->language->get('button_continue');
      		$this->data['continue'] = $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=common/home');
	  				
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/error/not_found.tpl')) {
				$this->template = $this->config->get('config_template') . '/template/error/not_found.tpl';
			} else {
				$this->template = 'default/template/error/not_found.tpl';
			}
			$this->children = array(
				'common/column_right',
				'common/column_left',
				'common/footer',
				'common/header'
			);	
			
			$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
		}
  	}
}
?>