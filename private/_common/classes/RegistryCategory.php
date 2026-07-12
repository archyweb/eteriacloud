<?php

class RegistryCategory{
	
	public 
	$db,
	
	$id, $domain, $category, $created_at, $updated_at;
	
	public function __construct(){
		global $app;
		$this->db=$app->get_db();
		
		$params=func_get_args();
		$params_count=func_num_args();
		if(method_exists($this,$f='__construct'.$params_count)):
			call_user_func_array(array($this,$f),$params);
		endif;
	}
	
	public function __construct1($id){
		$columns=mysqli_fetch_array($this->db->query("select * from archy_registry_categories where id=".$id));
		foreach($columns as $name=>$value):
			$this->$name=$value;
		endforeach;
	}
	
}

?>