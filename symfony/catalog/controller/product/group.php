<?php

class ControllerProductGroup extends Controller {

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
       // $this->data['path']=$this->request->get['path'];
        
        $this->document->breadcrumbs = array();
        $this->document->breadcrumbs[] = array(
            'href' => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => FALSE
        );

        $pv = '';
        if(isset($this->request->get['pv'])){
            $pv = $this->request->get['pv'];
            $param_name = $this->model_catalog_product->getParamValue($pv);
        }
        $category_id = '';
        if(isset($this->request->get['category'])){
            $category_id = $this->request->get['category'];
        }
        
        
        if ($pv) {
 
            $this->document->title = $param_name . ' | Gra.ua | ' ;
            $this->document->keywords = '';
            $this->document->description = '';

            $this->data['heading_title'] = $param_name;
           // $this->data['description'] = html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8');

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
            
            $sort = isset($this->request->get['sort']) ? $this->request->get['sort'] : 'p.price';
                        
            $order = isset($this->request->get['order']) ? $this->request->get['order'] : 'ASC';

            $url = HTTP_SERVER . 'index.php?route=product/group';

            $url_sort = '';
            $url_limit = '';

            $url_pv = '';
            if (isset($this->request->get['pv'])){
                $url .= '&pv=' . $this->request->get['pv'];
                $url_pv .= '&pv=' . $this->request->get['pv'];
            }
            if (isset($this->request->get['category']))
                $url .= '&category=' . $this->request->get['category'];

            if (isset($this->request->get['limit'])) {
                $url_limit .= '&limit=' . $this->request->get['limit'];
            }
            if (isset($this->request->get['sort']))
                $url_sort .= '&sort=' . $sort;
            if (isset($this->request->get['order']))
                $url_sort .= '&order=' . $order;
 
             $this->document->breadcrumbs[] = array(
            'href' => $this->model_tool_seo_url->rewrite($url),
            'text' => $param_name,
            'separator' => $this->language->get('text_separator')
             );
            
             
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
             
            $product_total = $this->model_catalog_product->getCategoryParamProductsTotal($pv, $category_id);
            $this->data['parametr_categories'] = $this->model_catalog_category->getParametrCategories($category_id ? explode(',', $category_id):array() , $pv,  $url_limit . $url_sort);
             if ($product_total) {
                
                // Products
                $results = $this->model_catalog_product->getCategoryParamProducts($pv, $category_id);
                
                $this->data['products'] = $this->model_catalog_product->formatProductList($results);


		foreach ($this->data['products'] as $akey=>$img) {
    		     $this->data['products'][$akey]['image'] = $this->data['products'][$akey]['image'] ? $this->data['products'][$akey]['image']:'/image/no_image.jpg';
		     $this->data['products'][$akey]['thumb'] = $this->data['products'][$akey]['thumb'] ? $this->data['products'][$akey]['thumb']:'/image/no_image.jpg';
		}
//print_r($this->data['products']);



                $this->data['sorts'] = $this->model_catalog_product->formatSorts($url . $url_limit );
                $this->data['pagination'] = $this->model_catalog_product->formatPagination($product_total, $url . $url_sort . $url_limit);


                $this->data['limits'] = array();

                $this->data['limits'][] = array(
                    'text' => $this->config->get('config_catalog_limit'),
                    'value' => $this->config->get('config_catalog_limit'),
                    'href' => $this->model_tool_seo_url->rewrite($url . $url_sort . '&limit=' . $this->config->get('config_catalog_limit'))
                );

                $this->data['limits'][] = array(
                    'text' => $this->config->get('config_catalog_limit') * 2,
                    'value' => $this->config->get('config_catalog_limit') * 2,
                    'href' => $this->model_tool_seo_url->rewrite($url . $url_sort . '&limit=' . $this->config->get('config_catalog_limit') * 2)
                );

                $this->data['limits'][] = array(
                    'text' => $this->config->get('config_catalog_limit') * 3,
                    'value' => $this->config->get('config_catalog_limit') * 3,
                    'href' => $this->model_tool_seo_url->rewrite($url . $url_sort . '&limit=' . $this->config->get('config_catalog_limit') * 3)
                ); 

                // Template
                $this->template = $this->config->get('config_template') . '/template/product/group.tpl';
                $this->children = array(
                    'common/column_right',
                    'common/footer',
                    'common/header',
                    'module/search'
                    
                );
                $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
            } else {
                $this->document->title = $param_name . ' | ' . $this->language->get('title');
                $this->document->description = '';

                $url = HTTP_SERVER . 'index.php?route=product/category' ;
                if (isset($this->request->get['sort']))
                    $url .= '&sort=' . $this->request->get['sort'];
                if (isset($this->request->get['order']))
                    $url .= '&order=' . $this->request->get['order'];


//                $this->data['url_filter_price'] = $this->model_tool_seo_url->rewrite($url);

                $this->data['with_subcats'] = false;
                $this->data['heading_title'] = $param_name;
                $this->data['text_error'] = $this->language->get('text_empty');
                $this->data['button_continue'] = $this->language->get('button_continue');
                $this->data['continue'] = $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=common/home');

                $this->data['filter_params'] = array();
                $this->data['categories'] = array();
                $this->data['products'] = array();

                if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/group.tpl')) {
                    $this->template = $this->config->get('config_template') . '/template/product/group.tpl';
                } else {
                    $this->template = 'default/template/product/group.tpl';
                }

                $this->children = array(
                    'common/footer',
                    'common/header',
                    'module/search',
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
                    'href' => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/group' . $url),
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
                'common/column_left',
                'common/footer',
                'common/header',
                'module/search',
                'module/cart'
            );
            $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
        }
    }

}

?>