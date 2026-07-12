<?php

class Support{
	
	public $app, $db, $support_db;
	public $types, $closed;
	
	public $customer_care, $closed_for_customer;
	
	//thread
	public $subject, $date, $type, $reply_to, $opener, $domain, $posts, $thread_closed;
	
	public function __construct(){
		
		global $app;
		$this->db=$app->get_db();
		$this->app=&$app;
		$this->types=['tech'=>'Ass. tecnica','admin'=>'Ass. amministrativa'];
		$this->customer_care=$app->options['customer_care'];
		$this->closed=['<strong class="text-danger">Aperto</strong>','<strong class="text-success">Chiuso</strong>'];
		$this->closed_for_customer=['Aperto','Chiuso'];
		
		$this->support_db=new mysqli($app->config['support_db']['host'],$app->config['support_db']['user'],$app->config['support_db']['pass'],$app->config['support_db']['name']);
		$this->support_db->set_charset("utf8");
		$this->support_db->query("set @@session.time_zone='+00:00'");
		
		$params=func_get_args();
		$params_count=func_num_args();
		if(method_exists($this,$f='__construct'.$params_count)):
			call_user_func_array(array($this,$f),$params);
		endif;
		
	}
	
	public function __construct1($thread_id){
		
		$this->posts=[];
		
		$thread=$this->support_db->query("select * from archy_support where thread_id=".$thread_id." order by id");
		while($post=mysqli_fetch_object($thread)):
			if($post->subject!=''):
				$this->subject=$post->subject;
				$this->date=App::latin_date_time($post->created_at);
				$this->type=$this->types[$post->type];
				$this->type_key=$post->type;
				$this->reply_to=$post->reply_to;
				$this->opener=$post->user;
				$this->domain=$post->domain;
				$this->thread_closed=$post->closed;
			endif;
			
			$tmp=new stdClass();
			$tmp->user=$post->user;
			$tmp->is_customer=$post->is_customer;
			$tmp->text=$post->request;
			$this->posts[]=$tmp;
		endwhile;		
		
	}
	
