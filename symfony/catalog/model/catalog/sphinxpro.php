<?php

class ModelCatalogSphinxPro extends Model {
	
	private $sphinx;
	
	public function checkSphinx() {
			
		if (!$this->sphinx instanceof \Sphinx\Sphinxsearch) {
			require_once(DIR_SYSTEM . 'library/sphinx/sphinxpro.php');
			$this->sphinx = new Sphinx\Sphinxpro($this->registry);
		} 
		return $this->sphinx;
	}
	
	public function getProducts($data) {
		
		$data['filter_name'] =  mb_strtolower($data['filter_name'], 'UTF-8');
		$data['filter_tag'] =  mb_strtolower($data['filter_tag'], 'UTF-8');
		
		$this->checkSphinx();
		
		$results = [];
		$product_total = 0;
		$result_suggested = '';
		$category_data = [];
		
		
	//	$product_data = $this->productQuery($data, 'product', true);
		
		//if($product_data['product_total'] > 1) {
			$product_data = $this->productQuery($data, 'product');
		//}
		

		if($product_data['product_total'] == 0) {
	
			$product_data = $this->productQuery($data);
						
			if($product_data['product_total'] == 0) {

				$words = $this->getWordsFromMeta($product_data['meta_data']);
				
				$suggest = $this->MakeMultiQSuggest($data['filter_name'], $words);
				
				if ($suggest) {
					$result_suggested =  $suggest;
					
					$data['filter_name'] = $suggest;
					$data['filter_tag'] = $suggest;
					$product_data = $this->productQuery($data, 'product');
					$product_total = $product_data['product_total'];
				}
		
			};
		
		}
		
		
		if(!empty($product_data['results']) ) {
			
			if($product_data['product_total'] > 1) {
				$category_data = $categories = $this->catQuery($data['filter_name'],  false);
			}
									
			$this->load->model('catalog/product');
			
			foreach($product_data['results'] as $product) {
				$results[] = $this->model_catalog_product->getProduct((int)$product['product_id_attr']);
			}
			
			$product_total = $product_data['product_total'];
		}

		return ['results' => $results, 'product_total' => $product_total, 'suggested' => $result_suggested, 'category_data' => $category_data] ;
	}
	
	private function catQuery($text, $match_phrase) {
		
		$results = [];
		
		$total = 0;
		
		$meta_data = [];
		
		$sql_where = '';
					
		$text = $this->sphinx->escape($text);
		
		if ($match_phrase) {
			$text = '^' . $text . '$';
			$index = 'main';
		} else {
			$index = 'product';
		}

	
					
		$sql = "SELECT groupby() as category_id, COUNT(*) as qty FROM ". $index ." WHERE MATCH ('".  $text ."') ". $sql_where ." GROUP BY categories_filter  ORDER BY qty DESC LIMIT 5 OPTION max_matches=100000; SHOW META;"; 

	

		$query = $this->sphinx->query($sql);
		
		
		
		if(isset($query[0]->rows)) {
			$results = $query[0]->rows;
		}
		
		if (isset($query[1])) {
			$meta_data = $query[1];
		}
		


		return ['results' => $results,  'meta_data' => $meta_data];
		
	}

	
	private function productQuery($data, $index = 'main', $match_phrase = false) {
	
			
		$results = [];
		
		$product_total = 0;
		
		$meta_data = [];
		
		$where_condition = [];
		
		$start = 0;
		$ranker = 'sph04';
		
		$limit = (int)$this->config->get('config_catalog_limit');
		

		$text = $data['filter_name'];
		
		if (isset($data['limit'])) {
			$limit = (int)$data['limit'];
			if ($limit > 100 ) $limit = 100;
		}
		
		if (isset($data['start'])) {
			$start = (int) $data['start'];
		}
		
		if (isset($data['filter_category_id'])) {
			if ($data['filter_category_id'] != 0 ) {
			$where_condition[] = " categories_filter = ". (int)$data['filter_category_id'] ."";
			}
		}
		
		//$sql_where = " AND stock_status_id IN (1, 2, 3) ";
		$sql_where = "";
		if(!empty($where_condition)) {
				$sql_where .=  " AND " . implode(' AND ', $where_condition);			
		}
		
		$text = $this->sphinx->escape($text);
		
		$sql_sort_order = '';
		$sort = 'weight';
		$order = 'asc';
		
		 
		 if (isset($data['order'])) { 
			if (($data['order']) == 'desc') {
				$order = 'DESC';
			}
			if (($data['order']) == 'asc') {
				$order = 'ASC';
			}
		}
		 

	
		if (isset($data['sort'])) {
			
			if(($data['sort']) == 'sort_order') { 
				$sort = 'weight';
			};	

			
			if(($data['sort']) == 'name') { 
				$sort = 'name';
			};
			
			if(($data['sort']) == 'price') {
				$sort = 'price';
			
			};			
			
			if(($data['sort']) == 'model') {
				$sort = 'price';
			
			};
		}
		
		//BY FIELD(stock_status_id, 2, 3, 6) DESC,
		
		if ($sort == 'name') { 
			$sql_sort_order = " ORDER BY sorter DESC, weight DESC, " . $sort  . " ". $order . " ";
		} else {
			$sql_sort_order = " ORDER BY sorter DESC,  " . $sort  . " ". $order . " ";			
		}
		
		
		if ($match_phrase) {
			$text = '^' . $text . '$';
			$index = 'main';
			$ranker = 'proximity_bm25';
		}
	
//expr
		
		$sql = "SELECT product_id_attr, WEIGHT() as weight FROM ". $index ." WHERE MATCH ('". $text ."')" .  $sql_where  . " GROUP BY product_id_attr ". $sql_sort_order  . " LIMIT ". $start .  ", " . $limit . " OPTION max_matches=100000,  field_weights=(category_names=1, name=100, description=1), idf=plain; SHOW META;"; 

		$query = $this->sphinx->query($sql);
		
		if(isset($query[0]->rows)) {
			$results = $query[0]->rows;
		}
		
		$meta = [];
			
		if (isset($query[1])) {

			foreach($query[1]->rows as $item) {
				
				$meta[$item['Variable_name']] = $item['Value'];
			
			}
		}
	
		if(isset($meta['total'])) {
			$product_total = (int)$meta['total'];
		}

		
		
		return ['results' => $results, 'product_total' => $product_total, 'meta_data' => $meta];
	}
	
