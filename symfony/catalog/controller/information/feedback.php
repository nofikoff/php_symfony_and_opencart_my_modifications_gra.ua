<?php 
class ControllerInformationFeedback extends Controller {
	private $error = array(); 
	    
  	public function index() {
      $this->language->load('information/feedback');
      $this->load->model('tool/seo_url');

      $this->document->title = $this->language->get('heading_title');

      
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
        $text = '';    
        $text.= '<b>Имя</b>:' . strip_tags(html_entity_decode($this->request->post['name'], ENT_QUOTES, 'UTF-8')) . '<br/><br/>';
        $text.= '<b>Телефон, e-mail</b>:' . strip_tags(html_entity_decode($this->request->post['address'], ENT_QUOTES, 'UTF-8')) . '<br/><br/>';
        $text.= '<b>Текст:</b>:' . strip_tags(html_entity_decode($this->request->post['enquiry'], ENT_QUOTES, 'UTF-8')) . '<br/><br/>';
            
            $mail = new Mail();
                        $mail->protocol = $this->config->get('config_mail_protocol');
                        $mail->parameter = $this->config->get('config_mail_parameter');
                        $mail->hostname = $this->config->get('config_smtp_host');
                        $mail->username = $this->config->get('config_smtp_username');
                        $mail->password = $this->config->get('config_smtp_password');
                        $mail->port = $this->config->get('config_smtp_port');
                        $mail->timeout = $this->config->get('config_smtp_timeout');				
                        $mail->setTo($this->config->get('config_email'));
                        $mail->setFrom('info@graua.com.ua');
                        $mail->setSender($this->request->post['name']);
                        $mail->setSubject('Отзыв руководителю, '. $this->request->post['name']);
                        $mail->setHtml($text);
                $mail->send();

                        $this->redirect($this->model_tool_seo_url->rewrite(HTTPS_SERVER . 'index.php?route=information/feedback/success'));
        }      
      
        $this->document->breadcrumbs = array();

      	$this->document->breadcrumbs[] = array(
        	'href'      => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=common/home'),
        	'text'      => $this->language->get('text_home'),
        	'separator' => FALSE
      	);

      	$this->document->breadcrumbs[] = array(
        	'href'      => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=information/feedback'),
        	'text'      => $this->language->get('heading_title'),
        	'separator' => $this->language->get('text_separator')
      	);
            
      $this->data['heading_title'] = $this->language->get('heading_title');

      $this->data['entry_name'] = $this->language->get('entry_name');
      $this->data['entry_address_contact'] = $this->language->get('entry_address_contact');
      $this->data['entry_enquiry'] = $this->language->get('entry_enquiry');
      $this->data['entry_text'] = $this->language->get('entry_text');
      
        
       if (isset($this->error['enquiry'])) {
                $this->data['error_enquiry'] = $this->error['enquiry'];
        } else {
                $this->data['error_enquiry'] = '';
        }      
                
    $this->data['button_enquiry'] = $this->language->get('button_enquiry');
    
    $this->data['action'] = $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=information/feedback');
           
    
    if (isset($this->request->post['name'])) {
            $this->data['name'] = $this->request->post['name'];
    } else {
            $this->data['name'] = '';
    }  
    
    if (isset($this->request->post['address'])) {
            $this->data['address'] = $this->request->post['address'];
    } else {
            $this->data['address'] = '';
    }
    
    if (isset($this->request->post['enquiry'])) {
            $this->data['enquiry'] = $this->request->post['enquiry'];
    } else {
            $this->data['enquiry'] = '';
    }    
                
	
    if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/information/feedback.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/information/feedback.tpl';
    } else {
            $this->template = 'default/template/information/feedback.tpl';
    }

    $this->children = array(
            'common/column_right',
            'common/footer',
            'common/header'
    );

    $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));	
    
  	}

  public function success() {
   
$this->language->load('information/feedback');
        $this->load->model('tool/seo_url');

		$this->document->title = $this->language->get('heading_title'); 

      	$this->document->breadcrumbs = array();

      	$this->document->breadcrumbs[] = array(
        	'href'      => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=common/home'),
        	'text'      => $this->language->get('text_home'),
        	'separator' => FALSE
      	);

      	$this->document->breadcrumbs[] = array(
        	'href'      => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=information/feedback'),
        	'text'      => $this->language->get('heading_title'),
        	'separator' => $this->language->get('text_separator')
      	);	
		
    	$this->data['heading_title'] = $this->language->get('heading_title');

    	$this->data['text_message'] = $this->language->get('text_message');

    	$this->data['button_continue'] = $this->language->get('button_continue');

    	$this->data['continue'] = $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=common/home');

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

    	if ((strlen(utf8_decode($this->request->post['enquiry'])) < 10) || (strlen(utf8_decode($this->request->post['enquiry'])) > 1000)) {
      		$this->error['enquiry'] = $this->language->get('error_enquiry');
    	}
		
        if (!$this->error) {
                return TRUE;
        } else {
                return FALSE;
        }     
    
  }
}

?>
