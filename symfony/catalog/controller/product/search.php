<?php 
class ControllerProductSearch extends Controller {
	public function index() { 
    	$this->language->load('product/search');
        $this->load->model('catalog/product');
        $this->load->model('tool/seo_url');
	  	  
        if(isset($this->request->get['keyword'])) {
            $keyword = $this->request->get['keyword'];
            $title = $this->language->get('heading_title') . ' "'.$keyword.'"';
        } else {
            $keyword = '';
            $title = $this->language->get('heading_title');
        }
        if(isset($this->session->data['price_from'])) {
            $price_from = $this->session->data['price_from'];
        } else {
            $price_from = 0;
        }
        if(isset($this->session->data['price_to'])) {
            $price_to = $this->session->data['price_to'];
        } else {
            $price_to = 0;
        }
        
        if (isset($this->request->get['limit'])) {
            $limit = $this->request->get['limit'];
        } else {
            $limit = $this->config->get('config_catalog_limit');
        }
        $this->data['limit'] = $limit;
        
        $this->document->title = $title . ' | '. $this->language->get('title');
    	$this->data['heading_title'] = $title;
		$this->data['text_sort'] = $this->language->get('text_sort');
        $this->data['text_brand'] = $this->language->get('text_brand');
        $this->data['text_empty'] = $this->language->get('text_empty');
        $this->data['text_read_more'] = $this->language->get('text_read_more');
        $this->data['text_price'] = $this->language->get('text_price');
        $this->data['text_in_stock'] = $this->language->get('text_in_stock');
    	$this->data['button_search'] = $this->language->get('button_search');
        $this->data['button_filter'] = $this->language->get('button_filter');
        $this->data['button_add_to_cart'] = $this->language->get('button_add_to_cart');
		
		$this->load->model('catalog/sphinxpro');
			
			if ($this->model_catalog_sphinxpro->checkSphinx()->connection) {
				
				       if (isset($this->request->get['page'])) {
							$page = (int)$this->request->get['page'];
						} else {
							$page = 1;
						}				      

						if (isset($this->request->get['sort'])) {
							$sort = (int)$this->request->get['sort'];
						} else {
							$sort = 'name';
						}						
						
						if (isset($this->request->get['order'])) {
							$order = (int)$this->request->get['order'];
						} else {
							$order = 'asc';
						}
				
					$filter_data = array(
						'filter_name'         => $keyword, 
						'filter_tag'          => $keyword, 
						'filter_description'  => false,
						'filter_category_id'  => false, 
						'filter_sub_category' => false, 
						'sort'                => $sort,
						'order'               => $order,
						'start'               => ($page - 1) * $limit,
						'limit'               => $limit
					);
			
				$products = $this->model_catalog_sphinxpro->getProducts($filter_data);
			
				$product_total = $products['product_total'];

				$results = $products['results'];
				
				
			} else {

				$product_total = $this->model_catalog_product->getSearchProductsTotal($keyword, $price_from , $price_to);
				$results = $this->model_catalog_product->getSearchProducts($keyword, $price_from , $price_to);
		
			}
             
        if ($product_total) {
            
            $this->data['products'] = $this->model_catalog_product->formatProductList($results);

            $url = $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/search');
            if (isset($this->request->get['keyword'])) 
                $url .= '&keyword=' . $this->request->get['keyword'];
            
            // Filter by price
			/*
            if (isset($this->request->get['sort']))
                $url .= '&sort=' . $this->request->get['sort'];
            if (isset($this->request->get['order']))
                $url .= '&order=' . $this->request->get['order'];
            */

            $this->data['limits'] = array();

                $this->data['limits'][] = array(
                    'text' => $this->config->get('config_catalog_limit'),
                    'value' => $this->config->get('config_catalog_limit'),
                    'href' => $this->model_tool_seo_url->rewrite($url . '&limit=' . $this->config->get('config_catalog_limit'))
                );

                $this->data['limits'][] = array(
                    'text' => $this->config->get('config_catalog_limit') * 2,
                    'value' => $this->config->get('config_catalog_limit') * 2,
                    'href' => $this->model_tool_seo_url->rewrite($url . '&limit=' . $this->config->get('config_catalog_limit') * 2)
                );

                $this->data['limits'][] = array(
                    'text' => $this->config->get('config_catalog_limit') * 3,
                    'value' => $this->config->get('config_catalog_limit') * 3,
                    'href' => $this->model_tool_seo_url->rewrite($url . '&limit=' . $this->config->get('config_catalog_limit') * 3)
                ); 
            
            $this->data['sorts'] = $this->model_catalog_product->formatSorts($url);
            $this->data['pagination'] = $this->model_catalog_product->formatPagination($product_total, $url);
            
		}
  
		$this->template = $this->config->get('config_template') . '/template/product/search.tpl';
		$this->children = array(
			'common/column_right',
			'common/column_left',
			'common/footer',
			'common/header'
		);
		$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
  	}


    public function suggest() {
        $output = '';

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->post['keyword'])) {

            $this->language->load('product/search');
            $this->load->model('catalog/product');
            $this->load->model('tool/seo_url');
            $this->load->model('tool/image');

            $keywords = $this->request->post['keyword'];
            $results = $this->model_catalog_product->getSearchSuggest($keywords, 0, 0 , 6);

            if($results) {

                $preg_search = array();
                $preg_replace = array();

                foreach(explode(' ',$keywords) as $keyword)
                    if($keyword) {
                        $preg_search[] = '#'.$keyword.'#ui';
                        $preg_replace[]= '<b>'.$keyword.'</b>';
                    }

                $output .= '<div class="suggest">';

                foreach($results as $key => $result){
                    $href = $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/product&product_id=' . $result['product_id']);
                    $name = ' <span class="name">'.$result['name'].'</span>';
                    
                    
                    $price = $result['price'];
                    $special = $this->model_catalog_product->getProductSpecial($result['product_id']);
                    if ($special['price']) {
                        if ($special['old_price'] > 0 && $special['price'] >= $price) {
                            $price = $special['old_price'];
                        } else if($special['price'] >= $price) {
                            $price = $special['price']*1.05;
                        }
                        $special = $this->currency->format($this->tax->calculate($special['price'], $result['tax_class_id'], $this->config->get('config_tax')));
                    } 
                    $price = $this->currency->format($this->tax->calculate($price, $result['tax_class_id'], $this->config->get('config_tax')));
                    
                    $price = ($price != 0  ? $price : '');
                    
                    $price_str = ($special ? '<span class="old_price">' . $price . '</span> ' . $special : $price);

                    $output .= '<div class="search_row">'
                                . '<a href="'. $href .'" class="'. (!$key ? 'first' : '') .'">'
                                . preg_replace($preg_search, $preg_replace, $name)    
                                . '<span class="image"><img src="'. $this->model_tool_image->resize(($result['image'] ? $result['image'] : 'no_image.jpg'), 80, 50).'" alt=""/></span>'    
                                . '<span class="desc">' .' <span class="search_category">('.$result['category'].')</span>'
                                . '<span class="price">' . $price_str . '</span></span>'
                                . '</a>'
                            . '</div>';
                }

                //if(count($results) == 6)
                    $output .= '<a href="'.$this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/search&keyword=' . $keywords).'"><span class="read_more">'.$this->language->get('text_all_results').'</span></a>';
                
                $output .= '</div>';
            }
        }
        
        $this->response->setOutput($output, $this->config->get('config_compression'));
    }
}
?>