	private function QSuggest($word, $index = 'main') {
			
		$this->checkSphinx();
		
		$word = $this->sphinx->escape($word);
	
		$sql = "CALL QSUGGEST( '". $word ."','". $index ."', 1 as limit, 2 as max_edits, 1 as result_stats, 3 as delta_len, 0 as result_line, 25 as max_matches, 4 as reject )";
			
		$query = $this->sphinx->singleQuery($sql);

		return $query;
	 
	}

	
	function MakeMultiQSuggest($query, $words, $index = 'main')	{
		
		
		if (empty($words)) {
			return;
		}
	
		$suggested = array();
		$llimf = 0;
		$i = 0;
		
		
		
		foreach ($words  as $key => $word) {
			if ($word['docs'] != 0)
				$llimf +=$word['docs'];$i++;
		}
		$llimf = $llimf / ($i * $i);
		
		$mis = [];
			
		foreach ($words  as $key => $word) {
			if ($word['docs'] == 0 | $word['docs'] < $llimf) {
				$mis[] = $word['keyword'];
			}
		}


			
		if (count($mis) > 0) {
	
			foreach ($mis as $m) {
				$re = $this->QSuggest($m, $index);  

				if ($re && isset($re->row['suggest'])) {
								
					if($m!=$re)
						
					$suggested[$m] = $re->row['suggest'];
				
				}
			}
			
	
			
			if(count($words) == 1 && empty($suggested)) {
				return false;
			}
			
				
			
			$phrase = explode(' ', $query);
	
	
			foreach ($phrase as $k => $word) {
						
				if (isset($suggested[strtolower($word)]))
				
						$phrase[$k] = $suggested[strtolower($word)];

			}
				
		
			$phrase = implode(' ', $phrase);
		

			return $phrase;
		}else{
			return false;
		}
	}
	
	private function getWordsFromMeta($meta_data){
		$data = [];

		
			
		/*
		foreach($meta_data as $item) {
			
			$data[$item['Variable_name']] = $item['Value'];
		}
		*/
		
		$words = [];
		
		foreach($meta_data as $k=>$v) {

			
			if(preg_match('/keyword\[\d+]/', $k)) {
				preg_match('/\d+/', $k,$key);
				$key = $key[0];
				$words[$key]['keyword'] = $v;
			}
			if(preg_match('/docs\[\d+]/', $k)) {
				preg_match('/\d+/', $k,$key);
				$key = $key[0];
				$words[$key]['docs'] = $v;
			}
		} 
	
	

		return $words;
	}
	
	private function getCategoriesWithProducts(){

		
		$data = array();
		$results = array();
		foreach($query->rows as $row) {
			$results[] = $row['category_id'];
		}
		
		if(!empty($results)) {
			$data = $this->addPath($results);
		}
		
		return $data;
	}
	
		private function addPath($results) {
		$csv = implode(',', $results);
		$query = $this->db->query('SELECT DISTINCT path_id from ' . DB_PREFIX . 'category_path WHERE category_id IN (' . $csv . ')');
		foreach($query->rows as $row) {
			$data[] = $row['path_id'];
		}
		return $data;
	}

	private function getCategoryParents($category_id) {
		$categories = array();

		$parent_query = $this->db->query("SELECT parent_id from " . DB_PREFIX . "category WHERE category_id = '" . $category_id . "'");

		$parent_id = $parent_query->row['parent_id'];
		if($parent_id != 0) {
			$categories[] = $parent_id;
			$parents = $this->getCategoryParents($parent_id);
			if($parents) {
				$categories = array_merge($parents, $categories);
			}
		}

		return $categories;
	}

}
?>