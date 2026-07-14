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
		$this->__construct2($thread_id,'support');
	}
	
	public function __construct2($thread_id,$source){
		
		$this->posts=[];
		$thread_id=(int)$thread_id;
		$db=$this->source_db($source);
		
		$thread=$db->query("select * from archy_support where thread_id=".$thread_id." order by id");
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
			$tmp->date=App::latin_date_time($post->created_at);
			$this->posts[]=$tmp;
		endwhile;		
		
	}
	
	private function source_db($source){
		return $source=='customer'? $this->db : $this->support_db;
	}
	
	private function insert_ticket_opening($db,$thread_id,$domain,$request){
		$thread_id_sql=$thread_id? "thread_id=".(int)$thread_id."," : "";
		$db->query("
			insert into archy_support set 
			".$thread_id_sql."
			user='".addslashes($this->app->user_full_name)."',
			domain='".addslashes($domain)."',
			is_customer=1,
			type='".addslashes($_POST['assistance_type'])."',
			subject='".addslashes($_POST['assistance_subject'])."',
			request='".addslashes($request)."',
			reply_to='".addslashes($_POST['assistance_reply'])."'
		");
		if(!$thread_id):
			$thread_id=$db->insert_id;
			$db->query("update archy_support set thread_id=".$thread_id." where id=".$thread_id);
		endif;
		return $thread_id;
	}
	
	private function insert_ticket_reply($db,$thread_id,$reply,$is_customer){
		$db->query("
			insert into archy_support set
			thread_id=".(int)$thread_id.",
			user='".addslashes($this->app->user_full_name)."',
			is_customer=".(int)$is_customer.",
			request='".addslashes($reply)."'
		");
	}
	
	public function open_ticket($avoid_redirect=false){
		$domain=isset($_POST['domain'])? $_POST['domain'] : $_SERVER['HTTP_HOST'];
		$request=str_replace("\n","<br>",$_POST['assistance_request']);
		$thread_id=$this->insert_ticket_opening($this->support_db,NULL,$domain,$request);
		$this->insert_ticket_opening($this->db,$thread_id,$domain,$request);
		
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
		$thread_id=(int)$_POST['thread_id'];
		$this->insert_ticket_reply($this->support_db,$thread_id,$reply,0);
		
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
		$thread_id=(int)$_POST['thread_id'];
		$this->insert_ticket_reply($this->db,$thread_id,$reply,1);
		$this->insert_ticket_reply($this->support_db,$thread_id,$reply,1);
		
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

		$threads=$this->support_db->query("
			select *
			from archy_support
			where id in (
				select min_id from (
					select min(id) as min_id
					from archy_support
					group by thread_id
				) as first_posts
			)
			order by thread_id desc
		");
		$response='';
		while($thread=mysqli_fetch_object($threads)):
			$thread_id=$thread->thread_id;
			$thread->created_at=App::latin_date_time($thread->created_at);
			unset($thread->updated_at);
			unset($thread->is_customer);
			$thread->reply_to=App::email($thread->reply_to);
			$thread->type=$this->types[$thread->type];
			if($thread->closed):
				$close_button='
					<a class="btn btn-sm btn-success" data-toggle="tooltip" title="Riapri ticket" href="app.php?do_action=reopen_ticket&thread_id='.$thread_id.'"
						onClick="return confirm(\'Riaprire il ticket?\')"><span class="glyphicon glyphicon-off"></span></a>';
			else:
				$close_button='
					<a class="btn btn-sm btn-danger" data-toggle="tooltip" title="Chiudi ticket" href="app.php?do_action=close_ticket&thread_id='.$thread_id.'"
						onClick="return confirm(\'Il cliente non potra inviare ulteriori risposte.\nContinuare?\')"><span class="fas fa-window-close"></span></a>';
			endif;
			unset($thread->thread_id);
			$thread->closed=$this->closed[$thread->closed];
			$response.='
				<tr>
					<td>'.join("</td><td>",(array)$thread).'</td>
					<td>
						<a class="btn btn-sm btn-info" data-toggle="tooltip" title="Dettagli" href="app.php?view=customer_care&thread_id='.$thread_id.'"><span class="fas fa-search"></span></a>
						'.$close_button.'
					</td>
				</tr>
			';
		endwhile;
		return $response;
		
	}
	
	public function close($thread_id){
		$thread_id=(int)$thread_id;
		$this->support_db->query("update archy_support set closed=1 where thread_id=".$thread_id);
	}
	
	public function reopen($thread_id){
		$thread_id=(int)$thread_id;
		$this->support_db->query("update archy_support set closed=0 where thread_id=".$thread_id);
	}
	
	public function user_tickets(){
		
		$threads=$this->db->query("
			select *
			from archy_support
			where id in (
				select min_id from (
					select min(id) as min_id
					from archy_support
					group by thread_id
				) as first_posts
			)
			order by thread_id desc
		");
		$response='';
		while($thread=mysqli_fetch_object($threads)):
			$thread_id=$thread->thread_id;
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
					<td><a class="btn btn-sm btn-info" data-toggle="tooltip" title="Dettagli" href="app.php?view=tickets&thread_id='.$thread_id.'#reply"><span class="fas fa-search"></span></a></td>
				</tr>
			';
		endwhile;
		return $response;
		
	}
	
}

?>
