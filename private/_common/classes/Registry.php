<?php

class Registry {
	
	public
	$app, $db, $shortcodes, $replaces, $billing_address,
	
	//registry
	$id,
	$type,
	$firm_name,
	$vat_country_id,
	$vat_country,
	$vat_id,
	$tax_code,
	$category_id,
	$contact_person,
	$contact_person_is_legal_representative,
	$city_id,
	$zip,
	$address,
	$telephone,
	$fax,
	$mobile,
	$email,
	$certified_email,
	$website,
	$gender,
	$birth_date,
	$birth_place,
    $main_picture,

	$is_customer,
	$customer_category_id,
	$customer_signatory,
	$customer_contact_person,
	$customer_contact_person_is_legal_representative,
	$customer_notes,
	
	$is_supplier,
	$supplier_category_id,
	$supplier_contact_person,
	$supplier_contact_person_is_legal_representative,
	$supplier_notes,
	
	$is_member,
	$member_category_id,
	$member_signatory,
   
	$created_at,
	$updated_at,
		
	$city; // instance of City class
	
	public function __construct(){
		global $app;
		$this->db=$app->get_db();
		$this->app=&$app;
		
		$this->shortcodes=[
			'[IndirizzoFatturazione Intestatario]',
			'[RagioneSociale/Nominativo Intestatario]',
			'[PartitaIVA Intestatario]',
			'[CodiceFiscale Intestatario]',
			'[Indirizzo Intestatario]',
			'[CAP Intestatario]',
			'[Comune Intestatario]',
			'[Provincia/Distretto Intestatario]',
			'[SiglaProvincia/Distretto Intestatario]',
			'[Regione/Stato Intestatario]',
			'[Nazione Intestatario]',
			'[Telefono Intestatario]',
			'[Fax Intestatario]',
			'[Cellulare Intestatario]',
			'[EMail Intestatario]',
			'[PEC Intestatario]',
			'[SitoWeb Intestatario]',
            '[Foto]'
		];
		
		$params=func_get_args();
		$params_count=func_num_args();
		if(method_exists($this,$f='__construct'.$params_count)):
			call_user_func_array(array($this,$f),$params);
		endif;
	}
	
	public function __construct1($id){
		$columns=mysqli_fetch_array($this->db->query("select * from archy_registry where id=".$id));
		foreach($columns as $name=>$value):
			$this->$name=$value;
		endforeach;

		if( $this->vat_country_id > 0 ):
			$this->vat_country=mysqli_fetch_object($this->db->query("select iso2 from archy_countries where id=".$this->vat_country_id))->iso2;
		endif;

		$this->city=new City($this->city_id);
		$this->billing_address=$this->billing_address();
		
		$this->replaces=[
			$this->billing_address,
			$this->label,
			$this->vat_id,
			$this->tax_code,
			$this->address,
			$this->zip,
			$this->city->city,
			$this->city->province_district,
			$this->city->province_district_code,
			$this->city->state_region,
			$this->city->country_en,
			$this->telephone,
			$this->fax,
			$this->mobile,
			$this->email,
			$this->certified_email,
			$this->website,
            $this->main_picture
		];
		
	}
	
	public function billing_address(){
		$address='<strong>'.$this->label.'</strong><br>';
		$address.=$this->address."<br>";
		$brackets=$this->city->province_district_code!=''? $this->city->province_district_code : $this->city->country_en;
		if(@$this->city->city!=''):
			$address.=$this->zip." - ".$this->city->city." (".$brackets.")";
		endif;
		if($this->vat_id!=''):
			$address.="<br>p. iva: ".$this->vat_id;
		endif;
		if($this->tax_code!=''):
			$address.="<br>cod. fisc.: ".strtoupper($this->tax_code);
		endif;
		return $address;
	}

	public function add_sdi_supplier($payload){

		$supplier=$payload->fattura_elettronica_header->cedente_prestatore;
		
		if($supplier->dati_anagrafici->anagrafica->denominazione!=NULL):
			$firm_name=$label=$supplier->dati_anagrafici->anagrafica->denominazione;
			$name='';
			$surname='';
		else:
			$firm_name='';
			$name=$supplier->dati_anagrafici->anagrafica->nome;
			$surname=$supplier->dati_anagrafici->anagrafica->cognome;
			$label=$name.' '.$surname;
		endif;

		$vat_country_id=mysqli_fetch_object($this->db->query("select id from archy_countries where iso2='".$supplier->dati_anagrafici->id_fiscale_iva->id_paese."'"))->id;

		$city=$this->db->query("select id from archy_cities where city='".$supplier->sede->comune."'");
		if($city->num_rows==0):
			$country_id=mysqli_fetch_object($this->db->query("select id from archy_countries where iso2='".$supplier->sede->nazione."'"))->id;	
			if($supplier->sede->provincia!=NULL):
				$province=$supplier->sede->provincia;
				$city_label=$supplier->sede->comune.' ('.$supplier->sede->provincia.')';
			else:
				$province='';
				$city_label=$supplier->sede->comune;
			endif;
			$this->db->query("
				insert into archy_cities set
				city='".addslashes($supplier->sede->comune)."',
				province_district_code='".$province."',
				zip='".$supplier->sede->cap."',
				country_id=".$country_id."
			");
			$city_id=$this->db->insert_id;
		else:
			$city_id=mysqli_fetch_object($city)->id;
		endif;

		$address=$supplier->sede->indirizzo;
		if($supplier->sede->numero_civico):
			$address.=', '.$supplier->sede->numero_civico;
		endif;

		$this->db->query("
			insert into archy_registry set
			type='Fornitore',
			legal_personality='1',
			firm_name='".addslashes($firm_name)."',
			name='".addslashes($name)."',
			surname='".addslashes($surname)."',
			label='".addslashes($label)."',
			vat_country_id='".$vat_country_id."',
			vat_id='".$supplier->dati_anagrafici->id_fiscale_iva->id_codice."',
			tax_code='".$supplier->dati_anagrafici->codice_fiscale."',
			city_id='".$city_id."',
			zip='".$supplier->sede->cap."',
			address='".addslashes($address)."',
			is_supplier=1
		");

		return $this->db->insert_id;

	}
	
}

?>