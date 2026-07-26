<?php

class Support{
	
	public $app, $db, $support_db;
	public $types, $closed;
	
	public $customer_care, $closed_for_customer;
	
	//thread
	public $subject, $date, $type, $type_key, $reply_to, $opener, $domain, $posts, $thread_closed;
	
	public function __construct(){
		
		global $app;
		$this->db=$app->get_db();
		$this->app=&$app;
		$this->types=['tech'=>'Ass. tecnica','admin'=>'Ass. amministrativa'];
		$this->customer_care=$app->options['customer_care'];
		$this->closed=[
			'<span class="badge bg-success">Aperto</span>',
			'<span class="badge bg-danger">Chiuso</span>'
		];
		$this->closed_for_customer=[
			'<span class="badge bg-success">Aperto</span>',
			'<span class="badge bg-danger">Chiuso</span>'
		];
		
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
	
	private function send_ticket_email($message,$params,$context){
		$sent=$message->email($params);
		if(!$sent):
			$error=$message->last_error!=''? ' Dettaglio: '.htmlspecialchars($message->last_error,ENT_QUOTES,'UTF-8') : '';
			set_message("danger","E-mail non inviata ".$context.".".$error);
		endif;
		return $sent;
	}
	
	private function notify_customer_care_reply($thread_id,$db=NULL){
		$notice=new Notice($db? $db : $this->db);
		$notice->support([
			'notice'=>'Il customer care ha risposto al ticket: '.strip_tags($this->subject),
			'intent'=>'archy_notices',
			'intent_id'=>'0'
		]);
	}

	private function customers_file(){
		$path=isset($this->app->config['core_update_customers_path'])? $this->app->config['core_update_customers_path'] : 'private/_common/update/customers.csv';
		return $this->app->main_root.'/'.ltrim($path,'/');
	}

	private function load_support_customers(){
		$file=$this->customers_file();
		if(!is_file($file)):
			return [];
		endif;

		$handle=fopen($file,'r');
		if(!$handle):
			return [];
		endif;

		$headers=fgetcsv($handle,0,';');
		if($headers && count($headers)==1 && strpos($headers[0],',')!==false):
			$headers=str_getcsv($headers[0],',');
		endif;
		$headers=array_map('trim',$headers ?: []);
		$customers=[];

		while(($row=fgetcsv($handle,0,';'))!==false):
			if(count($row)==1 && strpos($row[0],',')!==false):
				$row=str_getcsv($row[0],',');
			endif;
			if(count(array_filter($row,'strlen'))==0):
				continue;
			endif;
			$row=array_slice(array_pad($row,count($headers),''),0,count($headers));
			$customer=array_combine($headers,$row);
			if(!$customer || !isset($customer['customer']) || trim((string)$customer['customer'])==''):
				continue;
			endif;
			$enabled=isset($customer['enabled'])? strtolower(trim((string)$customer['enabled'])) : '1';
			if(in_array($enabled,['0','false','no','off'],true)):
				continue;
			endif;
			$customers[]=$customer;
		endwhile;

		fclose($handle);
		return $customers;
	}

	private function normalize_ticket_domain($domain){
		$domain=strtolower(trim((string)$domain));
		$domain=preg_replace('#^https?://#','',$domain);
		$domain=explode('/',$domain)[0];
		$domain=explode(':',$domain)[0];
		return preg_replace('/^www\./','',$domain);
	}

	private function customer_matches_domain($customer,$domain){
		$domain=$this->normalize_ticket_domain($domain);
		$customer_name=isset($customer['customer'])? $this->normalize_ticket_domain($customer['customer']) : '';
		if($domain=='' || $customer_name==''):
			return false;
		endif;
		if($domain==$customer_name):
			return true;
		endif;
		$domain_parts=explode('.',$domain);
		if($domain_parts[0]==$customer_name):
			return true;
		endif;
		if(isset($customer['main_root']) && trim((string)$customer['main_root'])!=''):
			$root=str_replace('\\','/',trim((string)$customer['main_root']));
			$root_domain=$this->normalize_ticket_domain(basename(rtrim($root,'/')));
			if($domain==$root_domain || preg_replace('/^www\./','',$domain)==$root_domain):
				return true;
			endif;
		endif;
		return false;
	}

	private function ticket_customer_config(){
		foreach($this->load_support_customers() as $customer):
			if($this->customer_matches_domain($customer,$this->domain)):
				return $customer;
			endif;
		endforeach;
		return NULL;
	}

	private function connect_ticket_customer_db(){
		$customer=$this->ticket_customer_config();
		if(!$customer):
			throw new RuntimeException('Cliente non trovato nel file customers.csv per il dominio '.$this->domain.'.');
		endif;
		foreach(['db_host','db_name','db_user'] as $key):
			if(!isset($customer[$key]) || trim((string)$customer[$key])==''):
				throw new RuntimeException('Configurazione DB cliente incompleta: '.$key.'.');
			endif;
		endforeach;
		$port=isset($customer['db_port']) && $customer['db_port']!==''? (int)$customer['db_port'] : 3306;
		$db=@new mysqli($customer['db_host'],$customer['db_user'],isset($customer['db_pass'])? $customer['db_pass'] : '',$customer['db_name'],$port);
		if($db->connect_errno):
			throw new RuntimeException($db->connect_error);
		endif;
		$db->set_charset('utf8mb4');
		return $db;
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
		$care_email_sent=$this->send_ticket_email($message,$params,'al reparto '.$this->types[$_POST['assistance_type']]);
		$params=[
			'recipient_name'=>$this->app->user_full_name,
			'recipient_mail'=>$_POST['assistance_reply'],
			//'bcc'=>'',
			'sender_name'=>$this->customer_care[$_POST['assistance_type']]['name'],
			'sender_mail'=>$this->customer_care[$_POST['assistance_type']]['mail'],
			'subject'=>'Il suo ticket: '.$_POST['assistance_subject'],
			'message'=>'<h4>Servizio Clienti Eteria</h4><p>Il suo ticket è stato preso in carico, riceverà gli aggiornamenti a questo indirizzo email.</p><hr>'.$request
		];
		$customer_email_sent=$this->send_ticket_email($message,$params,'al cliente');
		if(!$avoid_redirect):
			if($care_email_sent && $customer_email_sent):
				set_message("success","La richiesta di assistenza è stata inoltrata al reparto ".$this->types[$_POST['assistance_type']].".");
			else:
				set_message("danger","La richiesta di assistenza e stata salvata, ma una o piu e-mail di notifica non sono state inviate.");
			endif;
			header('location: '.$_SERVER['HTTP_REFERER']);
		endif;

	}
	
	public function ticket_reply(){
		
		$reply=str_replace("\n","<br>",$_POST['reply']);
		$thread_id=(int)$_POST['thread_id'];
		$this->insert_ticket_reply($this->support_db,$thread_id,$reply,0);
		$customer_db=NULL;
		$customer_sync=true;
		try{
			$customer_db=$this->connect_ticket_customer_db();
			$this->insert_ticket_reply($customer_db,$thread_id,$reply,0);
			$this->notify_customer_care_reply($thread_id,$customer_db);
		}catch(Throwable $e){
			$customer_sync=false;
			set_message("danger","Risposta salvata nel supporto, ma non sincronizzata nel DB cliente. Dettaglio: ".htmlspecialchars($e->getMessage(),ENT_QUOTES,'UTF-8'));
		}
		if($customer_db instanceof mysqli):
			$customer_db->close();
		endif;
		
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
		
		$email_sent=$this->send_ticket_email($message,$params,'al cliente');
		
		if($email_sent && $customer_sync):
			set_message("success","Risposta inoltrata al cliente.");
		elseif(!$email_sent):
			set_message("danger","Risposta salvata, ma e-mail non inviata al cliente.");
		endif;
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
		$email_sent=$this->send_ticket_email($message,$params,'al reparto '.$this->type);
		if($email_sent):
			set_message("success","Risposta inoltrata.");
		else:
			set_message("danger","Risposta salvata, ma e-mail non inviata al reparto ".$this->type.".");
		endif;
		header('location: '.$_SERVER['HTTP_REFERER']);
	
	}
	
	public function tickets_list(){

		$threads=$this->support_db->query("
			select first_posts.*,
				last_posts.created_at as last_activity_at,
				last_posts.is_customer as last_is_customer,
				thread_stats.total_posts
			from (
				select min(id) as min_id, max(id) as max_id, count(*) as total_posts
				from archy_support
				group by thread_id
			) as thread_stats
			inner join archy_support as first_posts on first_posts.id=thread_stats.min_id
			inner join archy_support as last_posts on last_posts.id=thread_stats.max_id
			order by last_posts.created_at desc, first_posts.thread_id desc
		");
		$response='';
		while($thread=mysqli_fetch_object($threads)):
			$thread_id=$thread->thread_id;
			$is_closed=(int)$thread->closed;
			$last_is_customer=(int)$thread->last_is_customer;
			$attention=$is_closed? '<span class="badge bg-danger">Chiuso</span>' : ($last_is_customer? '<span class="badge bg-warning">Da gestire</span>' : '<span class="badge bg-info">Risposto</span>');
			$messages='<span class="badge bg-200 text-700">'.(int)$thread->total_posts.'</span>';
			$created_at_raw=$thread->created_at;
			$last_activity_raw=$thread->last_activity_at;
			$type=isset($this->types[$thread->type])? $this->types[$thread->type] : $thread->type;
			if($thread->closed):
				$close_button='';
			else:
				$close_button='
					<a class="btn btn-sm btn-danger" target="_self" data-toggle="tooltip" title="Chiudi ticket" href="/archy/app.php?do_action=close_ticket&thread_id='.$thread_id.'"
						onClick="return confirm(\'Il cliente non potra inviare ulteriori risposte.\nContinuare?\')"><span class="fas fa-window-close"></span></a>';
			endif;
			$response.='
				<tr>
					<td>'.$thread->id.'</td>
					<td>'.$this->closed[$is_closed].'</td>
					<td>'.$attention.'</td>
					<td>'.$thread->user.'</td>
					<td>'.$thread->domain.'</td>
					<td>'.$type.'</td>
					<td>'.$thread->subject.'</td>
					<td>'.$thread->request.'</td>
					<td data-order="'.(int)$thread->total_posts.'">'.$messages.'</td>
					<td>'.App::email($thread->reply_to).'</td>
					<td data-order="'.$created_at_raw.'">'.App::latin_date_time($created_at_raw).'</td>
					<td data-order="'.$last_activity_raw.'">'.App::latin_date_time($last_activity_raw).'</td>
					<td>
						<a class="btn btn-sm btn-info ticket-frame-link" target="_self" data-toggle="tooltip" title="Dettagli" href="/archy/app.php?view=customer_care&thread_id='.$thread_id.'" onClick="window.location.href=this.href; return false;"><span class="fas fa-search"></span></a>
						'.$close_button.'
					</td>
				</tr>
			';
		endwhile;
		return $response;
		
	}
	
	public function close($thread_id){
		$thread_id=(int)$thread_id;
		$customer_sync=true;
		$thread=$this->support_db->query("select domain from archy_support where thread_id=".$thread_id." and domain<>'' order by id limit 1");
		if($thread && $thread->num_rows>0):
			$this->domain=mysqli_fetch_object($thread)->domain;
		endif;
		$this->support_db->query("update archy_support set closed=1 where thread_id=".$thread_id);
		$customer_db=NULL;
		try{
			$customer_db=$this->connect_ticket_customer_db();
			$customer_db->query("update archy_support set closed=1 where thread_id=".$thread_id);
		}catch(Throwable $e){
			$customer_sync=false;
			set_message("danger","Ticket chiuso nel supporto, ma non sincronizzato nel DB cliente. Dettaglio: ".htmlspecialchars($e->getMessage(),ENT_QUOTES,'UTF-8'));
		}
		if($customer_db instanceof mysqli):
			$customer_db->close();
		endif;
		return $customer_sync;
	}
	
	public function reopen($thread_id){
		$thread_id=(int)$thread_id;
		$this->support_db->query("update archy_support set closed=0 where thread_id=".$thread_id);
	}
	
	public function user_tickets(){
		
		$threads=$this->db->query("
			select first_posts.*,
				last_posts.created_at as last_activity_at,
				last_posts.is_customer as last_is_customer,
				thread_stats.total_posts
			from (
				select min(id) as min_id, max(id) as max_id, count(*) as total_posts
				from archy_support
				group by thread_id
			) as thread_stats
			inner join archy_support as first_posts on first_posts.id=thread_stats.min_id
			inner join archy_support as last_posts on last_posts.id=thread_stats.max_id
			order by last_posts.created_at desc, first_posts.thread_id desc
		");
		$response='';
		while($thread=mysqli_fetch_object($threads)):
			$thread_id=$thread->thread_id;
			$is_closed=(int)$thread->closed;
			$last_is_customer=(int)$thread->last_is_customer;
			$attention=$is_closed? '<span class="badge bg-secondary">Chiuso</span>' : ($last_is_customer? '<span class="badge bg-warning text-dark">In attesa</span>' : '<span class="badge bg-success">Nuova risposta</span>');
			$messages='<span class="badge bg-200 text-700">'.(int)$thread->total_posts.'</span>';
			$created_at_raw=$thread->created_at;
			$last_activity_raw=$thread->last_activity_at;
			$type=isset($this->types[$thread->type])? $this->types[$thread->type] : $thread->type;
			$response.='
				<tr>
					<td>'.$thread->id.'</td>
					<td>'.$this->closed_for_customer[$is_closed].'</td>
					<td>'.$attention.'</td>
					<td>'.$thread->user.'</td>
					<td>'.$type.'</td>
					<td>'.$thread->subject.'</td>
					<td data-order="'.(int)$thread->total_posts.'">'.$messages.'</td>
					<td>'.App::email($thread->reply_to).'</td>
					<td data-order="'.$created_at_raw.'">'.App::latin_date_time($created_at_raw).'</td>
					<td data-order="'.$last_activity_raw.'">'.App::latin_date_time($last_activity_raw).'</td>
					<td><a class="btn btn-sm btn-info ticket-frame-link" target="_self" data-toggle="tooltip" title="Dettagli" href="/archy/app.php?view=tickets&thread_id='.$thread_id.'" onClick="window.location.href=this.href; return false;"><span class="fas fa-search"></span></a></td>
				</tr>
			';
		endwhile;
		return $response;
		
	}
	
}

?>
