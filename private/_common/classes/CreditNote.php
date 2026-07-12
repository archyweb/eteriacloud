<?php
class CreditNote{
	
	public 
	$app, $db, $year;
	
	public
	//invoice
	$id,
	$firm_id,
	$number,
	$credit_note_number, /* with prefix and number length */
	$date,
	$customer_id,
	$credit_note,
	$payment_method_id,
	$payment_method, /* joined */
	$due_date,
	$taxable_amount,
	$vat,
	$total,
	$payment_status,
	$notes,
	$sent_on,
	$file_path,
	$created_at,
	$updated_at;
	
	public
	$firm; /* instance of Firm class */
	
	public
	$customer; /* instance of Registry class */
	
	public
	$folder, $file_name, $file_physical_path, $main_folder;
	
	public
	$shortcodes, $replaces;
	
	public function __construct(){
		global $app;
		$this->db=$app->get_db();
		$this->app=&$app;
		
		$this->shortcodes=[
			'[NumeroNota]',
			'[DataNota]',
			'[ImportoNota]',
			'[CorpoNota]',
			'[Note]'
		];
		
		$this->main_folder='assets/documents/credit_notes';
		
		$params=func_get_args();
		$params_count=func_num_args();
		if(method_exists($this,$f='__construct'.$params_count)):
			call_user_func_array(array($this,$f),$params);
		endif;
	}
	
	public function __construct1($id){
		
		$columns=mysqli_fetch_object($this->db->query("select * from archy_credit_notes where id=".$id));
		foreach($columns as $name=>$value):
			$this->$name=$value;
		endforeach;
		$this->credit_note=unserialize($this->credit_note);
		$this->firm=new Firm($this->firm_id);
		$this->customer=new Registry($this->customer_id);
		$this->latin_date=App::latin_date($this->date);
		
		$this->year=(new DateTime($this->date))->format('Y');
		$this->credit_note_number=$this->firm->credit_note_prefix.str_repeat("0",$this->firm->credit_note_number_length-strlen($this->number)).$this->number.'-'.$this->year;
		
		$this->folder=$this->app->subdomain_root.'/'.$this->main_folder.'/'.$this->year.'/'.$this->firm_id;
		$this->file_name=$this->credit_note_number.'_'.App::file_system_string($this->customer->firm_name).'.pdf';
		$this->xml_file_name=$this->id.'.xml';
		$this->json_file_name=$this->id.'.json';
		$this->file_physical_path=$this->folder.'/'.$this->file_name;

		$this->sdi_xml_path=$this->folder.'/'.$this->xml_file_name;
		$this->sdi_json_path=$this->folder.'/'.$this->json_file_name;

		$this->replaces=[
			$this->credit_note_number,
			$this->latin_date,
			App::currency($this->total),
			$this->app->invoice_table($this->credit_note,$this->taxable_amount,$this->vat,$this->total),
			$this->notes
		];
						
	}
	
	public function get_default_next_number(){
		
		$_POST['table']='archy_credit_notes';
		$_POST['firm_id']=$this->app->default_firm_id;
		$_POST['year']=date('Y');
		$_POST['first_number']='first_credit_note_number';
		$_POST['ajax']=false;
		$number=$this->app->get_next_number_by_year_and_firm();
		unset($_POST['table']);
		unset($_POST['firm_id']);
		unset($_POST['year']);
		unset($_POST['first_number']);
		unset($_POST['ajax']);
		return $number;
		
	}
	
	public function store(){
		
		require_once $this->app->main_root.'/private/_common/classes/mpdf/vendor/autoload.php';
		
		$pdf=$this->build_pdf();
		
		if(!is_dir($this->folder)):
			mkdir($this->folder, 0775,true);
		endif;
        
		$customer_id=mysqli_fetch_object($this->db->query("select customer_id from archy_invoices where id=".$this->invoice_id))->customer_id;
        
        // RICARICO CUSTOMER ALTRIMENTI FUNZIONA SOLO IN MODIFICA (quando nasce l'obj, il costruttore non sa ancora quale sarà il customer)
        $this->customer_id=$customer_id;
        $this->customer = new Registry($this->customer_id);
        
        $customer_label=mysqli_fetch_object($this->db->query("select label from archy_registry where id=".$customer_id))->label;
        $this->file_name=$this->credit_note_number.'_'.App::file_system_string($customer_label).'.pdf';
        $this->file_physical_path=$this->folder.'/'.$this->file_name;

		if(is_file($this->file_physical_path)):
			unlink($this->file_physical_path);
		endif;
		
		$footer=$this->firm->credit_note_footer;
		$footer=str_replace($this->customer->shortcodes,$this->customer->replaces,$footer);
		$footer=str_replace($this->shortcodes,$this->replaces,$footer);
		
		$mpdf = new \mPDF('utf-8', 'A4-P');
		$mpdf->SetHTMLFooter($footer);
		$mpdf->WriteHTML($pdf);
		$mpdf->Output($this->file_physical_path,'F');
		
        $file_path="/".$this->main_folder.'/'.$this->year.'/'.$this->firm_id.'/'.$this->file_name; 
		$this->db->query("update archy_credit_notes set customer_id=".$customer_id.", file_path='".addslashes($file_path)."', unpaid=total-paid where id=".$this->id);
        $this->app->sdi_xml($this,$document_type='TD04',$number=$this->credit_note_number,$body_location='credit_note',$file_path=$this->sdi_xml_path);
        $this->app->sdi_json($this,$document_type='TD04',$number=$this->credit_note_number,$body_location='credit_note',$file_path=$this->sdi_json_path);

	}

