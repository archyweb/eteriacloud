<?php

class Quote{
	
	public 
	$app, $db, $shortcodes, $replaces, $year;
	
	public
	//quote
	$id,
	$firm_id,
	$date,
	$number,
	$quote_number, /* with prefix and number length */ 
	$customer_id,
	$quote_table,
	$quote,
	$taxable_amount,
	$vat,
	$total,
	$status,
	$sent_on,
	$file_path,
	$created_at,
	$updated_at;
	
	public
	$firm, // instance of Firm class 
	$customer; // instance of Registry class 
	
	public
	$folder, $file_name, $file_physical_path, $main_folder;
	
	public function __construct(){
		global $app;
		$this->db=$app->get_db();
		$this->app=&$app;		
		
		$this->shortcodes=[
			'[NumeroPreventivo]',
			'[DataPreventivo]',
			'[ImportoPreventivo]',
			'[TabellaPreventivo]',
			'[CorpoPreventivo]'
		];
		
		$this->main_folder='assets/documents/quotes';
		
		$params=func_get_args();
		$params_count=func_num_args();
		if(method_exists($this,$f='__construct'.$params_count)):
			call_user_func_array(array($this,$f),$params);
		endif;
	}
	
	public function __construct1($id){
		
		$columns=mysqli_fetch_array($this->db->query("select * from archy_quotes where id=".$id));
		foreach($columns as $name=>$value):
			$this->$name=$value;
		endforeach;
		$this->quote_table=unserialize($this->quote_table);
		$this->firm=new Firm($this->firm_id);
		$this->customer=new Registry($this->customer_id);		
		
		$this->year=(new DateTime($this->date))->format('Y');
		$this->quote_number=$this->firm->quote_prefix.str_repeat("0",$this->firm->quote_number_length-strlen($this->number)).$this->number.'-'.$this->year;
		
		$this->folder=$this->app->subdomain_root.'/'.$this->main_folder.'/'.$this->year.'/'.$this->firm_id;
		$this->file_name=$this->quote_number.'_'.App::file_system_string($this->customer->label).'.pdf';
		$this->file_physical_path=$this->folder.'/'.$this->file_name;
		
		$this->replaces=[
			$this->quote_number,
			App::latin_date($this->date),
			App::currency($this->total),
			$this->app->invoice_table($this->quote_table,$this->taxable_amount,$this->vat,$this->total),
			$this->quote
		];
		
	}
	
	public function store(){
		
		require_once $this->app->main_root.'/private/_common/classes/mpdf/vendor/autoload.php';
		
		$pdf=$this->build_pdf();
		
		if(!is_dir($this->folder)):
			mkdir($this->folder, 0775,true);
		endif;
  
		if(is_file($this->file_physical_path)):
			unlink($this->file_physical_path);
		endif;
		
		$footer=$this->firm->quote_footer;
		$footer=str_replace($this->customer->shortcodes,$this->customer->replaces,$footer);
		$footer=str_replace($this->shortcodes,$this->replaces,$footer);
		
		$mpdf = new \mPDF('utf-8', 'A4-P');
		$mpdf->SetHTMLFooter($footer);
		$mpdf->WriteHTML($pdf);
		$mpdf->Output($this->file_physical_path,'F');
		
        $file_path="/".$this->main_folder.'/'.$this->year.'/'.$this->firm_id.'/'.$this->file_name; 
		$this->db->query("update archy_quotes set file_path='".addslashes($file_path)."'");
		
	}
	
	public function build_pdf(){
		
		$template=$this->firm->quote_template; // firm's shortcodes have been replaced in Firm class 
		$template=str_replace($this->customer->shortcodes,$this->customer->replaces,$template);
		$template=str_replace($this->shortcodes,$this->replaces,$template);
		
		$styles='<style type="text/css">';
        $styles.=file_get_contents($this->app->get_subdomain_url() . '/assets/tinymce/templates/content_css.css');
		$styles.='</style>';			
		
		return '<html><head>'.$styles.'</head><body>'.$template.'</body></html>';
		
	}
	
	public function send(){
		
		if($this->customer->email!=''):
		
			$message=new Message();
			
			$mail_subject=$this->firm->quote_mail_subject;
			$mail_subject=str_replace($this->shortcodes,$this->replaces,$mail_subject);
			$mail_subject=str_replace($this->firm->shortcodes,$this->firm->replaces,$mail_subject);
			$mail_subject=str_replace($this->customer->shortcodes,$this->customer->replaces,$mail_subject);
			
			$mail_body=$this->firm->quote_mail_body;
			$mail_body=str_replace($this->shortcodes,$this->replaces,$mail_body);
			$mail_body=str_replace($this->firm->shortcodes,$this->firm->replaces,$mail_body);
			$mail_body=str_replace($this->customer->shortcodes,$this->customer->replaces,$mail_body);
			
			$email=[
				'recipient_name'=>$this->customer->firm_name,
				'recipient_mail'=>$this->customer->email,
				'sender_name'=>$this->firm->firm_name,
				'sender_mail'=>$this->firm->email,
				'subject'=>$mail_subject,
				'message'=>$mail_body,
				'attachments'=>[$this->file_name=>file_get_contents($this->file_physical_path)]
			];
			if($this->firm->quote_mail_bcc!=''):
				$bcc=explode(',',$this->firm->quote_mail_bcc);
				$email['bcc']=array_map('trim',$bcc);
			endif;

			$message->email($email);
			
			$this->db->query("update archy_quotes set sent_on=NOW() where id=".$this->id);
			
			set_message("success","Fattura inviata correttamente.");
			
		else:
			
			set_message("danger","Non è stato possibile inviare il preventivo, al cliente non è associato un indirizzo e-mail.");
			
		endif;
		
		header('location: '.$_SERVER['HTTP_REFERER']);
		
	}
		
}

?>