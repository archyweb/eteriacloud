<?php
class AppActions extends ExtraActions{
	
	public function __construct(string $subdomain_root){
	    parent::__construct($subdomain_root);
	}
	
	public function load_extra_intent_form($intent_to_load,$prefix='',$suffix='',$id_suffix='',$scope=NULL,$new_load=false){
            global $app;

            $backup=clone $app;

            $app->data=$app->intent=NULL;
            require $app->main_root.'/private/_common/intents/'.$intent_to_load.'.php';
            $app->init();
            $data=$app->get_form($new_load);

            $app->backup=$backup;

            return $data;
	}
	
	public function restore(){
            global $app;
            $app=$app->backup;
	}
	
	public function send_notice(){
		global $app;
		$category=isset($_POST['category'])? $_POST['category'] : 'general';
		$priority=isset($_POST['priority'])? $_POST['priority'] : 'normal';
		$title=isset($_POST['title'])? trim($_POST['title']) : '';
		$message=isset($_POST['message'])? trim($_POST['message']) : '';
		$recipients_mode=isset($_POST['recipients_mode'])? $_POST['recipients_mode'] : 'local';
		
		if($message==''):
			set_message("danger","Inserire il testo della notifica.");
			header('location: /archy/app.php?view=notice_sender');
			return;
		endif;
		
		$notice_text='';
		if($title!=''):
			$notice_text.='<strong>'.htmlspecialchars($title,ENT_QUOTES,'UTF-8').'</strong><br>';
		endif;
		$notice_text.=nl2br(htmlspecialchars($message,ENT_QUOTES,'UTF-8'));
		
		$notice=new Notice();
		$payload=[
			'notice'=>$notice_text,
			'intent'=>'archy_notices',
			'intent_id'=>'0',
			'source'=>'archy',
			'priority'=>$priority
		];
		
		if($recipients_mode=='local'):
			$this->store_notice_payload($notice,$payload,$category);
			set_message("success","Notifica inviata.");
		else:
			$result=$this->broadcast_notice_payload($payload,$category,$recipients_mode,isset($_POST['customers'])? $_POST['customers'] : []);
			$this->log_notice_broadcast($payload,$category,$priority,$title,$recipients_mode,$result);
			if($result['target']==0):
				set_message("warning","Nessun cliente destinatario selezionato o disponibile.");
			elseif($result['failed']>0 || $result['email_failed']>0):
				set_message("warning","Broadcast completato con errori: ".$result['success']." notifiche inviate, ".$result['failed']." errori DB, ".$result['email_success']." e-mail inviate, ".$result['email_failed']." e-mail non inviate.");
			else:
				set_message("success","Broadcast completato: ".$result['success']." notifiche inviate, ".$result['email_success']." e-mail inviate.");
			endif;
		endif;
		
		header('location: /archy/app.php?view=notice_sender');
	}
	
	private function store_notice_payload($notice,$payload,$category){
		if($category=='support'):
			$notice->support($payload);
		elseif($category=='administrative'):
			$notice->administrative($payload);
		elseif($category=='update'):
			$notice->update($payload);
		else:
			$payload['category']='general';
			$notice->set($payload);
		endif;
	}
	
	private function broadcast_notice_payload($payload,$category,$mode,$selected_customers){
		$customers=$this->load_notice_customers();
		$selected_customers=array_map('strval', is_array($selected_customers)? $selected_customers : []);
		$selected_lookup=array_fill_keys($selected_customers,true);
		$result=['success'=>0,'failed'=>0,'target'=>0,'email_target'=>0,'email_success'=>0,'email_failed'=>0,'customers'=>[],'failed_customers'=>[]];
		
		foreach($customers as $customer):
			if($mode=='selected' && !isset($selected_lookup[$customer['customer']])):
				continue;
			endif;
			$result['target']++;
			$mysqli=NULL;
			try{
				$mysqli=$this->connect_notice_customer($customer);
				$this->ensure_customer_notice_columns($mysqli);
				$this->insert_notice_for_customer($mysqli,$payload,$category);
				$email_result=$this->email_notice_to_customer_users($mysqli,$payload,$category,$customer);
				$result['email_target']+=$email_result['target'];
				$result['email_success']+=$email_result['success'];
				$result['email_failed']+=$email_result['failed'];
				$result['success']++;
				$result['customers'][]=$this->notice_customer_summary($customer,$email_result);
			}catch(Throwable $e){
				$result['failed']++;
				$result['failed_customers'][]=$this->notice_customer_summary($customer,['target'=>0,'success'=>0,'failed'=>0],$e->getMessage());
				error_log('[NoticeBroadcast] customer='.$customer['customer'].' db='.($customer['db_name'] ?? '-').' error='.$e->getMessage());
			}
			if($mysqli instanceof mysqli):
				$mysqli->close();
			endif;
		endforeach;
		
		return $result;
	}

	private function notice_customer_summary($customer,$email_result,$error=''){
		return [
			'customer'=>isset($customer['customer'])? $customer['customer'] : '',
			'subdomain'=>isset($customer['subdomain'])? $customer['subdomain'] : '',
			'db_name'=>isset($customer['db_name'])? $customer['db_name'] : '',
			'email_target'=>isset($email_result['target'])? (int)$email_result['target'] : 0,
			'email_success'=>isset($email_result['success'])? (int)$email_result['success'] : 0,
			'email_failed'=>isset($email_result['failed'])? (int)$email_result['failed'] : 0,
			'error'=>$error
		];
	}

	private function ensure_notice_broadcast_log_table(){
		$db=$this->get_db();
		$sql="
			create table if not exists archy_notice_broadcasts (
				id int unsigned not null auto_increment,
				title varchar(190) not null default '',
				notice mediumtext null,
				category varchar(32) not null default 'general',
				priority varchar(16) not null default 'normal',
				recipients_mode varchar(32) not null default 'all',
				target_count int unsigned not null default 0,
				success_count int unsigned not null default 0,
				failed_count int unsigned not null default 0,
				email_target int unsigned not null default 0,
				email_success int unsigned not null default 0,
				email_failed int unsigned not null default 0,
				customers_json mediumtext null,
				failed_customers_json mediumtext null,
				created_by varchar(190) not null default '',
				created_at timestamp not null default current_timestamp,
				updated_at timestamp null default null on update current_timestamp,
				primary key (id),
				key created_at (created_at),
				key category (category),
				key priority (priority)
			) engine=InnoDB default charset=utf8mb4
		";
		if(!$db->query($sql)):
			throw new RuntimeException($db->error);
		endif;
	}

	private function log_notice_broadcast($payload,$category,$priority,$title,$recipients_mode,$result){
		$this->ensure_notice_broadcast_log_table();
		$db=$this->get_db();
		$notice_text=isset($payload['notice'])? $payload['notice'] : '';
		if(trim((string)$title)==''):
			$parts=$this->notice_mail_parts($payload);
			$title=$parts['title']!=''? $parts['title'] : 'Notifica';
		endif;
		$customers_json=json_encode($result['customers'],JSON_UNESCAPED_UNICODE);
		$failed_customers_json=json_encode($result['failed_customers'],JSON_UNESCAPED_UNICODE);
		$user=isset($this->user_full_name)? $this->user_full_name : '';
		$sql="
			insert into archy_notice_broadcasts set
			title='".$db->real_escape_string($title)."',
			notice='".$db->real_escape_string($notice_text)."',
			category='".$db->real_escape_string($category)."',
			priority='".$db->real_escape_string($priority)."',
			recipients_mode='".$db->real_escape_string($recipients_mode)."',
			target_count=".(int)$result['target'].",
			success_count=".(int)$result['success'].",
			failed_count=".(int)$result['failed'].",
			email_target=".(int)$result['email_target'].",
			email_success=".(int)$result['email_success'].",
			email_failed=".(int)$result['email_failed'].",
			customers_json='".$db->real_escape_string($customers_json)."',
			failed_customers_json='".$db->real_escape_string($failed_customers_json)."',
			created_by='".$db->real_escape_string($user)."'
		";
		if(!$db->query($sql)):
			throw new RuntimeException($db->error);
		endif;
	}
	
