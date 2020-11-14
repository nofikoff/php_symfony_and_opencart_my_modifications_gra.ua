<?php

class ControllerInformationSitemap extends Controller
{

    public function index()
    {
        $this->language->load('information/sitemap');
        $this->load->model('tool/seo_url');
        $this->load->model('catalog/category');
        $this->load->model('catalog/information');
        $this->load->model('catalog/product');
        $this->load->model('catalog/manufacturer');

        $this->document->title = $this->language->get('heading_title');

        $this->document->breadcrumbs = array();

        $this->document->breadcrumbs[] = array(
            'href' => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => FALSE
        );

        $this->document->breadcrumbs[] = array(
            'href' => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=information/sitemap'),
            'text' => $this->language->get('heading_title'),
            'separator' => $this->language->get('text_separator')
        );



        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
            $this->data['page'] = $this->request->get['page'];
        } else {
            $page = 1;
            $this->data['page'] = 1;
        }


        $this->data['heading_title'] = $this->language->get('heading_title');
        $this->data['text_information'] = $this->language->get('text_information');


        $this->data['rus'] = array('А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я');
        $this->data['eng'] = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');

        $this->data['letter_href'] = $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=information/sitemap');

        $letter = isset($this->request->get['letter']) ? $this->request->get['letter'] : false;
        //количество записей
        $item_on_page = 150;
        $info_total = $this->model_catalog_information->getTotalInformations($letter);

        $prod_total = $this->model_catalog_product->getTotalAllProducts($letter);

        $param_total = $this->model_catalog_product->getTotalAllParams($letter);

        $this->category_count = 0;
        //Получаем категории
        if ($page == 1)
            $this->data['category'] = $this->getCategories(0);
        else {
            $this->getCategories(0);
            $this->data['category'] = array();
        }

        $this->data['letter'] = $letter;
        //всего
        $total = $info_total + $prod_total + $param_total + $this->category_count;
        //кол-во страниц

        $pages = ceil($total / $item_on_page);

//        if (!$letter) {
//            //что выводить на каждой странице
//            $on_page = array();
//
//            for ($index = 1; $index <= $pages; $index++) {
//
//                if ($index == 1) {
//                    $on_page[1]['info_page']['end'] = $info_total;
//                    $on_page[1]['param_page']['end'] = $item_on_page - $this->category_count + $info_total;
//                    $on_page[1]['prod_page']['end'] = $item_on_page - $this->category_count + $info_total + $param_total;
//
//                    $on_page[1]['info_page']['start'] = 0;
//                    $on_page[1]['param_page']['start'] = 0;
//                    $on_page[1]['prod_page']['start'] = 0;
//                } else {
//                    $on_page[$index]['prod_page']['start'] = $on_page[$index - 1]['prod_page']['start'] + $item_on_page;
//                    $on_page[$index]['prod_page']['end'] = $item_on_page;
//                }
//            }
//        } else {
//            $on_page[1]['info_page']['end'] = $info_total;
//            $on_page[1]['param_page']['end'] = $param_total;
//            $on_page[1]['prod_page']['end'] = $prod_total;
//
//            $on_page[1]['info_page']['start'] = 0;
//            $on_page[1]['param_page']['start'] = 0;
//            $on_page[1]['prod_page']['start'] = 0;
//            $total = 0;
//        }

        $information = array(0 => array("label" => $this->language->get('text_information')));
        $collection = array(0 => array("label" => $this->language->get('text_collection')));
        $product = array(0 => array("label" => $this->language->get('text_product')));
        $getInformations = $this->model_catalog_information->getInformations(null, null, null, $letter);
        $getAllParams = $this->model_catalog_product->getAllParams(null, null, $letter);
        $getAllProducts = $this->model_catalog_product->getAllProducts(null, null, $letter);
        
