<?php

class ControllerProductProduct extends Controller {

    private $error = array();

    public function index() {
        $this->language->load('product/product');
        $this->load->model('catalog/category');
        $this->load->model('catalog/product');
        $this->load->model('catalog/review');
        $this->load->model('tool/seo_url');
        $this->load->model('tool/image');

        $this->document->breadcrumbs = array();
        $this->document->breadcrumbs[] = array(
            'href' => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => FALSE
        );


        if (!$this->config->get('config_store_id')) {
            $this->data['product_page_block'] = html_entity_decode($this->config->get('config_product_page_block_' . $this->config->get('config_language_id')), ENT_QUOTES, 'UTF-8');
        } else {
            $store_info = $this->model_setting_store->getStore($this->config->get('config_store_id'));

            if ($store_info) {
                $this->data['product_page_block'] = html_entity_decode($store_info['description'], ENT_QUOTES, 'UTF-8');
            } else {
                $this->data['product_page_block'] = '';
            }
        }
       

        $product_id = isset($this->request->get['product_id']) ? $this->request->get['product_id'] : 0;
        $product_info = $this->model_catalog_product->getProduct($product_id);

        if ($product_info) {

            // Viewed products
            $this->model_catalog_product->addToViewed($product_id);

            // Category path
            $path = '';
            $path_title = array();
            $group_param_value_id = $this->model_catalog_product->getParamId($product_id);
//            if($group_param_value_id){
//               
//                $param_name = $this->model_catalog_product->getParamValue($group_param_value_id);
//                $this->document->breadcrumbs[] = array(
//                            'href' => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/group&pv=' . $group_param_value_id),
//                            'text' => $param_name,
//                            'separator' => $this->language->get('text_separator')
//                );
//            } 
            $category_id=0;
            if (isset($this->request->get['path'])) {
                foreach (explode('_', $this->request->get['path']) as $path_id) {
                    $category_info = $this->model_catalog_category->getCategory($path_id);
                    if (!$path)
                        $path = $path_id;
                    else
                        $path .= '_' . $path_id;

                    if ($category_info) {
                        
                        $this->document->breadcrumbs[] = array(
                            'href' => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/category&path=' . $path),
                            'text' => $category_info['name'],
                            'separator' => $this->language->get('text_separator')
                        );
                        $category_id = $category_info['category_id'];
                        $path_title[] = $category_info['name'];
                    }
                }
            } else {
                $path_array = $this->model_catalog_product->getProductPath($product_id);
                if(!$group_param_value_id) {
                    foreach ($path_array as $key => $path_item) {
                                   $path .= ( $key ? '_' : '') . $path_item['category_id'];

                                       $this->document->breadcrumbs[] = array(
                                           'href' => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/category&path=' . $path),
                                           'text' => $path_item['name'],
                                           'separator' => $this->language->get('text_separator')
                                       );
                                   $category_id = $path_item['category_id'];
                                   $path_title[] = $path_item['name'];
                               }

                } else {
                    $this->document->breadcrumbs[] = array(
                        'href' => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/category&path=' . $path_array[0]['category_id']),
                        'text' => $path_array[0]['name'],
                        'separator' => $this->language->get('text_separator')
                    );
                }
                
            }
            $url = '&path=' . $path;

            $this->document->breadcrumbs[] = array(
                'href' => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/product' . $url . '&product_id=' . $this->request->get['product_id']),
                'text' => $product_info['model_name'] . ' ' . $product_info['name'],
                'separator' => $this->language->get('text_separator')
            );

            // Meta info
            $this->document->title = $product_info['meta_title'];
            $this->document->keywords = $product_info['meta_keywords'];
            $this->document->description = $product_info['meta_description'];

            
            $this->document->links = array();
            $this->document->links[] = array(
                'href' => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/product&product_id=' . $this->request->get['product_id']),
                'rel' => 'canonical'
            );
            // .meta info

            $this->data['heading_title'] = $product_info['name'];
            $this->data['text_price'] = $this->language->get('text_price');
            $this->data['text_product_description'] = $this->language->get('text_product_description');
            $this->data['text_warranty'] = $this->language->get('text_warranty');
            $this->data['text_availability'] = $this->language->get('text_availability');
            $this->data['text_model'] = $this->language->get('text_model');
            $this->data['text_manufacturer'] = $this->language->get('text_manufacturer');
            $this->data['text_order_quantity'] = $this->language->get('text_order_quantity');
            $this->data['text_price_per_item'] = $this->language->get('text_price_per_item');
            $this->data['text_qty'] = $this->language->get('text_qty');
            $this->data['text_write'] = $this->language->get('text_write');
            $this->data['text_average'] = $this->language->get('text_average');
            $this->data['text_no_rating'] = $this->language->get('text_no_rating');
            $this->data['text_note'] = $this->language->get('text_note');
            $this->data['text_no_images'] = $this->language->get('text_no_images');
            $this->data['text_no_related'] = $this->language->get('text_no_related');
            $this->data['text_wait'] = $this->language->get('text_wait');
            $this->data['text_tags'] = $this->language->get('text_tags');
            $this->data['text_minimum'] = sprintf($this->language->get('text_minimum'), $product_info['minimum']);
            $this->data['text_similar'] = $this->language->get('text_similar');
            $this->data['text_similar_price'] = $this->language->get('text_similar_price');
            $this->data['text_no_similar'] = $this->language->get('text_no_similar');
            $this->data['entry_name'] = $this->language->get('entry_name');
            $this->data['entry_review'] = $this->language->get('entry_review');
            $this->data['entry_rating'] = $this->language->get('entry_rating');
            $this->data['entry_good'] = $this->language->get('entry_good');
            $this->data['entry_bad'] = $this->language->get('entry_bad');
            $this->data['entry_captcha'] = $this->language->get('entry_captcha');
            $this->data['text_price'] = $this->language->get('text_price');
            $this->data['text_in_stock'] = $this->language->get('text_in_stock');
            $this->data['text_buy'] = $this->language->get('text_buy');
            $this->data['text_read_more'] = $this->language->get('text_read_more');
            $this->data['text_price'] = $this->language->get('text_price');
            $this->data['text_group_others'] = $this->language->get('text_group_others');
            $this->data['text_group_colors'] = $this->language->get('text_group_colors');
            $this->data['text_group_sizes'] = $this->language->get('text_group_sizes');
            $this->data['button_add_to_cart'] = $this->language->get('button_add_to_cart');

            $this->data['action'] = $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=checkout/symple_order&product=' . $this->request->get['product_id']);
            $this->data['redirect'] = $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/product' . $url . '&product_id=' . $this->request->get['product_id']);

            $this->data['wish_list_href'] = $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/wish_list');
            
            // Product info
            $this->data['model'] = $product_info['model'];
            $this->data['manufacturer'] = $product_info['manufacturer'];
            $this->data['manufacturers'] = $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/manufacturer&manufacturer_id=' . $product_info['manufacturer_id']);
            $this->data['description'] = html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8');
       
            $this->data['quote'] = html_entity_decode($product_info['quote'], ENT_QUOTES, 'UTF-8');
            
            $this->data['short_description'] = html_entity_decode($product_info['short_description'], ENT_QUOTES, 'UTF-8');
            $this->data['product_id'] = $this->request->get['product_id'];
            $this->data['warranty'] = $this->formatWarranty($product_info['warranty']);
            $this->data['brand_param_id'] = $this->config->get('config_brand_param_id');
            $this->data['is_waiting'] = $product_info['is_waiting'];
            $this->data['product_id'] = $product_id;
            $this->data['category_id'] = $category_id;
            $this->data['video'] = html_entity_decode($product_info['video']);
            $this->data['stock_status'] = html_entity_decode($product_info['stock_status']);
            
            
            
            
           
            // Price
            $price = $product_info['price'];
            $old_price = $product_info['old_price'];
            
            $this->data['is_new'] = $product_info['is_new'];
            $this->data['price'] = $this->currency->format($this->tax->calculate($price, $product_info['tax_class_id'], $this->config->get('config_tax')));
            $this->data['old_price'] = $product_info['old_price'] ? $this->currency->format($this->tax->calculate($old_price, $product_info['tax_class_id'], $this->config->get('config_tax'))) : 0;
           
            // Params
           
            $this->data['params'] = $this->model_catalog_product->getParamsDesc($product_id, $category_id);

if ($_GET[t]==3) print_r($product_info['image']);

            // Images
            $image = $product_info['image'] ? $product_info['image'] : '/image/no_image.jpg';

            $this->data['popup'] = $this->model_tool_image->resize($image, $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height')) 
            ? $this->model_tool_image->resize($image, $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height')) : '/image/no_image.jpg';
            
            $this->data['middle'] = $this->model_tool_image->resize($image, $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height')) 
            ? $this->model_tool_image->resize($image, $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height')) : '/image/no_image.jpg';
            
            $this->data['thumb'] = $this->model_tool_image->resize($image, 100, 100)?$this->model_tool_image->resize($image, 100, 100) : '/image/no_image.jpg';


            $this->data['images'] = array();
            $results = $this->model_catalog_product->getProductImages($this->request->get['product_id']);


//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

foreach ($results AS $ikey => $res) {
    $a=explode('/', $res['image']);
    //print_r($a);
    //echo $res['image'];
    $resultIm= 'cache/data-product-'.$a[2].'-'.preg_replace('/\.jpg/iu','-650x650.jpg',$a[3]);
    //by Novikov костыль после переноса на хостинг часть картнок провалились в корневую папку кэша
    if (!file_exists('image/'.$res['image'])) {
	if (file_exists('image/'.$resultIm))  $results[$ikey]['image']=$resultIm;
    }
} //foreach
//print_r($results);



            
if ($_GET[t]==2)        print_r($results);

            foreach ($results as $result) {
                $this->data['images'][] = array(
                    'popup' => $this->model_tool_image->resize($result['image'], $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height'))?
                    $this->model_tool_image->resize($result['image'], $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height')) : '/image/no_image.jpg',
                    
                    'middle' => $this->model_tool_image->resize($result['image'], $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'))?
                    $this->model_tool_image->resize($result['image'], $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height')) : '/image/no_image.jpg',
                    
                    'image_id' => $result['product_image_id'] ? $result['product_image_id'] : '/image/no_image.jpg',
                    'image' => HTTPS_IMAGE . $result['image'] ? HTTPS_IMAGE . $result['image'] : '/image/no_image.jpg',
                    'thumb' => $this->model_tool_image->resize($result['image'],  100, 100) ? $this->model_tool_image->resize($result['image'],  100, 100) : '/image/no_image.jpg',
                );
            }
           
if ($_GET[t]==1)           print_r($this->data['images']);
           
            $this->data['review_status'] = $this->config->get('config_review');
            
            $this->data['tab_review'] = sprintf($this->language->get('tab_review'), $this->model_catalog_review->getTotalReviewsByProductId($this->request->get['product_id']));
            $this->data['similar_products'] = array();
            if ($this->config->get('config_review')) {
                            $average = $this->model_catalog_review->getAverageRating($product_id);
                        } else {
                            $average = false;
                        }
            $this->data['review_total'] = $this->model_catalog_review->getTotalReviewsByProductId($this->request->get['product_id']);
           
            $results = $this->model_catalog_product->getSimilarProducts($product_id, $category_id);
            $this->data['similar_products'] = $this->model_catalog_product->formatProductModuleList($results, $url);
            
            /*Похожие по цене товары */
            $results = $this->model_catalog_product->getSimilarPriceProducts($price, $product_id, $category_id);
            $this->data['similar_price_products'] = $this->model_catalog_product->formatProductModuleList($results, $url);
            
            $this->model_catalog_product->updateViewed($product_id);
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/product.tpl')) {
                $this->template = $this->config->get('config_template') . '/template/product/product.tpl';
            } else {
                $this->template = 'default/template/product/product.tpl';
            }	$this->data['ecommerce'] = '';
		$this->data['ecommerce'] .= '<script type="text/javascript">'."\n";
		$this->data['ecommerce'] .= 'window.dataLayer = window.dataLayer || [];'."\n";
		$this->data['ecommerce'] .= 'dataLayer.push({'."\n";
		$this->data['ecommerce'] .= "'ecommerce': {"."\n";
		$this->data['ecommerce'] .= "'currencyCode': '" . $this->session->data['currency']. "',"."\n";
		$this->data['ecommerce'] .= "'detail': {"."\n";
		$this->data['ecommerce'] .= "'actionField': {'list': 'Карточка товара'},"."\n";
		$this->data['product_name'] = $product_info['name'];
		$this->data['category_name'] =  $category_info['name'];
		$this->data['clear_price'] =  $product_info['special'] ? preg_replace('/[^\d.]/','', $product_info['special']) : preg_replace('/[^\d.]/','', $product_info['price']);
			$this->data['ecommerce'] .= "'products': [{"."\n";
			$this->data['ecommerce'] .= "'name': '" . $product_info['name'] . "',"."\n";
			$this->data['ecommerce'] .= "'id': '" . $product_info['model'] . "',"."\n";
			$this->data['ecommerce'] .= "'price': '" . ($product_info['special'] ? preg_replace('/[^\d.]/','', $product_info['special']) : preg_replace('/[^\d.]/','', $product_info['price'])) . "',"."\n";
			if ($product_info['manufacturer']) $this->data['ecommerce'] .= "'brand': '" . $product_info['manufacturer'] . "',"."\n";
			if($category_info['name']) $this->data['ecommerce'] .= "'category': '" . $category_info['name'] . "',"."\n";
			if(isset( $path_array[0]['name'])) $this->data['ecommerce'] .= "'category': '" . $path_array[0]['name'] . "',"."\n";
			$this->data['ecommerce'] .= "}]},\n"; 
				
		$this->data['ecommerce'] .= "'impressions': ["."\n";
		$i = 1;
		if ($this->data['products']) {
		foreach($this->data['products'] as $product){
			$this->data['ecommerce'] .= "{"."\n";
			$this->data['ecommerce'] .= "'name': '" . $product['name'] . "',"."\n";
			$this->data['ecommerce'] .= "'id': '" . $product['model'] . "',"."\n";
			$this->data['ecommerce'] .= "'price': '" . ($product['special'] ? preg_replace('/[^\d.]/','', $product['special']) : preg_replace('/[^\d.]/','', $product['price'])) . "',"."\n";
			$this->data['ecommerce'] .= "'list': 'Рекомендуемые в карточке товара',"."\n";
			$this->data['ecommerce'] .= "'position': '" . $i . "',"."\n},\n";
			$i++;
		}
		}
		$this->data['ecommerce'] = rtrim($this->data['ecommerce'], ',');
		$this->data['ecommerce'] .= "]},\n";
		$this->data['ecommerce'] .= "'event': 'gtm-ee-event',
		'gtm-ee-event-category': 'Enhanced Ecommerce',
		'gtm-ee-event-action': 'Product Details',
		'gtm-ee-event-non-interaction': 'True'";
		$this->data['ecommerce'] .= '});'."\n</script>\n";
			

            $this->children = array(
                'common/column_left',
                'module/viewed',
                'common/footer',
                'common/header'
            );

//if ($_GET[t]==1) print_r($this);

            $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
        } else {
            $url = '';

            if (isset($this->request->get['path'])) {
                $url .= '&path=' . $this->request->get['path'];
            }

            $this->document->breadcrumbs[] = array(
                'href' => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/product' . $url . '&product_id=' . $product_id),
                'text' => $this->language->get('text_error'),
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
                'common/column_left',
                'module/viewed',
                'common/footer',
                'common/header'
            );

            $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
        }
    }
	
public function shortProduct() {
		$json = array();
		
		if ($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->post['product_id'])) {
			$this->load->model('catalog/product');

			$json['product'] = $this->model_catalog_product->getProduct($this->request->post['product_id']);
			
			if($json['product']) $json['success'] = 'true';
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
		} 
		
		
		 
		
    public function review() {
        $this->language->load('product/product');
        $this->load->model('tool/seo_url');
        $this->load->model('catalog/review');

        $this->data['text_no_reviews'] = $this->language->get('text_no_reviews');

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $this->data['reviews'] = array();

        $results = $this->model_catalog_review->getReviewsByProductId($this->request->get['product_id'], ($page - 1) * 5, 5);

        foreach ($results as $result) {
            $this->data['reviews'][] = array(
                'author' => $result['author'],
                'rating' => $result['rating'],
                'text' => strip_tags($result['text']),
                'stars' => sprintf($this->language->get('text_stars'), $result['rating']),
                'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
            );
        }

        $review_total = $this->model_catalog_review->getTotalReviewsByProductId($this->request->get['product_id']);

        $pagination = new Pagination();
        $pagination->total = $review_total;
        $pagination->page = $page;
        $pagination->limit = 5;
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/product/review&product_id=' . $this->request->get['product_id'] . '&page={page}');

        $this->data['pagination'] = $pagination->render();

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/review.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/product/review.tpl';
        } else {
            $this->template = 'default/template/product/review.tpl';
        }

        $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
    }

    public function write() {
        $this->language->load('product/product');

        $this->load->model('catalog/review');

        $json = array();

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_catalog_review->addReview($this->request->get['product_id'], $this->request->post);

            $json['success'] = $this->language->get('text_success');
        } else {
            $json['error'] = $this->error['message'];
        }

        $this->load->library('json');

        $this->response->setOutput(Json::encode($json));
    }

    public function captcha() {
        $this->load->library('captcha');

        $captcha = new Captcha();

        $this->session->data['captcha'] = $captcha->getCode();

        $captcha->showImage();
    }

    private function formatWarranty($warranty) {
        if ($warranty > 12 && $warranty % 12 == 0) {
            $warranty = $warranty / 12;
            $num = $warranty - (floor($warranty / 10) * 10);
            if ($num == 1) {
                $str = "год";
            } elseif (($num > 1) && ($num < 5)) {
                $str = "года";
            } elseif (($num >= 5) && ($num <= 9)) {
                $str = "лет";
            }

            return $warranty . " " . $str;
        } else if ($warranty == 0) {
            return $warranty;
        } else {
            if (($warranty >= 5) && ($warranty <= 14))
                $str = "месяцев";
            else {
                $num = $warranty - (floor($warranty / 10) * 10);
                if ($num == 1) {
                    $str = "месяц";
                } elseif ($num == 0) {
                    $str = "месяцев";
                } elseif (($num >= 2) && ($num <= 4)) {
                    $str = "месяца";
                } elseif (($num >= 5) && ($num <= 9)) {
                    $str = "месяцев";
                }
            }

            return $warranty . " " . $str;
        }
    }

    private function validate() {
        if ((strlen(utf8_decode($this->request->post['name'])) < 3) || (strlen(utf8_decode($this->request->post['name'])) > 25)) {
            $this->error['message'] = $this->language->get('error_name');
        }

        if ((strlen(utf8_decode($this->request->post['text'])) < 25) || (strlen(utf8_decode($this->request->post['text'])) > 1000)) {
            $this->error['message'] = $this->language->get('error_text');
        }

        if (!$this->request->post['rating']) {
            $this->error['message'] = $this->language->get('error_rating');
        }

        if (!isset($this->session->data['captcha']) || ($this->session->data['captcha'] != $this->request->post['captcha'])) {
            $this->error['message'] = $this->language->get('error_captcha');
        }

        if (!$this->error) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}

?>