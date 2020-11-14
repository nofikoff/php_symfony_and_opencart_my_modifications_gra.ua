<?php
class ModelCatalogBrand extends Model {

	public function getBrand($brand_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "param_value pv LEFT JOIN " . DB_PREFIX . "param_value_to_image pv2i ON (pv.param_value_id = pv2i.param_value_id) WHERE pv.param_value_id= '" . $this->config->get('config_brand_param_id') . "_" . $this->db->escape($brand_id) ."'");

		return $query->row;
	}

	public function getAllBrands() {	
		$query = $this->db->query("SELECT pv.param_value_id, pv.alias, pv.value, pv2i.image FROM " . DB_PREFIX . "param_value pv LEFT JOIN param_value_to_image pv2i ON(pv.param_value_id=pv2i.param_value_id) WHERE pv.param_id = '" . $this->config->get('config_brand_param_id') ."' AND pv2i.image != ''");
	
		return $query->rows;
	}
}
?>