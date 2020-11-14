<?php

class  ControllerModuleSphinxpro extends Controller {

   	public function suggest() {
		
		$result = [];

		if (isset($this->request->get['search'])) {
	
		$this->load->model('catalog/sphinxpro');
		$this->load->model('tool/seo_url');
                

			if ($this->model_catalog_sphinxpro->checkSphinx()->connection) {
				
				$search = $this->request->get['search'];
				$limit = 10;
				$sort = 'name';
				$order = 'ASC';
				
				$filter_data = array(
					'filter_name'         => $search,
					'filter_tag'          => '',
					'sort'                => $sort,
					'order'               => $order,
					'start'               => 0,
					'limit'               => $limit
				);
				
						
						
				$result['products'] = [];
				$result['categories'] = [];
				$result['more'] = [];
				$result['suggested'] = '';
		
					$sphinx_data = $this->model_catalog_sphinxpro->getProducts($filter_data);
					
					
						
					$products = $sphinx_data['results'];
					$product_total = $sphinx_data['product_total'];
					
					if ($sphinx_data['suggested'] && ($sphinx_data['suggested'] != $search)) {
						$result['suggested'] = 'показаны результаты по запросу <span class="sugg_suggested_text">' . $sphinx_data['suggested'] . '</span>';
					}
				
					
	

				if ($product_total > 5 ) {
								
					//$href = $this->url->link('product/search');
					
					$result['more'] = [
						'text' => 'Показать все результаты (' . $product_total . ')',
						'href' => $href
					];
				}
				
				foreach ($products as $product) {
					/*
					if ($result['image']) {
						$image = $this->model_tool_image->resize($result['image'], $setting['image_width'], $setting['image_height']);
					} else {
						$image = $this->model_tool_image->resize('no_image.jpg', $setting['image_width'], $setting['image_height']);
					}
					*/

		
					$result['products'][] = [
						'name'			=> htmlspecialchars_decode($product['name'], ENT_COMPAT),
						//'href'			=> $this->url->link('product/product', 'product_id=' . $product['product_id']),
						'href' => $this->model_tool_seo_url->rewrite(HTTPS_SERVER . 'index.php?route=product/product&product_id=' . $product['product_id']),
						'manufacturer'  => $product['manufacturer'],
						'sku'  => $product['model'],
				
					   ];
				}
				
				
				
				$categories = [];
		
				if(isset($sphinx_data['category_data']['results'])) {
					$categories = $sphinx_data['category_data']['results'];
				}

			
				if(!empty($categories)){
					$this->load->model('catalog/category');
					
					foreach ($categories as $category) {
						
				
						
						$category_info =  $this->model_catalog_category->getCategory($category['category_id']);
						if(!empty($category_info)) {
							$result['categories'][] = [
								'name' => $category_info['name'],
								'href' => $this->model_tool_seo_url->rewrite(HTTPS_SERVER . 'index.php?route=product/category&path=' . $category['category_id']),
							//	'href' => $this->url->link('product/category', 'path=' . $category['category_id']),
								'qty' =>  $category['qty'],
								
							
							];
						};
					};
				}
	
			
				//$autoCompleteCatLimit = (int)$this->config->get('sphinx_autocomplete_cat_limit');
				
				
				//$searchData['limit'] = $autoCompleteCatLimit;

				//$resultsCategories = $this->model_catalog_sphinx->search($searchData, 'categories', true);
			
			} 

		}
		
		header('Content-Type: application/json');
		$this->response->setOutput(json_encode($result));		


    }

}
?>