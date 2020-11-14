<?php 
class ControllerFeedSitemapxxl extends Controller {
	public function index() {

			
			//if(!$this->config->get('cron_sitemap_status') || !$this->config->get('google_sitemap_status')) exit();
			
			$limit = 50000;
			$basedir = (realpath(dirname(__FILE__))) . '/../../../' ;

			$server = HTTPS_SERVER;
			
			$this->load->model('tool/seo_url');
			$this->load->model('catalog/product');
			$products = $this->getProducts();
			$total = count($products);

			for( $i = 0 ; $i <= floor($total/$limit) ; $i++ )	{
				$output = '';	
				$tmp_product = array_slice($products, $i*$limit, $limit);
				$output .= '<?xml version="1.0" encoding="UTF-8"?>';
				$output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
				
				foreach ($tmp_product as $product) {
					$output .= '<url>';
					//$output .= '<loc>' . str_replace('&', '&amp;', str_replace('&amp;', '&', $this->url->link('product/product', 'product_id=' . $product['product_id']))) . '</loc>';
					$output .= '<loc>' . str_replace('&', '&amp;', $this->model_tool_seo_url->rewrite(HTTPS_SERVER . 'index.php?route=product/product&product_id=' . $product['product_id'])) . '</loc>';
					$output .= '<lastmod>' . substr(max($product['date_added'], $product['date_modified']), 0, 10) . '</lastmod>';
					$output .= '<changefreq>weekly</changefreq>';
					$output .= '<priority>1.0</priority>';
					$output .= '</url>';
				}
				
				$output .= '</urlset>';	

			$gzfile = $basedir . 'sitemap' . $i . '.xml.gz';
			$fp = gzopen ($gzfile, "w9");
			gzwrite ($fp, $output);
			gzclose($fp);

			}
			

			$output ='<?xml version="1.0" encoding="UTF-8"?>';
			$output .='<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
			$output .='<sitemap>';
			$output .='<loc>' . $server . 'base.xml.gz</loc>';
			$output .='<lastmod>'. date("Y-m-d\TH:i:sP") .'</lastmod>';
			$output .='</sitemap>';
			
			for( $i = 0 ; $i <= floor($total/$limit) ; $i++ )	{
				$output .='<sitemap>';
				$output .='<loc>' . $server . 'sitemap' . $i . '.xml.gz</loc>';
				$output .='<lastmod>'. date("Y-m-d\TH:i:sP") .'</lastmod>';
				$output .='</sitemap>';
			}
			$output .='</sitemapindex>';
			
					
					
			
			$file_name = $basedir . "sitemap.xml";  
			$w = fopen($file_name,'w'); 
			fwrite($w, $output); 
			fclose($w); 
				
				
			
			$output = '<?xml version="1.0" encoding="UTF-8"?>';
			$output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
			
			$this->load->model('catalog/category');
			
			$output .= $this->getCategories(0);

			$this->load->model('catalog/manufacturer');

			$manufacturers = $this->model_catalog_manufacturer->getManufacturers();

			foreach ($manufacturers as $manufacturer) {
				$output .= '<url>';
				//$output .= '<loc>' . str_replace('&', '&amp;', str_replace('&amp;', '&', $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $manufacturer['manufacturer_id']))) . '</loc>';
				$output .= '<loc>' . str_replace('&', '&amp;', $this->model_tool_seo_url->rewrite(HTTPS_SERVER . 'index.php?route=product/manufacturer&manufacturer_id=' . $manufacturer['manufacturer_id'])) . '</loc>';
				$output .= '<changefreq>weekly</changefreq>';
				$output .= '<priority>0.7</priority>';
				$output .= '</url>';
			}

			$this->load->model('catalog/information');

			$informations = $this->model_catalog_information->getInformations();
/*
			foreach ($informations as $information) {
				$output .= '<url>';
				$output .= '<loc>' . str_replace('&', '&amp;', str_replace('&amp;', '&', $this->url->link('information/information', 'information_id=' . $information['information_id']))) . '</loc>';
				$output .= '<changefreq>weekly</changefreq>';
				$output .= '<priority>0.5</priority>';
				$output .= '</url>';
			}
	*/				
			$output .= '</urlset>';

			$gzfile = $basedir . "base.xml.gz";
			$fp = gzopen ($gzfile, "w9");
			gzwrite ($fp, $output);
			gzclose($fp);			


	}
	
	protected function getCategories($parent_id, $current_path = '') {
		$output = '';

		$results = $this->model_catalog_category->getCategories($parent_id);

		foreach ($results as $result) {
			if (!$current_path) {
				$new_path = $result['category_id'];
			} else {
				$new_path = $current_path . '_' . $result['category_id'];
			}

			$output .= '<url>';
			//$output .= '<loc>' . str_replace('&', '&amp;', str_replace('&amp;', '&', $this->url->link('product/category', 'path=' . $new_path))) . '</loc>';
			$output .= '<loc>' . str_replace('&', '&amp;', $this->model_tool_seo_url->rewrite(HTTPS_SERVER . 'index.php?route=product/category&path=' . $new_path)) . '</loc>';
			$output .= '<lastmod>' . substr(max($result['date_added'], $result['date_modified']), 0, 10) . '</lastmod>';
			$output .= '<changefreq>weekly</changefreq>';
			$output .= '<priority>0.7</priority>';
			$output .= '</url>';

			$output .= $this->getCategories($result['category_id'], $new_path);
		}

		return $output;
	}
	
	private function getProducts() {

		$customer_group_id = $this->config->get('config_customer_group_id');

		$sql = "SELECT DISTINCT p.product_id, p.date_added, p.date_modified"; 
		
		$sql .= " FROM " . DB_PREFIX . "product p";
	
		$sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() ";
			
        $query = $this->db->query($sql);
 
		return  $query->rows;

	}
	
}
?>