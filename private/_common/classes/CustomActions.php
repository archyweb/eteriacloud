<?php

class CustomActions extends AppActions{
	
	public $card_prefix;

	function __construct(string $subdomain_root){
		parent::__construct($subdomain_root);
	}
	
	public function ticket_reply(){
		
		$support=new Support($_POST['thread_id']);
		$support->ticket_reply();		
	}

	public function next_card_number($subdomain){

		$settings=$this->get_settings($domain='association_2');
		$this->card_prefix=isset($settings->card_prefix)? $settings->card_prefix : '';
		$this->card_number_length=$settings->card_number_length;
		$this->card_first_number=$settings->card_first_number;

            return $this->default_next_card_number();
	}

	public function default_next_card_number(){

            $q="SELECT SUBSTR(card,".(strlen($this->card_prefix)+1).") as lastnumber FROM archy_memberships WHERE LEFT(card,".strlen($this->card_prefix).") = '".$this->card_prefix."' ORDER BY SUBSTR(card,".(strlen($this->card_prefix)+1).") DESC LIMIT 1";       
            //error_log("@@@@@@@@@@@@@ q = ".$q);
            $lastcard=$this->db->query($q);
            if($lastcard->num_rows==0) {
                $number=$this->card_first_number;
                //error_log("@@@@@@@@@@@@@ 1");
            } else {
                $lastnumber=mysqli_fetch_object($lastcard)->lastnumber;
                //error_log("@@@@@@@@@@@@@ last $lastnumber");
                $number=$lastnumber+1;
                //error_log("@@@@@@@@@@@@@ number $number");
            }
            if($this->card_number_length != ''){
                return $this->card_prefix.str_pad($number,$this->card_number_length,'0',STR_PAD_LEFT);
            }else{
                return $this->card_prefix.$number;
            }
	}
}
