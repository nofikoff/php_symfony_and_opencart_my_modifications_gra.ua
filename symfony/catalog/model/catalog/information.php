<?php
class ModelCatalogInformation extends Model {
	public function getInformation($information_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "information i 
            LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id)
            LEFT JOIN " . DB_PREFIX . "information_to_store i2s ON (i.information_id = i2s.information_id)
            LEFT JOIN " . DB_PREFIX . "information_category ic ON (i.information_category_id = ic.information_category_id AND ic.status = 1)
            WHERE i.information_id = '" . (int)$information_id . "' AND id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND i.status = '1'");
	
		return $query->row;
	}
	
//	public function getInformations($information_category = null,$limit = null) {
//
//        $sql = "SELECT *, DATE_FORMAT(i.date_added, '%d.%m.%Y') AS date_added FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) LEFT JOIN " . DB_PREFIX . "information_to_store i2s ON (i.information_id = i2s.information_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND i.status = '1' AND i.sort_order <> '-1'";
//
//        if ($information_category)
//            $sql .= " AND i.information_category_id = " . (int)$information_category;
//
//         $sql .= " ORDER BY i.sort_order, LCASE(id.title) ASC ";
//
//        if ($limit)
//            $sql .= " LIMIT " . (int)$limit;
//
//        $query = $this->db->query($sql);
//		
//		return $query->rows;
//	}
    
    public function getInformations($information_category = null, $start = null, $limit = null, $letter = null) {

            $sql = "SELECT *, DATE_FORMAT(i.date_added, '%d.%m.%Y') AS date_added, 'information/information&information_id' AS href 
                FROM " . DB_PREFIX . "information i 
                LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) 
                LEFT JOIN " . DB_PREFIX . "information_to_store i2s ON (i.information_id = i2s.information_id) 
                WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "' 
                    AND i2s.store_id = '" . (int)$this->config->get('config_store_id') . "' 
                    AND i.status = '1' 
                    AND i.sort_order <> '-1'";
            if($letter){
            $sql .= "AND id.title LIKE '" . $letter . "%'";
            }
             if ($information_category)
            $sql .= " AND i.information_category_id = " . (int)$information_category;

            $sql .= " ORDER BY i.sort_order, LCASE(id.title) ASC ";

            if ($limit && $start){
                $sql .= " LIMIT " . (int)$start . ', ' . (int)$limit;
            }elseif ($start) {
                $sql .= " LIMIT " . (int)$start ;
            }elseif ($limit) {
                $sql .= " LIMIT " . (int)$limit ;
            }

            $query = $this->db->query($sql);
           
            return $query->rows;
	}
    
//Получение списка всех информационных категорий
	public function getInformationCategories() {

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information_category WHERE status = '1'");

		return $query->rows;
	}
//Получение списка всех статей для информационной категории
	public function getCategoryInformations($data = array()) {

		$sql = "SELECT *, DATE_FORMAT(i.date_added, '%d.%m.%Y') AS date_added FROM " . DB_PREFIX . "information i LEFT JOIN information_description id ON(i.information_id=id.information_id)  WHERE i.information_category_id = '" . (int)$data['information_category_id'] . "' AND i.status = '1'";


		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

        $query = $this->db->query($sql);

		return $query->rows;
	}

//Получение сведений о информационной категории
	public function getInformationCategory($information_category_id) {

		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "information_category WHERE information_category_id = " . (int)$information_category_id);

		return $query->row;
	}

    public function getTotalInformationsByCategoryId($information_category_id) {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "information WHERE information_category_id = '" . (int) $information_category_id . "' AND status = '1'");

        return $query->row['total'];
    }
    
    public function getTotalInformations($letter = false) {

        $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "information i 
            LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) 
            LEFT JOIN " . DB_PREFIX . "information_to_store i2s ON (i.information_id = i2s.information_id) 
            WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "' 
            AND i2s.store_id = '" . (int)$this->config->get('config_store_id') . "' 
            AND i.status = '1' 
            AND i.sort_order <> '-1'";
        if($letter){
           $sql .= "AND id.title LIKE '" . $letter . "%'";
        } 

        $query = $this->db->query($sql);
        
		return $query->row['total'];
	}
}
?>