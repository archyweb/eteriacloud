<?php

class Firm{
	
	public $app, $db, $shortcodes, $replaces;
	//firm
	public
		$id, 
		$firm_name, 
		$tax_rate_id, //default tax rate id
		$vat_id, 
		$tax_id, 
		$address, 
		$zip, 
		$city_id, 
		$phone, 
		$fax, 
		$mobile, 
		$email, 
		$certified_email, 
		$website, 
		$logo, /* logo path */
		$logo_tag, /* logo img tag */
		$invoice_prefix, 
		$invoice_number_length, 
		$first_invoice_number, 
		$invoice_template, 
		$invoice_footer, 
		$invoice_mail_subject,
		$invoice_mail_body,
		$invoice_bcc,
		$first_receipt_number, 
		$receipt_template, 
		$receipt_footer,
		$quote_prefix, 
		$quote_number_length,
		$first_quote_number, 
		$quote_template, 
		$quote_footer, 
		$quote_mail_subject,
		$quote_mail_body,
		$quote_bcc,
		$created_at, 
		$updated_at;
	
	public 
	$city, // instance of City class
	$stable_org_city;
	
	public $billing_address;
	
	public function __construct(){
		global $app;
		$this->db=$app->get_db();
		$this->app=&$app;
		
		$this->shortcodes=[
			'[Logo]',
			'[IndirizzoFatturazione Emittente]',
			'[RagioneSociale Emittente]',
			'[PartitaIVA Emittente]',
			'[CodiceFiscale Emittente]',
			'[Indirizzo Emittente]',
			'[CAP Emittente]',
			'[Comune Emittente]',
			'[Provincia/Distretto Emittente]',
			'[SiglaProvincia/Distretto Emittente]',
			'[Regione/Stato Emittente]',
			'[Nazione Emittente]',
			'[Telefono Emittente]',
			'[Fax Emittente]',
			'[Cellulare Emittente]',
			'[EMail Emittente]',
			'[PEC Emittente]',
			'[SitoWeb Emittente]'
		];
		
		$params=func_get_args();
		$params_count=func_num_args();
		if(method_exists($this,$f='__construct'.$params_count)):
			call_user_func_array(array($this,$f),$params);
		endif;
	}
	
	public function __construct1($id){
		$columns=mysqli_fetch_array($this->db->query("select * from archy_firms where id=".$id));
		foreach($columns as $name=>$value):
			$this->$name=$value;
		endforeach;
		$this->city=new City($this->city_id);
		$this->billing_address=$this->billing_address();
		
		if($this->stable_org_city_id>0):
			$this->stable_org_city=new City($this->stable_org_city_id);
		endif;
		
		if(is_file($this->app->subdomain_root.$this->logo)):
			$image=base64_encode(file_get_contents($this->app->subdomain_root.$this->logo));
			$image_info=getimagesize($this->app->subdomain_root.$this->logo);
			$this->logo_tag='<img src="data:'.$image_info['mime'].';base64,'.$image.'" alt="Logo" style="max-height:100px;width:auto;">';
		else:
			$this->logo_tag='';
		endif;
		
		$this->replaces=[
			$this->logo_tag,
			$this->billing_address,
			$this->firm_name,
			$this->vat_id,
			$this->tax_id,
			$this->address,
			$this->zip,
			$this->city->city,
			$this->city->province_district,
			$this->city->province_district_code,
			$this->city->state_region,
			$this->city->country_en,
			$this->phone,
			$this->fax,
			$this->mobile,
			$this->email,
			$this->certified_email,
			$this->website
		];
		
		$this->quote_template=str_replace($this->shortcodes,$this->replaces,$this->quote_template);
		$this->quote_footer=str_replace($this->shortcodes,$this->replaces,$this->quote_footer);
		
		$this->receipt_template=str_replace($this->shortcodes,$this->replaces,$this->receipt_template);
		$this->receipt_footer=str_replace($this->shortcodes,$this->replaces,$this->receipt_footer);
		
		$this->credit_note_template=str_replace($this->shortcodes,$this->replaces,$this->credit_note_template);
		$this->credit_note_footer=str_replace($this->shortcodes,$this->replaces,$this->credit_note_footer);
		
		$this->invoice_template=str_replace($this->shortcodes,$this->replaces,$this->invoice_template);
		$this->invoice_footer=str_replace($this->shortcodes,$this->replaces,$this->invoice_footer);
		
	}
	
	public function billing_address(){
		
		$address='
			<strong>'.$this->firm_name.'</strong><br>
			'.$this->address.'<br>
			'.$this->zip.' - '.$this->city->city;
			if($this->city->province_district_code!=''):
				$address.=' ('.$this->city->province_district_code.') ';
			endif;
		$address.=$this->city->country_en.'<br>
			p. iva: '.$this->vat_id;
		if($this->tax_id!=''):
			$address.='<br>cod. fisc.: '.$this->tax_id;
		endif;
		return $address;
		
	}
        
        public function getDefaultFirm(){
            $firms = $this->db->query("select * from archy_firms ORDER BY id ASC LIMIT 0,1");
            $firm = mysqli_fetch_object($firms);
            return $firm;
        }
        
}

?>