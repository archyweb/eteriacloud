<?php

class ExtraActions{

    public $config, $options, $config_menu;
    public $auth;
    public $admin_template, $admin_template_child;
    public $subdomain_root;
    public $subdomain_url;
    public $main_root;
    public $protocol;
    
    function __construct(string $subdomain_root){
	    $this->subdomain_root=$subdomain_root;
        $this->subdomain_root=$subdomain_root;
        $this->subdomain_url = $_SERVER['HTTP_HOST'];

        $this->config=require $subdomain_root.'/config-local.php';
        $this->main_root=dirname(dirname(dirname($subdomain_root)));
        $this->admin_template= $this->subdomain_root.'/admin_template';
        $this->admin_template_child= $this->subdomain_root.'/admin_template_child';
        $this->options=require $this->main_root.'/private/_common/config.php';
        $this->config_menu=require $this->main_root.'/private/_common/config_menu.php';
        $this->protocol = $this->config['domain_ssl'] ? 'https' : 'http';
        
        //var_dump($this->options);

        $this->init_auth();
	}
        
        

	public function get_root_url() {
    	return $this->config['root_url'];
	}

	public function get_subdomain_url() {
		$protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https'?'https':'http';
		return $protocol . "://" . $this->subdomain_url;
	}

    public function init_auth() {
        $dbh = new PDO("mysql:host=" . $this->config['db']['host'] . ";dbname=" . $this->config['db']['name'], $this->config['db']['user'], $this->config['db']['pass']);
        $auth_config = new PHPAuth\Config($dbh);
        $rootUrl = rtrim($this->config['root_url'] ?? $this->protocol . '://' . $_SERVER['HTTP_HOST'], '/');
        $auth_config->config['site_url'] = $rootUrl;
        $auth_config->config['site_name'] = $this->config['customer_name'] ?? 'Archy Art';
        $auth_config->config['site_password_reset_page'] = 'archy/login.php?mode=reset-password';
        $auth_config->config['site_password_reset_page_append_code'] = 0;

        $mailSettings = [];
        $settingsQuery = $dbh->query("SELECT `name`, `value` FROM archy_settings WHERE `domain` = 'notifications'");

        foreach ($settingsQuery->fetchAll(PDO::FETCH_ASSOC) as $setting) {
            $mailSettings[$setting['name']] = $setting['value'];
        }

        $rootHost = parse_url($rootUrl, PHP_URL_HOST) ?: ($_SERVER['HTTP_HOST'] ?? 'localhost');
        $mailSettings['sender_name'] = !empty($mailSettings['sender_name']) ? $mailSettings['sender_name'] : ($this->config['customer_name'] ?? 'Archy Art');
        $mailSettings['sender_email'] = !empty($mailSettings['sender_email']) ? $mailSettings['sender_email'] : 'noreply@' . $rootHost;
        $mailSettings['mail_transport'] = $this->config['mail_transport'] ?? 'auto';
        $mailSettings['mail_smtp_allow_self_signed'] = $this->config['mail_smtp_allow_self_signed'] ?? (($this->config['env'] ?? '') !== 'production');

        $auth_config->setMailer(new PHPAuth\Mailer\ArchyPHPAuthMailer($mailSettings));

        $this->auth  = new PHPAuth\ArchyAuth($dbh, $auth_config);
    }

    public function isLogged() {
        return $this->auth->isLoggedAuth();
    }

    public function member_extra_action(){
		if($_POST['action']=='insert into'):
			// add to members history
			$membership=new Membership($this->insert_id);
			$membership->add_history_item();
			if(@$_POST['subscription_paid']=='1'):
				$this->db->query("insert into archy_subscriptions (date,member_id,amount) values('".App::db_date($_POST['subscription_date'])."',".$this->insert_id.",".$_POST['subscription_amount'].")");
			endif;
			// generate report
			//$member=$membership->get_member($this->insert_id);
			//$membership->generate_report($model_name='acceptance_report',$report_title='Ammissione socio '.$member->firm_name,$member_id=$this->insert_id);
		endif;
        
        // Se update aggiorno anche la card...
        if($_POST['action']=='update'):
            //error_log("@@@@@@@@@@@@@@ member_extra_action : ExtraAction UPDATE id=".$_POST['intent_id']);
            $membership=new Membership($_POST['intent_id']);
            $membership->create_card($_POST['intent_id']);
		endif;
	}
	
	public function subscription_fee(){
		if($_POST['action']=='insert into'):
			$this->db->query("
				update archy_memberships set 
					status='1',
					membership_begin='".App::db_date($_POST['begin'])."', 
					membership_end='".App::db_date($_POST['end'])."' 
				where id=".$_POST['main']['member_id']
			);
			
			$receipt=new Receipt();
			$new_receipt=[
				'firm_id'=>$_POST['main']['firm_id'],
				'number'=>$this->next_document_number_by_year_and_firm('archy_tax_receipts',$_POST['main']['firm_id'],date('Y'),'first_receipt_number',$ajax=false),
                'date'=>"'".date('Y-m-d')."'",
				'customer_id'=>mysqli_fetch_object($this->db->query("select registry_id from archy_memberships where id=".$_POST['main']['member_id']))->registry_id,
				'receipt'=>"'".addslashes(serialize([
					'description'=>['Versamento quota associativa'],
					'quantity'=>['1'],
					'price'=>[$_POST['main']['amount']],
					'amount'=>[$_POST['main']['amount']]
				]))."'",
				'payment_method_id'=>$_POST['main']['payment_method_id'],
				'total'=>$_POST['main']['amount'],
				'paid'=>$_POST['main']['amount'],
				'payment_status'=>"'paid'"
			];
			$new_receipt=$receipt->add($new_receipt);
			
			$this->db->query("update archy_subscriptions set receipt_id=".$new_receipt->id." where id=".$this->insert_id);
			
			$petty_cash=new PettyCash();
			$new_petty_cash=[
				'date'=>"'".App::db_date($_POST['main']['date'])."'",
                'petty_cash'=>"'Quota associato: ".addslashes($new_receipt->customer->label)."'",
				'petty_cash_purpose_id'=>'2',
				'petty_cash_type_id'=>$_POST['main']['petty_cash_type_id'],
                'amount'=>$_POST['main']['amount'],
                'bulk_payment'=>'0',
				'type'=>'0',
				'document_table'=>"'archy_tax_receipts'",
				'document_id'=>$new_receipt->id
			];
			$new_petty_cash=$petty_cash->add($new_petty_cash);
			
		endif;
	}
	
	public function get_subscription_info(){
		
		$membership=new Membership($_REQUEST['id']);
		$member=$membership->member;
		
		$is_first_membership=$this->db->query("select id from archy_subscriptions where member_id=".$member->id." limit 1")->num_rows==0;
		
		$fees=$membership->get_subscription_fee_list();
		$member->fee_amount=$fees->categories[$member->category_id]->subscription_fee;
		
		$member->membership_begin=App::latin_date($member->membership_begin);
		$member->membership_end=App::latin_date($member->membership_end);
		
		if($is_first_membership && $member->status=='5')://imported members have not subscriptions stored...
			
			//$member->dates_display='none';
			$member->dates_display='block';
			$member->new_begin=$member->membership_begin;
			$member->new_end=$member->membership_end;
                        $member->fee_amount=$membership->get_subscription_fee($member->category_id); // Ricalcolo la fee se c'è il frazionamento 
			
		else:
			
			$member->dates_display='block';
			
			if($member->status=='4'):
				$member->new_begin=date('d/m/Y');
				$member->new_end=App::latin_date($membership->get_first_membership_end_date());
			else:
                                $next_year = date('Y', strtotime("+12 months ".App::db_date($member->membership_end).""));
                                $next_year_type = date('L', mktime(0,0,0, 1, 1, $next_year)); 
                                //$member->next_year=$next_year.'-'.$next_year_type;
                                $days_to_add = '+364 days';
                                if($next_year_type == 1){ $days_to_add = '+365 days'; }
				$end=new DateTime(App::db_date($member->membership_end));
				$end->modify('+1 days');
				$member->new_begin=$end->format('d/m/Y');
				$end->modify($days_to_add);
				$member->new_end=$end->format('d/m/Y');
			endif;
		endif;
		
		echo json_encode($member);
		
	}

