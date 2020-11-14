<?php

class ControllerCommonHome extends Controller {

    public function index() {
        $this->language->load('common/home');
        $this->document->title = $this->language->get('title');
        $this->data['heading_title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
        $this->data['seo_text'] = html_entity_decode($this->config->get('config_seo_text_' . $this->config->get('config_language_id')), ENT_QUOTES, 'UTF-8');
        
        //получение списка картинок для слайд-шоу
        $this->load->model('account/download');
        $images = $this->model_account_download->getDownloadImages();
        $this->data['images'] = array();
        foreach ($images as $image) {
            $this->data['images'][] = array(
                'link_href' => $image['link_href'],
                'name' => $image['name'],
                'image' => HTTPS_IMAGE . $image['image']
            );
        }      
        // .слайд-шоу

        $this->load->model('tool/seo_url');
        $this->load->model('catalog/category');
        $this->data['parametr_categories'] = $this->model_catalog_category->getParametrCategories();
        
        $this->data['top_href'] = $this->model_tool_seo_url->rewrite(HTTPS_SERVER . 'index.php?route=product/top');
        
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/home.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/common/home.tpl';
        } else {
            $this->template = 'default/template/common/home.tpl';
        }
        $this->children = array(
            'common/column_right',
            'common/column_left',
            'module/top',
            'common/footer',
            'common/header'
        );
        $this->load->model('checkout/extension');
        $module_data = $this->model_checkout_extension->getExtensionsByPosition('module', 'home');
        $this->data['modules'] = $module_data;
        foreach ($module_data as $result) {
            $this->children[] = 'module/' . $result['code'];
        }
        $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
    }

}

?>
