<?php

class Api {
	
    public $app, $db, $config;
    
    public $main_root;
    
    public $endpoints, $endpointName, $requestData, $BASE_URI, $tokens;
      
    private static $cf_images_hash = '9SuSEzNRJh_MWD3dytly4A';
    
    public function __construct($requestData){

        global $app;

        $this->app=$app;
        $this->requestData = $requestData;
        $this->main_root=dirname(dirname(dirname(dirname(__FILE__))));
        //authorized tokens
        $this->tokens = array(
			"fa3b2c9c-a96d-48a8-82ad-0cb775dd3e5d" => "archiviobcm",
            "6IY8hCefp0EhhJpqWgMRa83PDLKX98CGJ4UK" => "demo"
        );
        $this->config=require $this->main_root.'/subdomains/'.$this->tokens[$requestData["token"]].'/httpdocs/config-local.php';
        $this->db=$this->get_db();
        $this->BASE_URI = "/simple-api/";
        $this->endpoints = array();
        
        
    }
    
    public function get_db(){
            //if($this->db==NULL):
                    $this->db=new mysqli($this->config['db']['host'],$this->config['db']['user'],$this->config['db']['pass'],$this->config['db']['name']);
                    $this->db->set_charset("utf8");
                    $this->db->query("set @@session.time_zone='".$this->config['db']['timezone']."'");
            //endif;
            return $this->db;
    }
    
    public static function getImageUrl(string $image_id, string $variant = 'public'): string
    {
        return "https://imagedelivery.net/".static::$cf_images_hash."/{$image_id}/{$variant}";
    }
	
	public static function is_serialized($string){
		return (@unserialize($string) !== false);
	}
    