    public function get_service_subscription_info(){
		
		$service_membership=new ServiceMembership($_REQUEST['id']);

        $member=$service_membership->membership;
        $member->service_id=$member->service->id;

        $member->membership_begin=App::latin_date($member->membership_begin);
		$member->membership_end=App::latin_date($member->membership_end);

        // Price splitting
        if($member->service->splitting){
            $m = array_values(unserialize($member->service->splitting_months));
            //error_log("@@@@@@@@@@: ".print_r($m,true)." @@@@@ ".date('n'). " @@@@@ ". $m[(date('n')-1)]);
            //error_log("@@@@@@@@@@: ".print_r($_REQUEST,true));

            $mese=date('n');
            if(isset($_REQUEST['data']) && $_REQUEST['data']!=''):
                $time=strtotime(str_replace('/', '-',$_REQUEST['data']));
                $mese=date("n",$time);
            endif;
            $member->service_amount=$m[$mese-1];
        } else {
            $member->service_amount=$member->service->amount;
        }
        
        // Se tipologia 1 (Commerciale) => aggiungo IVA
        if($member->service->accounting_type=='1'){ 
            $percentage=mysqli_fetch_object($this->db->query("SELECT percentage FROM archy_tax_rates WHERE id=".$member->service->tax_rate_id." LIMIT 1"))->percentage;
            $member->service_amount=round($member->service_amount*(1+($percentage/100)),2);
        }

        $member->dates_display='block';
        $member->new_end_readonly=false;
        $member->subscription_list=$service_membership->get_service_subscription_list();

        $is_first_membership=$this->db->query("select id from archy_services_subscriptions where services_memberships_id=".$member->id." limit 1")->num_rows==0;

        /*
        echo "@@@@@@@@@@@@@: is_first_membership: ".$is_first_membership;
        echo "@@@@@@@@@@@@@: member: ".print_r($member,true);
        error_log("@@@@@@ renew_type".$member->service->renew_type);
        error_log("@@@@@@ expire_date".$member->service->expire_date);
        error_log("@@@@@@ mm".substr($member->service->expire_date,5,2));
        error_log("@@@@@@ dd".substr($member->service->expire_date,8,2));
        exit();
        */

        if($is_first_membership): // have not subscriptions yet...
            $d=new DateTime();
            $member->new_begin=$d->format('d/m/Y');
        else:
            $d=new DateTime(App::db_date($member->membership_end));
            $d->modify('+1 days');
            $member->new_begin=$d->format('d/m/Y');
        endif;

        switch($member->service->renew_type) {
            case 0: // 365 giorni
                $next_year = date('Y', strtotime("+12 months ".App::db_date($member->membership_end).""));
                $next_year_type = date('L', mktime(0,0,0, 1, 1, $next_year));
                $days_to_add = '+364 days';
                if($next_year_type == 1){ $days_to_add = '+365 days'; }
                $d=new DateTime(App::db_date($member->new_begin));
                $d->modify($days_to_add);
                $member->new_end=$d->format('d/m/Y');
                break;
            case 1: // Una tantum
                $d=new DateTime("2999-12-31");
                $member->new_end=$d->format('d/m/Y');
                $member->new_end_readonly=true;
                break;
            case 2: // Annuale a data specifica
                $now=new DateTime();

                $mm=substr($member->service->expire_date,5,2);
                $dd=substr($member->service->expire_date,8,2);
                $yy=date('Y');
                $end=new DateTime(sprintf("%d-%02d-%02d",$yy,$mm,$dd));
                
                if(!$is_first_membership || $now>=$end):
                    $yy=date('Y')+1;
                    $end=new DateTime(sprintf("%d-%02d-%02d",$yy,$mm,$dd));
                endif;

                $member->new_end=$end->format('d/m/Y');
                break;
            case 3: // Mensile a data specifica
                $now=new DateTime();

                $mm=substr($member->service->expire_date,5,2);
                $dd=substr($member->service->expire_date,8,2);
                $yy=date('Y');
                $end=new DateTime(sprintf("%d-%02d-%02d",$yy,$mm,$dd));
                
                if(!$is_first_membership || $now>=$end):
                    $mm=date('m')+1;
                    if($mm==13){
                        $mm=1;
                        $yy++;
                    }
                    $end=new DateTime(sprintf("%d-%02d-%02d",$yy,$mm,$dd));
                endif;

                $member->new_end=$end->format('d/m/Y');
                break;
        }
                
		echo json_encode($member);		
	}
	
	public function store_report(){
		$report=new Report();
		$report->store_pdf($_POST['main']['report'],$this->insert_id);		
	}
	
	public function get_report_model(){
		echo mysqli_fetch_object($this->db->query("select report_model from archy_meeting_categories where id=".$_POST['id']))->report_model;
	}
	
    /*
	public function activities_extra_action(){
		$this->db->query("delete from archy_activities_events where activity_id=".$this->insert_id);
		foreach($_POST['event']['title'] as $event_index=>$title):
			$extras='';
			if(isset($_POST['event']['extras'][$event_index])):
				$extras=[];
				foreach($_POST['event']['extras'][$event_index]['description'] as $extra_index=>$description):
					$extras[]=["description"=>$description,"amount"=>$_POST['event']['extras'][$event_index]['amount'][$extra_index]];
				endforeach;
				$extras=serialize($extras);
			endif;
			$this->db->query("
				insert into archy_activities_events set 
					activity_id=".$this->insert_id.",
					title='".addslashes($title)."',
					description='".addslashes($_POST['event']['description'][$event_index])."',
					begin_date='".App::db_date($_POST['event']['begin_date'][$event_index])."',
					end_date='".App::db_date($_POST['event']['end_date'][$event_index])."',
					credits=".$_POST['event']['credits'][$event_index].",
					speaker_id=".$_POST['event']['speaker_id'][$event_index].",
					location_id=".$_POST['event']['location_id'][$event_index].",
					members_price=".$_POST['event']['members_price'][$event_index].",
					non_members_price=".$_POST['event']['non_members_price'][$event_index].",
					certificate='".addslashes($_POST['event']['certificate'][$event_index])."',
					extras='".addslashes($extras)."'
			");
		endforeach;
	}
    */
	

	public function event_certificate(){
		//$activity=new Activity();
		//$activity->event_certificate();
	}
	
	public function activity_certificate(){
		//$activity=new Activity($_GET['activity_id']);
		//$activity->activity_certificate();
	}
	
	public function bulk_event_certificate(){
		//$activity=new Activity();
		//$activity->bulk_event_certificate();
	}
	
	public function bulk_activity_certificate(){
		//$activity=new Activity();
		//$activity->bulk_activity_certificate();
	}

	public function account_for_event(){
            
            $event=new Event();
            
            $event_id=$_POST["event_id"];
            $session_id=$_POST["session_id"];
            $session_title=$_POST["session_title"];
            $subscription_id=$_POST["subscription_id"];
            $subscriber_id=$_POST["subscriber_id"];
            $credits=$_POST["credits"];
         
            $accounted=mysqli_fetch_object($this->db->query("SELECT accounted FROM archy_events_subscriptions WHERE id=".$subscription_id))->accounted;
            if($this->is_serialized($accounted)):
                    $accounted=unserialize($accounted);
            else:
                    $accounted=[];
            endif;
            // controllare se sessione corrente è già in accounted se sì non aggiungere
            $accounted[]=[
                'session_id' => $session_id,
                'session_title' => $session_title,
                'session_credits' => $credits
            ];
            $this->db->query("UPDATE archy_events_subscriptions SET accounted='".addslashes(serialize($accounted))."' WHERE id=".$subscription_id);

            // OTTENGO CREDITI SESSIONE
            $event_session_credits=mysqli_fetch_object($this->db->query("SELECT * FROM archy_events_sessions WHERE event_id=".$event_id." AND id=".$session_id));
            
            $event->update_credits_by_event($event_session_credits->credits,$event_id,$subscriber_id);
            $event->update_earned_rating_id($subscriber_id,$event_session_credits->earned_rating_id);
            
            // chiamo la funzione per la generazione dell'attestato dell'evento se raggiunto il limite dei crediti
            $manage_pdf=new ManagePdf();
            $manage_pdf->init_pdf($subscription_id,'custom_event',array('data_intent'=>'archy_events_subscriptions','session_id'=>$session_id,'subscriber_id'=>$subscriber_id));
            
            echo "OK";
            die();

            $this->db->query("update archy_events_subscriptions set accounted='".addslashes(serialize($headcounts))."' where id=".$_POST['subscription_id']);
	}
        
        public function store_rating_relations(){ // update rating_relations after save
            $rating_rel=mysqli_fetch_object($this->db->query("SELECT * FROM archy_rating_relations WHERE id=".$this->insert_id));            
            $event=new Event();
            $event->update_earned_rating_id($rating_rel->registry_id,$rating_rel->rating_id,'update',$this->insert_id);
        }
	
        public function load_payments_tpl($id=null,$data_payments=''){
            
            $html = '<input type="hidden" name="main[payments][]" value="">'; // this will clear payments field in db if all payments are deleted by user
                 //$html .= "###".$data_payments;        
                if ($data_payments=='') :  
                    //$html .= "###PRIMO STEP"; 
                    if(isset($_POST['add_ajax'])):
                        $html .= include $this->main_root.'/private/_common/intent/includes/events_subscriptions_form_payments_tpl.php';
                    endif;
                else :							
                    $payments = unserialize($data_payments); 
                    //$html .= '### SECONDO STEP'.$payments;
                //$html .= '######'.print_r($payments['payment_date']);
                    if (is_array(@$payments['payment_date'])) :
                        foreach ($payments['payment_date'] as $index => $date) :
                       // $html .= $payments['payment_unique_id'][$index];
                            if(@$payments['payment_unique_id'][$index]!='') :
                                $html .= include $this->main_root.'/private/_common/intent/includes/events_subscriptions_form_payments_tpl_readonly.php';
                            else :	
                                $html .= include $this->main_root.'/private/_common/intent/includes/events_subscriptions_form_payments_tpl.php';
                            endif;
                        endforeach;
                    endif;
                endif;
             
            echo $html;
        }
        
        public function get_rating_name($id){
            $ret=mysqli_fetch_object($this->db->query("SELECT title FROM archy_ratings WHERE id=".$id));
            return $ret->title;
        }
        
        
        
