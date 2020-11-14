<?php
class ControllerCommonBreadcrumb extends Controller {
	protected function index() {
      	$this->data['breadcrumbs'] = $this->document->breadcrumbs;

		$this->id = 'breadcrumb';
		$this->template = $this->config->get('config_template') . '/template/common/breadcrumb.tpl';
		$this->render();
	}
}
?>