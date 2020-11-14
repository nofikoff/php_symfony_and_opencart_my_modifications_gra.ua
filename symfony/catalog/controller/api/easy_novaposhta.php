<?php 
class ControllerApiEasyNovaposhta extends Controller {

    public function cities() {

        $result_arr = [];
        
        $curl = curl_init();
        
        if ($this->request->get['type'] && $this->request->get['term']) {
        
            if ($this->request->get['type'] == 'address') {
            
                $post_data = array(
                    "apiKey" => $this->config->get('easy_novaposhta_api_key'),
                    "modelName" => "Address",
                    "calledMethod" => "searchSettlements",
                    "methodProperties" => 
                    array(
                        "CityName"=>$this->request->get['term'], 
                        "Language"=>"ru", 
                        "Limit"=>5
                    )
                );
            } else {
            
                $post_data = array(
                    "apiKey" => $this->config->get('easy_novaposhta_api_key'),
                    "modelName" => "Address",
                    "calledMethod" => "getCities",
                    "methodProperties" => 
                    array(
                        "FindByString"=>$this->request->get['term'], 
                        "Language"=>"ru", 
                        "Limit"=>5
                    )
                );
            }                       
            
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.novaposhta.ua/v2.0/json/",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($post_data),
                CURLOPT_HTTPHEADER => array('Content-type: application/json; charset=utf-8'),
            ));
            
            $response = curl_exec($curl);                 
            $result = json_decode($response,true);
            
            if (isset($result['success']) && $result['success']) {
                
                if ($this->request->get['type'] == 'address') {
                
                    if (isset($result['data'][0]['TotalCount']) && $result['data'][0]['TotalCount'] > 0 && 
                        isset($result['data'][0]['Addresses']) && !empty($result['data'][0]['Addresses'])) {
            
                        foreach ($result['data'][0]['Addresses'] AS $result_city) {
                            
                               $data['id'] = $result_city['Ref'];
                            $data['value'] = $result_city['Present'];                    
                            array_push($result_arr,$data);
                        }
                    
                    }
                } else {
                
                    if (isset($result['data']) && !empty($result['data'])) {
            
                        foreach ($result['data'] AS $result_city) {
                            
                               $data['id'] = $result_city['Ref'];
                            $data['value'] = $result_city['DescriptionRu'];                    
                            array_push($result_arr,$data);
                        }
                    
                    }
                }
            }                       
            
            curl_close($curl);
        }
        
        /*
        echo '<pre>';
        print_r($result_arr);
        echo '</pre>';
        exit;
        */
        
        echo json_encode($result_arr);
        die();
    }
    
    public function adresses() {
        
        $result_arr = [];
        
        $curl = curl_init();
        
        if ($this->request->get['city_ref'] && $this->request->get['term']) {

            $post_data = array(
                "apiKey" => $this->config->get('easy_novaposhta_api_key'),
                "modelName" => "Address",
                "calledMethod" => "searchSettlementStreets",
                "methodProperties" => 
                array(
                    "StreetName"=>$this->request->get['term'], 
                    "SettlementRef"=>str_replace('_','-',$this->request->get['city_ref']), 
                    "Language"=>"ru", 
                    "Limit"=>5
                )
            );                       
            
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.novaposhta.ua/v2.0/json/",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($post_data),
                CURLOPT_HTTPHEADER => array('Content-type: application/json; charset=utf-8'),
            ));
            
            $response = curl_exec($curl);                 
            $result = json_decode($response,true);
            
            if (isset($result['success']) && $result['success']) {
                
                if (isset($result['data'][0]['TotalCount']) && $result['data'][0]['TotalCount'] > 0 && 
                    isset($result['data'][0]['Addresses']) && !empty($result['data'][0]['Addresses'])) {
        
                    foreach ($result['data'][0]['Addresses'] AS $result_city) {
                        
                           $data['id'] = $result_city['SettlementStreetRef'];
                        $data['value'] = $result_city['Present'];                    
                        array_push($result_arr,$data);
                    }
                
                }
            }                       
            
            curl_close($curl);
        }
        
        echo json_encode($result_arr);
        die();
    }
    
    public function offices() {
        $result_arr = [];
        $curl = curl_init();
        if ($this->request->post['city_ref']) {
            $post_data = array(
                "apiKey" => $this->config->get('easy_novaposhta_api_key'),
                "modelName" => "AddressGeneral",
                "calledMethod" => "getWarehouses",
                "methodProperties" => 
                array(
                    "CityRef"=>str_replace('_','-',$this->request->post['city_ref']), 
                    "Language"=>"ru",
                    //"Limit"=>30
                )
            );

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.novaposhta.ua/v2.0/json/",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($post_data),
                CURLOPT_HTTPHEADER => array('Content-type: application/json; charset=utf-8'),
            ));
            
            $response = curl_exec($curl);            
            $result = json_decode($response,true);
			$sort_arr = [];
            
            if (isset($result['success']) && $result['success']) {
                if (isset($result['data']) && !empty($result['data'])) {
                    foreach ($result['data'] AS $result_city) {
                        $data['id'] = $result_city['DescriptionRu'];
                        $data['value'] = $result_city['DescriptionRu'];
                        array_push($result_arr,$data);
                    }
                }
            }                       
            curl_close($curl);
        }
		array_multisort(array_column($result_arr, 'value'), SORT_NATURAL, $result_arr);
		echo json_encode($result_arr);
        die();
    }

    public function cities2() {
        $result_arr = [];
        $curl = curl_init();
        if ($this->request->post['region_ref']) {
			$arearef=str_replace('_','-',$this->request->post['region_ref']);
//			$arearef="71508131-9b87-11de-822f-000c2965ae0e";
			$cities = $this->db->query("SELECT Ref,DescriptionRu FROM `oc_novaposhta_cities` where `Area`='$arearef'")->rows;
			foreach ($cities AS $result_city) {
				$data['id'] = $result_city['Ref'];
				$data['value'] = $result_city['DescriptionRu'];
				array_push($result_arr,$data);
			}                       
            curl_close($curl);
			echo json_encode($result_arr);
			die();
		}
    }

    public function regions() {
        $result_arr = [];
        $curl = curl_init();
        if ($this->request->post['region_ref']) {
            $post_data = array(
                "apiKey" => $this->config->get('easy_novaposhta_api_key'),
                "modelName" => "Address",
                "calledMethod" => "getAreas",
                "methodProperties" => 
                array(
                    "Language"=>"ru",
                    //"Limit"=>30
                )
            );
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.novaposhta.ua/v2.0/json/",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($post_data),
                CURLOPT_HTTPHEADER => array('Content-type: application/json; charset=utf-8'),
            ));

            $response = curl_exec($curl);            
            $result = json_decode($response,true);
            
            if (isset($result['success']) && $result['success']) {
                if (isset($result['data']) && !empty($result['data'])) {
                    foreach ($result['data'] AS $result_area) {
						if ($result_area['Description'] != "АРК"){
							$data['id'] = $result_area['Ref'];
							$data['value'] = $result_area['Description'];
							array_push($result_arr,$data);
						}
                    }
                }
            }                       
            curl_close($curl);
			echo json_encode($result_arr);
			die();
		}
    }

}
?>
