<?php
class PassiveReceipt{
	
	public $app, $db, $year;
	
	public
	//Receipt
	$id,
	$firm_id,
	$number,
	$receipt_number, /* with prefix and number length */ 
	$date,
	$customer_id,
	$receipt,
	$payment_method_id,
	$due_date,
	$total,
	$payment_status,
	$notes,
	$sent_on,
	$file_path,
	$created_at,
	$updated_at;

	public
	$firm, // instance of Firm class 
	$customer; // instance of Registry class 
	
	public
	$folder, $file_name, $file_physical_path, $main_folder;
	
	public
	$shortcodes, $replaces;
	
	public function __construct(){
		global $app;
		$this->app=&$app;
		$this->db=$app->get_db();

		$this->shortcodes=[
			'[NumeroRicevuta]',
			'[DataRicevuta]',
			'[ImportoRicevuta]',
			'[ScadenzaRicevuta]',
			'[CorpoRicevuta]',
			'[NoteRicevuta]'
		];
		
		$this->main_folder='assets/documents/receipts';
		
		$params=func_get_args();
		$params_count=func_num_args();
		if(method_exists($this,$f='__construct'.$params_count)):
			call_user_func_array(array($this,$f),$params);
		endif;
	}
	
	public function __construct1($id){
		
		$columns=mysqli_fetch_array($this->db->query("select * from archy_passive_receipts where id=".$id));
		foreach($columns as $name=>$value):
			$this->$name=$value;
		endforeach;
		$this->receipt=unserialize($this->receipt);
		$this->firm=new Firm($this->firm_id);
		$this->customer=new Registry($this->customer_id);
		$this->latin_date=App::latin_date($this->date);
		
		$this->year=(new DateTime($this->date))->format('Y');
		$this->receipt_number=$this->firm->passive_receipt_prefix.str_repeat("0",$this->firm->passive_receipt_number_length-strlen($this->number)).$this->number.'-'.$this->year;
		
		$this->folder=$this->app->subdomain_root.'/'.$this->main_folder.'/'.$this->year.'/'.$this->firm_id;
		$this->file_name=$this->receipt_number.'_'.App::file_system_string($this->customer->firm_name).'.pdf';
		$this->file_physical_path=$this->folder.'/'.$this->file_name;
		
		$this->replaces=[
			$this->receipt_number,
			$this->latin_date,
			App::currency($this->total),
			App::latin_date($this->due_date),
			$this->app->receipt_table($this->receipt,$this->total),
			$this->notes
		];
		
	}
	
	public function add($receipt_row){
		
		$this->db->query("insert into archy_passive_receipts (".join(',',array_keys($receipt_row)).") values (".join(',',array_values($receipt_row)).")");
		$new_receipt=new PassiveReceipt($this->db->insert_id);
		$new_receipt->store();
		return $new_receipt;
		
	}
	
	public function store(){
		
		require_once $this->app->main_root.'/private/_common/classes/mpdf/vendor/autoload.php';
		
		$data=mysqli_fetch_object($this->db->query("select receipt,total,paid from archy_passive_receipts where id=".$this->id));
		$rows=unserialize($data->receipt);
		foreach($rows['price'] as $index=>$amount):
			$rows['price'][$index]=-abs($amount);
		endforeach;
		foreach($rows['amount'] as $index=>$amount):
			$rows['amount'][$index]=-abs($amount);
		endforeach;
		$total=-abs($data->total);
		
		$unpaid=abs($total)-$data->paid;
		
		$this->db->query("update archy_passive_receipts set receipt='".addslashes(serialize($rows))."', total=".$total.", unpaid=".$unpaid." where id=".$this->id);
		
		$pdf=$this->build_pdf();
		
		if(!is_dir($this->folder)):
			mkdir($this->folder, 0775,true);
		endif;
		
		if(is_file($this->file_physical_path)):
			unlink($this->file_physical_path);
		endif;
		
		$footer=$this->firm->receipt_footer;
		$footer=str_replace($this->customer->shortcodes,$this->customer->replaces,$footer);
		$footer=str_replace($this->shortcodes,$this->replaces,$footer);
		
		$mpdf = new \mPDF('utf-8', 'A4-P');
		$mpdf->SetHTMLFooter($footer);
		$mpdf->WriteHTML($pdf);
		$mpdf->Output($this->file_physical_path,'F');
		
		$file_path='/'.$this->main_folder.'/'.$this->year.'/'.$this->firm_id.'/'.$this->file_name;
		
		
		$this->db->query("update archy_passive_receipts set file_path='".addslashes($file_path)."' where id=".$this->id);
		
	}
	
	public function build_pdf(){
		
		$template=$this->firm->receipt_template; // firm's shortcodes have been replaced in Firm class 
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
			
			$mail_subject=$this->firm->receipt_mail_subject;
			$mail_subject=str_replace($this->shortcodes,$this->replaces,$mail_subject);
			$mail_subject=str_replace($this->firm->shortcodes,$this->firm->replaces,$mail_subject);
			$mail_subject=str_replace($this->customer->shortcodes,$this->customer->replaces,$mail_subject);
			
			$mail_body=$this->firm->receipt_mail_body;
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
			if($this->firm->receipt_mail_bcc!=''):
				$bcc=explode(',',$this->firm->receipt_mail_bcc);
				$email['bcc']=array_map('trim',$bcc);
			endif;

			$message->email($email);
			
			$this->db->query("update archy_tax_receipts set sent_on=NOW() where id=".$this->id);
			
			set_message("success","Ricevuta inviata correttamente.");
			
		else:
			
			set_message("danger","Non è stato possibile inviare la ricevuta, al cliente non è associato un indirizzo e-mail.");
			
		endif;
		
		header('location: '.$_SERVER['HTTP_REFERER']);
		
	}
	
	public function duplicate(){
		
		$_POST['year']=date('Y');
		$_POST['firm_id']=$this->firm_id;
		$_POST['table']='archy_tax_receipts';
		$_POST['first_number']='first_receipt_number';
		$number=$this->app->get_next_number_by_year_and_firm();
		
		$this->db->query("
			insert into archy_passive_receipts set
			firm_id=".$this->firm_id.",
			number=".$number.",
			date='".date('Y-m-d')."',
			customer_id=".$this->customer_id.",
			receipt='".addslashes(serialize($this->receipt))."',
            payment_method_id=".(empty($this->payment_method_id)?'NULL':$this->payment_method_id).",
			total=".$this->total.",
			payment_status='not_paid',
			notes='".addslashes($this->notes)."'
		");
		
		$new_id=$this->db->insert_id;
		
		$receipt=new Receipt($new_id);
		$receipt->store();
		
		header('location: /archy/app.php?intent=archy_passive_receipts&view=edit&id='.$new_id);
		
	}

}
?>