	private function load_notice_customers(){
		global $app;
		$path=isset($app->config['core_update_customers_path'])? $app->config['core_update_customers_path'] : 'private/_common/update/customers.csv';
		$file=$app->main_root.'/'.ltrim($path,'/');
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
			$row=array_map('trim',$row);
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
	
	private function connect_notice_customer($customer){
		foreach(['customer','db_host','db_name','db_user'] as $key):
			if(!isset($customer[$key]) || trim((string)$customer[$key])==''):
				throw new RuntimeException('Configurazione cliente incompleta.');
			endif;
		endforeach;
		$port=isset($customer['db_port']) && $customer['db_port']!==''? (int)$customer['db_port'] : 3306;
		$mysqli=@new mysqli($customer['db_host'],$customer['db_user'],isset($customer['db_pass'])? $customer['db_pass'] : '',$customer['db_name'],$port);
		if($mysqli->connect_errno):
			throw new RuntimeException($mysqli->connect_error);
		endif;
		$mysqli->set_charset('utf8mb4');
		return $mysqli;
	}
	
	private function insert_notice_for_customer($mysqli,$payload,$category){
		$columns=['notice','intent','intent_id','source','priority'];
		$values=[
			"'".$mysqli->real_escape_string($payload['notice'])."'",
			"'".$mysqli->real_escape_string($payload['intent'])."'",
			"'".$mysqli->real_escape_string($payload['intent_id'])."'",
			"'".$mysqli->real_escape_string($payload['source'])."'",
			"'".$mysqli->real_escape_string($payload['priority'])."'"
		];
		$has_category=$mysqli->query("show columns from archy_notices like 'category'");
		if($has_category && $has_category->num_rows>0):
			$columns[]='category';
			$values[]="'".$mysqli->real_escape_string($category)."'";
		endif;
		$sql="insert into archy_notices (".join(',',$columns).") values (".join(',',$values).")";
		if(!$mysqli->query($sql)):
			throw new RuntimeException($mysqli->error);
		endif;
	}
	
	private function ensure_customer_notice_columns($mysqli){
		$this->ensure_customer_notice_column($mysqli,'source',"varchar(32) not null default 'local'");
		$this->ensure_customer_notice_column($mysqli,'priority',"varchar(16) not null default 'normal'");
	}
	
	private function ensure_customer_notice_column($mysqli,$column,$definition){
		$column_safe=$mysqli->real_escape_string($column);
		$result=$mysqli->query("show columns from archy_notices like '".$column_safe."'");
		if($result && $result->num_rows>0):
			return;
		endif;
		if(!$mysqli->query("alter table archy_notices add column ".$column." ".$definition)):
			throw new RuntimeException($mysqli->error);
		endif;
	}

	private function email_notice_to_customer_users($mysqli,$payload,$category,$customer){
		global $app;
		$result=['target'=>0,'success'=>0,'failed'=>0];
		$settings=$app->get_settings($domain='notifications');
		$sender_email=isset($settings->sender_email)? trim((string)$settings->sender_email) : '';
		$sender_name=isset($settings->sender_name) && trim((string)$settings->sender_name)!=''? trim((string)$settings->sender_name) : ($this->config['customer_name'] ?? 'Archy Art');
		if($sender_email==''):
			error_log('[NoticeBroadcast] sender email not configured for mail notification.');
			return $result;
		endif;

		$users=$mysqli->query("select email, name, surname from archy_users where id>2 and email<>'' order by id");
		if(!$users):
			error_log('[NoticeBroadcast] archy_users query failed for customer='.$customer['customer'].' db='.($customer['db_name'] ?? '-').' error='.$mysqli->error);
			return $result;
		endif;

		$parts=$this->notice_mail_parts($payload);
		$subject='Nuova notifica';
		if($parts['title']!=''):
			$subject=$parts['title'];
		endif;
		$message_html='
			<p>Hai ricevuto una nuova notifica nel gestionale.</p>
			<hr>
			<p><strong>'.htmlspecialchars($parts['title']!=''? $parts['title'] : 'Notifica',ENT_QUOTES,'UTF-8').'</strong></p>
			<p>'.nl2br(htmlspecialchars($parts['body'],ENT_QUOTES,'UTF-8')).'</p>
			<hr>
			<p>Accedi al tuo pannello per visualizzare il dettaglio della notifica.</p>
		';
		$mailer=new Message();
		$sent_lookup=[];

		while($user=mysqli_fetch_object($users)):
			$email=trim((string)$user->email);
			if($email=='' || !filter_var($email,FILTER_VALIDATE_EMAIL)):
				continue;
			endif;
			$email_key=strtolower($email);
			if(isset($sent_lookup[$email_key])):
				continue;
			endif;
			$sent_lookup[$email_key]=true;
			$result['target']++;
			$recipient_name=trim(trim((string)$user->name).' '.trim((string)$user->surname));
			$sent=$mailer->email([
				'recipient_name'=>$recipient_name!=''? $recipient_name : $email,
				'recipient_mail'=>$email,
				'sender_name'=>$sender_name,
				'sender_mail'=>$sender_email,
				'subject'=>$subject,
				'message'=>$message_html
			]);
			if($sent):
				$result['success']++;
			else:
				$result['failed']++;
				error_log('[NoticeBroadcast] email failed customer='.$customer['customer'].' db='.($customer['db_name'] ?? '-').' to='.$email.' error='.$mailer->last_error);
			endif;
		endwhile;

		return $result;
	}

	private function notice_mail_parts($payload){
		$html=isset($payload['notice'])? (string)$payload['notice'] : '';
		$title='';
		$body_html=$html;
		if(preg_match('/<strong[^>]*>(.*?)<\/strong>\s*(<br\s*\/?>)?/is',$html,$matches)):
			$title=html_entity_decode(trim(strip_tags($matches[1])),ENT_QUOTES,'UTF-8');
			$body_html=preg_replace('/<strong[^>]*>.*?<\/strong>\s*(<br\s*\/?>)?/is','',$html,1);
		endif;
		$body=html_entity_decode(trim(strip_tags(str_ireplace(['<br>','<br/>','<br />'],"\n",$body_html))),ENT_QUOTES,'UTF-8');
		if($title==''):
			$full_text=html_entity_decode(trim(strip_tags(str_ireplace(['<br>','<br/>','<br />'],"\n",$html))),ENT_QUOTES,'UTF-8');
			$lines=preg_split('/\r\n|\r|\n/',$full_text);
			foreach($lines as $line):
				$line=trim($line);
				if($line!=''):
					$title=$line;
					break;
				endif;
			endforeach;
		endif;
		if($body==''):
			$body=$title;
		endif;
		return ['title'=>$title,'body'=>$body];
	}
	
	// main actions and helpers
	
	public function store($real_save = false){
            global $app;

            if(isset($_POST['action'])):

                if(isset($_POST['before_update'])):
                        $before_update=$_POST['before_update'];
                        $this->$before_update();
                endif;

                if (isset($_POST['intent']) && $_POST['intent'] === 'archy_users') {
                    $this->validate_archy_user_credentials_request();
                }

                if(isset($_POST['main'])) {
                    $values=array();
                    foreach($_POST['main'] as $field=>$value):
                        if (
                            isset($_POST['intent']) &&
                            $_POST['intent'] === 'archy_users' &&
                            $field === 'password'
                        ) {
                            $value = '';
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | ARCHY MEDIA - CAMPI TAGS CONTROLLATI
                        |--------------------------------------------------------------------------
                        |
                        | I campi type="tags" vengono normalizzati prima della
                        | serializzazione standard degli array.
                        |
                        */
                        if($this->archy_is_tags_field($field)):
                            $value = $this->archy_normalize_tags_post_value($field, $value);

                        elseif(is_array($value)):                        
                            $value = array_filter($value, fn($v) => $v !== '');
                            $value = serialize($value);

                        elseif(preg_match('/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/',$value))://latin dates format
                            $value=App::db_date($value);
                        elseif(preg_match('/^[0-9]{2}\/[0-9]{2}\/[0-9]{4} [0-9]{2}:[0-9]{2}$/',$value))://latin dates format +time 00:00
                            $value=App::db_datetime($value);
                        elseif(preg_match('/^[0-9]{2}\/[0-9]{2}\/[0-9]{4} [0-9]{2}:[0-9]{2}:[0-9]{2}$/',$value))://latin dates format +time 00:00:00
                            $value=App::db_datetime($value);
                        endif;
                        //error_log('@@@@@ VALUE TO APPEND #### '.$field."=".$value.' ####');
                        // Campi che finiscono per "_id" e sono vuoti, valorizzo a NULL
                        if(substr($field,-3)=="_id" && $value=="") {
                            $values[]=$field."=NULL";
                            //error_log('@@@@@ REPLACE #### '.$field.'=NULL ####');
                        } else {
                            $values[]=$field."='".addslashes($value)."'";
                        }
                    endforeach;
                    $values=join(',',$values);

                    $q=$_POST['action']." ".$_POST['intent']." set ".$values;
                    if($_POST['action']=='update'):
                        $q.=" where id=".$_POST['intent_id'];
                    endif;

                    if(!$this->db->query($q)) {
                        //error_log('@@@@@ '.__FILE__.'/store() : #### '.date("Y-m-d h:i:s").' ####');
                        //error_log('@@@@@ QUERY #### '.$q.' ####');
                        //error_log('@@@@@ ERROR LIST #### '.print_r($this->db->error_list,true).' #####');
                        if($app->is_admin) {
                            echo("<h1>".__FILE__."/store(): database query error!</h1><p>".$this->db->error."</p>");
                            echo("<h1>Query:</h1><p>".$q."</p>");
                        } else {
                            echo("<h1>Database error! (".date("Y-m-d h:i:s").")</h1>");
                        }
                        exit();
                    } else {
                        if($app->is_admin) {
                            //error_log('@@@@@ QUERY #### '.$q.' ####');
                        }
                    }
                }
                $insert_id=$_POST['action']=='update'? $_POST['intent_id'] : $this->db->insert_id;
                $this->insert_id=$insert_id;
                
                if($real_save){ return $this->insert_id; exit; }
                
                $this->store_additional_fields();			
                //if (!empty($_FILES)) {$this->store_files();	}		    

                if(isset($_POST['extra_action'])):
                        $extra_action=$_POST['extra_action'];
                        $this->$extra_action();
                endif;

                $message=$_POST['intent_name']." ";
                $message.=$_POST['action']=='update'? 'aggiornato/a.' : 'salvato/a.';
                if(isset($_POST['goto'])):
                        $redirect_url=$_POST['goto'];
                else:
                        $redirect_url='/archy/app.php?intent='.$_POST['intent'].'&view=';
                        if(isset($_POST['save'])):
                                $redirect_url.='grid';
                        elseif(isset($_POST['save_and_new'])):
                                $redirect_url.='edit';
                        elseif(isset($_POST['save_and_stay'])):
                                $redirect_url.='edit&id='.$this->insert_id;
                        endif;
                endif;

                if(isset($_POST['intent_filter'])):
                    $redirect_url.='&intent_filter='.$_POST['intent_filter'];
                endif;

                if(isset($_POST['after_update'])):
                        $after_update=$_POST['after_update'];
                        $this->$after_update();
                endif;

                set_message("success",$message);
                if(isset($_POST['extra_notice'])):
                        set_message("warning",$this->extra_notices[$_POST['extra_notice']]);
                endif;
                header('location: '.$redirect_url);
                exit();
                
                
                
            endif;
	}

    protected function validate_archy_user_credentials_request()
    {
        $email = $_POST['main']['email'] ?? '';
        $password = $_POST['main']['password'] ?? '';
        $old_email = $_POST['email'] ?? $email;
        $is_new_auth_user = !$this->auth->getUID($old_email);
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';

        if ($is_new_auth_user && $password === '') {
            set_message('error', 'Inserisci una password per creare il nuovo utente.');
            header('location: '.$referer);
            exit;
        }

        if ($old_email !== $email) {
            $old_uid = $this->auth->getUID($old_email);
            $new_uid = $this->auth->getUID($email);

            if ($new_uid && (!$old_uid || $new_uid !== $old_uid)) {
                set_message('error', 'Questa email e gia in uso da un altro utente.');
                header('location: '.$referer);
                exit;
            }
        }

        if ($password !== '') {
            $validation = $this->auth->validateStrictPassword($password, $email);

            if (!$validation['valid']) {
                set_message('error', $validation['message']);
                header('location: '.$referer);
                exit;
            }
        }
    }
	
    /*
    |--------------------------------------------------------------------------
    | ARCHY MEDIA - TAGS CONTROLLATI V2 / SALVATAGGIO ROBUSTO
    |--------------------------------------------------------------------------
    |
    | Obiettivo:
    | - intercettare i campi type="tags" prima della serializzazione standard;
    | - non dipendere solo da $app->cols, perché in alcuni intent la config non
    |   è sempre disponibile dentro AppActions::store();
    | - accettare valori provenienti da Choices.js come ID, stringhe, JSON oppure
    |   array di oggetti {value,label};
    | - creare i nuovi tag in archy_tag e salvare nel record principale il
    |   serializzato degli ID, formato atteso dalla visualizzazione.
    |
    | Nota debug:
    | per attivare un log temporaneo, aggiungere in URL:
    | &archy_tags_debug=1
    | Il log viene scritto in error_log senza mostrare nulla a video.
    |
    */
    public function archy_tags_debug_enabled(){
        return isset($_GET['archy_tags_debug']) && $_GET['archy_tags_debug'] == '1';
    }

    public function archy_tags_debug($message, $data = null){
        if(!$this->archy_tags_debug_enabled()):
            return;
        endif;

        $line = '[ARCHY_TAGS] '.$message;
        if($data !== null):
            $line .= ' '.print_r($data, true);
        endif;
        error_log($line);
    }

    public function archy_get_current_table(){
        global $app;

        if(isset($_POST['intent']) && trim((string)$_POST['intent']) !== ''):
            return trim((string)$_POST['intent']);
        endif;

        if(isset($app->intent) && isset($app->intent->table) && trim((string)$app->intent->table) !== ''):
            return trim((string)$app->intent->table);
        endif;

        if(isset($this->intent) && isset($this->intent->table) && trim((string)$this->intent->table) !== ''):
            return trim((string)$this->intent->table);
        endif;

        return '';
    }

    public function archy_get_column_config($field){
        global $app;

        $table = $this->archy_get_current_table();
        $possible_keys = [];

        $field = trim((string)$field);
        $possible_keys[] = $field;

        if($table != ''):
            $possible_keys[] = $table.'.'.$field;
        endif;

        foreach($possible_keys as $key):
            if(isset($app) && isset($app->cols) && isset($app->cols[$key])):
                return $app->cols[$key];
            endif;

            if(isset($app) && isset($app->main_cols) && isset($app->main_cols[$key])):
                return $app->main_cols[$key];
            endif;

            if(isset($this->cols) && isset($this->cols[$key])):
                return $this->cols[$key];
            endif;

            if(isset($this->main_cols) && isset($this->main_cols[$key])):
                return $this->main_cols[$key];
            endif;
        endforeach;

        return [];
    }

    public function archy_is_tags_field($field){
        $config = $this->archy_get_column_config($field);

        if(isset($config['type']) && $config['type'] === 'tags'):
            return true;
        endif;

        if(isset($config['tag_category']) && trim((string)$config['tag_category']) !== ''):
            return true;
        endif;

        /*
        | Fallback prudente: nel catalogo questi sono i campi usati come tags.
        | Serve quando la config colonne non è leggibile dentro store().
        */
        $field_clean = strtolower(trim((string)$field));
        /*
        | Fallback prudente: NON includiamo 'tag' perché in archy_art_catalog
        | esiste anche il vecchio campo storico 'tag' come editor/testo libero.
        | Se lo trattiamo come campo tags, crea duplicati nella categoria generica Tag.
        | Il campo nuovo 'tags' viene comunque riconosciuto dall'intent se type => tags.
        */
        $known_tag_fields = [
			'tags',

			'tecnica',
			'tecniche',
			'technique',
			'techniques',

			'materiale',
			'materiali',
			'material',
			'materials',

			'supporto',
			'supporti',
			'support',
			'supports'
		];

        if(in_array($field_clean, $known_tag_fields)):
            return true;
        endif;

        return false;
    }

    public function archy_get_tags_field_config($field){
        return $this->archy_get_column_config($field);
    }

    public function archy_get_tags_category($field){
        $config = $this->archy_get_tags_field_config($field);

        if(isset($config['tag_category']) && trim((string)$config['tag_category']) !== ''):
            return trim((string)$config['tag_category']);
        endif;

        /*
        | Fallback categorie coerenti con i campi catalogo.
        | Se nell'intent è presente tag_category, questa parte non viene usata.
        */
        $field_clean = strtolower(trim((string)$field));

        switch($field_clean):
            case 'tecnica':
            case 'tecniche':
            case 'technique':
            case 'techniques':
                return 'Tecnica';

            case 'materiale':
            case 'materiali':
            case 'material':
            case 'materials':
                return 'Materiali';

            case 'supporto':
            case 'supporti':
            case 'support':
            case 'supports':
                return 'Supporto';

            case 'tag':
            case 'tags':
            default:
                return 'Tag';
        endswitch;
    }

    public function archy_parse_tags_post_value($value){
        if($value === null || $value === ''):
            return [];
        endif;

        if(is_array($value)):
            $values = $value;
        elseif(class_exists('App') && method_exists('App', 'is_serialized') && App::is_serialized($value)):
            $values = @unserialize($value);
        else:
            $json = json_decode((string)$value, true);
            if(is_array($json)):
                $values = $json;
            else:
                $values = preg_split('/[,;|\r\n]+/', (string)$value);
            endif;
        endif;

        if(!is_array($values)):
            return [];
        endif;

        $clean = [];
        foreach($values as $item):
            if(is_array($item)):
                if(isset($item['value']) && trim((string)$item['value']) !== ''):
                    $item = $item['value'];
                elseif(isset($item['label']) && trim((string)$item['label']) !== ''):
                    $item = $item['label'];
                else:
                    continue;
                endif;
            endif;

            $item = trim((string)$item);
            if($item !== ''):
                $clean[] = $item;
            endif;
        endforeach;

        return array_values(array_unique($clean));
    }

    public function archy_tag_slug($value){
        if(class_exists('App') && method_exists('App', 'tag_slug')):
            return App::tag_slug($value);
        endif;

        $value = trim((string)$value);
        if(function_exists('iconv')):
            $converted = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
            if($converted !== false):
                $value = $converted;
            endif;
        endif;
        $value = strtolower($value);
        $value = preg_replace('/[^a-z0-9]+/', '-', $value);
        $value = trim($value, '-');

        return $value !== '' ? $value : 'tag';
    }

    public function archy_normalize_tags_post_value($field, $value){
        $category = $this->archy_get_tags_category($field);
        $values = $this->archy_parse_tags_post_value($value);
        $tag_ids = [];

        $this->archy_tags_debug('normalize start', [
            'field' => $field,
            'category' => $category,
            'raw_value' => $value,
            'parsed_values' => $values
        ]);

        foreach($values as $item):
            $item = trim((string)$item);
            if($item === ''):
                continue;
            endif;

            /*
            | Se arriva un ID numerico da un suggerimento esistente, lo salvo.
            | Se invece arriva una nuova etichetta, creo/recupero il tag in archy_tag.
            */
            if(ctype_digit($item)):
                $tag_ids[] = (int)$item;
                continue;
            endif;

            $label = preg_replace('/\s+/', ' ', $item);
            $slug = $this->archy_tag_slug($label);

            $category_safe = $this->db->real_escape_string($category);
            $slug_safe = $this->db->real_escape_string($slug);
            $label_safe = $this->db->real_escape_string($label);

            $res = $this->db->query("
                SELECT id
                FROM archy_tag
                WHERE categoria = '".$category_safe."'
                AND slug = '".$slug_safe."'
                LIMIT 1
            ");

            if($res && mysqli_num_rows($res) > 0):
                $tag_ids[] = (int) mysqli_fetch_object($res)->id;
                continue;
            endif;

            $insert = $this->db->query("
                INSERT INTO archy_tag
                SET
                    tag = '".$label_safe."',
                    slug = '".$slug_safe."',
                    categoria = '".$category_safe."',
                    status = 1,
                    created_at = NOW(),
                    updated_at = NOW()
            ");

            if($insert && $this->db->insert_id):
                $tag_ids[] = (int)$this->db->insert_id;
            else:
                $this->archy_tags_debug('insert tag failed', [
                    'field' => $field,
                    'label' => $label,
                    'error' => $this->db->error
                ]);
            endif;
        endforeach;

        $tag_ids = array_values(array_unique(array_filter($tag_ids)));
        $serialized = empty($tag_ids) ? '' : serialize(array_map('strval', $tag_ids));

        $this->archy_tags_debug('normalize end', [
            'field' => $field,
            'tag_ids' => $tag_ids,
            'serialized' => $serialized
        ]);

        return $serialized;
    }


	public function store_additional_fields(){
		if(isset($_POST['additional'])):
			//$db=$this->get_db();
			foreach($_POST['additional'] as $domain_id=>$value):
				if(is_array($value)):
					$value=serialize($value);
				endif;
				$domain_id=str_replace('additional_','',$domain_id);
				$this->db->query("delete from additional_fields_values where domain_id='".addslashes($domain_id)."' and record_id='".addslashes($this->insert_id)."'");
				$this->db->query("insert into additional_fields_values set domain_id='".addslashes($domain_id)."', record_id='".addslashes($this->insert_id)."', value='".addslashes($value)."'");
			endforeach;
		endif;
	}
	
	public function store_files(){
                //$storeCloud = true;
                global $app;
                $params = [];
                foreach ($_POST as $key => $value) {

                    if (strpos($key, 'file_target_') === 0) {

                        $field = str_replace('file_target_', '', $key);

                        $params['targets'][$field] = $value;
                    }
                }
                $params['intent'] = $_POST['intent'];
                $params['action'] = $_POST['action'];
                if(isset($_POST['real_save_id'])){
                    $params['insert_id'] = $_POST['real_save_id'];
                }else{
                    $params['insert_id'] = $this->insert_id;
                }
                $StoreManager = new StoreManager($app);
                $response = $StoreManager->store($_FILES,$params);
                return $response;
	}
        
      /*  public function addToFiles($intent,$field_name,$file_name,$params=[]){
            $filename_ext = pathinfo($file_name, PATHINFO_EXTENSION);
            $this->db->query("INSERT INTO archy_files VALUES (NULL, '".$this->insert_id."', '".$intent."','".$field_name."', 0, 0, 1, '".$params['file_value']."', '".$file_name."','".$params['file_type']."','".$filename_ext."','','".$params['file_location']."', NULL, NULL)"); 
           // if (in_array($file_name_type, $storeCloud::getMymeTypes('video'))){ $_SESSION['archy_files_ids'] = $this->insert_id; } // creo la sessione per poter fare l'update del cloud id una volta ricevuta la notifica
        }*/

	
        
        public function uploadToCloudVideo($src,$file_name){
            $storeVideo = new StoreCloud();
            $storeVideo->uploadVideo($src,$file_name);
        }
        
        
	
	/*public function clean_folder($old_file,$file_name,$value){
		if($old_file!=''):
			if(!is_array($file_name)):
				if(is_file($this->subdomain_root.$old_file)):
					unlink($this->subdomain_root.$old_file);
				endif;
			else:
				$files=unserialize($old_file);
				$value=unserialize($value);
				foreach($files as $file_path):
					if($file_path!=''):
						$value[]=$file_path;
					endif;
				endforeach;
				$value=serialize($value);
			endif;
		endif;
		return $value;
	}*/
        
        public function getStats(){
            $stats_arr=[];
            $stats=mysqli_fetch_object($this->db->query("select * from archy_stats"));//BINARY performs a case-sensitive comparison
            $stats_arr[0]=$stats->stats_weight_cloud_img;
            $stats_arr[1]=$stats->stats_weight_local_img;
            return $stats_arr;
        }
	
	public function petty_cash_book_before_update(){
		
		$this->petty_cash=new PettyCash($_POST['intent_id']);
		$this->petty_cash->before_update();
		
	}
	public function petty_cash_book_after_update(){
		
		$this->petty_cash->after_update();
		
	}
		
	public function credit_note_before_update(){
		
		$this->credit_note=new CreditNote($_POST['intent_id']);
		$this->credit_note->before_update();
		
	}
	public function credit_note_after_update(){
		
		$this->credit_note->after_update();
		
	}
		
	public function invoice_after_update(){
		
		$invoice=new Invoice($_POST['intent_id']);
		$invoice->after_update();
		
	}
	
	public function delete(){
		if(trim(@$_GET['intent'])!='' and trim(@$_GET['id'])!=''):
			
			if(@$_GET['before_delete']!=''):
				$method=$_GET['before_delete'];
				$this->$method($_GET['id']);
			endif;
			
            $ret = $this->db->query("delete from ".$_GET['intent']." where id=".$_GET['id']);
            if($ret === FALSE) {
                error_log("@@@@@@ db query error: >".$this->db->error."<");
                $qerr="Non è possibile cancellare questo record perché collegato a una tabella principale";
                set_message("warning",$qerr);
            } 

			if(@$_GET['after_delete']!=''):
				$method=$_GET['after_delete'];
				$this->$method($_GET['id']);
			endif;
            
            if($ret === TRUE) {
                set_message("success","Il record è stato cancellato.");
            }
                      
			if(isset($_GET['extra_notice'])):
				set_message("warning",$this->extra_notices[$_GET['extra_notice']]);
			endif;
			header('location: '.$_SERVER['HTTP_REFERER']);
			exit();
		endif;
	}
	
	public function petty_cash_book_before_delete($id) {
		$this->petty_cash=new PettyCash($id);
		$this->petty_cash->before_delete();
		
	}
	public function petty_cash_book_after_delete() {
		$this->petty_cash->after_delete();
		
	}
	
	public function credit_note_before_delete($id) {
		$this->credit_note=new CreditNote($id);
	}
	public function credit_note_after_delete($id) {
		$this->credit_note->after_delete();
	}
	
	public function invoice_before_delete($id) {
		$this->invoice=new Invoice($id);
	}
	public function invoice_after_delete() {
		$this->invoice->after_delete();
	}

    public function events_sessions_after_delete($session_id) {
        $this->db->query("DELETE FROM archy_events_extras WHERE domain = 'session' AND domain_id=" . $session_id);
        $this->db->query("DELETE FROM archy_events_speeches WHERE session_id=" . $session_id);
    }

    public function events_before_delete($id) {
		$this->event=new Event($id);
	}
    public function events_after_delete() {
        $this->event->after_delete();
    }
	
    public function check_unique(){
        $binary=@$_GET['case_sensitive']=='no'? '' : 'BINARY';
        $q="select id from ".$_GET['table']." where ".$_GET['column']."= ".$binary." '".addslashes($_GET['value'])."'";//BINARY performs a case-sensitive comparison
        if(isset($_GET['id']) && $_GET['id']!=''){
            $q.=" and id!=".$_GET['id'];
        }
        echo $this->db->query($q)->num_rows==0? 'ok':'ko';
    }
    
    public function check_unique_taxcode(){
        $q="SELECT id 
            FROM archy_registry 
            WHERE tax_code = '".addslashes($_GET['value'])."'
            AND legal_personality = ".$_GET['lp'];
        echo $this->db->query($q)->num_rows==0? 'ok':'ko';
	}
	
	public function registry_duplicated_entry(){
		$data=mysqli_fetch_object($this->db->query("select id,firm_name from archy_registry where ".$_GET['column']."='".addslashes($_GET['value'])."'"));
		$data->message=$_GET['column']=='tax_code'? 'Il codice fiscale' : 'La partita IVA';
		$data->message.=' <strong>'.$_GET['value'].'</strong> ';
		$data->message.='è già ';
		$data->message.=$_GET['column']=='tax_code'? 'associato' : 'associata';
		$data->message.=' all\'anagrafica <strong>'.$data->firm_name.'</strong>';
		echo json_encode($data);
	}
	
	public function member_duplicated_entry(){
		
		$membership=new Membership();
		$response=$membership->check_duplicated_entry($_GET['registry_id']);
		
		echo json_encode($response);
		
	}
	
	public function valid_tax_code(){
		$url='http://webservices.dotnethell.it/codicefiscale.asmx/ControllaCodiceFiscale';
		$data=['CodiceFiscale'=>$_POST['CodiceFiscale']];
		$options = [
    		'http' => [
				'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
				'method'  => 'POST',
				'content' => http_build_query($data)
			]
		];
		$context=stream_context_create($options);
		$response=trim(strip_tags(file_get_contents($url, false, $context)));
		echo $response=='Il codice è valido!'? true : false;
	}
	
	public function get_city_by_tax_code(){
		$url='http://webservices.dotnethell.it/codicefiscale.asmx/NomeComune';
		$data=['CodiceComune'=>$_POST['CodiceComune']];
		$options = [
    		'http' => [
				'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
				'method'  => 'POST',
				'content' => http_build_query($data)
			]
		];
		$context=stream_context_create($options);
		$city=trim(strip_tags(file_get_contents($url, false, $context)));
		$result=$this->db->query("select label from archy_cities where city='".addslashes($city)."'");
		if($result->num_rows>0):
			echo mysqli_fetch_object($result)->label;
		endif;
	}
	
	public function invoice_table($body,$taxable_amount,$vat,$total){
		
		$table='
			<table cellpadding="4" cellspacing="0">
				<tr>
					<th class="left" width="45%">Causale</th>
					<th class="left">Quantità</th>
					<th class="left">Prezzo</th>
					<th class="right">Imponibile</th>
					<th class="right">IVA</th>
					<th class="right">Subtotale</th>
				</tr>';
		
		foreach($body['description'] as $index=>$description):
			$table.='
				<tr>
					<td>'.$description.'</td>
					<td>'.$body['quantity'][$index].'</td>
					<td>'.App::currency($body['price'][$index]).'</td>
					<td class="right">'.App::currency($body['amount'][$index]).'</td>
					<td class="right">('.str_replace(".00","",$body['tax_percentage'][$index]).'%) '.App::currency($body['tax'][$index]).'</td>
					<td class="right">'.App::currency($body['subtotal'][$index]).'</td>
				</tr>';
		endforeach;
		
		$table.='
			<tr class="totals">
				<th colspan="3">Totali</th>
				<th>'.App::currency($taxable_amount).'</th>
				<th>'.App::currency($vat).'</th>
				<th>'.App::currency($total).'</th>
			</tr>
		</table>';
		
		return $table;
		
	}
	
	public function receipt_table($body,$total){
		
		$table='
			<table cellpadding="4" cellspacing="0">
				<tr>
					<th class="left" width="60%">Causale</th>
					<th class="left">Quantità</th>
					<th class="left">Prezzo</th>
					<th class="right">Subtotale</th>
				</tr>';
				
		foreach($body['description'] as $index=>$description):
			$table.='
				<tr>
					<td>'.$description.'</td>
					<td>'.$body['quantity'][$index].'</td>
					<td>'.App::currency($body['price'][$index]).'</td>
					<td class="right">'.App::currency($body['amount'][$index]).'</td>
				</tr>';
		endforeach;
		
		$table.='
			<tr class="totals">
				<th colspan="3">Totale</th>
				<th>'.App::currency($total).'</th>
			</tr>
		</table>';
		
		return $table;
			
	}
	
	public function open_ticket(){
		$support=new Support();
		$support->open_ticket();		
	}
	
	public function ticket_user_reply(){
		$support=new Support($_POST['thread_id'],'customer');
		$support->user_reply();		
	}
	
	public function close_ticket(){
		$support=new Support();
		if($support->close($_GET['thread_id'])):
			set_message("success","Ticket chiuso.");
		endif;
		header('location: '.$_SERVER['HTTP_REFERER']);
	}
	
	public function reopen_ticket(){
		set_message("warning","La riapertura dei ticket non e disponibile.");
		header('location: '.$_SERVER['HTTP_REFERER']);
	}
	
	public function get_subscription_fee(){
		$membership=new Membership();
		echo $membership->get_subscription_fee($_POST['category_id']);
    }
    
    public function get_eps_fee() {
        $membership=new Membership();
        echo $membership->get_eps_fee($_POST['eps']);
    }
	
	public function check_candidate(){
		
		$response=new stdClass();
		$response->is_already_candidate=$this->db->query("select id from archy_memberships_examinee where registry_id=".$_POST['registry_id']." and request_status=2")->num_rows>0;
		$response->is_already_member=$this->db->query("select id from archy_memberships where registry_id=".$_POST['registry_id']." and status!=4")->num_rows>0;
		echo json_encode($response);
		
	}

    public function check_duplicate_service_membership(){
		
		$response=new stdClass();
		$response->is_already_member=$this->db->query("select id from archy_services_memberships where registry_id=".$_POST['registry_id']." and services_id=".$_POST['services_id'])->num_rows>0;
        //$response->registry_id=$_POST['registry_id'];
        //$response->services_id=$_POST['services_id'];
		echo json_encode($response);
		
	}

	public function document_info(){
		
		error_reporting(0);
		
		switch($_POST['table']):
			
			case 'archy_invoices':
				$invoice=new Invoice($_POST['id']);
				unset($invoice->app);
				unset($invoice->db);
				echo json_encode($invoice);
			break;
			
			case 'archy_credit_notes':
				$credit_note=new CreditNote($_POST['id']);
				unset($credit_note->app);
				unset($credit_note->db);
				echo json_encode($credit_note);
			break;
			
			case 'archy_tax_receipts':
				$receipt=new Receipt($_POST['id']);
				unset($receipt->app);
				unset($receipt->db);
				echo json_encode($receipt);
			break;
		
			case 'archy_passive_receipts':
				$receipt=new PassiveReceipt($_POST['id']);
				unset($receipt->app);
				unset($receipt->db);
				echo json_encode($receipt);
			break;
		
			case 'archy_payables':
				$payable=mysqli_fetch_object($this->db->query("select paid,total,supplier_id,number,date from archy_payables where id=".$_POST['id']));
				$payable->customer=new Registry($payable->supplier_id);
				$payable->latin_date=App::latin_date($payable->date);
				echo json_encode($payable);
			break;
		
		endswitch;
		
	}
	
	public function update_document_payments($table,$total,$paid,$id){
		
		switch(true):
			case $paid<=0:			$payment_status='not_paid';			break;
			case $total<=$paid:	$payment_status='paid';					break;
			case $total>$paid:	$payment_status='partially_paid';	break;
		endswitch;
		
		$total_column=$table=='archy_passive_receipts'? 'abs(total)' : 'total';
		
		$this->db->query("
			update ".$table." set 
			paid=".$paid.",
			unpaid=".$total_column."-paid,
			payment_status='".$payment_status."'
			where id=".$id
		);
		
	}
	
	public function next_document_number_by_year_and_firm($table,$firm_id,$year,$first_number,$ajax){
        $num_rows=$this->db->query("select id from ".$table." where firm_id=".$firm_id." and date between '".$year."-01-01' and '".$year."-12-31' limit 1")->num_rows;
        //error_log("@@@@@@@@@@@@@@@@ next_document_number_by_year_and_firm not_empty:".$num_rows);
		if($num_rows>0):
			$number=mysqli_fetch_object($this->db->query("
				select coalesce(max(number),0)+1 as number 
				from ".$table." 
				where firm_id=".$firm_id." and date between '".$year."-01-01' and '".$year."-12-31'
			"))->number;
		else:
			$first_number=mysqli_fetch_object($this->db->query("select ".$first_number." as first_number from archy_firms where id=".$firm_id))->first_number;
			$number=$first_number!=0? $first_number : 1;
        endif;
        //error_log("@@@@@@@@@@@@@@@@ next_document_number_by_year_and_firm number:".$number);
		if($ajax==='true'):
			echo $number;
		else:
			return $number;
		endif;
	}
	
	public function get_next_number_by_year_and_firm(){
		
		$this->next_document_number_by_year_and_firm($_POST['table'],$_POST['firm_id'],$_POST['year'],$_POST['first_number'],$ajax='true');
		
	}
	
	public function get_invoice_number_by_id(){
		error_reporting(0);
		$invoice=new Invoice($_POST['id']);
		echo $invoice->invoice_number;
	}
	public function get_invoice_id_by_credit_note_id($credit_note_id){
		return mysqli_fetch_object($this->db->query("select invoice_id from archy_credit_notes where id=".$credit_note_id))->invoice_id;
	}
	
	public function invoice_balance($invoice_id){
		
		$invoice=new Invoice($invoice_id);
		$credit_notes=$this->db->query("select * from archy_credit_notes where invoice_id=".$invoice_id." order by id");
		$invoice_paid=mysqli_fetch_object($this->db->query("select coalesce(sum(amount),0) as total from archy_petty_cash_book where document_table='archy_invoices' and document_id=".$invoice_id))->total;		
		
		$invoice_balance=$invoice->total-$invoice_paid;
		
		while($credit_note=mysqli_fetch_object($credit_notes)):
		
			if($invoice_balance<=0):
				
				$this->update_document_payments('archy_credit_notes',$credit_note->total,0,$credit_note->id);
				
			else:
				
				if($credit_note->total<=$invoice_balance):
					$this->update_document_payments('archy_credit_notes',$credit_note->total,$credit_note->total,$credit_note->id);
					$invoice_balance-=$credit_note->total;
					$invoice_paid+=$credit_note->total;
				else:
					$credit_note_balance=$credit_note->total-$invoice_balance;
					$credit_note_paid=$credit_note->total-$credit_note_balance;
					$this->update_document_payments('archy_credit_notes',$credit_note->total,$credit_note_paid,$credit_note->id);
					$invoice_balance=0;
					$invoice_paid+=$credit_note_paid;
				endif;
				
			endif;
			
		endwhile;
		
		$this->update_document_payments('archy_invoices',$invoice->total,$invoice_paid,$invoice_id);
				
	}
	
	public function merge_pdfs($target_folder,$source_folder,$merged_name){
		
		if(is_dir($source_folder)):
			$pdf_notices=array_diff(scandir($source_folder),['..','.']);
			if(count($pdf_notices)>0):
				$outputName=$target_folder.$merged_name;
				$cmd="/usr/bin/gs -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile=$outputName ";
				foreach($pdf_notices as $file_name):
					$cmd.=$source_folder.$file_name." ";
				endforeach;
				shell_exec($cmd);
			endif;
			array_map('unlink', glob($source_folder."*"));
			rmdir($source_folder);
		endif;
		
	}

	public function sdi_xml($document,$document_type,$number,$body_location,$file_path){

        // NON PIU' SUPPORTATO, VEDERE sdi_json() !!
        return;

		$transmission_format=$document->customer->customer_private=='1'? 'FPR12' : 'FPA12';

		if($recipient=$document->customer->customer_sending_method=='1'):
			$pec='<PECDestinatario>'.$document->customer->customer_sdi_pec.'</PECDestinatario>';
			if($document->customer->vat_country=='IT' or $document->customer->legal_personality=='2'):
				$code=$document->customer->customer_private=='1'? '0000000' : '000000';
			else:
				$code="0000000";
			endif;
			$code_tag='<CodiceDestinatario>'.$code.'</CodiceDestinatario>';
		else:
			$pec='';
			if($document->customer->vat_country=='IT'):
				$code=$document->customer->customer_recipient_code;
			else:
				$code="0000000";
			endif;
			$code_tag='<CodiceDestinatario>'.$code.'</CodiceDestinatario>';
		endif;

		$provincia_sede=$document->firm->city->iso2=='IT'? $document->firm->city->province_district_code : '';

		$EORI=$document->firm->EORI!=''? "<CodEORI>".$document->firm->EORI."</CodEORI>" : '';
		$customer_EORI=$document->customer->customer_EORI!=''? "<CodEORI>".$document->customer->customer_EORI."</CodEORI>" : '';
		

		$xml='<?xml version="1.0" encoding="UTF-8" ?>
			<p:FatturaElettronica versione="'.$transmission_format.'" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:p="http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2 http://www.fatturapa.gov.it/export/fatturazione/sdi/fatturapa/v1.2/Schema_del_file_xml_FatturaPA_versione_1.2.xsd">';
		$xml.="
			<FatturaElettronicaHeader>
				
				<DatiTrasmissione>
					
					<IdTrasmittente>
						<IdPaese>".$document->firm->city->iso2."</IdPaese>
						<IdCodice>".$document->firm->vat_id."</IdCodice>
					</IdTrasmittente>
					
					<ProgressivoInvio>".$this->name."-".$document->id."</ProgressivoInvio>
					
					<FormatoTrasmissione>".$transmission_format."</FormatoTrasmissione>
					
					".$code_tag;

		if($document->firm->phone!='' or $document->firm->email!=''):
			$xml.="	<ContattiTrasmittente>";
			if($document->firm->phone!=''):
				$xml.="	<Telefono>".$document->firm->phone."</Telefono>";
			endif;

			if($document->firm->email!=''):
				$xml.="	<Email>".$document->firm->email."</Email>";
			endif;
			$xml.="	</ContattiTrasmittente>";
		endif;

		$xml.="
					".$pec."
				</DatiTrasmissione>

				<CedentePrestatore>

					<DatiAnagrafici>
						
						<IdFiscaleIVA>
							<IdPaese>".$document->firm->city->iso2."</IdPaese>
							<IdCodice>".$document->firm->vat_id."</IdCodice>							
						</IdFiscaleIVA>

						<CodiceFiscale>".$document->firm->tax_id."</CodiceFiscale>

						<Anagrafica>
							<Denominazione><![CDATA[".htmlentities($document->firm->firm_name)."]]></Denominazione>
							".$EORI."
						</Anagrafica>";
		if($document->firm->professional_register_name!='' and $document->firm->professional_register_province!='' and $document->firm->professional_register_subscription_number!='' and $document->firm->professional_register_subscription_date!=''):
			$xml.="
						<AlboProfessionale>".$document->firm->professional_register_name."</AlboProfessionale>
						<ProvinciaAlbo>".$document->firm->professional_register_province."</ProvinciaAlbo>
						<NumeroIscrizioneAlbo>".$document->firm->professional_register_subscription_number."</NumeroIscrizioneAlbo>
						<DataIscrizioneAlbo>".$document->firm->stable_org_city->professional_register_subscription_date."</DataIscrizioneAlbo>";
		endif;

		$xml.="
						<RegimeFiscale>".$document->firm->tax_regime."</RegimeFiscale>

					</DatiAnagrafici>

					<Sede>

						<Indirizzo><![CDATA[".htmlentities($document->firm->address)."]]></Indirizzo>
						<CAP>".$document->firm->zip."</CAP>
						<Comune>".htmlentities($document->firm->city->city)."</Comune>
						<Provincia>".$provincia_sede."</Provincia>
						<Nazione>".$document->firm->city->iso2."</Nazione>

					</Sede>";
		
		if($document->firm->stable_org_address!='' and $document->firm->stable_org_zip!='' and $document->firm->stable_org_city_id!=''):
			$xml.="
					<StabileOrganizzazione>
						<Indirizzo><![CDATA[".htmlentities($document->firm->stable_org_address)."]]></Indirizzo>
						<CAP>".$document->firm->stable_org_zip."</CAP>
						<Comune>".htmlentities($document->firm->stable_org_city->city)."</Comune>
						<Provincia>".$document->firm->stable_org_city->province_district_code."</Provincia>
						<Nazione>IT</Nazione>
					</StabileOrganizzazione>";
		endif;

		if($document->firm->rea_office!='' and $document->firm->rea_number!='' and $document->firm->rea_share_capital!='' and $document->firm->rea_sole_shareholder!='' and $document->firm->rea_in_liquidation!=''):
			$xml.="
					<IscrizioneREA>
						<Ufficio>".$document->firm->rea_office."</Ufficio>
						<NumeroREA>".$document->firm->rea_number."</NumeroREA>
						<CapitaleSociale>".$document->firm->rea_share_capital."</CapitaleSociale>
						<SocioUnico>".$document->firm->rea_sole_shareholder."</SocioUnico>
						<StatoLiquidazione>".$document->firm->rea_in_liquidation."</StatoLiquidazione>
					</IscrizioneREA>";
		endif;
		if($document->firm->phone!='' or $document->firm->fax!='' or $document->firm->email!=''):
			$xml.="<Contatti>";
			if($document->firm->phone!=''):
				$xml.="<Telefono>".$document->firm->phone."</Telefono>";
			endif;
			if($document->firm->fax!=''):
				$xml.="<Fax>".$document->firm->fax."</Fax>";
			endif;
			if($document->firm->email!=''):
				$xml.="<Email>".$document->firm->email."</Email>";
			endif;
			$xml.="</Contatti>";
		endif;
		$xml.="
					<RiferimentoAmministrazione>".$this->name."|".$document->id."</RiferimentoAmministrazione>

				</CedentePrestatore>

				<CessionarioCommittente>
					
					<DatiAnagrafici>";
		if($document->customer->legal_personality=='1'):
			$xml.="
						<IdFiscaleIVA>
							<IdPaese>".$document->customer->vat_country."</IdPaese>
							<IdCodice>".$document->customer->vat_id."</IdCodice>
						</IdFiscaleIVA>";
		else:
			$xml.="
						<CodiceFiscale>".$document->customer->tax_code."</CodiceFiscale>";
		endif;

		$xml.="
						<Anagrafica>";
		if($document->customer->legal_personality=='1' or $document->customer->legal_personality=='2'):
			$xml.="
							<Denominazione><![CDATA[".htmlentities($document->customer->firm_name)."]]></Denominazione>
			";
		else:
			$xml.="
							<Nome>".htmlentities($document->customer->name)."</Nome>
							<Cognome>".htmlentities($document->customer->surname)."</Cognome>
			";
		endif;
		$xml.="
							".$customer_EORI."

						</Anagrafica>

					</DatiAnagrafici>

					<Sede>

						<Indirizzo><![CDATA[".str_replace("\n"," ",htmlentities($document->customer->address))."]]></Indirizzo>";
		if(strlen($document->customer->zip)!=5 or !is_numeric($document->customer->zip)):
			if($document->customer->vat_country=='IT'):
				set_message("danger","Errore nella generazione del file xml per la fatturazione elettronica. Verificare il CAP del cliente intestatario di questa fattura e aggiornare la fattura stessa usando il pulsante \"Aggiorna\"");
			else:
				$document->customer->zip='00000';
			endif;
		endif;
		$xml.="
						<CAP>".$document->customer->zip."</CAP>
						<Comune>".htmlentities($document->customer->city->city)."</Comune>";
		if(trim($document->customer->city->province_district_code)!=''):
			$xml.="
						<Provincia>".$document->customer->city->province_district_code."</Provincia>";
		endif;
		$xml.="
						<Nazione>".$document->customer->city->iso2."</Nazione>

					</Sede>";

		if($document->customer->customer_tax_representative_firm_name!='' and $document->customer->customer_tax_representative_vat_country_id!='' and $document->customer->customer_tax_representative_vat_id!=''):
			$xml.="
					<RappresentanteFiscale>
						<IdFiscaleIVA>
							<IdPaese>IT</IdPaese>
							<IdCodice>".$document->customer->customer_tax_representative_vat_id."</IdCodice>
						</IdFiscaleIVA>
						<Denominazione><![CDATA[".htmlentities($document->customer->customer_tax_representative_firm_name)."]]></Denominazione>
					</RappresentanteFiscale>
			";
		endif;

		$xml.="
				</CessionarioCommittente>

			</FatturaElettronicaHeader>

			<FatturaElettronicaBody>

				<DatiGenerali>

					<DatiGeneraliDocumento>

						<TipoDocumento>".$document_type."</TipoDocumento>
						<Divisa>EUR</Divisa>
						<Data>".$document->date."</Data>
						<Numero>".$number."</Numero>";
		if($document->stamp_duty>0):
			$xml.="
						<DatiBollo>
							<BolloVirtuale>SI</BolloVirtuale>
							<ImportoBollo>".$document->stamp_duty."</ImportoBollo>
						</DatiBollo>
			";
		endif;

		$xml.="
						<ImportoTotaleDocumento>".$document->total."</ImportoTotaleDocumento>

					</DatiGeneraliDocumento>

				</DatiGenerali>

				<DatiBeniServizi>";
		
		$rows_by_percentage_and_exemption=[];
		foreach($document->$body_location['description'] as $index=>$description):

			$vat_exemption=mysqli_fetch_object($this->db->query("select vat_exemption from archy_tax_rates where id=".$document->$body_location['tax_rate_id'][$index]))->vat_exemption;
			$tax_percentage=$document->$body_location['tax_percentage'][$index];
			$taxable_amount=$document->$body_location['amount'][$index];
			$tax_amount=$document->$body_location['tax'][$index];
			if(isset($rows_by_percentage_and_exemption[$tax_percentage][$vat_exemption])):
				$rows_by_percentage_and_exemption[$tax_percentage][$vat_exemption]['taxable_amount']+=$taxable_amount;
				$rows_by_percentage_and_exemption[$tax_percentage][$vat_exemption]['tax']+=$tax_amount;
			else:
				$rows_by_percentage_and_exemption[$tax_percentage][$vat_exemption]=[ 'taxable_amount'=>$taxable_amount, 'tax'=>$tax_amount, 'exemption_nature'=>$vat_exemption ];
			endif;
			
			$xml.="
					<DettaglioLinee>

						<NumeroLinea>".($index+1)."</NumeroLinea>
						<Descrizione><![CDATA[".htmlentities($description)."]]></Descrizione>
						<Quantita>".$this->decimals($document->$body_location['quantity'][$index])."</Quantita>
						<UnitaMisura>PZ</UnitaMisura>
						<PrezzoUnitario>".$this->decimals($document->$body_location['price'][$index])."</PrezzoUnitario>
						<PrezzoTotale>".$this->decimals($taxable_amount)."</PrezzoTotale>
						<AliquotaIVA>".$this->decimals($tax_percentage)."</AliquotaIVA>";
			if($tax_percentage==0):
				$xml.="
						<Natura>".$vat_exemption."</Natura>
				";
			endif;
			$xml.="
					</DettaglioLinee>";

		endforeach;

		foreach($rows_by_percentage_and_exemption as $tax_percentage=>$level):
			foreach($level as $exemption=>$data):

				$xml.="
					<DatiRiepilogo>
						<AliquotaIVA>".$this->decimals($tax_percentage)."</AliquotaIVA>";
				if($exemption!=''):
					$xml.="
						<Natura>".$exemption."</Natura>
					";
				endif;
				$xml.="
						<ImponibileImporto>".$this->decimals($data['taxable_amount'])."</ImponibileImporto>
						<Imposta>".$this->decimals($data['tax'])."</Imposta>
						<EsigibilitaIVA>".$document->VAT_collectability."</EsigibilitaIVA>
					</DatiRiepilogo>";
			endforeach;
		endforeach;

		$xml.="
				</DatiBeniServizi>

			</FatturaElettronicaBody>
		</p:FatturaElettronica>";

		$file=fopen($file_path, "w") or die('cannot open file '.$file_path);
		fwrite($file, $xml);
		fclose($file);
	}

    public function sdi_json($document,$document_type,$number,$body_location,$file_path) {

        //var_dump($document); 
        //exit;
        
        $transmission_format=$document->customer->customer_private=='1'? 'FPR12' : 'FPA12';

        if($recipient=$document->customer->customer_sending_method=='1'):
            $pec=$document->customer->customer_sdi_pec;
            if($document->customer->vat_country=='IT' or $document->customer->legal_personality=='2'):
                $code=$document->customer->customer_private=='1'? '0000000' : '000000';
            else:
                $code="0000000";
            endif;
        else:
            $pec='';
            if($document->customer->vat_country=='IT'):
                $code=$document->customer->customer_recipient_code;
            else:
                $code="0000000";
            endif;
        endif;

        $provincia_sede=$document->firm->city->iso2=='IT'? $document->firm->city->province_district_code : '';

        $EORI=$document->firm->EORI!=''? $document->firm->EORI : '';
        $customer_EORI=$document->customer->customer_EORI!=''? $document->customer->customer_EORI : '';

        $f = new stdClass();

        // Header
        $f->fattura_elettronica_header = new stdClass();
        $f->fattura_elettronica_header->dati_trasmissione = new stdClass();

        $f->fattura_elettronica_header->dati_trasmissione->id_trasmittente = new stdClass();
        $f->fattura_elettronica_header->dati_trasmissione->id_trasmittente->id_paese = $document->firm->city->iso2;
        $f->fattura_elettronica_header->dati_trasmissione->id_trasmittente->id_codice = $document->firm->vat_id;
        $f->fattura_elettronica_header->dati_trasmissione->codice_destinatario = $code;

        if($document->firm->phone!='' or $document->firm->email!=''):
            $f->fattura_elettronica_header->dati_trasmissione->contatti_trasmittente = new stdClass();
            if($document->firm->phone!=''):
                $f->fattura_elettronica_header->dati_trasmissione->contatti_trasmittente->telefono = $document->firm->phone;
            endif;
            if($document->firm->email!=''):
                $f->fattura_elettronica_header->dati_trasmissione->contatti_trasmittente->email = $document->firm->email;
            endif;
        endif;

        if ($pec) {
            $f->fattura_elettronica_header->dati_trasmissione->pec_destinatario = $pec;
        }

        // Cedente
        $f->fattura_elettronica_header->cedente_prestatore = new stdClass();
        $f->fattura_elettronica_header->cedente_prestatore->dati_anagrafici = new stdClass();
        $f->fattura_elettronica_header->cedente_prestatore->dati_anagrafici->id_fiscale_iva = new stdClass();
        $f->fattura_elettronica_header->cedente_prestatore->dati_anagrafici->id_fiscale_iva->id_paese = $document->firm->city->iso2;
        $f->fattura_elettronica_header->cedente_prestatore->dati_anagrafici->id_fiscale_iva->id_codice = $document->firm->vat_id;
        $f->fattura_elettronica_header->cedente_prestatore->dati_anagrafici->codice_fiscale = $document->firm->tax_id;

        $f->fattura_elettronica_header->cedente_prestatore->dati_anagrafici->anagrafica = new stdClass();
        $f->fattura_elettronica_header->cedente_prestatore->dati_anagrafici->anagrafica->denominazione = $document->firm->firm_name;
        if ($EORI) {
            $f->fattura_elettronica_header->cedente_prestatore->dati_anagrafici->anagrafica->cod_eori = $EORI;
        }
        // $f->fattura_elettronica_header->cedente_prestatore->dati_anagrafici->anagrafica->nome = '';
        // $f->fattura_elettronica_header->cedente_prestatore->dati_anagrafici->anagrafica->cognome = '';
        // $f->fattura_elettronica_header->cedente_prestatore->dati_anagrafici->anagrafica->titolo = '';

        if($document->firm->professional_register_name!='' and $document->firm->professional_register_province!='' and $document->firm->professional_register_subscription_number!='' and $document->firm->professional_register_subscription_date!=''):
            $f->fattura_elettronica_header->cedente_prestatore->dati_anagrafici->albo_professionale = $document->firm->professional_register_name;
            $f->fattura_elettronica_header->cedente_prestatore->dati_anagrafici->provincia_albo = $document->firm->professional_register_province;
            $f->fattura_elettronica_header->cedente_prestatore->dati_anagrafici->numero_iscrizione_albo = $document->firm->professional_register_subscription_number;
            $f->fattura_elettronica_header->cedente_prestatore->dati_anagrafici->data_iscrizione_albo = $document->firm->stable_org_city->professional_register_subscription_date;
        endif;

        $f->fattura_elettronica_header->cedente_prestatore->dati_anagrafici->regime_fiscale = $document->firm->tax_regime;

        $f->fattura_elettronica_header->cedente_prestatore->sede = new stdClass();
        $f->fattura_elettronica_header->cedente_prestatore->sede->indirizzo = $document->firm->address;
        // $f->fattura_elettronica_header->cedente_prestatore->sede->numero_civico = '';
        $f->fattura_elettronica_header->cedente_prestatore->sede->cap = $document->firm->zip;
        $f->fattura_elettronica_header->cedente_prestatore->sede->comune = $document->firm->city->city;
        $f->fattura_elettronica_header->cedente_prestatore->sede->provincia = $provincia_sede;
        $f->fattura_elettronica_header->cedente_prestatore->sede->nazione = $document->firm->city->iso2;

        if($document->firm->stable_org_address!='' and $document->firm->stable_org_zip!='' and $document->firm->stable_org_city_id!=''):

            $f->fattura_elettronica_header->cedente_prestatore->stabile_organizzazione = new stdClass();
            $f->fattura_elettronica_header->cedente_prestatore->stabile_organizzazione->indirizzo = $document->firm->stable_org_address;
            $f->fattura_elettronica_header->cedente_prestatore->stabile_organizzazione->cap = $document->firm->stable_org_zip;
            $f->fattura_elettronica_header->cedente_prestatore->stabile_organizzazione->comune = $document->firm->stable_org_city->city;
            $f->fattura_elettronica_header->cedente_prestatore->stabile_organizzazione->provincia = $document->firm->stable_org_city->province_district_code;
            $f->fattura_elettronica_header->cedente_prestatore->stabile_organizzazione->nazione = 'IT';
        endif;

        if($document->firm->rea_office!='' and $document->firm->rea_number!='' and $document->firm->rea_share_capital!='' and $document->firm->rea_sole_shareholder!='' and $document->firm->rea_in_liquidation!=''):
            $f->fattura_elettronica_header->cedente_prestatore->iscrizione_rea = new stdClass();
            $f->fattura_elettronica_header->cedente_prestatore->iscrizione_rea->ufficio = $document->firm->rea_office;
            $f->fattura_elettronica_header->cedente_prestatore->iscrizione_rea->numero_rea = $document->firm->rea_number;
            $f->fattura_elettronica_header->cedente_prestatore->iscrizione_rea->capitale_sociale = $document->firm->rea_share_capital;
            $f->fattura_elettronica_header->cedente_prestatore->iscrizione_rea->socio_unico = $document->firm->rea_sole_shareholder;
            $f->fattura_elettronica_header->cedente_prestatore->iscrizione_rea->stato_liquidazione = $document->firm->rea_in_liquidation;
        endif;

        if($document->firm->phone!='' or $document->firm->fax!='' or $document->firm->email!=''):
            $f->fattura_elettronica_header->cedente_prestatore->contatti = new stdClass();
            if($document->firm->phone!=''):
                $f->fattura_elettronica_header->cedente_prestatore->telefono = $document->firm->phone;
            endif;
            if($document->firm->fax!=''):
                $f->fattura_elettronica_header->cedente_prestatore->fax = $document->firm->fax;
            endif;
            if($document->firm->email!=''):
                $f->fattura_elettronica_header->cedente_prestatore->email = $document->firm->email;
            endif;
        endif;

        $f->fattura_elettronica_header->cedente_prestatore->riferimento_amministrazione = $this->name."|".$document->id;

        // Cessionario/committente
        $f->fattura_elettronica_header->cessionario_committente = new stdClass();
        $f->fattura_elettronica_header->cessionario_committente->dati_anagrafici = new stdClass();
        if($document->customer->legal_personality=='1'):
            $f->fattura_elettronica_header->cessionario_committente->dati_anagrafici->id_fiscale_iva = new stdClass();
            $f->fattura_elettronica_header->cessionario_committente->dati_anagrafici->id_fiscale_iva->id_paese = $document->customer->vat_country;
            $f->fattura_elettronica_header->cessionario_committente->dati_anagrafici->id_fiscale_iva->id_codice = $document->customer->vat_id;
        else:
            $f->fattura_elettronica_header->cessionario_committente->dati_anagrafici->codice_fiscale = $document->customer->tax_code;
        endif;

        $f->fattura_elettronica_header->cessionario_committente->dati_anagrafici->anagrafica = new stdClass();
        if($document->customer->legal_personality=='1' or $document->customer->legal_personality=='2'):
            $f->fattura_elettronica_header->cessionario_committente->dati_anagrafici->anagrafica->denominazione = $document->customer->firm_name;
        else:
            $f->fattura_elettronica_header->cessionario_committente->dati_anagrafici->anagrafica->nome = $document->customer->name;
            $f->fattura_elettronica_header->cessionario_committente->dati_anagrafici->anagrafica->cognome = $document->customer->surname;
        endif;

        if ($customer_EORI) {
            $f->fattura_elettronica_header->cessionario_committente->dati_anagrafici->anagrafica->cod_eori = $customer_EORI;
        }

        $f->fattura_elettronica_header->cessionario_committente->sede = new stdClass();
        $f->fattura_elettronica_header->cessionario_committente->sede->indirizzo = str_replace("\n"," ",$document->customer->address);

        if(strlen($document->customer->zip)!=5 or !is_numeric($document->customer->zip)):
            if($document->customer->vat_country=='IT'):
                set_message("danger","Errore nella generazione del file xml per la fatturazione elettronica. Verificare il CAP del cliente intestatario di questa fattura e aggiornare la fattura stessa usando il pulsante \"Aggiorna\"");
            else:
                $document->customer->zip='00000';
            endif;
        endif;
        $f->fattura_elettronica_header->cessionario_committente->sede->cap = $document->customer->zip;
		$f->fattura_elettronica_header->cessionario_committente->sede->comune = $document->customer->city->city;
        if(trim($document->customer->city->province_district_code)!=''):
            $f->fattura_elettronica_header->cessionario_committente->sede->provincia = $document->customer->city->province_district_code;
        endif;
        $f->fattura_elettronica_header->cessionario_committente->sede->nazione = $document->customer->city->iso2;

        if($document->customer->customer_tax_representative_firm_name!='' and $document->customer->customer_tax_representative_vat_country_id!='' and $document->customer->customer_tax_representative_vat_id!=''):
            $f->fattura_elettronica_header->cessionario_committente->rappresentante_fiscale = new stdClass();
            $f->fattura_elettronica_header->cessionario_committente->rappresentante_fiscale->id_fiscale_iva = new stdClass();
            $f->fattura_elettronica_header->cessionario_committente->rappresentante_fiscale->id_fiscale_iva->id_paese = 'IT';
            $f->fattura_elettronica_header->cessionario_committente->rappresentante_fiscale->id_fiscale_iva->id_codice = $document->customer->customer_tax_representative_vat_id;
            $f->fattura_elettronica_header->cessionario_committente->rappresentante_fiscale->denominazione = $document->customer->customer_tax_representative_firm_name;
        endif;

        // Body
        $f->fattura_elettronica_body = [];

        $fbody = new stdClass();
        $fbody->dati_generali = new stdClass();
        $fbody->dati_generali->dati_generali_documento = new stdClass();

        $fbody->dati_generali->dati_generali_documento->tipo_documento = $document_type;
        $fbody->dati_generali->dati_generali_documento->divisa = 'EUR';
        $fbody->dati_generali->dati_generali_documento->data = $document->date;
        $fbody->dati_generali->dati_generali_documento->numero = $number;
        if($document->stamp_duty>0):
            $fbody->dati_generali->dati_generali_documento->dati_bollo = new stdClass();
            $fbody->dati_generali->dati_generali_documento->dati_bollo->bollo_virtuale = 'SI';
            $fbody->dati_generali->dati_generali_documento->dati_bollo->importo_bollo = $document->stamp_duty;
        endif;
        $fbody->dati_generali->dati_generali_documento->importo_totale_documento = $document->total;

        $fbody->dati_beni_servizi = new stdClass();

        $fbody->dati_beni_servizi->dettaglio_linee = [];

        $rows_by_percentage_and_exemption=[];
        foreach($document->$body_location['description'] as $index=>$description):

            $vat_exemption=mysqli_fetch_object($this->db->query("select vat_exemption from archy_tax_rates where id=".$document->$body_location['tax_rate_id'][$index]))->vat_exemption;
            $tax_percentage=$document->$body_location['tax_percentage'][$index];
            $taxable_amount=$document->$body_location['amount'][$index];
            $tax_amount=$document->$body_location['tax'][$index];
            if(isset($rows_by_percentage_and_exemption[$tax_percentage][$vat_exemption])):
                $rows_by_percentage_and_exemption[$tax_percentage][$vat_exemption]['taxable_amount']+=$taxable_amount;
                $rows_by_percentage_and_exemption[$tax_percentage][$vat_exemption]['tax']+=$tax_amount;
            else:
                $rows_by_percentage_and_exemption[$tax_percentage][$vat_exemption]=[ 'taxable_amount'=>$taxable_amount, 'tax'=>$tax_amount, 'exemption_nature'=>$vat_exemption ];
            endif;

            $linea = new stdClass();
            $linea->numero_linea = $index+1;
            $linea->descrizione = $description;
            $linea->quantita = $this->decimals($document->$body_location['quantity'][$index]);
            $linea->unita_misura = 'PZ';
            $linea->prezzo_unitario = $this->decimals($document->$body_location['price'][$index]);
            $linea->prezzo_totale = $this->decimals($taxable_amount);
            $linea->aliquota_iva = $this->decimals($tax_percentage);
            if($tax_percentage==0):
                $linea->natura =  $vat_exemption;
            endif;

            $fbody->dati_beni_servizi->dettaglio_linee[] = $linea;
        endforeach;

        $fbody->dati_beni_servizi->dati_riepilogo = [];

        foreach($rows_by_percentage_and_exemption as $tax_percentage=>$level):
            foreach($level as $exemption=>$data):
                $riepilogo = new stdClass();
                $riepilogo->aliquota_iva = $this->decimals($tax_percentage);
                $riepilogo->imponibile_importo = $this->decimals($data['taxable_amount']);
                $riepilogo->imposta = $this->decimals($data['tax']);
                $riepilogo->esigibilita_iva = $document->VAT_collectability;
                // if($exemption!=''):
                if($riepilogo->aliquota_iva==0):
                    $riepilogo->natura = $exemption;
                endif;

                $fbody->dati_beni_servizi->dati_riepilogo[] = $riepilogo;
            endforeach;
        endforeach;

        /*
        <DatiPagamento>
            <CondizioniPagamento>TP01</CondizioniPagamento>
            <DettaglioPagamento>
                <ModalitaPagamento>MP01</ModalitaPagamento>
                <DataScadenzaPagamento>2015-01-30</DataScadenzaPagamento>
                <ImportoPagamento>6.10</ImportoPagamento>
            </DettaglioPagamento>
        </DatiPagamento>
        */

        $pagamento = new stdClass();
        if(!empty($document->payment_cond_type)){
            $pagamento->condizioni_pagamento = $document->payment_cond_type;
        }
        $pagamento->dettaglio_pagamento = [];

        $m_pagamento = new stdClass();
        if(!empty($document->payment_method_code)){
            $m_pagamento->modalita_pagamento = $document->payment_method_code;
        }

        $m_pagamento->importo_pagamento = $document->total;
        
        if(!empty($document->due_date) && $document->due_date != '0000-00-00'){
            $m_pagamento->data_scadenza_pagamento = $document->due_date;
        }

        $ret = $this->db->query("SELECT iban FROM archy_banks WHERE id=".$document->bank_id);
        if($ret != FALSE) {
            $iban=mysqli_fetch_object($ret)->iban;
            if($iban!=null){
                $m_pagamento->iban = $iban;
            }
        }
        
        $pagamento->dettaglio_pagamento[] = $m_pagamento;

        if(!empty($document->payment_cond_type) && !empty($document->payment_method_code) && !empty($iban)) {
            $fbody->dati_pagamento[] = $pagamento;
        }

        $f->fattura_elettronica_body[] = $fbody;

        $json=json_encode($f,JSON_PRETTY_PRINT);
        // echo $json;

        $file=fopen($file_path, "w") or die('cannot open file '.$file_path);
        fwrite($file, $json);
        fclose($file);
    }

	public function decimals($n){
		return number_format($n,2,'.','');
	}

	public function store_sdi_invoice($payload,$suppress_notice=false,$check_for_duplicates=false){

		$vat_id=$payload->fattura_elettronica_header->cedente_prestatore->dati_anagrafici->id_fiscale_iva->id_codice;
		$vat_country=$payload->fattura_elettronica_header->cedente_prestatore->dati_anagrafici->id_fiscale_iva->id_paese;
		$vat_country_id=mysqli_fetch_object($this->db->query("select id from archy_countries where iso2='".$vat_country."'"))->id;
		$supplier=$this->db->query("select id from archy_registry where vat_country_id='".$vat_country_id."' and vat_id='".$vat_id."'");
		
		if($supplier->num_rows==0):
			$registry=new Registry();
			$supplier_id=$registry->add_sdi_supplier($payload);
			$new_supplier=true;
		else:
			$supplier_id=mysqli_fetch_object($supplier)->id;
			$new_supplier=false;
		endif;

		$supplier=new Registry($supplier_id);
		
		$body=$payload->fattura_elettronica_body[0];

		$invoice_number=$body->dati_generali->dati_generali_documento->numero;
		$invoice_date=$body->dati_generali->dati_generali_documento->data;

		$taxable_amount=$vat=0;
		foreach($body->dati_beni_servizi->dati_riepilogo as $data):
			$taxable_amount+=$data->imponibile_importo;
			$vat+=$data->imposta;
		endforeach;

		$total=$taxable_amount+$vat;

		if($check_for_duplicates===true):
			$found=$this->db->query("select id from archy_payables where supplier_id=".$supplier_id." and number='".addslashes($invoice_number)."'")->num_rows;
			if($found>0):
				return;
			endif;
		endif;

		$document_type=$body->dati_generali->dati_generali_documento->tipo_documento;

		if($document_type=='TD04'):

			$table='archy_credit_notes_from_sdi';
			$dump_column='sdi_payload';
			$document_name='nota di credito';
			
		else:

			$table='archy_payables';
			$dump_column='sdi_invoice';
			$document_name='fattura';

		endif;

		$this->db->query("
			insert into ".$table." set
			date='".$invoice_date."',
			number='".addslashes($invoice_number)."',
			supplier_id=".$supplier_id.",
			taxable_amount=".$taxable_amount.",
			vat=".$vat.",
			total=".$total.",
			unpaid=".$total.",
			".$dump_column."='".addslashes(json_encode($payload))."'
		");

		if(!$suppress_notice):
			$notice_text="Ricevuta ".$document_name." ".$invoice_number." del ".App::latin_date($invoice_date)." da ".$supplier->label;
			if($document_name=='nota di credito'):
				$notice_text.=" - Collega una fattura passiva a questa nota di credito per aggiornare i saldi della stessa.";
			endif;
			if($new_supplier):
				$notice_text.=" (&Egrave; stata creata una anagrafica per il nuovo fornitore).";
			endif;
			$notice=new Notice();
			$notice_to_add=[
				'notice'=>$notice_text,
				'intent'=>'archy_notices',
				'intent_id'=>'0'
			];
			$notice->set($notice_to_add);
		endif;

		@$this->count_payables++;

	}

	public function import_payables(){
		
		$before=$_POST['before']!=''? App::db_date($_POST['before']) : NULL;
		$after=$_POST['after']!=''? App::db_date($_POST['after']) : NULL;
		$vat_id=$_POST['vat_id'];

        //$vat_id='09876543211';
        //$since=NULL;

        if($vat_id) {
            $acube=new Acube();
            $collection=$acube->invoices_collection($vat_id,$before,$after);

            $this->count_payables=0;

            $pagination=$collection->{'hydra:view'};
            //error_log("@@@@@@@@@@@@@@@@@@@ import_payables - view: ".print_r($pagination,true));
            $totalItems=$collection->{'hydra:totalItems'};
            //error_log("@@@@@@@@@@@@@@@@@@@ import_payables - total items: ".$totalItems);

            foreach($collection->{'hydra:member'} as $data):
                //error_log("@@@@@@@@@@@@@@@@@@@ import_payables - payload raw: ".print_r($data->payload,true));
                $payload=json_decode($data->payload);
                //error_log("@@@@@@@@@@@@@@@@@@@ import_payables - payload decode: ".print_r($payload,true));
                //error_log("@@@@@@@@@@@@@@@@@@@ import_payables - codice_destinatario: ".$payload->fattura_elettronica_header->dati_trasmissione->codice_destinatario);
                
                $body=$payload->fattura_elettronica_body[0];
                $document_type=$body->dati_generali->dati_generali_documento->tipo_documento;
                $this->store_sdi_invoice($payload,$suppress_notice=true,$check_for_duplicates=true);
            endforeach;

            set_message("info","Sono stati importati ".$this->count_payables." documenti.");
            header('location: '.$_SERVER['HTTP_REFERER']);
        } else {
            set_message("error","Non sono stati importati documenti. Controllare Partita IVA.");
            header('location: '.$_SERVER['HTTP_REFERER']);
        }

	}

}
?>
