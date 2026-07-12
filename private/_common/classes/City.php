<?php

class City{
	
	public $app, $db;
	//city
	public
		$id, 
		$city, 
		$province_district, 
		$province_district_code, 
		$state_region, 
		$zip, 
		$country_id, 
		$city_label, 
		$created_at, 
		$updated_at;
	//country
	public
		$codice_istat,
		$country_it,
		$country_en,
		$nationality,
		$in_ue,
		$iso,
		$iso2,
		$iso3,
		$country_created_at,
		$country_updated_at;
	
	public function __construct(){
		global $app;
		$this->db=$app->get_db();
		$this->app=&$app;
		
		$params=func_get_args();
		$params_count=func_num_args();
		if(method_exists($this,$f='__construct'.$params_count)):
			call_user_func_array(array($this,$f),$params);
		endif;
	}
	
	public function __construct1($id){
		$columns=mysqli_fetch_array($this->db->query("
			select 
				city.*,
				country.codice_istat,
				country.country_it,
				country.country_en,
				country.nationality,
				country.in_ue,
				country.iso,
				country.iso2,
				country.iso3,
				country.created_at as country_created_at,
				country.updated_at as country_updated_at
			from archy_cities as city 
			left join archy_countries as country on (city.country_id=country.id) 
			where city.id=".$id."
		"));
		foreach($columns as $name=>$value):
			$this->$name=$value;
		endforeach;
	}
	
}

?>