	public function send_to_sdi(){
		
		$this->db->query("update archy_credit_notes set sdi_sent_on=now() where id=".$this->id);
		$acube=new ACube();
		$response=$acube->send_invoice($this->sdi_xml_path);
		if(isset($response->uuid)):
			$this->db->query("update archy_credit_notes set sdi_uuid='".$response->uuid."' where id=".$this->id);
			set_message("success","La nota di credito ".$this->credit_note_number." è stata inviata al sistema di interscambio.");
		else:
			$this->open_sdi_ticket();
			set_message("danger","Non è stato possibile inviare la nota di credito ".$this->credit_note_number." al sistema di interscambio. &Egrave; stato aperto automaticamente un ticket al riguardo, verrà contattato dall'assistenza tecnica.");
		endif;

		header('location: '.$_SERVER['HTTP_REFERER']);

	}

	public function send_to_sdi_json(){

		$this->db->query("update archy_credit_notes set sdi_sent_on=now() where id=".$this->id);
		$acube=new ACube();
		$response=$acube->send_invoice_json($this->sdi_json_path);
		if(isset($response->uuid)):
			$this->db->query("update archy_credit_notes set sdi_uuid='".$response->uuid."' where id=".$this->id);
			set_message("success","La nota di credito ".$this->credit_note_number." è stata inviata al sistema di interscambio.");
		else:
            error_log("SDI INVOICE ERROR: " . json_encode($response));
			$this->open_sdi_ticket();
			set_message("danger","Non è stato possibile inviare la nota di credito ".$this->credit_note_number." al sistema di interscambio. &Egrave; stato aperto automaticamente un ticket al riguardo, verrà contattato dall'assistenza tecnica.");
		endif;

		header('location: '.$_SERVER['HTTP_REFERER']);

	}

    public function download_sdi_xml() {

        $acube = new ACube();
        $xml = $acube->get_xml($this->sdi_uuid);
        header('Content-Type: application/xml');
        header('Set-Cookie: fileDownload=true; path=/');
        header('Cache-Control: max-age=60, must-revalidate');
        header('Content-Disposition: attachment;filename="' . $this->invoice_number . '.xml"');
        echo $xml;

    }
    
	public function open_sdi_ticket(){

		$settings=$this->app->get_settings($domain='notifications');

		$_POST['assistance_type']='tech';
		$_POST['assistance_reply']=$settings->sender_email;
		$_POST['assistance_subject']='Errore invio nota di credito al Sistema Di Interscambio';
		$_POST['assistance_request']='<p>Errore invio nota di credito '.$this->credit_note_number."</p><code>Path fattura: ".$this->sdi_xml_path."</code>";

		$support=new Support();
		$support->open_ticket();

	}
	
	public function after_delete(){
		
		$this->app->invoice_balance($this->invoice_id);
		
	}
	
	public function before_update(){
		
		if($this->invoice_id!=$_POST['main']['invoice_id']):
			
			$this->stored_previous_invoice_id=$this->app->get_invoice_id_by_credit_note_id($this->id);
			$this->stored_invoice_id=$this->app->get_invoice_id_by_credit_note_id($_POST['main']['invoice_id']);
			
		elseif($this->total!=$_POST['main']['total']):
			
			$this->stored_invoice_id=$this->app->get_invoice_id_by_credit_note_id($this->id);
			
		endif;
		
	}
	
	public function after_update(){
		
		if(isset($this->stored_invoice_id)):
			$this->app->invoice_balance($this->stored_invoice_id);
		endif;
		
		if(isset($this->stored_previous_invoice_id)):
			$this->app->invoice_balance($this->stored_previous_invoice_id);
		endif;
		
	}
	
	/*public function update_invoice($invoice_id,$credit_note_amount){
		
		$credit_notes=$this->db->query("select * from archy_credit_notes where invoice_id=".$invoice_id." order by id desc");
		
		$invoice=new Invoice($invoice_id);
		
		if($credit_note_amount<=$invoice->unpaid):
			
			$invoice_paid=$invoice->paid+$credit_note_amount;
			$this->app->update_document_payments('archy_invoices',$invoice->total,$invoice_paid,$invoice_id);
			$this->app->update_document_payments('archy_credit_notes',$credit_note_amount,$credit_note_amount,$this->id);
			
		else:
			
			$this->app->update_document_payments('archy_invoices',$invoice->total,$invoice->total,$invoice_id);
			$this->app->update_document_payments('archy_credit_notes',$credit_note_amount,$invoice->unpaid,$this->id);
			
		endif;
		
	}*/
	
	public function build_pdf(){
		
		$template=$this->firm->credit_note_template; // firm's shortcodes have been replaced in Firm class 
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
			
			$mail_subject=$this->firm->credit_note_mail_subject;
			$mail_subject=str_replace($this->shortcodes,$this->replaces,$mail_subject);
			$mail_subject=str_replace($this->firm->shortcodes,$this->firm->replaces,$mail_subject);
			$mail_subject=str_replace($this->customer->shortcodes,$this->customer->replaces,$mail_subject);
			
			$mail_body=$this->firm->credit_note_mail_body;
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
			if($this->firm->credit_note_mail_bcc!=''):
				$bcc=explode(',',$this->firm->credit_note_mail_bcc);
				$email['bcc']=array_map('trim',$bcc);
			endif;

			$message->email($email);
			
			$this->db->query("update archy_credit_notes set sent_on=NOW() where id=".$this->id);
			
			set_message("success","Nota di Credito inviata correttamente.");
			
		else:
			
			set_message("danger","Non è stato possibile inviare la Nota di Credito, al cliente non è associato un indirizzo e-mail.");
			
		endif;
		
		header('location: '.$_SERVER['HTTP_REFERER']);
		
	}
		
}
