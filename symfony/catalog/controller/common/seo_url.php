<?php
class ControllerCommonSeoUrl extends Controller {
	public function index() {
		if (isset($this->request->get['_route_'])) {
			$parts = explode('/', $this->request->get['_route_']);

            // custom URL
            $action = isset($parts[0]) ? $parts[0] : 'x';
            $this->load->model('tool/seo_url');
            $custom_routs = array_flip($this->model_tool_seo_url->custom_routs);

            if(isset($custom_routs[$action])) {
                $this->request->get['route'] = $custom_routs[$action];
            }
            // . custom URL

            else {
            
            
			foreach ($parts as $part) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE keyword = '" . $this->db->escape($part) . "'");

				if ($query->num_rows) {
					$url = explode('=', $query->row['query']);

					if ($url[0] == 'product_id') {
						$this->request->get['product_id'] = $url[1];
						//by Novikov
						$path_prod=$part;	
					}

					if ($url[0] == 'category_id') {
						if (!isset($this->request->get['path'])) {
							$this->request->get['path'] = $url[1];
							//by Novikov
							$path_cat=$part;
						} else {
							$this->request->get['path'] .= '_' . $url[1];
                                                        //by Novikov
                                                        $path_cat=$part;
 						}
					}

					if ($url[0] == 'manufacturer_id') {
							$this->request->get['manufacturer_id'] = $url[1];
                                                        //by Novikov
                                                        $path_bran=$part;
 					
					}

					if ($url[0] == 'information_id') {
						$this->request->get['information_id'] = $url[1];
					}
				} else {
					$this->request->get['route'] = 'error/not_found';
				}
			}//foreach

//by Novikov если в адресе помиомо товара катеонрия или бренд - редирект на облегченнй арес только с товаром
if ($path_prod AND ($path_cat OR $path_bran)) {
	header('HTTP/1.1 301 Moved Permanently');
	header ("Location: /product/".$path_prod);
}


			if (isset($this->request->get['product_id'])) {
				$this->request->get['route'] = 'product/product';
			} elseif (isset($this->request->get['path'])) {
				$this->request->get['route'] = 'product/category';
			} elseif (isset($this->request->get['manufacturer_id'])) {
				$this->request->get['route'] = 'product/manufacturer';
			} elseif (isset($this->request->get['information_id'])) {
				$this->request->get['route'] = 'information/information';
			}
        }
        
			if (isset($this->request->get['route'])) {
				return $this->forward($this->request->get['route']);
			}
		}//get['_route_']
	}//function
}
?>