<?php
class Budget{
	
	public $db, $app, $notices, $notifications, $settings;
	
	public function __construct(){
		global $app;
		$this->db=$app->get_db();
		$this->app=&$app;
		
		$this->settings=$this->app->get_settings($domain='association');
		
//		$this->notices=[
//			'budget'=>[
//				//'sms'=>@$this->settings->budget_SMS,
//				'date'=>$this->settings->preventive_document_deadline, /* dd/mm */
//				'days_before'=>$this->settings->budget_days,
//				'recipients'=>explode(',',$this->settings->budget_recipients),
//				'mail_body'=>$this->settings->budget_mail,
//				'mail_subject'=>$this->settings->budget_subject,
//				'label'=>'Bilancio Preventivo'
//			],
//			'final_budget'=>[
//				//'sms'=>@$this->settings->final_budget_SMS,
//				'date'=>$this->settings->final_document_deadline, /* dd/mm */
//				'days_before'=>$this->settings->final_budget_days,
//				'recipients'=>explode(',',$this->settings->final_budget_recipients),
//				'mail_body'=>$this->settings->final_budget_mail,
//				'mail_subject'=>$this->settings->final_budget_subject,
//				'label'=>'Bilancio Consuntivo'				
//			]
//		];
		
	}
	
	public function reminder(){
		
		$message=new Message();
		$notice=new Notice();
		
		foreach($this->notices as $key=>$data):
			
			$today=new DateTime();
			
			list($day,$month)=explode('/',$data['date']);
			$send_date=new DateTime(date('Y').'-'.$month.'-'.$day);
			$send_date->modify('-'.$data['days_before'].' days');
			
			if($today->format('Y-m-d')==$send_date->format('Y-m-d')):
				
				if(count($data['recipients']>0)):
				
					$data['recipients']=array_map('trim',$data['recipients']); 
					$main_address=array_shift($data['recipients']);
					$email=[
						'sender_name'=>$this->settings->sender_name,
						'sender_mail'=>$this->settings->sender_email,
						'recipient_mail'=>$main_address,
						'bcc'=>is_array($data['recipients'])? $data['recipients'] : [],
						'subject'=>$data['mail_subject'],
						'message'=>$data['mail_body']
					];
	
					$message->email($email);
										
				endif;
				
				//notification
				$new_notice=[
					'notice'=>'Promemoria: tra '.$data['days_before'].' giorni scadrà il termine per l\'approvazione del '.$data['label'].'.',
					'intent'=>'archy_notices',
					'intent_id'=>'0'
				];
				$notice->set($new_notice);
				
			endif;
		
		endforeach;
		
	}
	
}
?>