	public function load_events(){
            
            $help_text = '';

        // INFO ATTIVITA'
        if(isset($_GET['id'])):
            $info=mysqli_fetch_object($this->db->query("SELECT * FROM archy_events_subscriptions WHERE id=".$_GET['id']));
            $_POST['event_id']=$info->event_id;
            // DEBUG
            //echo "<pre>";
            //echo "event_extras ".$info->event_extras."<br>";
            //echo "sessions ".$info->sessions."<br>";
            //echo "session_extras ".$info->session_extras."<br>";
            //echo "</pre>";
        endif;
            $cnt=mysqli_fetch_object($this->db->query("SELECT count(*) AS cnt FROM archy_events_subscriptions WHERE event_id=".$_POST['event_id']))->cnt;
		$event=mysqli_fetch_object($this->db->query("SELECT * FROM archy_events WHERE id=".$_POST['event_id']));
		$html='<div><h4>Dettagli evento</h4>
            <p>Crediti totali: '.$event->total_credits.' (Minimi: '.$event->minimum_credits.')</p>
            <p>Iscritti: '.$cnt.' (Massimo: '.$event->max_subscribers.')</p></div>';
            
            
               $html .= '<h4>Sessioni e Extra</h4>
			<label class="btn btn-sm btn-default"><span id="all_events"> Seleziona tutte le sessioni</span></label>
			<label class="btn btn-sm btn-default"><span id="all_extras"> Seleziona tutti gli extra</span></label>
			<div class="events_dl">';
		           
      	
        // EXTRA ATTIVITA'
        $activity_extras=$this->db->query("SELECT * FROM archy_events_extras WHERE domain='event' and domain_id=".$_POST['event_id']);
        $selected_activity_extras=unserialize($info->event_extras);
        while($activity_extra=mysqli_fetch_object($activity_extras)):
            $activity_extra_checked=in_array($activity_extra->id, array_keys($selected_activity_extras))? 'checked' : '';
			$html.='
				<div class="checkbox">
					<label>
						<input type="checkbox" 
                            name="main[event_extras]['.$activity_extra->id.'][activity]" 
                            value="'.form_value($activity_extra->description).'" 
                            class="extra_cbox" '.$activity_extra_checked.' 
							data-amount="'.$activity_extra->price.'">
                        <strong>EXTRA EVENTO:</strong> '.$activity_extra->description.', PREZZO:
						    <price>'.App::currency($activity_extra->price).'</price>
					</label>
				</div>';
        endwhile;
        
        $html.='<hr>';
        
        // SESSIONI ATTIVITA' + EXTRA
        $sessions=$this->db->query("SELECT * FROM archy_events_sessions WHERE event_id=".$_POST['event_id']);
        $selected_sessions=unserialize($info->sessions);
		while($session=mysqli_fetch_object($sessions)):
                                            
                        $rating_name=$this->get_rating_name($session->required_rating_id);
                    
			$session_required_rating_id=$session->required_rating_id ? $rating_name : 'Nessuno';
                        $subscriber_id=$_POST['subscriber_id']; 
                    
                        // CHECK IF SUBSCRIBER IS ALREADY SUBSCRIBED TO THIS SESSION
                        $sessions_subscriber_check=mysqli_fetch_object($this->db->query("SELECT sessions FROM archy_events_subscriptions WHERE event_id=".$_POST['event_id']." AND subscriber_id=".$subscriber_id));
                        $selected_sessions_subscriber_arr=unserialize($sessions_subscriber_check->sessions);
                        $selected_sessions_subscriber_check=in_array($session->id, array_keys($selected_sessions_subscriber_arr))? true : false;
                        
			// CHECK SUBSCRIBER RATING
			$session_check = '';			
			$subscriber_check_rating=$this->load_subscriber_rating($subscriber_id,$session->required_rating_id);
                        if(!$selected_sessions_subscriber_check){ // utente non iscritto alla sessione
                            if($session->required_rating_id!=''){
                                if(!$subscriber_check_rating){$session_check = ' disabled';}
                            }
                        }
                        if($selected_sessions_subscriber_check){ $help_text = '<span class="text-danger"> | Utente già iscritto a questa sessione</span>'; $session_check = ' disabled';  }
			$session_checked=in_array($session->id, array_keys($selected_sessions))? 'checked' : '';
			$html.='
				<div class="checkbox">
					<label>
						<input type="checkbox" 
                                                    name="main[sessions]['.$session->id.'][event]" 
                                                    value="'.form_value($session->title).'" 
                                                    class="event_cbox" '.$session_checked.' 
                                                    data-members_price="'.$session->members_price.'" 
                                                    data-required_rating="'.$session->required_rating_id.'" 
                                                    data-price="'.$session->price.'"'.$session_check.'>
                                                        
                                                <strong>SESSIONE: </strong> '.$session->title.',
                                                    CREDITI: '.$session->credits.', 
                                                    PREZZO: <price>'.App::currency($session->price).'</price>
                                                    (Soci: <members_price>'.App::currency($session->members_price).'</members_price>)
                                                    | Rating richiesto: '.$session_required_rating_id.$help_text.'
					</label>
				</div>';
$html.='<hr>';
            $session_extras=$this->db->query("SELECT * FROM archy_events_extras WHERE domain='session' and domain_id=".$session->id);
			while($session_extra=mysqli_fetch_object($session_extras)):
				$selected_extras=unserialize($info->session_extras);
                $event_checked=in_array($session_extra->id, array_keys($selected_extras))? 'checked' : '';
                $html.='
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" 
                                name="main[session_extras]['.$session_extra->id.'][extras]" 
                                value="'.form_value($session_extra->description).'" 
                                class="extra_cbox" '.$event_checked.'
                                data-amount="'.$session_extra->price.'"> 
                                <strong>EXTRA SESSIONE:</strong> '.$session_extra->description.' 
                             , PREZZO: '.App::currency($session_extra->price).'
                        </label>
                    </div>';
            endwhile;
		endwhile;
		$html.='</div>';
		echo $html;
	}
	
