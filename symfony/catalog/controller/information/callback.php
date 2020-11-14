<?php

class ControllerInformationCallback extends Controller {

    private $error = array();

    public function index() {
        $this->load->model('tool/seo_url');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
 
            $text = '';
            $text.= '<b>Имя</b>: ' . strip_tags(html_entity_decode($this->request->post['name'], ENT_QUOTES, 'UTF-8')) . '<br/>';
            $text.= '<b>Телефон</b>: ' . strip_tags(html_entity_decode($this->request->post['phone'], ENT_QUOTES, 'UTF-8')) . '<br/>';
            $text.= '<b>Сообщение</b>: ' . strip_tags(html_entity_decode($this->request->post['enquiry'], ENT_QUOTES, 'UTF-8')) . '<br/>';

            $mail = new Mail();
            $mail->protocol = $this->config->get('config_mail_protocol');
            $mail->parameter = $this->config->get('config_mail_parameter');
            $mail->hostname = $this->config->get('config_smtp_host');
            $mail->username = $this->config->get('config_smtp_username');
            $mail->password = $this->config->get('config_smtp_password');
            $mail->port = $this->config->get('config_smtp_port');
            $mail->timeout = $this->config->get('config_smtp_timeout');
            $mail->setTo($this->config->get('config_email'));
//            $mail->setFrom($this->config->get('config_email'));
            $mail->setFrom($this->config->get('config_smtp_username'));
            $mail->setSender($this->request->post['name']);
            $mail->setSubject('Обратный звонок, ' . $this->request->post['name']);
            $mail->setHtml($text);
            $mail->send();
            
            $this->redirect($this->model_tool_seo_url->rewrite(HTTPS_SERVER . 'index.php?route=information/callback/success'));
        }

        $this->document->breadcrumbs = array();

        $this->document->breadcrumbs[] = array(
            'href' => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => FALSE
        );

        $this->document->breadcrumbs[] = array(
            'href' => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=information/callback'),
            'text' => $this->language->get('heading_title'),
            'separator' => $this->language->get('text_separator')
        );

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['entry_name'] = $this->language->get('entry_name');
        $this->data['entry_address_contact'] = $this->language->get('entry_address_contact');
        $this->data['entry_enquiry'] = $this->language->get('entry_enquiry');
        $this->data['entry_text'] = $this->language->get('entry_text');
        $this->data['phone_standart'] = '+380';

        if (isset($this->error['phone'])) {
            $this->data['error_phone'] = $this->error['phone'];
        } else {
            $this->data['error_phone'] = '';
        }
        
        if (isset($this->error['name'])) {
            $this->data['error_name'] = $this->error['name'];
        } else {
            $this->data['error_name'] = '';
        }

        $this->data['button_enquiry'] = $this->language->get('button_enquiry');

        $this->data['action'] = $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=information/callback');
        


        if (isset($this->request->post['name'])) {
            $this->data['name'] = $this->request->post['name'];
        } else {
            $this->data['name'] = '';
        }

        if (isset($this->request->post['phone'])) {
            $this->data['phone'] = $this->request->post['phone'];
        } else {
            $this->data['phone'] = '+380';
        }
        

        if (isset($this->request->post['enquiry'])) {
            $this->data['enquiry'] = $this->request->post['enquiry'];
        } else {
            $this->data['enquiry'] = '';
        }


        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/information/callback.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/information/callback.tpl';
        } else {
            $this->template = 'default/template/information/callback.tpl';
        }

        $this->children = array(
            'common/column_right',
            'common/footer',
            'common/header'
        );

        $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
    }

    public function success() {

        $this->language->load('information/callback');
        $this->load->model('tool/seo_url');

        $this->document->title = $this->language->get('heading_title');

        $this->document->breadcrumbs = array();

        $this->document->breadcrumbs[] = array(
            'href' => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => FALSE
        );

        $this->document->breadcrumbs[] = array(
            'href' => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=information/callback'),
            'text' => $this->language->get('heading_title'),
            'separator' => $this->language->get('text_separator')
        );

        $this->data['heading_title'] = "Ваш запрос принят";
        $this->data['text_message'] = "Спасибо  за ваш запрос! В ближайшее время наши менеджеры вам перезвонят.";
        $this->data['button_continue'] = $this->language->get('button_continue');
        $this->data['continue'] = $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=common/home');

        $this->data['ga_action'] = '/callback-success';
                
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/success_ajax.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/common/success_ajax.tpl';
        } else {
            $this->template = 'default/template/common/success_ajax.tpl';
        }

        $this->children = array(
            'common/column_right',
            'common/column_left',
            'common/footer',
            'common/header'
        );

        $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
    }

    private function validate() {

        $pattern = '/^\+?3?8?\(?0\d{2}\)?\d{7}$/';
        $phone = strlen(utf8_decode($this->request->post['phone']));
        if ($phone < 7 || $phone > 15 || !preg_match($pattern, $this->request->post['phone'])) {
            $this->error['phone'] = 'Не верный телефон. Пример: +380501111111 или (099)8888888';
        }
        $name = strlen(utf8_decode($this->request->post['name']));
        if ($name < 3 ) {
            $this->error['name'] = 'Введите свое имя!';
        }

        if (!$this->error) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}

?>
