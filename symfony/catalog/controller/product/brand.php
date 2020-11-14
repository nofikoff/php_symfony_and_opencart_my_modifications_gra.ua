<?php 
class ControllerProductBrand extends Controller {
	public function index() { 
		$this->language->load('product/brand');
        $this->load->model('catalog/brand');
        $this->load->model('catalog/product'); 
		$this->load->model('tool/seo_url');
        $this->load->model('tool/image');

		$this->document->breadcrumbs = array();

        $this->document->breadcrumbs[] = array(
            'href'      => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=common/home'),
            'text'      => $this->language->get('text_home'),
            'separator' => FALSE
        );  

		if (isset($this->request->get['brand'])) {
            $brand_id = $this->db->escape($this->request->get['brand']);
		} else {
            $brand_id = 0;
		}
        
		$brand_info = $this->model_catalog_brand->getBrand($brand_id);
		if ($brand_info) {
            $this->document->breadcrumbs[] = array(
                'href'      => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/brand&brand=' . $brand_id),
                'text'      => $brand_info['value'],
                'separator' => $this->language->get('text_separator')
            );

            $this->document->title = $brand_info['value'] . ' | '. $this->language->get('title');
			$this->data['heading_title'] = $brand_info['value'];
			$this->data['description'] = html_entity_decode($brand_info['value'], ENT_QUOTES, 'UTF-8');

            $this->data['text_sort'] = $this->language->get('text_sort');
            $this->data['text_brand'] = $this->language->get('text_brand');
            $this->data['text_error'] = $this->language->get('text_empty');
            $this->data['button_add_to_cart'] = $this->language->get('button_add_to_cart');
            $this->data['button_filter'] = $this->language->get('button_filter');
            $this->data['text_price'] = $this->language->get('text_price');
            $this->data['text_in_stock'] = $this->language->get('text_in_stock');
            $this->data['text_buy'] = $this->language->get('text_buy');
            $this->data['text_read_more'] = $this->language->get('text_read_more');

			$product_total = $this->model_catalog_product->getBrandProductsTotal($brand_id);
			
			if ($product_total) {
                
                // Products
				$results = $this->model_catalog_product->getBrandProducts($brand_id);
                $url = '';
                if (isset($this->request->get['brand']))
                    $url .= '&brand=' . $this->request->get['brand'];
                $this->data['products'] = $this->model_catalog_product->formatProductList($results, $url);
                $url = HTTP_SERVER . 'index.php?route=product/brand' . $url;
                $this->data['sorts'] = $this->model_catalog_product->formatSorts($url);
                $this->data['pagination'] = $this->model_catalog_product->formatPagination($product_total, $url);

                // Filter by price
                if (isset($this->request->get['sort']))
                    $url .= '&sort=' . $this->request->get['sort'];
                if (isset($this->request->get['order']))
                    $url .= '&order=' . $this->request->get['order'];              

                // Template
				$this->template = $this->config->get('config_template') . '/template/product/brand.tpl';
				$this->children = array(
					'common/column_right',
					'common/column_left',
					'common/footer',
					'common/header'
				);
				$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));										
      		}
            else {
        		$this->document->title = $brand_info['value'] . ' | '. $this->language->get('title');

        		$this->data['heading_title'] = $brand_info['value'];
        		$this->data['text_error'] = $this->language->get('text_empty');
        		$this->data['button_continue'] = $this->language->get('button_continue');
        		$this->data['continue'] = $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=common/home');
				$this->data['categories'] = array();
				$this->data['products'] = array();
						
				if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/brand.tpl')) {
					$this->template = $this->config->get('config_template') . '/template/product/brand.tpl';
				} else {
					$this->template = 'default/template/product/brand.tpl';
				}	

				$this->children = array(
					'common/column_right',
					'common/column_left',
					'common/footer',
					'common/header'
				);
                
				$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
      		}
    	} else {
			$url = '';
			if (isset($this->request->get['sort'])) 
				$url .= '&sort=' . $this->request->get['sort'];
			if (isset($this->request->get['order'])) 
				$url .= '&order=' . $this->request->get['order'];
			if (isset($this->request->get['page'])) 
				$url .= '&page=' . $this->request->get['page'];
            if (isset($this->request->get['brand']))
				$url .= '&brand=' . $this->request->get['brand'];
			if (isset($this->request->get['brand'])) {
	       		$this->document->breadcrumbs[] = array(
   	    			'href'      => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/brand' . $url),
    	   			'text'      => $this->language->get('text_error'),
        			'separator' => $this->language->get('text_separator')
        		);
			}
				
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
				'common/column_left',
				'common/footer',
				'common/header'
			);
			$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
		}
  	}
}
?>