    public function load_sconto_by_subscriber(){ // // ORDER BY rr.id desc
        $available_discounts=[];
		if(isset($_POST['subscriber_id'])):
                    $subscriber_id=$_POST['subscriber_id'];
                    
                    /*$info=mysqli_fetch_object($this->db->query("
                    SELECT d.id, d.title, d.type, d.amount, rr.rating_id, ra.title as rating_title
                      FROM archy_registry r, archy_rating_relations rr 
                      left join archy_discount_relations dr on rr.rating_id = dr.rating_id
                      left join archy_discounts d on d.id = dr.discount_id
                      left join archy_ratings ra on rr.rating_id = ra.id
                     WHERE rr.registry_id = r.id 
                       AND r.id = ".$subscriber_id." 
                     ORDER BY d.amount desc LIMIT 0,1
                     "));*/
                    $available_ratings_arr=$this->load_all_subscriber_rating($subscriber_id);
                    
                    foreach($available_ratings_arr as $rating){
                        $available_ratings[] = $rating;
                        $check_parent=$this->db->query("SELECT parent_rating_id FROM archy_ratings WHERE id=".$rating);
                        $row=mysqli_fetch_object($check_parent);
                        if($row->parent_rating_id!=null){ $available_ratings[] = $row->parent_rating_id; }
                    }
                endif;
                
                $available_ratings=array_unique($available_ratings);
                $available_ratings_list = implode(',', $available_ratings);
              
                    $available_discounts=mysqli_fetch_object($this->db->query("
                        SELECT r.discount_id,d.amount,d.title,d.type,d.id 
                        FROM archy_discount_relations AS r 
                        INNER JOIN archy_discounts as d ON r.discount_id=d.id 
                        WHERE r.rating_id IN (".$available_ratings_list.")
                        ORDER BY d.amount DESC LIMIT 0,1"));
          
        
            echo json_encode($available_discounts);
	}
 
    public function load_sconto(){
        $info=null;
		if(isset($_POST['discount_id'])):
            $discount_id=$_POST['discount_id'];
            //error_log("@@@@@:".$discount_id);
            $info=mysqli_fetch_object($this->db->query("
            SELECT d.id, d.title, d.type, d.amount
              FROM archy_discounts d
             WHERE d.id = ".$discount_id));
        endif;
        //error_log("@@@@@@: ".print_r($info,true));
        echo json_encode($info);
	}
    
    public function load_all_subscriber_rating($subscriber_id){		
            $return = [];       
            $res = $this->db->query("SELECT rating_id FROM archy_rating_relations WHERE registry_id = ".$subscriber_id);
            while($row=mysqli_fetch_object($res)){$return[]=$row->rating_id;}
            return $return;
	}
    
    public function chech_registry_rating($subscriber_id,$rating,$children_ratings=false){ 
        $return = false;     
        if($children_ratings){     
            $check_rating = $this->db->query("SELECT rating_id FROM archy_rating_relations WHERE registry_id=".$subscriber_id." AND FIND_IN_SET(".$rating.",rating_children)");
        }else{
            $check_rating = $this->db->query("SELECT rating_id FROM archy_rating_relations WHERE registry_id=".$subscriber_id." AND rating_id=".$rating);
        }
        if($check_rating->num_rows>0){ $return = true; }
        return $return;
    }
        
    public function load_subscriber_rating($subscriber_id,$required_rating){		
            $return = false;       
            $res = $this->chech_registry_rating($subscriber_id,$required_rating);
            if($res){ $return = true; }
            else{
                $res_parent = $this->chech_registry_rating($subscriber_id,$required_rating,true); 
                if($res_parent){ $return = true; }
                /*$parent_rating_id = mysqli_fetch_object($this->db->query("SELECT parent_rating_id FROM archy_ratings WHERE id=".$required_rating));
                if($parent_rating_id->parent_rating_id!=NULL){ 
                    $check_children_ratings = $this->db->query("SELECT rating_children FROM archy_rating_relations WHERE registry_id=".$subscriber_id." AND rating_id=".$parent_rating_id->parent_rating_id);
                    $res_parent = $this->chech_registry_rating($subscriber_id,$required_rating,$check_children_ratings->rating_children); 
                    if($res_parent){ $return = true; }
                }*/
            }
            return $return;
	}

    public function load_subscriber_status(){
        $info=null;
		if(isset($_POST['subscriber_id'])):
            $subscriber_id=$_POST['subscriber_id'];
            error_log("@@@@@:".$subscriber_id);
            $info=mysqli_fetch_object($this->db->query("
            SELECT m.status
              FROM archy_memberships m
             WHERE m.registry_id = ".$subscriber_id));
        endif;
        $m=new Membership();
        //error_log("@@@@@@: status = ".print_r($info,true)." desc = ".$m->statuses[$info->status]);
        $info->status_desc=$m->statuses[$info->status];
        unset($m);
        echo json_encode($info);
	}

        public function get_cap(){
		$city=new City($_POST['city_id']);
		echo $city->zip;
	}
        
        public function store_esposizione(){
		$label_esposizione = $_POST['main']['anno_esposizione'].' '.$_POST['main']['nome_esposizione'];
		$this->db->query("update archy_art_exhibitions set label_esposizione='".$label_esposizione."' where id=".$this->insert_id);
	}
	
	public function store_registry_types(){
		$types=[];
		if($_POST['main']['is_customer']=='1'):
			$types[]='Cliente';
		endif;
		if($_POST['main']['is_supplier']=='1'):
			$types[]='Fornitore';
		endif;
		if($_POST['main']['is_member']=='1'):
			$types[]='Socio';
		endif;
		$field=count($types)>0? join(', ',$types) : 'Generica';
		
		$registry_label=($_POST['main']['legal_personality']=='0' or $_POST['main']['legal_personality']=='3')?
			$_POST['main']['name'].' '.$_POST['main']['surname']:
			$_POST['main']['firm_name']; 
		
		$this->db->query("update archy_registry set type='".$field."', label='".addslashes($registry_label)."' where id=".$this->insert_id);
	}

    /*
    public function store_events_sessions(){
        var_dump('POST',$_POST);
        var_dump('=====================================================================================================');

        $sessions=$_POST['session'];
        foreach($sessions as $subscription_id => $session) {    
            var_dump('subscription_id',$subscription_id);
            $subscription=mysqli_fetch_object($this->db->query("SELECT * FROM archy_events_subscriptions WHERE id=".$subscription_id));
            var_dump('subscription',$subscription);
            $registry_id=$subscription->subscriber_id;
            var_dump('subscriber_id',$registry_id);
            
            $session_id=$_POST['intent_id'];
            $session_event=mysqli_fetch_object($this->db->query("select credits, earned_rating_id from archy_events_sessions where id=".$session_id));
            var_dump('session_event',$session_event);
            
            // Segno la presenza
            $accounted=$session["accounted"];
            $this->db->query("UPDATE archy_events_subscriptions SET accounted='".$accounted."' WHERE id=".$subscription_id);

            // Aggiungo la qualifica
            $data=mysqli_fetch_object($this->db->query("SELECT count(*) AS cnt FROM archy_rating_relations WHERE registry_id=".$registry_id." AND rating_id=".$session->earned_rating_id));
            var_dump('data',$data);
            if(is_null($data) || $data->cnt==0) {
                $this->db->query("INSERT INTO archy_rating_relations SET registry_id=".$registry_id.", rating_id=".$session_event->earned_rating_id);
            }

            // Non aggiungo invece i crediti, perché vanno calcolati in base a quello che ha fatto...
            var_dump('@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@');
        }

        exit;

	}
    */
	
	public function get_firm_default_tax_rate($echo=true){
		$tax_rate_id=mysqli_fetch_object($this->db->query("select tax_rate_id from archy_firms where id=".$_POST['firm_id']))->tax_rate_id;
		$tax_rate_percent=mysqli_fetch_object($this->db->query("select percentage from archy_tax_rates where id=".$tax_rate_id))->percentage;
		$data=['id'=>$tax_rate_id,'percentage'=>$tax_rate_percent];
		if($echo===true):
			echo json_encode($data);
		else:
			return $data;
		endif;
	}
		
	public function store_invoice(){
		$invoice=new Invoice($this->insert_id);
		$invoice->store();
	}
	public function send_invoice(){
		$invoice=new Invoice($_GET['invoice_id']);
		$invoice->send();
	}
	public function duplicate_invoice(){
		$invoice=new Invoice($_GET['invoice_id']);
		$invoice->duplicate();
	}
	public function send_to_sdi(){
		if(@$_GET['invoice_id']!=''):
			$object=new Invoice($_GET['invoice_id']);
		elseif(@$_GET['credit_note_id']!=''):
			$object=new CreditNote($_GET['credit_note_id']);
		endif;
		$object->send_to_sdi_json();
	}
	public function download_sdi_xml(){
		if(@$_GET['invoice_id']!=''):
			$object=new Invoice($_GET['invoice_id']);
		elseif(@$_GET['credit_note_id']!=''):
			$object=new CreditNote($_GET['credit_note_id']);
		endif;
		$object->download_sdi_xml();
	}
	
	public function store_credit_note(){
		$credit_note=new CreditNote($this->insert_id);
		$credit_note->store();
		
		$this->invoice_balance($credit_note->invoice_id);
	}

	public function send_credit_note(){
		$credit_note=new CreditNote($_GET['credit_note_id']);
		$credit_note->send();
	}
	
	public function petty_cash_book(){
		
		if($_POST['main']['document_table']!='' and $_POST['main']['document_id']!='' and $_POST['action']=='insert into'):
			
			$petty_cash=new PettyCash();
			$petty_cash->on_insert();
			
		endif;		
		
	}
	
	public function store_receipt(){
		$receipt=new Receipt($this->insert_id);
		$receipt->store();
	}
	public function send_receipt(){
		$receipt=new Receipt($_GET['receipt_id']);
		$receipt->send();
	}
	public function duplicate_receipt(){
		$receipt=new Receipt($_GET['receipt_id']);
		$receipt->duplicate();
	}
	
	public function store_passive_receipt(){
		$receipt=new PassiveReceipt($this->insert_id);
		$receipt->store();
	}
	public function duplicate_passive_receipt(){
		$receipt=new PassiveReceipt($_GET['receipt_id']);
		$receipt->duplicate();
	}
	
	public function store_quote(){
		$quote=new Quote($this->insert_id);
		$quote->store();
	}
	public function send_quote(){
		$quote=new Quote($_GET['quote_id']);
		$quote->send();
	}
	
	public function payable_extra_action(){
        $total=$_POST['main']['taxable_amount']+$_POST['main']['vat'];
        // Update totale=imponibile+iva, insoluto
        $this->db->query("update archy_payables set total=".$total.", unpaid=total-paid where id=".$this->insert_id);
        // Update payment_status
        $this->db->query("
            UPDATE archy_payables
            SET payment_status = 
                CASE 
                    WHEN paid<=0
                        THEN 'not_paid'
                    WHEN total<=paid 
                        THEN 'paid'
                    WHEN total>paid
                        THEN 'partially_paid'
                    ELSE payment_status
                END
            WHERE id=".$this->insert_id
        );
	}
	
	public function store_city_label(){
		$label=$_POST['main']['city'];
		if(trim($_POST['main']['province_district_code'])!=''):
			$label.=' ('.$_POST['main']['province_district_code'].')';
		endif;
		$this->db->query("update archy_cities set city_label='".addslashes($label)."' where id=".$this->insert_id);
	}
	
	/*
|--------------------------------------------------------------------------
| ARCHY MEDIA - DUPLICAZIONE SCHEDA CATALOGO
|--------------------------------------------------------------------------
|
|
*/
	public function duplicate_art_catalog(){

    global $app;

    $db = $app->get_db();

    $id = isset($_GET['catalog_id']) ? (int) $_GET['catalog_id'] : 0;

    if(!$id){
        set_message('danger', 'ID scheda catalogo mancante.');
        header('location:/archy/app.php?intent=archy_art_catalog&view=grid');
        exit;
    }

    $q = $db->query("
        SELECT *
        FROM archy_art_catalog
        WHERE id = ".$id."
        LIMIT 1
    ");

    if(!$q || mysqli_num_rows($q) == 0){
        set_message('danger', 'Scheda catalogo da duplicare non trovata.');
        header('location:/archy/app.php?intent=archy_art_catalog&view=grid');
        exit;
    }

    $row = mysqli_fetch_assoc($q);

    /*
    |--------------------------------------------------------------------------
    | ARCHY MEDIA - DUPLICAZIONE SCHEDA CATALOGO + ALLEGATI
    |--------------------------------------------------------------------------
    |
    | Obiettivo:
    | - duplicare la scheda mantenendo gli allegati visibili;
    | - evitare che originale e duplicato condividano gli stessi record
    |   tecnici di archy_files;
    | - aggiornare i campi file / bulk_upload copiati, se contengono ID
    |   dei record archy_files originali.
    |
    | Nota:
    | i file fisici/cloud possono restare gli stessi, ma i record archy_files
    | devono essere nuovi e riferiti alla nuova scheda. In questo modo la
    | cancellazione di un allegato dal duplicato rimuove solo il riferimento
    | del duplicato e non quello dell'originale.
    |
    */

    $native_file_fields = [];

    if(isset($app->intent->columns) && is_array($app->intent->columns)){
        foreach($app->intent->columns as $column_key => $column_conf){
            if(
                isset($column_conf['type']) &&
                in_array($column_conf['type'], ['file', 'bulk_upload'], true)
            ){
                $field_name = str_replace('archy_art_catalog.', '', $column_key);
                $native_file_fields[] = $field_name;
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER - RIMAPPA ID FILE NEI VALORI SALVATI
    |--------------------------------------------------------------------------
    |
    | Alcuni campi upload possono contenere:
    | - ID singolo;
    | - array serializzato;
    | - lista CSV;
    | - valori misti.
    |
    | Se il valore contiene l'ID del vecchio record archy_files, lo sostituiamo
    | con l'ID del nuovo record archy_files creato per il duplicato.
    |
    */
    $remap_file_value = function($value, $file_id_map) use (&$remap_file_value){

        if($value === null || $value === '' || empty($file_id_map)){
            return $value;
        }

        $unserialized = @unserialize($value);

        if($unserialized !== false || $value === 'b:0;'){

            $walk = function($item) use (&$walk, $file_id_map){
                if(is_array($item)){
                    foreach($item as $k => $v){
                        $item[$k] = $walk($v);
                    }
                    return $item;
                }

                if(is_numeric($item)){
                    $old_id = (int) $item;
                    if(isset($file_id_map[$old_id])){
                        return (string) $file_id_map[$old_id];
                    }
                }

                return $item;
            };

            return serialize($walk($unserialized));
        }

        if(is_numeric($value)){
            $old_id = (int) $value;
            return isset($file_id_map[$old_id]) ? (string) $file_id_map[$old_id] : $value;
        }

        /*
        |--------------------------------------------------------------------------
        | CSV / LISTE SEMPLICI
        |--------------------------------------------------------------------------
        */
        if(strpos($value, ',') !== false){
            $parts = explode(',', $value);
            foreach($parts as $i => $part){
                $part_trim = trim($part);
                if(is_numeric($part_trim)){
                    $old_id = (int) $part_trim;
                    if(isset($file_id_map[$old_id])){
                        $parts[$i] = str_replace($part_trim, (string) $file_id_map[$old_id], $part);
                    }
                }
            }
            return implode(',', $parts);
        }

        return $value;
    };

    /*
    |--------------------------------------------------------------------------
    | 1. DUPLICA RECORD PRINCIPALE
    |--------------------------------------------------------------------------
    |
    | Copiamo inizialmente anche i campi file/bulk_upload. Dopo aver duplicato
    | i record archy_files, li aggiorniamo con i nuovi ID tecnici se necessario.
    |
    */
    unset($row['id']);

    $fields = [];
    $values = [];

    foreach($row as $field => $value){

        $fields[] = "`".$db->real_escape_string($field)."`";

        if($value === null){
            $values[] = "NULL";
        }else{
            $values[] = "'".$db->real_escape_string($value)."'";
        }
    }

    $db->query("
        INSERT INTO archy_art_catalog
        (".implode(',', $fields).")
        VALUES
        (".implode(',', $values).")
    ");

    $new_id = (int) $db->insert_id;

    if(!$new_id){
        set_message('danger', 'Errore durante la duplicazione della scheda catalogo.');
        header('location:/archy/app.php?intent=archy_art_catalog&view=grid');
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | 2. DUPLICA RECORD ARCHY_FILES
    |--------------------------------------------------------------------------
    |
    | Creiamo nuovi record archy_files per la scheda duplicata e conserviamo
    | la mappa:
    |
    | vecchio_id_file => nuovo_id_file
    |
    */
    $file_id_map = [];

    $files_q = $db->query("
        SELECT *
        FROM archy_files
        WHERE file_reference_id = ".$id."
        AND file_reference_table = 'archy_art_catalog'
    ");

    if($files_q){
        while($file_row = mysqli_fetch_assoc($files_q)){

            $old_file_id = isset($file_row['id']) ? (int) $file_row['id'] : 0;

            unset($file_row['id']);

            $file_row['file_reference_id'] = $new_id;
            $file_row['file_reference_table'] = 'archy_art_catalog';

            $file_fields = [];
            $file_values = [];

            foreach($file_row as $file_field => $file_value){
                $file_fields[] = "`".$db->real_escape_string($file_field)."`";

                if($file_value === null){
                    $file_values[] = "NULL";
                }else{
                    $file_values[] = "'".$db->real_escape_string($file_value)."'";
                }
            }

            $db->query("
                INSERT INTO archy_files
                (".implode(',', $file_fields).")
                VALUES
                (".implode(',', $file_values).")
            ");

            $new_file_id = (int) $db->insert_id;

            if($old_file_id > 0 && $new_file_id > 0){
                $file_id_map[$old_file_id] = $new_file_id;
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 3. AGGIORNA CAMPI FILE NATIVI DELLA NUOVA SCHEDA
    |--------------------------------------------------------------------------
    |
    | Se il valore del campo contiene ID dei vecchi record archy_files,
    | li sostituiamo con i nuovi ID.
    |
    */
    if(!empty($native_file_fields) && !empty($file_id_map)){

        $updates = [];

        foreach($native_file_fields as $field_name){

            if(array_key_exists($field_name, $row)){
                $new_value = $remap_file_value($row[$field_name], $file_id_map);
                $updates[] = "`".$db->real_escape_string($field_name)."` = '".$db->real_escape_string((string)$new_value)."'";
            }
        }

        if(!empty($updates)){
            $db->query("
                UPDATE archy_art_catalog
                SET ".implode(', ', $updates)."
                WHERE id = ".$new_id."
                LIMIT 1
            ");
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 4. DUPLICA CAMPI AGGIUNTIVI
    |--------------------------------------------------------------------------
    |
    | Copiamo anche i campi aggiuntivi file/bulk_upload, ma rimappiamo gli
    | eventuali ID dei vecchi record archy_files.
    |
    */
    $afv_q = $db->query("
        SELECT
            afv.domain_id,
            afv.value,
            af.type
        FROM additional_fields_values afv
        LEFT JOIN additional_fields_domain afd ON afd.id = afv.domain_id
        LEFT JOIN additional_fields af ON af.id = afd.field_id
        WHERE afv.record_id = ".$id."
    ");

    if($afv_q){
        while($afv_row = mysqli_fetch_assoc($afv_q)){

            $afv_value = $afv_row['value'];

            if(in_array($afv_row['type'], ['file', 'bulk_upload'], true)){
                $afv_value = $remap_file_value($afv_value, $file_id_map);
            }

            $db->query("
                INSERT INTO additional_fields_values
                (
                    domain_id,
                    record_id,
                    value
                )
                VALUES
                (
                    ".(int)$afv_row['domain_id'].",
                    ".$new_id.",
                    ".($afv_value === null ? "NULL" : "'".$db->real_escape_string($afv_value)."'")."
                )
            ");
        }
    }

    set_message('success', 'Scheda catalogo duplicata correttamente.');

    header('location:/archy/app.php?intent=archy_art_catalog&view=edit&id='.$new_id);
    exit;
}




/*
|--------------------------------------------------------------------------
| ARCHY MEDIA - OLD STORE CREDENTIALS
|--------------------------------------------------------------------------
|
| Versione originale senza gestione ruoli.
|
*/

/*
public function store_credentials(){		
    $password=$_POST['main']['password'];
    $email=$_POST['main']['email'];		
    $old_email=$_POST['email'];

    $this->auth->addOrUpdateUser($old_email, $email, $password);
}
*/

/*
|--------------------------------------------------------------------------
| ARCHY MEDIA - STORE USER CREDENTIALS + ROLES
|--------------------------------------------------------------------------
|
| Gestione:
| - aggiornamento credenziali utente;
| - salvataggio ruoli utente;
|
*/


public function store_credentials()
{
    /*
    |--------------------------------------------------------------------------
    | CREDENZIALI UTENTE
    |--------------------------------------------------------------------------
    */
	
	global $app;
	$db = $app->db;

    $password = $_POST['main']['password'] ?? '';
    $email = $_POST['main']['email'] ?? '';
    $old_email = $_POST['email'] ?? $email;
    $is_new_auth_user = !$this->auth->getUID($old_email);
    $referer = $_SERVER['HTTP_REFERER'] ?? '/';

    if ($is_new_auth_user && $password === '') {
        set_message('error', 'Inserisci una password per creare il nuovo utente.');
        header('location: '.$referer);
        exit;
    }

    if ($password !== '') {
        $validation = $this->auth->validateStrictPassword($password, $email);

        if (!$validation['valid']) {
            set_message('error', $validation['message']);
            header('location: '.$referer);
            exit;
        }
    }

    $credentials = $this->auth->addOrUpdateUser($old_email, $email, $password);

    if (!empty($credentials['error'])) {
        set_message('error', $credentials['message']);
        header('location: '.$referer);
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | ID UTENTE
    |--------------------------------------------------------------------------
    */

    $user_id = 0;

    if (isset($_POST['intent_id']) && (int) $_POST['intent_id'] > 0) {
        $user_id = (int) $_POST['intent_id'];
    } elseif (isset($this->insert_id) && (int) $this->insert_id > 0) {
        $user_id = (int) $this->insert_id;
    } elseif (isset($_GET['id']) && (int) $_GET['id'] > 0) {
        $user_id = (int) $_GET['id'];
    }

    if ($user_id <= 0) {
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | RUOLI UTENTE
    |--------------------------------------------------------------------------
    */

    require_once __DIR__ . '/ArchyUserRoles.php';

    $roles = $_POST['user_roles'] ?? [];

global $app;

$db = $app->db;

$rolesManager = new ArchyUserRoles($db);
$rolesManager->saveUserRoles($user_id, $roles);

    /*
    |--------------------------------------------------------------------------
    | MENU VIETATI UTENTE
    |--------------------------------------------------------------------------
    */

    require_once __DIR__ . '/ArchyUserMenus.php';

    $menus = $_POST['user_menus'] ?? [];

    if (!is_array($menus)) {
        $menus = [$menus];
    }
$menusManager = new ArchyUserMenus($db);
$menusManager->saveUserMenus($user_id, $menus);
}
	
	public function store_message_attachments(){
		if(isset($_POST['temp_folder'])):
			rename( $this->subdomain_root.$_POST['temp_folder'] , $this->subdomain_root.'/assets/documents/attachments/'.$this->insert_id );
			$stored_attachments=mysqli_fetch_object($this->db->query("select attachments from archy_messages where id=".$this->insert_id))->attachments;
			if($this->is_serialized($stored_attachments)):
				$stored_attachments=unserialize($stored_attachments);
				foreach($stored_attachments as $index=>$file_path):
					$stored_attachments[$index]=str_replace($_POST['temp_folder'],'/assets/documents/attachments/'.$this->insert_id,$file_path);
				endforeach;
				$this->db->query("update archy_messages set attachments='".serialize($stored_attachments)."' where id=".$this->insert_id);
			endif;
		endif;
	}
	
	public function registry_categories(){
		if($_POST['main']['domain']=='memberships'):  
            
            // registro importo annuale fee e (se abilitato lo splitting)
            
            $fee=$_POST['subscription_fee'];
            $is_splitted=isset($_POST['main']['splitting']) ? $_POST['main']['splitting'] : 0;
            $splitting_months=isset($_POST['splitting_months']) ? serialize($_POST['splitting_months']) : "";

            $qp="update archy_registry_categories set subscription_fee=?,splitting=?,splitting_months=? where id=?";
            $stmt=$this->db->prepare($qp);
            $stmt->bind_param('disi', $fee, $is_splitted, $splitting_months, $this->insert_id);
            $status=$stmt->execute();
            if ($status === false) {
                show_query_error($status,$this->db,$qp);
            }

        endif;
	}
	
	public function board_meeting(){
		
		
		
		// error_reporting(0);
		
		$membership=new Membership();
		$session_id=session_id();
		
		$approved_list=$refused_list=$ceased_list=[];
		
		foreach($_POST['examinee'] as $examinee_id=>$action):
			
			switch($action):
				
				case 'refuse':
					$refused_list[]=$membership->refuse_candidate($examinee_id,$notify=($_POST['notify_by_mail']=='refused' or $_POST['notify_by_mail']=='all'),$tmp_folder=$session_id);
				break;
				
				case 'approve':
					$approved_list[]=$membership->approve_candidate($examinee_id,$notify=($_POST['notify_by_mail']=='approved' or $_POST['notify_by_mail']=='all'),$tmp_folder=$session_id);
				break;
				
			endswitch;
			
		endforeach;
		
		$notify=$_POST['notify_quitted_by_mail']=='ceased';
		foreach($_POST['quitted'] as $member_id=>$action):
			
			if($action=='ceased'):
				
				$ceased_list[]=$membership->cease_member($member_id,$notify);
				
			endif;
			
		endforeach;
		
		$meeting_folder=$this->subdomain_root.'/assets/documents/meetings_book/'.$this->insert_id.'/';
		if(!is_dir($meeting_folder)):
			mkdir($meeting_folder,0775,true);
		endif;
		
		$pdf_dir=$this->subdomain_root.'/assets/documents/temps/accepted_candidates'.$session_id.'/';		
		$this->merge_pdfs($meeting_folder,$pdf_dir,'candidati_accettati.pdf');
		
		$pdf_dir=$this->subdomain_root.'/assets/documents/temps/refused_candidates'.$session_id.'/';		
		$this->merge_pdfs($meeting_folder,$pdf_dir,'candidati_respinti.pdf');
		
		// lapsed members
		$lapsed_list=[];
		foreach($_POST['member']['name'] as $index=>$member_name):
          
            $q = "
                update archy_memberships set
                status='4',
                resignation_date='".date('Y-m-d')."',
                resignation_reason='".addslashes($_POST['member']['reason'][$index])."',
                resignation_meeting_id=".$membership->app->insert_id."
                where id=".$_POST['member']['id'][$index]."
                ";
			$ret=$this->db->query($q);
            show_query_error($ret,$this->db,$q);
			
			$member_card=mysqli_fetch_object($this->db->query("select card from archy_memberships where id=".$_POST['member']['id'][$index]))->card;
            
            $q="
                update archy_members_history set
                status='4',
                resignation_date='".date('Y-m-d')."',
                resignation_reason='".addslashes($_POST['member']['reason'][$index])."'
                where card='".$member_card."'
            ";
			$ret=$this->db->query($q);
            show_query_error($ret,$this->db,$q);
			
			$lapsed_list[]=$member_name;
			
		endforeach;
		
		// Meeting Report
		$unsigned_report=$_POST['main']['unsigned_report'];
		$shortcodes=[
			'[NomePresidente]',
			'[NomeSegretario]',
			'[DataAdunanza]',
			'[OrarioAdunanza]',
			'[LuogoAdunanza]',
			'[ElencoCandidatiAmmessi]',
			'[ElencoCandidatiNonAmmessi]',
			'[ElencoAssociatiCessati]',
			'[ElencoAssociatiDecaduti]'
		];
		$date=explode(' ',$_POST['main']['date_time']);
		$replaces=[
			$_POST['main']['president'],
			$_POST['main']['secretary'],
			$date[0],
			$date[1],
			$_POST['main']['place'],
			'<ul><li>'.join('</li><li>',$approved_list).'</li></ul>',
			'<ul><li>'.join('</li><li>',$refused_list).'</li></ul>',
			'<ul><li>'.join('</li><li>',$ceased_list).'</li></ul>',
			'<ul><li>'.join('</li><li>',$lapsed_list).'</li></ul>'
		];
		$unsigned_report=str_replace($shortcodes,$replaces,$unsigned_report);
		require_once $this->main_root.'/private/_common/classes/mpdf/vendor/autoload.php';
		$pdf='
			<html>
			<head>
			<style type="text/css">
			body{background:#FFF;color:#000;font-family:Verdana,Arial,Helvetica,sans-serif;font-size:11px;}
			</style>
			</head>
			<body>'.$unsigned_report.'</body>
			</html>';
		$filename=$meeting_folder.'verbale_da_firmare.pdf';
		$mpdf = new \mPDF('utf-8','A4-P');
		$mpdf->WriteHTML($pdf);
		$mpdf->Output($filename,'F');
		
		$this->db->query("update archy_meetings_book set unsigned_report='".addslashes($unsigned_report)."' where id=".$this->insert_id);
		
	}
	
	public function store_examinee(){
		
		$membership=new Membership();
		$membership->store_examinee();		
		
	}

	public function register_firm(){
        /*
        error_log("@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@");
		$feeds_db=new mysqli($this->config['feeds_db']['host'],$this->config['feeds_db']['user'],$this->config['feeds_db']['pass'],$this->config['feeds_db']['name']);
		$feeds_db->set_charset("utf8");
		$feeds_db->query("set @@session.time_zone='".$this->config['feeds_db']['timezone']."'");
		
		if($_POST['action']=='update'):
			$feeds_db->query("update archy_firms_registry set vat_id='".$_POST['main']['vat_id']."' where subdomain='".$this->name."' and firm_id=".$this->insert_id);
		elseif($_POST['action']=='insert into'):
			$feeds_db->query("insert into archy_firms_registry set vat_id='".$_POST['main']['vat_id']."', firm_id=".$this->insert_id.", subdomain='".$this->name."'");
		endif;
        error_log("@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@");
		*/
	}
	
	public function test_email_credentials(){
		$message=new Message();
		echo $message->test_email_credentials($_POST['recipient']);
	}
        
        /* NEW MOD ANDREA */
        
        public function dispatch_ajax_load(){ // NEW FUNCTION FOR CALLING FROM AJAX
            $id=isset($_POST['id'])? $_POST['id'] : NULL;
            if(isset($_POST['class'])){ $class = $_POST['class']; }
            if(isset($_POST['method'])){ $method = $_POST['method']; }
            $q = new $class();
            $q->$method($id);
        }
        
        
        
        /* NEW MOD ANDREA */
        
       /* public function clear_session(){
                $event=new Event();
                $id=isset($_POST['id'])? $_POST['id'] : NULL;
		$event->clear_session($id);
        }*/
	
        public function clear_event_add(){
                $event=new Event();
                $id=isset($_POST['id'])? $_POST['id'] : NULL;
                $table=isset($_POST['table'])? $_POST['table'] : NULL;
                $domain=isset($_POST['domain'])? $_POST['domain'] : NULL;
		$event->clear_event_add($id,$table,$domain);
        }
        
	public function load_event_extras(){
		$event=new Event();
                $id=isset($_POST['id'])? $_POST['id'] : NULL;
		$event->load_event_extras($id);
	}
	
	public function load_session_extra(){
		$event=new Event();
		$id=isset($_POST['id'])? $_POST['id'] : NULL;
		$event->load_session_extra($id);
	}
	
	public function load_event_session(){
		$event=new Event();
		$now=new DateTime();
		$event->event_session($id=$now->format('Ymdhisv'),$visibility=$_POST['visibility']);
	}
	
	public function load_event_speech(){
		$event=new Event();
		$id=isset($_POST['id'])? $_POST['id'] : NULL;
		$id_suffix=isset($_POST['id_suffix'])? $_POST['id_suffix'] : NULL;
		$event->load_event_speech($id,$id_suffix);
	}
	
	public function store_event(){
		$event=new Event();
		$event->store($this->insert_id);		
	}
	   
    public function store_services_splitting() {
        $splitting_months=serialize($_POST['splitting_months']);
        $qp="update archy_services set splitting_months=? where id=?";
        $stmt=$this->db->prepare($qp);
        $stmt->bind_param('si', $splitting_months, $this->insert_id);
        $status=$stmt->execute();
        if ($status === false) {
            show_query_error($status,$this->db,$qp);
        }


        // TODO: store template settings
    }

    public function store_services_subscriptions() {

        $services_subscriptions_id=$this->db->insert_id;
        $service_membership_id=$_POST['main']['services_memberships_id'];

        if($_POST['action']=='insert into'):

            // Aggiorno abbonamento al servizio con date/status
            $membership_status=$_POST['status'];
            $qp="UPDATE archy_services_memberships SET membership_begin=?, membership_end=?, status=? WHERE id=?";
            $stmt=$this->db->prepare($qp);
            $stmt->bind_param('sssi', App::db_date($_POST['begin']), App::db_date($_POST['end']), $membership_status, $service_membership_id);
            $status=$stmt->execute();
            if ($status === false) {
                show_query_error($status,$this->db,$qp);
            }

            $receipt_id = NULL;
            $invoice_id = NULL;
            $paid=0;
            $document_id=0;
            $document_table='';
            $archy_services_id=mysqli_fetch_object($this->db->query("SELECT services_id FROM archy_services_memberships WHERE id=".$service_membership_id))->services_id;
            $service = mysqli_fetch_object($this->db->query("SELECT * FROM archy_services WHERE id=".$archy_services_id));
            $archy_services_accounting_type = $service->accounting_type;
            $archy_services_cost_centre_id = $service->cost_centre_id;
            if ( $archy_services_accounting_type == 0 ) { // 0: Istituzionale => ricevuta
                //error_log("@@@@@ EMETTO RICEVUTA @@@@@");
                // Emetto ricevuta
                $receipt=new Receipt();
                $n=$this->next_document_number_by_year_and_firm('archy_tax_receipts',$_POST['main']['firm_id'],date('Y'),'first_receipt_number',$ajax=false);
                $new_receipt=[
                    'firm_id'=>$_POST['main']['firm_id'],
                    'number'=>$n,
                    'date'=>"'".date('Y-m-d')."'",
                    'customer_id'=>mysqli_fetch_object($this->db->query("select registry_id from archy_services_memberships where id=".$service_membership_id))->registry_id,
                    'receipt'=>"'".addslashes(serialize([
                        'description'=>[$service->accounting_reason],
                        'quantity'=>['1'],
                        'price'=>[$_POST['main']['amount']],
                        'amount'=>[$_POST['main']['amount']]
                    ]))."'",
                    'payment_method_id'=>$_POST['main']['payment_method_id'],
                    'total'=>$_POST['main']['amount'],
                    'paid'=>$_POST['main']['amount'],
                    'payment_status'=>"'paid'"
                ];
                $new_receipt=$receipt->add($new_receipt);
                $receipt_id=$new_receipt->id;
                $paid=$_POST['main']['amount'];
                $document_id=$receipt_id;
                $document_table='archy_tax_receipts';
                $label=$new_receipt->customer->label;
                $s="RICEVUTA";
            } else {  // 1: Commerciale => fattura 
                //error_log("@@@@@ EMETTO FATTURA @@@@@");
                // Emetto fattura
                $invoice=new Invoice();
                $n=$this->next_document_number_by_year_and_firm('archy_invoices',$_POST['main']['firm_id'],date('Y'),'first_invoice_number',$ajax=false);
                $percentage=mysqli_fetch_object($this->db->query("SELECT percentage FROM archy_tax_rates WHERE id=".$service->tax_rate_id))->percentage;
                
                $amount=$_POST['main']['amount'];
                $price=$amount/(1+$percentage/100);
                $vat=$amount-$price;

                $new_invoice=[
                    'document_type'=>"'TD01'",
                    'VAT_collectability'=>"'I'",
                    'firm_id'=>$_POST['main']['firm_id'],
                    'number'=>$n,
                    'date'=>"'".date('Y-m-d')."'",
                    //'due_date'=>"'".date('Y-m-d')."'",
                    'customer_id'=>mysqli_fetch_object($this->db->query("select registry_id from archy_services_memberships where id=".$service_membership_id))->registry_id,
                    'invoice'=>"'".addslashes(serialize([
                        'description'=>[$service->accounting_reason],
                        'quantity'=>['1'],
                        'price'=>[$price],
                        'amount'=>[$price],
                        'tax_rate_id'=>[$service->tax_rate_id],
                        'tax_percentage'=>[$percentage],
                        'tax'=>[$_POST['main']['amount']-$_POST['main']['amount']/(1+$percentage/100)],
                        'subtotal'=>[$_POST['main']['amount']],
                    ]))."'",
                    'payment_method_id'=>$_POST['main']['payment_method_id'],
                    'bank_id'=>(empty($_POST['main']['bank_id']) ? "null" : $_POST['main']['bank_id']),
                    'payment_cond_type'=>"'TP02'",                                      
                    'taxable_amount'=>$price,
                    'vat'=>$vat,
                    'total'=>$_POST['main']['amount'],
                    'paid'=>$_POST['main']['amount'],
                    'payment_status'=>"'paid'",
                ];
                $new_invoice=$invoice->add($new_invoice);
                $invoice_id=$new_invoice->id;
                $paid=$_POST['main']['amount'];
                $document_id=$invoice_id;
                $document_table='archy_invoices';
                $label=$new_invoice->customer->label;
                $s="FATTURA";
            }

            // Aggiorno quota con fattura/ricevuta
            $qp="UPDATE archy_services_subscriptions SET receipt_id=?, invoice_id=? WHERE id=?";
            $stmt=$this->db->prepare($qp);
            $stmt->bind_param('iii',$receipt_id,$invoice_id,$services_subscriptions_id);
            $status=$stmt->execute();
            if ($status === false) {
                show_query_error($status,$this->db,$qp);
            }

            // Emetto Prima nota
			//error_log("@@@@@ EMETTO PRIMA NOTA @@@@@");
            $petty_cash=new PettyCash();
			$new_petty_cash=[
				'date'=>"'".App::db_date($_POST['main']['date'])."'",
                'petty_cash'=>"'Incasso ".$s." numero: ".$n.", Servizio: ".addslashes($service->description).", Cliente: ".addslashes($label)."'",
				'petty_cash_purpose_id'=>$archy_services_cost_centre_id,
				'petty_cash_type_id'=>$_POST['main']['petty_cash_type_id'],
                'amount'=>$paid,
                'bulk_payment'=>'0',
				'type'=>$archy_services_accounting_type,
				'document_table'=>"'".$document_table."'",
				'document_id'=>$document_id,
			];
			$new_petty_cash=$petty_cash->add($new_petty_cash);
			
		endif;

        // Creo/Aggiorno certificato/attestato del servizio/abbonamento
        $service_membership=new ServiceMembership($service_membership_id);
        $service_membership->create_certificate_of_attendance($service_membership_id);

    }

	
	


    /* ################### NEWMOD ANDREA ##################*/

	public function store_events_subscriptions() {
            if($_POST['main']['payments']['payment_date'][0]!=''){ // se ci sono pagamenti lo registra
		if($_POST['action']=='insert into'){ 	
			$events_subscriptions_id=$this->db->insert_id; 
			$customer_id=mysqli_fetch_object($this->db->query("select subscriber_id from archy_events_subscriptions where id=".$events_subscriptions_id))->subscriber_id;
		}else{ 
			$events_subscriptions_id=$this->insert_id; 
			$customer_id=$_POST['main']['subscriber_id'];
		}	
		
		$archy_event_firm_id=mysqli_fetch_object($this->db->query("SELECT id FROM archy_firms"))->id;
		$archy_event_firm_bank_id=mysqli_fetch_object($this->db->query("SELECT bank_id FROM archy_firms"))->bank_id;
		$archy_events_id=mysqli_fetch_object($this->db->query("SELECT event_id FROM archy_events_subscriptions WHERE id=".$events_subscriptions_id))->event_id;
		$archy_subscriptions_payments=mysqli_fetch_object($this->db->query("SELECT payments FROM archy_events_subscriptions WHERE id=".$events_subscriptions_id))->payments;
		$event = mysqli_fetch_object($this->db->query("SELECT * FROM archy_events WHERE id=".$archy_events_id));
		$payment_cost_centre_id = $event->cost_centre_id;
                $payment_status = 'paid';
                
                				

                //if($_POST['action']=='insert into'):
			
                for($i=0;$i<count($_POST['main']['payments']['payment_date']);$i++){
                    if($_POST['main']['payments']['payment_unique_id'][$i]==''){    
                        $receipt_id = NULL;
                        $invoice_id = NULL;
                        $paid=0;
                        $document_id=0;
                        $document_table='';
                        
                        $events_price=$_POST['main']['events_price'];
                        $extra_price=$_POST['main']['extras_price'];
                        $price=$events_price+$extra_price;
                        $total=$_POST['main']['subscription_total'];
                        $total_paid = $_POST['main']['payments']['paid'][$i];					
                        $payment_type = $_POST['main']['payments']['fattura'][$i];
                        $payment_date=$_POST['main']['payments']['payment_date'][$i];
                        $payment_method_id = $_POST['main']['payments']['payment_method_id'][$i];
                        $petty_cash_type_id = $_POST['main']['payments']['petty_cash_type_id'][$i];
                        
                        if ( $payment_type == 0 ) { // 0: Istituzionale => ricevuta
                                // Emetto ricevuta
                                $receipt=new Receipt();				
                                $n=$this->next_document_number_by_year_and_firm('archy_tax_receipts',$archy_event_firm_id,date('Y'),'first_receipt_number',$ajax=false);				
                                $new_receipt=[
                                        'firm_id'=>$archy_event_firm_id,
                                        'number'=>$n,
                                        'date'=>"'".date('Y-m-d')."'",
                                        'customer_id'=>$customer_id, // da controllare
                                        'receipt'=>"'".addslashes(serialize([
                                                'description'=>[$event->accounting_reason],
                                                'quantity'=>['1'],
                                                'price'=>[$total_paid],
                                                'amount'=>[$total_paid]
                                        ]))."'",
                                        'payment_method_id'=>$payment_method_id,
                                        'total'=>$total_paid,
                                        'paid'=>$total_paid,
                                        'payment_status'=>"'".$payment_status."'"
                                ];
                                //print_r($new_receipt);
                                $new_receipt=$receipt->add($new_receipt);
                                $receipt_id=$new_receipt->id;

                                $paid=$total_paid;
                                $document_id=$receipt_id;
                                $document_table='archy_tax_receipts';
                                $label=$new_receipt->customer->label;
                                $s="RICEVUTA";
                        } else {  // 1: Commerciale => fattura 
                                // Emetto fattura
                                $invoice=new Invoice();
                                $n=$this->next_document_number_by_year_and_firm('archy_invoices',$archy_event_firm_id,date('Y'),'first_invoice_number',$ajax=false);
                                $percentage=mysqli_fetch_object($this->db->query("SELECT percentage FROM archy_tax_rates WHERE id=".$event->tax_rate_id))->percentage;

                                //$amount=$_POST['main']['payments']['paid'][0];
                                $price=$total_paid/(1+$percentage/100);
                                $vat=$total_paid-$price;

                                $new_invoice=[
                                        'document_type'=>"'TD01'",
                                        'VAT_collectability'=>"'I'",
                                        'firm_id'=>$archy_event_firm_id,
                                        'number'=>$n,
                                        'date'=>"'".date('Y-m-d')."'",
                                        //'due_date'=>"'".date('Y-m-d')."'",
                                        'customer_id'=>$customer_id,
                                        'invoice'=>"'".addslashes(serialize([
                                                'description'=>[$event->accounting_reason],
                                                'quantity'=>['1'],
                                                'price'=>[$price],
                                                'amount'=>[$price],
                                                'tax_rate_id'=>[$event->tax_rate_id],
                                                'tax_percentage'=>[$percentage],
                                                'tax'=>[$total_paid-$total_paid/(1+$percentage/100)],
                                                'subtotal'=>[$total_paid],
                                        ]))."'",
                                        'payment_method_id'=>$payment_method_id,
                                        'bank_id'=>(empty($archy_event_firm_bank_id) ? "null" : $archy_event_firm_bank_id),
                                        'payment_cond_type'=>"'TP02'",                                      
                                        'taxable_amount'=>$price,
                                        'vat'=>$vat,
                                        'total'=>$total_paid,
                                        'paid'=>$total_paid,
                                        'payment_status'=>"'".$payment_status."'",
                                ];
                                $new_invoice=$invoice->add($new_invoice);
                                $invoice_id=$new_invoice->id;
                                $paid=$total_paid;
                                $document_id=$invoice_id;
                                $document_table='archy_invoices';
                                $label=$new_invoice->customer->label;
                                $s="FATTURA";
                        }

                        $petty_cash=new PettyCash();
                        $new_petty_cash=[
                                'date'=>"'".App::db_date($payment_date)."'",
                                'petty_cash'=>"'Incasso ".$s." numero: ".$n.", Evento: ".addslashes($event->title).", Cliente: ".addslashes($label)."'",
                                'petty_cash_purpose_id'=>$payment_cost_centre_id, // CENTRO DI COSTO
                                'petty_cash_type_id'=>$petty_cash_type_id,
                                'amount'=>$paid,
                                'bulk_payment'=>'0',
                                'type'=>$payment_type,
                                'document_table'=>"'".$document_table."'",
                                'document_id'=>$document_id,
                        ];
                        $new_petty_cash=$petty_cash->add($new_petty_cash);

                            // aggiorno subscribers payments con id ricevuta/fattura per avere un riferimento                                    
                            $arr_payments = unserialize($archy_subscriptions_payments);
                            $arr_payments['payment_unique_id'][] = $document_id;
                            $arr_payments_ser = serialize($arr_payments);
                            $this->db->query("UPDATE archy_events_subscriptions SET payments='".$arr_payments_ser."' WHERE id=".$events_subscriptions_id);
                    }
                }
			
                /*endif;
		
		if($_POST['action']=='update'): // se update, check sui pagamenti e creo eventuali altre ricevute/fatture
			
			
								
				for($i=0;$i<count($_POST['main']['payments']['payment_date']);$i++){
					
					$payment_unique_id = $_POST['main']['payments']['payment_unique_id'][$i]; // se unique_id è == '' salvo il nuovo pagamento
					$payment_type = $_POST['main']['payments']['fattura'][$i]; // 0 Ricevuta, 1 Fattura
					
                                        if($payment_unique_id==''){
                                            
                                            
                                            
                                        }
										
				}
				
			
		
		endif;*/
        }else{ error_log('#### NESSUN PAGAMENTO INSERITO #####'); }
    }
    
    public function send_services_certificate(){
        $service=new ServiceMembership();
        $id=isset($_GET['id'])? $_GET['id'] : NULL;
        $type=isset($_GET['type'])? $_GET['type'] : NULL;
        $service->send($id,$type);
    }

    public function send_events_certificate(){
        $event=new Event();
        $subscription_id=isset($_GET['subscription_id'])? $_GET['subscription_id'] : NULL;
        $session_id=isset($_GET['session_id'])? $_GET['session_id'] : NULL;
        $event->send($subscription_id,$session_id);
    }
    
    public function send_membership_card(){
        $membership=new Membership();
        $id=isset($_GET['id'])? $_GET['id'] : NULL;
        $membership->send($id);
    }
	
	public function open_pdf() {

    $record_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    $type = isset($_GET['type']) ? $_GET['type'] : 'catalog';

    if ($record_id <= 0) {
        die('ID opera non valido');
    }

    $folder_relative = '/assets/documents/scheda_catalogo/';
    $folder_physical = rtrim($this->subdomain_root, '/') . $folder_relative;

    if (!is_dir($folder_physical)) {
        die('Cartella PDF non trovata');
    }

    $files = glob(rtrim($folder_physical, '/') . '/*.pdf');
    $candidates = [];

    foreach ($files as $file) {

        if (!is_file($file)) {
            continue;
        }

        $basename = basename($file);
        $lower = strtolower($basename);

        if (!preg_match('/(^|[_\-])'.$record_id.'\.pdf$/i', $basename)) {
            continue;
        }

        $is_generic = (
            strpos($lower, 'scheda_generica_') === 0 ||
            strpos($lower, 'archiviazione') !== false ||
            strpos($lower, 'archive') !== false ||
            strpos($lower, 'generica') !== false
        );

        if ($type == 'generic' && !$is_generic) {
            continue;
        }

        if ($type == 'catalog' && $is_generic) {
            continue;
        }

        $candidates[] = $file;
    }

    if (empty($candidates)) {
        die('PDF non trovato per questa scheda');
    }

    usort($candidates, function($a, $b) {
        return filemtime($b) - filemtime($a);
    });

    $url = $folder_relative . basename($candidates[0]);

    header('Location: '.$url);
    exit;
}
    
    public function store_manage_pdf() {
        $manage_pdf_id=$this->insert_id;		
        $manage_pdf=new ManagePdf();
        $manage_pdf->init_pdf($manage_pdf_id);
    } 
    
    public function get_placeholder_template(){
        $name = trim($_REQUEST['template'], '[]');
        
        $template = file_get_contents($this->main_root.'/private/_common/assets/settings/placeholder/templates/'.$name.'.php');
        echo $template;
        exit;
    }
    
    public function get_model(){
        $id = $_POST['id'];
        $model_target = mysqli_fetch_object($this->db->query("select model_template from archy_models where id=" . $_POST['id']))->model_template;
        if($model_target == null){
            $model_target = mysqli_fetch_object($this->db->query("select model_archive from archy_models where id=" . $_POST['id']))->model_archive;
        }
        echo $model_target;
    }
    
    // Function: expand_multiple_select
    // Parameters:
    //      $p_intent = archy_pubblicazione
    //      $p_ids = 'a:2:{i:0;s:1:"2";i:1;s:1:"1";}'
    // Note: Esegue una query su una tabella detail dagli id in un campo serializzato di una master. 
    //       Es. archy_pubblicazioni da archy_scheda_catalogo.pubblicazioni
    // Return: Ritorna un blocco HTML con "label: value<br>... di tutti i record trovati"
    /*public function expand_multiple_select($p_intent, $p_ids) {
        global $app;

        $app_save=clone $app;
        require_once $this->main_root.'/private/_common/intents/'.$p_intent.'.php';
        $app->init();
        $data='<div class="multiple_select">';
        $res=$this->db->query("SELECT * FROM ".$p_intent." WHERE id IN (".implode(',',unserialize($p_ids)).")");
        while($obj = mysqli_fetch_object($res)) {
            $data.='<div class="multiple_select_rows">';
            foreach($obj as $k=>$v) {
                if($k=="created_at" || $k=="updated_at") {
                    continue;
                }
                if($k=="id") {
                    $view=$app->load_view($v);
                } else {
                    $label=$view->data[$p_intent.'.'.$k]['label'];
                    $classname="allfields_".$k;
                    if($app->intent->columns[$p_intent.'.'.$k]['type']=='file') {
                        $v=App::htmlImg($v,$classname,""); // Gestire le classi css con "allfields_NOMECAMPO"
                    } else {
                        $v=$view->data[$p_intent.'.'.$k]['value'];
                    }
                    if($v!="") {
                        $data.='<div class="'.$classname.'">'.$label.': '.$v."</div>";
                    }
                }
            }
            $data.="</div>";
        }
        $data.="</div>";
        //error_log("#########>".$data."<#########");
        //exit;
        $app=clone $app_save;
        return $data;
    }*/
    
    
    /* ################################### */
    /* ####### ARCHY ART FUNCTIONS ####### */
    /* ################################### */
    
    public function get_art_catalog_field_byid(){
        $info=mysqli_fetch_object($this->db->query("SELECT ".$_POST['target']." FROM archy_art_catalog WHERE id = ".$_POST['field_id']));
        echo $info->{$_POST['target']};
    }
    
    /*public function create_authenticity_certificate(){
        if($_POST['main']['status']=='1'):
                $auth_certificate=new AuthCertificate();
                $auth_certificate->create_certificate($this->insert_id);
        endif;
    }*/

    
    /*
    |--------------------------------------------------------------------------
    | ARCHY MEDIA - SALVATAGGIO TAG CONTROLLATI
    |--------------------------------------------------------------------------
    |
    | Extra action usata dall'intent archy_tag.
    | Genera automaticamente lo slug se mancante e mantiene updated_at.
    |
    */
    public function store_tag_slug(){
        global $app;

        $db = $app->get_db();
        $id = isset($this->insert_id) ? (int)$this->insert_id : 0;

        if($id <= 0){
            return;
        }

        $row = mysqli_fetch_object($db->query("SELECT tag, slug, categoria FROM archy_tag WHERE id = ".$id." LIMIT 1"));
        if(!$row){
            return;
        }

        $tag = trim((string)$row->tag);
        $categoria = trim((string)$row->categoria) !== '' ? trim((string)$row->categoria) : 'Tag';
        $slug = trim((string)$row->slug) !== '' ? trim((string)$row->slug) : App::tag_slug($tag);

        $db->query("
            UPDATE archy_tag
            SET
                slug = '".$db->real_escape_string($slug)."',
                categoria = '".$db->real_escape_string($categoria)."',
                updated_at = NOW(),
                created_at = IFNULL(created_at, NOW())
            WHERE id = ".$id."
            LIMIT 1
        ");
    }


}
