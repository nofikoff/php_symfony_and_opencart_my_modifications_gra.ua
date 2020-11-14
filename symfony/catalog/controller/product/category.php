<?php

class ControllerProductCategory extends Controller {

    public function index() {

        $this->language->load('product/category');
        $this->load->model('catalog/category');
        $this->load->model('catalog/product');
        $this->load->model('tool/seo_url');
        $this->load->model('tool/image');

        if (isset($this->request->get['limit'])) {
            $limit = $this->request->get['limit'];
        } else {
            $limit = $this->config->get('config_catalog_limit');
        }
        $this->data['limit'] = $limit;
    
        $this->document->breadcrumbs = array();
        $this->document->breadcrumbs[] = array(
            'href' => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => FALSE
        );

        $path_title = array();
        if (isset($this->request->get['path'])) {
            $path = '';
            $parts = explode('_', $this->request->get['path']);

            $breadcrumbs = [];

            foreach ($parts as $path_id) {
                $category_info = $this->model_catalog_category->getCategory($path_id);

                if ($category_info) {
                    if (!$path)
                        $path = $path_id;
                    else
                        $path .= '_' . $path_id;

                    /*$this->document->breadcrumbs[]*/ $breadcrumbs[] = array(
                        'href' => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/category&path=' . $path),
                        'text' => $category_info['name'],
                        'separator' => $this->language->get('text_separator')
                    );
                    $path_title[] = $category_info['name'];
                }

                $parent_id = $category_info['parent_id'];
                while ($parent_id > 0) {
                    $category_info = $this->model_catalog_category->getCategory($parent_id);
                    /*$this->document->breadcrumbs[]*/$breadcrumbs[] = array(
                        'href' => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/category&path=' . $path),
                        'text' => $category_info['name'],
                        'separator' => $this->language->get('text_separator')
                    );
                    $parent_id = $category_info['parent_id'];
                }
            }

            $breadcrumbs = array_reverse($breadcrumbs);
            foreach ($breadcrumbs as $crumb) {
                $this->document->breadcrumbs[] = $crumb;
            }

            if(isset($_GET['ee'])) {

                die;
            }
            $category_id = array_pop($parts);
        } else {
            $category_id = 0;
            $path = '';
        }
        
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

        $this->data['informations'] = array();

        foreach ($this->model_catalog_information->getInformations($footer_block_left) as $result) {
        $this->data['informations'][] = array(
                'title' => $result['title'],
                'href'  => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=information/information&information_id=' . $result['information_id'])
        );}
        
        $this->data['parametr_categories'] = $this->model_catalog_category->getParametrCategories();
             
        
        $this->data['category_id']=$category_id;
        
        $category_info = $this->model_catalog_category->getCategory($category_id);

        $this->data['breadcrumbs'] = $this->document->breadcrumbs;

        if ($category_info) {

            $this->document->title =  $category_info['meta_title'];
            $this->document->keywords = $category_info['meta_keywords'];
            $this->document->description = $category_info['meta_description'];

            $category_products = $this->model_catalog_category->getTotalCategoryProducts($category_id);
            $this->data['category_products'] = $category_products;

            $this->data['heading_title'] = $category_info['name'];
            $this->data['description'] = html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8');

            $this->data['text_sort'] = $this->language->get('text_sort');
            $this->data['text_brand'] = $this->language->get('text_brand');
            $this->data['text_clear_filters'] = $this->language->get('text_clear_filters');
            $this->data['text_error'] = $this->language->get('text_empty');
            $this->data['button_add_to_cart'] = $this->language->get('button_add_to_cart');
            $this->data['button_add_to_wish_list'] = $this->language->get('button_add_to_wish_list');
            $this->data['button_wish_list'] = $this->language->get('button_wish_list');
            $this->data['button_filter'] = $this->language->get('button_filter');
            $this->data['text_price'] = $this->language->get('text_price');
            $this->data['text_in_stock'] = $this->language->get('text_in_stock');
            $this->data['text_buy'] = $this->language->get('text_buy');
            $this->data['text_read_more'] = $this->language->get('text_read_more');
            $this->data['clear_filters_href'] = $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/category&path=' . $this->request->get['path']);

            $sort = isset($this->request->get['sort']) ? $this->request->get['sort'] : 'p.price';
            $order = isset($this->request->get['order']) ? $this->request->get['order'] : 'ASC';

            $url_part = '';
            if (isset($this->request->get['path']))
                $url_part .= '&path=' . $path;
            if (isset($this->request->get['sort']))
                $url_part .= '&sort=' . $sort;
            if (isset($this->request->get['order']))
                $url_part .= '&order=' . $order;

            //***** PARAMS *****//
            $title = '';
            $filter_url = '';
            $url = $url_part;

            if (isset($this->request->get['limit'])) {
                $url .= '&limit=' . $this->request->get['limit'];
            }

            $selected_params = array();
            $selected_params_url = array();
            $selected_params_ids = array();
            $selected_params_ids_array = array();
            $filter_params = array();

            $params = $this->model_catalog_product->getParamsByCategory($category_id);

            foreach ($params as $param_value) {
                if (!isset($filter_params[$param_value['param_alias']])) {
                    if (isset($this->request->get[$param_value['param_alias']])) {
                        $selected_params[$param_value['param_alias']] = array(
                            'array' => explode(',', $this->request->get[$param_value['param_alias']]),
                        );
                        $selected_params_url[$param_value['param_alias']] = '&' . $param_value['param_alias'] . '=' . $this->request->get[$param_value['param_alias']];
                    }
                    $filter_params[$param_value['param_alias']] = array(
                        'name' => $param_value['param_name']
                    );
                }
                $filter_params[$param_value['param_alias']]['values'][$param_value['alias']] = array(
                    'value' => $param_value['value'],
                    'param_value_id' => $param_value['param_value_id']
                );
            }

            foreach ($filter_params as $param_alias => $param) {
                $without_this_param = $selected_params_url;

                if (isset($selected_params[$param_alias])) {
                    unset($without_this_param[$param_alias]);
                    $without_this_param = implode('', array_diff($without_this_param, array($param_alias)));

                    foreach ($param['values'] as $alias => $value) {
                        $without_this_value = implode(',', array_diff($selected_params[$param_alias]['array'], array($alias)));
                        if (in_array($alias, $selected_params[$param_alias]['array'])) {

                            $filter_params[$param_alias]['values'][$alias] = array(
                                'value' => $value['value'],
                                'active' => true,
                                'href' => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/category' . $url . $without_this_param . ($without_this_value ? "&" . $param_alias . "=" . $without_this_value : ''))
                            );
                            $selected_params_ids[] = $value['param_value_id'];
                            $selected_params_ids_array[$param_alias][] = $value['param_value_id'];
                            $title .= ' ' . $value['value'];
                        } else {
                            $filter_params[$param_alias]['values'][$alias] = array(
                                'value' => $value['value'],
                                'active' => false,
                                'href' => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/category' . $url . $without_this_param . "&" . $param_alias . "=" . $alias . ($without_this_value ? ',' . $without_this_value : ''))
                            );
                        }
                    }
                } else {
                    $without_this_param = implode('', $without_this_param);

                    foreach ($param['values'] as $alias => $value) {
                        $filter_params[$param_alias]['values'][$alias] = array(
                            'value' => $value['value'],
                            'active' => false,
                            'href' => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/category' . $url . $without_this_param . "&" . $param_alias . "=" . $alias)
                        );
                    }
                }
            }

            // UNSET params
            if ($selected_params_ids) {
                $active_params = $this->model_catalog_product->getSelectedParamsByCategory($category_id, $selected_params_ids, count($selected_params));

                foreach ($filter_params as $param_alias => $param) {
                    if (isset($selected_params[$param_alias])) {
                        $s_ids = array_diff($selected_params_ids, $selected_params_ids_array[$param_alias]);
                        if ($s_ids) {
                            $s_active_params = $this->model_catalog_product->getSelectedParamsByCategory($category_id, $s_ids, count($selected_params) - 1, $param_alias);
                            foreach ($filter_params[$param_alias]['values'] as $alias => $value)
                                if (!in_array($alias, $s_active_params[$param_alias]))
                                    unset($filter_params[$param_alias]['values'][$alias]);
                        }
                    } else {
                        if (isset($active_params[$param_alias])) {
                            foreach ($filter_params[$param_alias]['values'] as $alias => $value)
                                if (!in_array($alias, $active_params[$param_alias]))
                                    unset($filter_params[$param_alias]['values'][$alias]);
                        } else {
                            unset($filter_params[$param_alias]);
                        }
                    }
                }
            }

            $this->data['filter_params'] = $filter_params;

            $this->data['unselect_all_href'] = $selected_params ? $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/category' . $url_part) : '';

            $category_total = $this->model_catalog_category->getTotalCategoriesByCategoryId($category_id);
            $product_total = $this->model_catalog_product->getCategoryProductsTotal($category_id, $selected_params_ids, count($selected_params));
        
            if ($category_total || $product_total) {

                // Categories
                $results = $this->model_catalog_category->getCategories($category_id);

                $categories = array();

                foreach ($results as $result)
                    $categories[] = array(
                        'name' => $result['name'],
                        'href' => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/category&path=' . $this->request->get['path'] . '_' . $result['category_id']),
                        'thumb' => $this->model_tool_image->resize($result['image'] ? $result['image'] : '/image/no_image.jpg', $this->config->get('config_image_category_width'), $this->config->get('config_image_category_height'))
                        
                    );
                    
//                    print_r($categories);
                    

                $this->data['categories'] = $categories;

                // Products
                $results = $this->model_catalog_product->getCategoryProducts($category_id, $selected_params_ids, count($selected_params));
                $this->data['products'] = $this->model_catalog_product->formatProductList($results, $url);

		$this->data['ecommerce'] = '';
		$this->data['ecommerce'] .= '<script type="text/javascript">'."\n";
		$this->data['ecommerce'] .= 'window.dataLayer = window.dataLayer || [];'."\n";
		$this->data['ecommerce'] .= 'dataLayer.push({'."\n";
		$this->data['ecommerce'] .= "'ecommerce': {"."\n";
		$this->data['ecommerce'] .= "'currencyCode': '" . $this->session->data['currency']. "',"."\n";
		$this->data['ecommerce'] .= "'impressions': ["."\n";
		$i = 1;
		foreach($this->data['products'] as $product){
			$this->data['ecommerce'] .= "{"."\n";
			$this->data['ecommerce'] .= "'name': '" . $product['name'] . "',"."\n";
			$this->data['ecommerce'] .= "'id': '" . $product['model'] . "',"."\n";
			$this->data['ecommerce'] .= "'price': '" . ($product['special'] ? preg_replace('/[^\d.]/','', $product['special']) : preg_replace('/[^\d.]/','', $product['price'])) . "',"."\n";
			if ($product['manufacturer']) $this->data['ecommerce'] .= "'brand': '" . $product['manufacturer'] . "',"."\n";
			$this->data['ecommerce'] .= "'category': '" . $category_info['name'] . "',"."\n";
			$this->data['ecommerce'] .= "'list': '" . $category_info['name'] . "',"."\n";
			$this->data['ecommerce'] .= "'position': '" . $i . "',"."\n},\n";
			$i++;
		}
		$this->data['ecommerce'] = rtrim($this->data['ecommerce'], ',');
		$this->data['ecommerce'] .= "]},\n";
		$this->data['ecommerce'] .= "'event': 'gtm-ee-event',
		'gtm-ee-event-category': 'Enhanced Ecommerce',
		'gtm-ee-event-action': 'Product Impressions',
		'gtm-ee-event-non-interaction': 'True'";
		$this->data['ecommerce'] .= '});'."\n</script>\n";
				
				
                $url = HTTP_SERVER . 'index.php?route=product/category' . '&path=' . $this->request->get['path'];

                $this_url = $url . implode('', $selected_params_url);
                $this->data['sorts'] = $this->model_catalog_product->formatSorts($this_url);
                $this->data['pagination'] = $this->model_catalog_product->formatPagination($product_total, $this_url);

                // Filter by price
                if (isset($this->request->get['sort']))
                    $url .= '&sort=' . $this->request->get['sort'];
                if (isset($this->request->get['order']))
                    $url .= '&order=' . $this->request->get['order'];

                $this->data['limits'] = array();

                $this->data['limits'][] = array(
                    'text' => $this->config->get('config_catalog_limit'),
                    'value' => $this->config->get('config_catalog_limit'),
                    'href' => $this->model_tool_seo_url->rewrite($url  . implode('', $selected_params_url) . '&limit=' . $this->config->get('config_catalog_limit'))
                );

                $this->data['limits'][] = array(
                    'text' => $this->config->get('config_catalog_limit') * 2,
                    'value' => $this->config->get('config_catalog_limit') * 2,
                    'href' => $this->model_tool_seo_url->rewrite($url . implode('', $selected_params_url) . '&limit=' . $this->config->get('config_catalog_limit') * 2)
                );

                $this->data['limits'][] = array(
                    'text' => $this->config->get('config_catalog_limit') * 3,
                    'value' => $this->config->get('config_catalog_limit') * 3,
                    'href' => $this->model_tool_seo_url->rewrite($url . implode('', $selected_params_url) . '&limit=' . $this->config->get('config_catalog_limit') * 3)
                ); 

                // Template
                $this->template = $this->config->get('config_template') . '/template/product/category.tpl';
                $this->children = array(
                    'common/footer',
                    'common/header',
                    'module/search',
                    'module/hit',
                    'module/latest',
                    'module/special'
                 
                );

                $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
            } else {
                $this->document->title = $category_info['name'] . ' | ' . $this->language->get('title');
                $this->document->description = $category_info['meta_description'];

                $url = HTTP_SERVER . 'index.php?route=product/category' . '&path=' . $this->request->get['path'];
                if (isset($this->request->get['sort']))
                    $url .= '&sort=' . $this->request->get['sort'];
                if (isset($this->request->get['order']))
                    $url .= '&order=' . $this->request->get['order'];


                $this->data['with_subcats'] = false;
                $this->data['heading_title'] = $category_info['name'];
                $this->data['text_error'] = $this->language->get('text_empty');
                $this->data['button_continue'] = $this->language->get('button_continue');
                $this->data['continue'] = $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=common/home');

                $this->data['categories'] = array();
                $this->data['products'] = array();

                if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/category.tpl')) {
                    $this->template = $this->config->get('config_template') . '/template/product/category.tpl';
                } else {
                    $this->template = 'default/template/product/category.tpl';
                }

                $this->children = array(
                    'common/footer',
                    'common/header',
                    'module/search',
                    'module/hit',
                    'module/latest',
                    'module/special',
                    'module/cart'
                );

                $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
            }
        } else {
            $url = '';

            if (isset($this->request->get['limit']))
                $url .= '&limit=' . $this->request->get['limit'];
            if (isset($this->request->get['sort']))
                $url .= '&sort=' . $this->request->get['sort'];
            if (isset($this->request->get['order']))
                $url .= '&order=' . $this->request->get['order'];
            if (isset($this->request->get['page']))
                $url .= '&page=' . $this->request->get['page'];
            if (isset($this->request->get['path'])) {
                $this->document->breadcrumbs[] = array(
                    'href' => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/category&path=' . $this->request->get['path'] . $url),
                    'text' => $this->language->get('text_error'),
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
                'common/footer',
                'common/header',
                'module/search',
                'module/hit',
                'module/latest',
                'module/special',
                'module/cart'
            );

            $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
        }
    }

}

?>