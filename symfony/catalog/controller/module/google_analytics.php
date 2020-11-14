<?php  
class ControllerModuleGoogleAnalytics extends Controller {
	protected function index() {
		

      	
		$status = $this->config->get('google_analytics_status');
		
                if($status){
                    $this->data['code'] = html_entity_decode($this->config->get('google_analytics_code'));
                }else{
                    $this->data['code'] = '';
                }
                
		$this->id = 'google_analytics';

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/google_analytics.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/module/google_analytics.tpl';
		} else {
			$this->template = 'default/template/module/google_analytics.tpl';
		}
		
		$this->render();
	}
}
?>