	public function open_ticket($avoid_redirect=false){
		$domain=isset($_POST['domain'])? $_POST['domain'] : $_SERVER['HTTP_HOST'];
		$request=str_replace("\n","<br>",$_POST['assistance_request']);
		$this->support_db->query("
			insert into archy_support set 
			user='".addslashes($this->app->user_full_name)."',
			domain='".$domain."',
			is_customer=1,
			type='".addslashes($_POST['assistance_type'])."',
			subject='".addslashes($_POST['assistance_subject'])."',
			request='".addslashes($request)."',
			reply_to='".addslashes($_POST['assistance_reply'])."'
		");
		$thread_id=$this->support_db->insert_id;
		$this->support_db->query("update archy_support set thread_id=".$thread_id." where id=".$thread_id);
		
		$message=new Message();
		$params=[
			'recipient_name'=>$this->customer_care[$_POST['assistance_type']]['name'],
			'recipient_mail'=>$this->customer_care[$_POST['assistance_type']]['mail'],
			//'bcc'=>'',
			'sender_name'=>$this->app->user_full_name,
			'sender_mail'=>$_POST['assistance_reply'],
			'subject'=>'[Eteria] Nuovo ticket '.$this->types[$_POST['assistance_type']],
			'message'=>'<p>Utente: <strong>'.$this->app->user_full_name.'</strong><br>Dominio: '.$_SERVER['HTTP_HOST'].'</p><p>Richiesta:</p>'.$request
		];
		$message->email($params);
		$params=[
			'recipient_name'=>$this->app->user_full_name,
			'recipient_mail'=>$_POST['assistance_reply'],
			//'bcc'=>'',
			'sender_name'=>$this->customer_care[$_POST['assistance_type']]['name'],
			'sender_mail'=>$this->customer_care[$_POST['assistance_type']]['mail'],
			'subject'=>'Il suo ticket: '.$_POST['assistance_subject'],
			'message'=>'<h4>Servizio Clienti Eteria</h4><p>Il suo ticket è stato preso in carico, riceverà gli aggiornamenti a questo indirizzo email.</p><hr>'.$request
		];
		$message->email($params);
		if(!$avoid_redirect):
			set_message("success","La richiesta di assistenza è stata inoltrata al reparto ".$this->types[$_POST['assistance_type']].".");
			header('location: '.$_SERVER['HTTP_REFERER']);
		endif;

	}
	
	public function ticket_reply(){
		
		$reply=str_replace("\n","<br>",$_POST['reply']);
		$this->support_db->query("
			insert into archy_support set
			thread_id=".$_POST['thread_id'].",
			user='".addslashes($this->app->user_full_name)."',
			request='".addslashes($reply)."'
		");
		
		$message=new Message();
		$params=[
			'recipient_name'=>$this->opener,
			'recipient_mail'=>$this->reply_to,
			//'bcc'=>'',
			'sender_name'=>$this->customer_care[$this->type_key]['name'],
			'sender_mail'=>$this->customer_care[$this->type_key]['mail'],
			'subject'=>'[Eteria] RE: '.$this->subject,
			'message'=>$reply
		];
		
		$message->email($params);
		
		set_message("success","Risposta inoltrata al cliente.");
		header('location: '.$_SERVER['HTTP_REFERER']);
	
	}
	
	public function user_reply(){
		
		$reply=str_replace("\n","<br>",$_POST['reply']);
		$this->support_db->query("
			insert into archy_support set
			thread_id=".$_POST['thread_id'].",
			user='".addslashes($this->app->user_full_name)."',
			is_customer=1,
			request='".addslashes($reply)."'
		");
		
		$message=new Message();
		$params=[
			'recipient_name'=>$this->customer_care[$this->type_key]['name'],
			'recipient_mail'=>$this->customer_care[$this->type_key]['mail'],
			//'bcc'=>'',
			'sender_name'=>$this->opener,
			'sender_mail'=>$this->reply_to,
			'subject'=>'[Eteria] Risposta del cliente: '.$this->subject,
			'message'=>$reply
		];
		$message->email($params);
		set_message("success","Risposta inoltrata.");
		header('location: '.$_SERVER['HTTP_REFERER']);
	
	}
	
	public function tickets_list(){

		$IDs=array_column(mysqli_fetch_all($this->db->query("select min(id) as id from archy_support group by (thread_id)"),MYSQLI_ASSOC),'id');
		$threads=$this->db->query("select* from archy_support where id in (".join(',',$IDs).")");
		$response='';
		while($thread=mysqli_fetch_object($threads)):
			$thread->created_at=App::latin_date_time($thread->created_at);
			unset($thread->updated_at);
			unset($thread->is_customer);
			$thread->reply_to=App::email($thread->reply_to);
			$thread->type=$this->types[$thread->type];
			if($thread->closed):
				$close_button='
					<a class="btn btn-sm btn-success" data-toggle="tooltip" title="Riapri ticket" href="app.php?do_action=reopen_ticket&thread_id='.$thread->thread_id.'"
						onClick="return confirm(\'Riaprire il ticket?\')"><span class="glyphicon glyphicon-off"></span></a>';
			else:
				$close_button='
					<a class="btn btn-sm btn-danger" data-toggle="tooltip" title="Chiudi ticket" href="app.php?do_action=close_ticket&thread_id='.$thread->thread_id.'"
						onClick="return confirm(\'Il cliente non potra inviare ulteriori risposte.\nContinuare?\')"><span class="glyphicon glyphicon-off"></span></a>';
			endif;
			unset($thread->thread_id);
			$thread->closed=$this->closed[$thread->closed];
			$response.='
				<tr>
					<td>'.join("</td><td>",(array)$thread).'</td>
					<td>
						<a class="btn btn-sm btn-info" data-toggle="tooltip" title="Dettagli" href="app.php?view=customer_care&thread_id='.$thread->id.'"><span class="glyphicon glyphicon-search"></span></a>
						'.$close_button.'
					</td>
				</tr>
			';
		endwhile;
		return $response;
		
	}
	
	public function close($thread_id){
		$this->db->query("update archy_support set closed=1 where thread_id=".$thread_id);
	}
	
	public function reopen($thread_id){
		$this->db->query("update archy_support set closed=0 where thread_id=".$thread_id);
	}
	
	public function user_tickets(){
		
		$threads=$this->support_db->query("select * from archy_support where domain='".$_SERVER['HTTP_HOST']."' group by (thread_id) order by thread_id desc");
		$response='';
		while($thread=mysqli_fetch_object($threads)):
			$thread->created_at=App::latin_date_time($thread->created_at);
			unset($thread->updated_at);
			unset($thread->thread_id);
			unset($thread->is_customer);
			unset($thread->domain);
			unset($thread->request);
			$thread->reply_to=App::email($thread->reply_to);
			$thread->type=$this->types[$thread->type];
			$thread->closed=$this->closed_for_customer[$thread->closed];
			$response.='
				<tr>
					<td>'.join("</td><td>",(array)$thread).'</td>
					<td><a class="btn btn-sm btn-info" data-toggle="tooltip" title="Dettagli" href="app.php?view=tickets&thread_id='.$thread->id.'#reply"><span class="glyphicon glyphicon-search"></span></a></td>
				</tr>
			';
		endwhile;
		return $response;
		
	}
	
}

?>