    public function checktoken(array $requestData) {

            //you can create secure tokens with this line, but that is a discussion for another post.. 
            //but i am using UUIDv4 instead.
            //$token = str_replace("=", "", base64_encode(random_bytes(160 / 8)));

            

            if (!isset($requestData["token"])) {
                echo json_encode("No token was received to authorize the operation. Verify the information sent");

                exit;
            }

            if (!isset($this->tokens[$requestData["token"]])) {
                echo json_encode("The token  " . $requestData["token"] . 
                " does not exists or is not authorized to perform this operation.");

                exit;
            }
            
            return true;
    }

    
    public function get_endpoint(string $endpoint, array $requestData = [], array $requestQueryString = [] ){
       
            /**
            * prints a default message if the API base path is queried.
            * @param array $requestData contains the parameters sent in the request, for this endpoint they are ignored.
            * @return void
            */
        switch ($endpoint) {
            case '/':
                echo json_encode("Welcome to my API!");
                break;
            case 'catalogue':
                $mio_array = array();
                $addFilter = ' WHERE scheda_pubblica=1 ';
                $total_filters = count($requestQueryString);
                if($total_filters>0){
                    $addFilter .=" AND ";
                    $i=0;
                    $counter=1;
                    
                    foreach($requestQueryString as $key => $value){
                        if($key == 'tecnica'){ // campi serializzati da fare
							$addFilter .= "tecnica LIKE '%\"" . (int)$value . "\"%'";
						}
						elseif(is_numeric($value)){
                            $addFilter .= $key."='".$value."'";
                        }else{
                            $addFilter .= $key." LIKE '%".$value."%'";
                        }
                        if($counter!=$total_filters){ $addFilter .= " AND "; }
                        $i++;
                        $counter++;                        
                    }
                }
                //error_log("SELECT * FROM archy_scheda_catalogo".$addFilter);
                $result = $this->db->query("SELECT * FROM archy_art_catalog".$addFilter." ORDER BY titolo_opera ASC");
                if($result->num_rows>0){
                   while($row = mysqli_fetch_assoc($result)) {
                        $mio_array['data']['records'][] = [
                            'id' => $row['id'],
                            'title' => $row['titolo_opera'],
                            'arch_number' => $row['numero_archiviazione'],
                            'technique' => $row['tecnica'],
                            'author' => $row['nominativo_autore'],
                            'date' => $row['data'],
                            'height' => $row['dimensioni_altezza'],
                            'width' => $row['dimensioni_lunghezza'],
                            'publisher' => $row['editore'],
                            'edition_number' => $row['numero_edizione'],
                            'place_production' => $row['luogo_produzione_opera'],
                            'narrative_description' => $row['descrizione_narrativa'],
                            'bibliographic_description' => $row['descrizione_fonte_bibliografica'],
                            'publisher' => $row['editore'],
                            'language' => $row['lingua'],
                            'total_pages' => $row['numero_pagine'],
                            'main_image' => $this->getImageUrl($row['foto_ufficiale'],'public')
                        ];
                   }
                   $mio_array['data']['total'] = $result->num_rows;
                   $mio_array['data']['status'] = 'OK';
                }else{
                    $mio_array['data']['status'] = 'Error';
                    $mio_array['data']['code'] = '401';
                }
                echo json_encode($mio_array);
                break;
            case 'catalogue_detail':
                $mio_array = array();
                foreach($requestQueryString as $key => $value){
                    $filter_id = $value;
                }
                if ($result = $this->db->query("SELECT * FROM archy_art_catalog WHERE id=".$filter_id)) {

                   $row = mysqli_fetch_assoc($result);
				   
				   $tecniche = [];

					if(!empty($row['tecnica']) && $this->is_serialized($row['tecnica'])){

						$ids = unserialize($row['tecnica']);

						$ids = array_map('intval', $ids);

						if(count($ids) > 0){

							$sql = "SELECT tag
									FROM archy_tag
									WHERE id IN (".implode(',', $ids).")";

							$resTecniche = $this->db->query($sql);

							while($r = mysqli_fetch_assoc($resTecniche)){
								$tecniche[] = $r['tag'];
							}
						}
					}
				   
                        $mio_array['data']['records'][] = [
                            'id' => $row['id'],
                            'title' => $row['titolo_opera'],
                            'arch_number' => $row['numero_archiviazione'],
                            'technique' => implode(', ', $tecniche),
                            'author' => $row['nominativo_autore'],
                            'date' => $row['data'],
                            'height' => $row['dimensioni_altezza'],
                            'width' => $row['dimensioni_lunghezza'],
                            'publisher' => $row['editore'],
                            'edition_number' => $row['numero_edizione'],
                            'place_production' => $row['luogo_produzione_opera'],
                            'narrative_description' => $row['descrizione_narrativa'],
                            'bibliographic_description' => $row['descrizione_fonte_bibliografica'],
                            'publisher' => $row['editore'],
                            'language' => $row['lingua'],
                            'total_pages' => $row['numero_pagine'],
                            'main_image' => $this->getImageUrl($row['foto_ufficiale'],'public')
                        ];                   
                   $mio_array['data']['total'] = $result->num_rows;
                }else{
                    $mio_array['data']['status'] = 'Error';
                    $mio_array['data']['code'] = '401';
                }
                echo json_encode($mio_array);
                break;
            case 'filters':
                $mio_array = array();
                $where_param = 'scheda_pubblica=1';
                foreach($requestQueryString as $key => $value){
                    if($key=='filter_name'){
                        $filter_name = $value;
                    }					
                    if($key!='filter_name'){
						if($key=='format'){
							
						}else{
							$where_param .= ' AND '.$key.'="'.$value.'"';
						}
                    }
					
                }
                
                if ($result = $this->db->query("SELECT ".$filter_name." FROM archy_art_catalog WHERE ".$where_param." GROUP BY ".$filter_name." ORDER BY ".$filter_name." ASC")) {
                   $i=1;
                   while($row = mysqli_fetch_assoc($result)) {
					   
					    if($this->is_serialized($row[$filter_name])){
							$tags = unserialize($row[$filter_name]);
							$result_tag = $this->db->query("SELECT id,tag FROM archy_tag WHERE categoria like '%".$filter_name."%' AND id='".$tags[0]."'");
							$row_tag = mysqli_fetch_assoc($result_tag);
							$mio_array['data']['records'][] = [
								'id' => $i,
								'title' => $row_tag['tag'],
								'id_row' => $row_tag['id']
							];
						}else{
							$mio_array['data']['records'][] = [
								'id' => $i,
								'title' => $row[$filter_name],
								'id_row' => ''
							];
						}
					   
                        
                        $i++;
                   }
                   
                   $mio_array['data']['total'] = $result->num_rows;
                   $mio_array['data']['status'] = 'OK';
                }else{
                    $mio_array['data']['status'] = 'Error';
                    $mio_array['data']['code'] = '401';
                }
                echo json_encode($mio_array);
                break;
            case '404':
                echo json_encode("Endpoint " . $requestData["endpointName"] . " not found.");
                break;
            default:
                //TODO: implement here any other type of request method that may arise.
                break;
        }   
        
           
    }

}