<?php

class ControllerCheckoutOrderSuccess extends Controller
{

    public function index()
    {

		$session_data = $this->session->data;
		
        $this->load->language('checkout/order_success');
        $this->load->model('tool/seo_url');

        $order_id = '';

        if (isset($this->session->data['order_id'])) {
            $order_id = $this->session->data['order_id'];
          //  unset($this->session->data['order_id']);
        }


        // Success page
        if ($order_id) {

			$this->document->title = sprintf($this->language->get('heading_success'), $order_id) . ' - ' . $this->language->get('title');
            $this->data['heading_title'] = sprintf($this->language->get('heading_success'), $order_id);
            //
            $this->data['text_message'] = $this->language->get('text_success');
            //
            //by Novikov 2018
            $this->data['text_message'] .= "<br><br>P.S. Ожидайте звонка оператора на ваш номер <b>" . $this->session->data['phone'] . "</b><br><br>";


   //by Novikov Google code on final page 2019
        $this->data['googlesurveyoptin'] = '
<script src="https://apis.google.com/js/platform.js?onload=renderOptIn" async defer></script>
<script>
  window.renderOptIn = function() {
    window.gapi.load(\'surveyoptin\', function() {
      window.gapi.surveyoptin.render(
        {
          // REQUIRED FIELDS
          "merchant_id": 121414289,
          "order_id": "'.$order_id.'",
          "email": "ruslan.novikov@gmail.com",
          "delivery_country": "UA",
          "estimated_delivery_date": "'.date('Y-m-d').'",

          // OPTIONAL FIELDS
          //"products": [{"gtin":"GTIN1"}, {"gtin":"GTIN2"}]
        });
    });
  }
</script>';







        } else {
            $this->document->title = 'Заказ принят!';
            $this->data['heading_title'] = 'Ваш заказ в обработке. Пожалуйста, ожидайте звонка.';
            $this->data['text_message'] = '';
        }


        $this->data['button_continue'] = $this->language->get('button_continue');
        $this->data['continue'] = HTTP_SERVER;

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/success.tpl'))
            $this->template = $this->config->get('config_template') . '/template/common/success.tpl';
        else
            $this->template = 'default/template/common/success.tpl';

        $this->children = array(
            'common/column_right',
            'common/footer',
            'common/column_left',
            'common/header'
        );

        $this->cart->clear();
        
        $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
    }

}

?>