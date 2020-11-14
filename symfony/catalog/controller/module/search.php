<?php
class ControllerModuleSearch extends Controller {
    public function index() {
		$this->language->load('module/special');
                $this->load->model('catalog/product');
		$this->load->model('tool/seo_url');
                
                $this->data['budget'] = array(); 
                $min_max = $this->model_catalog_product->getMinMaxProductsBudget();
               
                if($min_max && (!is_null($min_max['min']) || !is_null($min_max['max']))){
                  $this->data['min_budget'] = $min_max['min'];
                  $this->data['max_budget'] = $min_max['max'];
                }else{
                  $this->data['min_budget'] = 0;
                  $this->data['max_budget'] = 0;  
                }
                
                if(isset($this->request->get['route']) && ($this->request->get['route']=='product/group'||$this->request->get['route']=='product/category')){
                    $this->data['local_url'] = 1;
                }else{
                    $this->data['local_url'] = 0;
                }
                
                if(isset($this->request->get['price_from']) && isset($this->request->get['price_to'])){
                    $this->session->data['price_from'] = $this->request->get['price_from'];
                    $this->session->data['price_to'] = $this->request->get['price_to'];
                }
               
                $this->data['budget_from'] = isset($this->session->data['price_from']) ? $this->session->data['price_from'] : 0;
                $this->data['budget_to'] = isset($this->session->data['price_to']) ? $this->session->data['price_to'] : 0;
                       

                $this->data['text_keyword'] = $this->language->get('text_keyword');
                if (isset($this->request->get['keyword'])) {
                $this->data['keyword'] = $this->request->get['keyword'];
                } else {
                $this->data['keyword'] = '';
                }
                if (isset($this->request->get['budget'])) {
                $this->data['cur_budget'] = $this->request->get['budget'];
                } else {
                $this->data['cur_budget'] = '';
                }
                $this->id = 'search';

                if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/search.tpl')) {
                        $this->template = $this->config->get('config_template') . '/template/module/search.tpl';
                } else {
                        $this->template = 'default/template/module/search.tpl';
                }

                $this->response->setOutput( $this->render(TRUE), $this->config->get('config_compression'));
	}
        public function set_price() {

            if(isset($this->request->post['price_from']) && isset($this->request->post['price_to'])){
                    $this->session->data['price_from'] = $this->request->post['price_from'];
                    $this->session->data['price_to'] = $this->request->post['price_to'];
            }
        }
}
?>