        $total_info = array_merge($information, $getInformations, $collection, $getAllParams, $product, $getAllProducts);
    
        
        $i = 0;
        $counter = ($page == 1 ? $this->category_count : 0);
        $this->data['infos'] = array();
        $item_on_my_page = ($page == $pages ? $item_on_page + $this->category_count : $item_on_page);
        while ($counter < $item_on_my_page) {
            $difference = ($page == 1 ? 0 : $this->category_count);
            $difference = ($item_on_my_page > $this->category_count ? $difference : $item_on_page);
            $counter++;
            $index = $i + $page * $item_on_page - $item_on_page - $difference;

            $this->data['infos'][] = array(
                'label' => (isset($total_info[$index]['label']) ?
                    "</ul><ul>" . $total_info[$index]['label'] : ''),
                'title' => (isset($total_info[$index]['title']) ?
                    $total_info[$index]['title'] :
                    (isset($total_info[$index]['name']) ?
                        $total_info[$index]['name'] : "")),
                'href' => (isset($total_info[$index]['href']) ? ($this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=' . $total_info[$index]['href'] . '=' .
                    (isset($total_info[$index]['information_id']) ?
                        $total_info[$index]['information_id'] :
                        (isset($total_info[$index]['product_id']) ?
                            $total_info[$index]['product_id'] :
                            (isset($total_info[$index]['param_value_id']) ?
                                $total_info[$index]['param_value_id'] : ""))))) : '')
            );
            $i++;
        }

        //Получаем информационные статьи
//        $this->data['informations'] = array();
//        if (isset($on_page[$page]['info_page']))
//            foreach ($this->model_catalog_information->getInformations($on_page[$page]['info_page']['start'], $on_page[$page]['info_page']['end'], $letter) as $result) {
//                $this->data['informations'][] = array(
//                    'title' => $result['title'],
//                    'href' => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=information/information&information_id=' . $result['information_id'])
//                );
//            }
//
//        //Получаем товары
//        $this->data['products'] = array();
//        if (isset($on_page[$page]['prod_page']))
//            foreach ($this->model_catalog_product->getAllProducts($on_page[$page]['prod_page']['start'], $on_page[$page]['prod_page']['end'], $letter) as $result) {
//                $this->data['products'][] = array(
//                    'title' => $result['name'],
//                    'href' => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/product&product_id=' . $result['product_id'])
//                );
//            }
//
//        //Получаем коллекции
//        $this->data['params'] = array();
//        if (isset($on_page[$page]['param_page']))
//            foreach ($this->model_catalog_product->getAllParams($on_page[$page]['param_page']['start'], $on_page[$page]['param_page']['end'], $letter) as $result) {
//                $this->data['params'][] = array(
//                    'title' => $result['name'],
//                    'href' => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/group&pv=' . $result['param_value_id'])
//                );
//            }


        $url_letter = isset($this->request->get['letter']) ? '&letter=' . $this->request->get['letter'] : '';
        $pagination = new Pagination();
        $pagination->total = $total;
        $pagination->page = $page;
        $pagination->num_links = ceil($total / $item_on_page);
        $pagination->limit = $item_on_page;
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=information/sitemap&page={page}' . $url_letter);
        $this->data['pagination'] = $pagination->render();

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/information/sitemap.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/information/sitemap.tpl';
        } else {
            $this->template = 'default/template/information/sitemap.tpl';
        }

        $this->children = array(
            'common/column_right',
            'common/footer',
            'common/column_left',
            'common/header'
        );

        $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
    }

    protected function getCategories($parent_id, $current_path = '')
    {
        $output = '';

        $results = $this->model_catalog_category->getCategories($parent_id);
        if ($results) {
            $output .= '<ul>';
        }

        foreach ($results as $result) {
            $this->category_count++;
            if (!$current_path) {
                $new_path = $result['category_id'];
            } else {
                $new_path = $current_path . '_' . $result['category_id'];
            }

            $output .= '<li>';

            $output .= '<a href="' . $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/category&path=' . $new_path) . '">' . $result['name'] . '</a>';

            $output .= $this->getCategories($result['category_id'], $new_path);

            $output .= '</li>';
        }

        if ($results) {
            $output .= '</ul>';
        }

              return $output;

    }

}

?>