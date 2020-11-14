<?php
class ModelToolSeoUrl extends Model {

    // custom URL
    public $custom_routs  = array(
                'common/home'           => 'home',
                'product/special'       => 'sale',
                'product/group'         => 'group',
                'checkout/cart'         => 'cart',
                'account/login'         => 'login',
                'checkout/symple_order' => 'order',
                'checkout/order_success' => 'success',
                'information/feedback'  => 'feedback'
            );
    // . custom URL

	public function rewrite($link) {

		if ($this->config->get('config_seo_url')) {

		    if(!isset($url_data['query'])) {
                $url_data['query']['t'] = time();
            }

            $url_data = parse_url(str_replace('&amp;', '&', $link));

			$url = '';

			$data = array();

			parse_str($url_data['query'], $data);

            $flag = true;
            $flagcategory = false;
            if (array_key_exists('product_id', $data) ) {

                $flag = false;
                if ((!strpos($link, 'product/product/review')) || (!strpos($link, 'product/product/write')) || (!strpos($link, 'product/product/captcha'))) {
                    $flag=true;
                }
            } else if (array_key_exists('path', $data)) {
                $flagcategory = true;
            }

			foreach ($data as $key => $value) {
               
				if ((($key == 'product_id') || ($key == 'manufacturer_id') || ($key == 'information_id')) && ((!strpos($link, 'product/product/review')) && (!strpos($link, 'product/product/write')) && (!strpos($link, 'product/product/captcha')))) {
              
                                    // APC
                                    $query_to_search = $this->db->escape($key . '=' . (int)$value);
                                    $has = false;
                                 //   apc_fetch('graua_'.$query_to_search, $has);

                                    if($has) {
                                       $url .= '/' . apc_fetch('graua_'.$query_to_search);
                                       unset($data[$key]);
                                    } else {
                                      $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` = '" . $query_to_search . "'");

                                      if ($query->num_rows) {
                                        $url .= '/' . $query->row['keyword'];
                                     //   apc_store('graua_'.$query_to_search, $query->row['keyword'], 60*60*24*7);
                                        unset($data[$key]);
                                      }
                                    }
                                    // .apc
                                    $flag = false;
				}
                elseif (($key == 'path') && ($flag)) {
					$categories = explode('_', $value);
                    //фикс для НЕотслеживания иерархии категорий в ссылке
                    $cat = end($categories);
                    $categories = array();
                    $categories[] = $cat;
                    //. конец

					foreach ($categories as $category) {
                        // APC
						$query_to_search = "category_id=" . (int)$category ;
                        $has = false;
                      //  apc_fetch('graua_'.$query_to_search, $has);
                        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` = '" . $query_to_search . "'");

                        if($has){
                         $url .= '/' . apc_fetch('graua_'.$query_to_search);
                        }
                        else {
                          $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` = '" . $query_to_search . "'");

                          if ($query->num_rows) {
                            $url .= '/' . $query->row['keyword'];
                          //  apc_store('graua_'.$query_to_search, $query->row['keyword'], 60*60*24*7);
                          }
                        }
                        // .apc
					}
					unset($data[$key]);
				}
			}

            $domain = $url_data['scheme'] . '://' . $url_data['host'] . (isset($url_data['port']) ? ':' . $url_data['port'] : '');
            $route = $data['route'];
            unset($data['route']);

            $query = '';

            if ($data) {
                foreach ($data as $key => $value) {
                    $query .= '&' . $key . '=' . $value;
                }

                if ($query) {
                    $query = '?' . trim($query, '&');
                }
            }
      
            if ($url) {
                
            if (!$flag) {
                return $domain . str_replace('/index.php', '', $url_data['path']) . '/product' . $url . $query;
            } else if ($flagcategory == true) {
                return $domain . str_replace('/index.php', '', $url_data['path']) . '/catalog' . $url . $query;
            } else  {
               
                return $domain . str_replace('/index.php', '', $url_data['path']) . $url . $query;
            }
				
            }
            else if(isset($this->custom_routs[$route])) {
                return $domain . '/' . $this->custom_routs[$route] . $query;
            }
            else {
				return $link;
                    }
            } else {
                    return $link;
            }
	}
}
?>