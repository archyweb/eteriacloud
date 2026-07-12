<?php
class Membership{
	
	public $app, $db, $notifications, $association_settings, $notices, $recessions;
	
	public $member, $statuses;
	
	public function __construct(){
		
		global $app;
		$this->db=$app->get_db();
		$this->app=$app;
		
		$settings=$app->get_settings($domain='association_1');
		$settings2=$app->get_settings($domain='association_2');
		$this->settings= (object) array_merge((array) $settings, (array) $settings2);
		
		$this->notices=[
			'first'=>[
				'days'=>@$this->settings->first['expiring_card_days'],
				'delay'=>@$this->settings->first['expiring_card_days_delay'],
				'mail_body'=>@$this->settings->first['expiring_card'],
				'mail_subject'=>@$this->settings->first['expiring_card_subject'],
				'sms'=>@$this->settings->first['expiring_card_SMS']
			],
			'second'=>[
				'days'=>@$this->settings->second['expiring_card_days'],
				'delay'=>@$this->settings->second['expiring_card_days_delay'],
				'mail_body'=>@$this->settings->second['expiring_card'],
				'mail_subject'=>@$this->settings->second['expiring_card_subject'],
				'sms'=>@$this->settings->second['expiring_card_SMS']
			],
			'third'=>[
				'days'=>@$this->settings->third['expiring_card_days'],
				'delay'=>@$this->settings->third['expiring_card_days_delay'],
				'mail_body'=>@$this->settings->third['expiring_card'],
				'mail_subject'=>@$this->settings->third['expiring_card_subject'],
				'sms'=>@$this->settings->third['expiring_card_SMS']
			],
			'final'=>[
				'days'=>@$this->settings->card_expired_days,
				'delay'=>@$this->settings->card_expired_days_delay,
				'mail_body'=>@$this->settings->card_expired,
				'mail_subject'=>@$this->settings->card_expired_subject,
				'sms'=>@$this->settings->card_expired_SMS
			],
			'first_recession'=>[
				'days'=>@$this->settings->first_recession['recession_days'],
				'delay'=>@$this->settings->first_recession['recession_days_delay'],
				'mail_body'=>@$this->settings->first_recession['recession'],
				'mail_subject'=>@$this->settings->first_recession['recession_subject'],
				'sms'=>@$this->settings->first_recession['recession_SMS']
			],
			'second_recession'=>[
				'days'=>@$this->settings->second_recession['recession_days'],
				'delay'=>@$this->settings->second_recession['recession_days_delay'],
				'mail_body'=>@$this->settings->second_recession['recession'],
				'mail_subject'=>@$this->settings->second_recession['recession_subject'],
				'sms'=>@$this->settings->second_recession['recession_SMS']
			],
			'third_recession'=>[
				'days'=>@$this->settings->third_recession['recession_days'],
				'delay'=>@$this->settings->third_recession['recession_days_delay'],
				'mail_body'=>@$this->settings->third_recession['recession'],
				'mail_subject'=>@$this->settings->third_recession['recession_subject'],
				'sms'=>@$this->settings->third_recession['recession_SMS']
			],
			'final_recession'=>[
				'days'=>@$this->settings->recession_days,
				'delay'=>@$this->settings->recession_days_delay,
				'mail_body'=>@$this->settings->recession,
				'mail_subject'=>@$this->settings->recession_subject,
				'sms'=>@$this->settings->recession_SMS
			]
		];
		
		$this->recessions=['first_recession','second_recession','third_recession','final_recession'];
		
		$this->statuses=['5'=>'In attesa prima quota','1'=>'Associato','2'=>'In attesa di rinnovo','3'=>'Dimesso','4'=>'Cessato',''=>''];
		
		$params=func_get_args();
		$params_count=func_num_args();
		if(method_exists($this,$f='__construct'.$params_count)):
			call_user_func_array(array($this,$f),$params);
		endif;
		
	}
	
	public function __construct1($member_id){
		
		$this->member=mysqli_fetch_object($this->db->query("select * from archy_memberships where id=".$member_id));
		$this->member->category=mysqli_fetch_object($this->db->query("select * from archy_registry_categories where id=".$this->member->category_id));
		$this->member->registry=mysqli_fetch_object($this->db->query("select * from archy_registry where id=".$this->member->registry_id));
		if($this->member->registry->city_id!='' and $this->member->registry->city_id!=0):
			$this->member->city=mysqli_fetch_object($this->db->query("select * from archy_cities where id=".$this->member->registry->city_id));
			$this->member->country=mysqli_fetch_object($this->db->query("select * from archy_countries where id=".$this->member->city->country_id));
		endif;
		
		$this->member->status_label=$this->statuses[$this->member->status];
		
	}
    
    // Feed per plugin Joomla / Wordpress
	public function feed(){
		$feed=$this->db->query("
			SELECT 
				m.id, m.picture,
				r.member_public_name, r.member_public_surname, r.member_public_address, r.member_public_zip, r.member_public_phone, r.member_public_mail, r.member_public_website,
				c.city, c.province_district, c.province_district_code, c.state_region, c.city_label,
				country.country_it
			  FROM archy_memberships AS m 
			  LEFT JOIN archy_registry AS r ON (m.registry_id=r.id)
			  LEFT JOIN archy_cities AS c ON (r.member_public_city_id=c.id)
			  LEFT JOIN archy_countries AS country ON (c.country_id=country.id)
			 WHERE 
				m.status='1' AND r.member_published=1
		");
		
		$response=[];
		while($member=mysqli_fetch_object($feed)):
			$response[]=$member;
		endwhile;
		
		echo json_encode($response);
	}
	
	public function check_duplicated_entry($registry_id){
		
		$response=new stdClass();
		
		$result=$this->db->query("select m.id as member_id, r.label as member_name from archy_memberships as m left join archy_registry as r on (m.registry_id=r.id) where m.registry_id=".$registry_id);
		if($result->num_rows==0):
			$response->is_duplicated=false;
		else:
			$response->is_duplicated=true;
			$member=mysqli_fetch_object($result);
			$response->member_id=$member->member_id;
			$response->member_name=$member->member_name;
		endif;
		
		return $response;
		
	}
    
    
    
    //$options=['1'=>'Associato','2'=>'In attesa di rinnovo','3'=>'Dimesso','4'=>'Cessato','5'=>'In attesa prima quota'];
	public function check_status(){ /* cronjob, runs once a day at 09:00 AM */
        $validity_after_expiration=empty($this->settings->validity_after_expiration)?0:$this->settings->validity_after_expiration;
		
        $queue=new Queue();
        $message=new Message();
		$shortcodes=$message->shortcodes;
		
		$expired=$withdrawn=[];

        echo "<br><br>################### GESTIONE NOTIFICHE ####################<br><br>";

        // Gestione NOTIFICHE
        foreach($this->notices as $key=>$data):
            
	    echo "<hr>";
            if(($data['days']=='')||empty($data['mail_body'])||empty($data['mail_subject'])):
		echo "<br>@@@@@@@@@@ key: >".$key."< (FOUND EMPTY FIELD) => SKIPPED!";
            else:
                echo("<br>@@@@@@@@@@ key: >".$key."<");
                echo("<br>@@@@@@@@@@ delay: >".$data['delay']."<");
                echo("<br>@@@@@@@@@@ days: >".$data['days']."<");
                echo("<br>@@@@@@@@@@ expire: >".$validity_after_expiration."<");

                $days=$data['delay']=='before'? '+':'-';
                $days.=$data['days']!=''? $data['days'] : '0';
                echo "<br>delta (+/-) days: ".$days;
                $date=new DateTime();
                echo "<br>date now: ".$date->format('Y-m-d');
                $date->modify($days.' days');
                echo "<br>data now +/- delta: ".$date->format('Y-m-d');
                if(in_array($key,$this->recessions)):
                    $date->modify('-'.$validity_after_expiration.' days');
                    echo "<br>date (-expire): ".$date->format('Y-m-d');
                endif;

                echo("<br>@@@@@@@@@@ date (end_membership==): >".$date->format('Y-m-d')."<");
		        echo"<br><br>";

                $memberships=$this->db->query("
                    select 
                        m.id as member_id, m.card, m.membership_begin, m.membership_end, m.request_date,
                        r.*,
                        archy_cities.city, archy_cities.state_region, archy_cities.province_district_code, archy_cities.province_district,
                        archy_countries.country_en, archy_countries.country_it,
                        archy_registry_categories.category, archy_registry_categories.subscription_fee 
                    from archy_memberships as m
                    left join archy_registry as r on (m.registry_id=r.id)
                    left join archy_cities on (r.city_id=archy_cities.id) 
                    left join archy_countries on (archy_cities.country_id=archy_countries.id)
                    left join archy_registry_categories on (m.category_id=archy_registry_categories.id)
                    where (m.status='1' or m.status='2' or m.status='3') and m.membership_end='".$date->format('Y-m-d')."'
                ");

                while($member=mysqli_fetch_object($memberships)):

                    echo "<br><b>FOUND: $member->label</b>";
                    
                    // Se NON è impostato rinnovo posticipato salta secondo gruppo notifiche 
                    if($validity_after_expiration==0) {
                        if(in_array($key,$this->recessions)) {
                            echo "<br>SKIP: $key";
                            continue;
                        }
                    }

                    echo "<br>>".$key."<";
                    echo("<br>################# mail to: >$member->label ($member->member_id)<");

                    $replaces=$message->get_shortcodes_replaces($member);
                    $email=[
                        'recipient_name'=>$member->label,
                        'recipient_mail'=>$member->email,
                        'sender_name'=>$this->settings->sender_name,
                        'sender_mail'=>$this->settings->sender_email,
                        'subject'=>str_replace($shortcodes,$replaces,$data['mail_subject']),
                        'message'=>str_replace($shortcodes,$replaces,$data['mail_body']),
                    ];
                    /*'bcc'=>['vferri@gmail.com'],*/
                    
                    echo("<br>################# enqueue mail msg: >".str_replace($shortcodes,$replaces,$data['mail_body'])."<");
                    //$message->email($email);
                    $queue->enqueue($email);
                    
                    /*$sms=[
                        'message'=>str_replace($shortcodes,$replaces,$data['sms']),
                        'recipient'=>$member->mobile,
                        'sender'=>$this->settings->sms_sender
                    ];
                    $message->text($sms);*/

                endwhile;
            endif;
        endforeach;
        

        echo "<br><br>################### GESTIONE STATUS ####################<br><br>";


        // GESTIONE STATUS (SCADENZA TESSERE E DIMISISONI SOCI)
        if($validity_after_expiration==0): 
            // Associazione SENZA rinnovo posticipato
            $date=new DateTime();
            echo "<br>SENZA RINNOVO POSTICIPATO";
            echo "<br>date now: ".$date->format('Y-m-d');
   
            $memberships=$this->db->query("
                    select 
                        m.id as member_id, m.card, m.membership_begin, m.membership_end, m.request_date,
                        r.*,
                        archy_cities.city, archy_cities.state_region, archy_cities.province_district_code, archy_cities.province_district,
                        archy_countries.country_en, archy_countries.country_it,
                        archy_registry_categories.category, archy_registry_categories.subscription_fee 
                    from archy_memberships as m
                    left join archy_registry as r on (m.registry_id=r.id)
                    left join archy_cities on (r.city_id=archy_cities.id) 
                    left join archy_countries on (archy_cities.country_id=archy_countries.id)
                    left join archy_registry_categories on (m.category_id=archy_registry_categories.id)
                    where (m.status='1' or m.status='2') and m.membership_end<'".$date->format('Y-m-d')."'
                ");
                
            // DIMESSO
            while($member=mysqli_fetch_object($memberships)):
                echo("<br>################# $member->label ($member->member_id) : status => 3 (dimesso)");
                $this->db->query("UPDATE archy_memberships SET status='3' WHERE id=".$member->member_id);
                $expired[$member->member_id]=$member->label;
                $withdrawn[$member->member_id]=$member->label;
            endwhile;
        else:
            // Associazione CON rinnovo posticipato
            echo "<br>RINNOVO POSTICIPATO";

            // --------------------------------------------
            // SCADENZA TESSERE
            $date=new DateTime();
            echo "<br>date now: ".$date->format('Y-m-d');
   
            $memberships=$this->db->query("
                    select 
                        m.id as member_id, m.card, m.membership_begin, m.membership_end, m.request_date,
                        r.*,
                        archy_cities.city, archy_cities.state_region, archy_cities.province_district_code, archy_cities.province_district,
                        archy_countries.country_en, archy_countries.country_it,
                        archy_registry_categories.category, archy_registry_categories.subscription_fee 
                    from archy_memberships as m
                    left join archy_registry as r on (m.registry_id=r.id)
                    left join archy_cities on (r.city_id=archy_cities.id) 
                    left join archy_countries on (archy_cities.country_id=archy_countries.id)
                    left join archy_registry_categories on (m.category_id=archy_registry_categories.id)
                    where (m.status='1') and m.membership_end<'".$date->format('Y-m-d')."'
                ");
                
            while($member=mysqli_fetch_object($memberships)):
                echo("<br>################# $member->label ($member->member_id) : status => 2 (in attesa di rinnovo)");
                $this->db->query("UPDATE archy_memberships SET status='2' WHERE id=".$member->member_id);
                $expired[$member->member_id]=$member->label;
            endwhile;
        
            // --------------------------------------------
            // SCADENZA DIMISSIONI
            $date=new DateTime();
            echo "<br>date now: ".$date->format('Y-m-d');
            $date->modify('-'.$validity_after_expiration.' days');
            echo "<br>date 2: ".$date->format('Y-m-d');
   
            $memberships=$this->db->query("
                    select 
                        m.id as member_id, m.card, m.membership_begin, m.membership_end, m.request_date,
                        r.*,
                        archy_cities.city, archy_cities.state_region, archy_cities.province_district_code, archy_cities.province_district,
                        archy_countries.country_en, archy_countries.country_it,
                        archy_registry_categories.category, archy_registry_categories.subscription_fee 
                    from archy_memberships as m
                    left join archy_registry as r on (m.registry_id=r.id)
                    left join archy_cities on (r.city_id=archy_cities.id) 
                    left join archy_countries on (archy_cities.country_id=archy_countries.id)
                    left join archy_registry_categories on (m.category_id=archy_registry_categories.id)
                    where (m.status='1' or m.status='2') and m.membership_end<'".$date->format('Y-m-d')."'
                ");
                
            // DIMESSO
            while($member=mysqli_fetch_object($memberships)):
                echo("<br>################# $member->label ($member->member_id) : status => 3 (dimesso)");
                $this->db->query("UPDATE archy_memberships SET status='3' WHERE id=".$member->member_id);
                $withdrawn[$member->member_id]=$member->label;
            endwhile;
        endif;

		$calendar=new Calendar();
		if(count($expired)>0):
			$event=[
				'date'=>date('Y-m-d'),
				'icon'=>'warning-sign',
				'css_class'=>'text-warning',
				'title'=>'Tessere scadute',
				'event'=>'
					<p><strong>La tessera dei seguenti soci è scaduta</strong>:</p>
					<p>'.implode('<br>',$expired).'</p>'
			];
			$calendar->add($event);
		endif;
		
		if(count($withdrawn)>0):
			$event=[
				'date'=>date('Y-m-d'),
				'icon'=>'exclamation-sign',
				'css_class'=>'text-danger',
				'title'=>'Soci dimessi',
				'event'=>'
					<p><strong>I seguenti soci sono dimessi</strong>:</p>
					<p>'.implode('<br>',$withdrawn).'</p>'
			];
			$calendar->add($event);			
		endif;
	}
	
	public function add_history_item(){
		
		$this->db->query("
			insert into archy_members_history set
				member_id=".$this->member->id.",
				card='".$this->member->card."',
				request_date='".$this->member->request_date."',
				acceptance_date='".$this->member->acceptance_date."',
				full_name='".addslashes($this->member->registry->label)."',
				birth_date='".$this->member->registry->birth_date."',
				birth_place='".addslashes($this->member->registry->birth_place)."',
				tax_code='".addslashes($this->member->registry->tax_code)."',
				address='".addslashes($this->member->registry->address)."',
				zip='".addslashes($this->member->registry->zip)."',
				city='".addslashes($this->member->city->city_label)."',
				category='".addslashes($this->member->category->category)."',
				resignation_date='".$this->member->resignation_date."',
				resignation_reason='".addslashes($this->member->resignation_reason)."'
		");
				
	}
	
	public function generate_report($model_name,$report_title,$member_id){
		
		$model=$this->settings->$model_name;
		
		$message=new Message();
		$shortcodes=$message->shortcodes;
		$member=$this->get_member($member_id);
		$replaces=$message->get_shortcodes_replaces($member);
		
		$report=str_replace($shortcodes,$replaces,$model);
		
		$report_obj=new Report();
		$report_id=$report_obj->store($report_title, $report);
		$report_obj->store_pdf($report,$report_id);		
		
	}
	
	public function get_member($member_id){
		return mysqli_fetch_object($this->db->query("
			select m.id as member_id, m.*, r.*, city.*, country.*, archy_registry_categories.*
			from archy_memberships as m 
			left join archy_registry as r on (m.registry_id=r.id)
			left join archy_registry_categories on (m.category_id=archy_registry_categories.id)
			left join archy_cities as city on (r.city_id=city.id)
			left join archy_countries as country on (city.country_id=country.id)
			where m.id=".$member_id
		));
	}
	
	public function get_examinee($examinee_id){
		return mysqli_fetch_object($this->db->query("
			select e.id as member_id, e.*, r.*, city.*, country.*, archy_registry_categories.*
			from archy_memberships_examinee as e 
			left join archy_registry as r on (e.registry_id=r.id)
			left join archy_cities as city on (r.city_id=city.id)
			left join archy_registry_categories on (e.category_id=archy_registry_categories.id)
			left join archy_countries as country on (city.country_id=country.id)
			where e.id=".$examinee_id
		));
	}
	
	public function get_history($member_id){
		$history=[];
		$items=$this->db->query("select * from archy_members_history where member_id=".$member_id." order by id desc");
		while($item=mysqli_fetch_object($items)):
			$history[]=$item;
		endwhile;
		return $history;
	}
	
	public function get_next_card(){
		return mysqli_fetch_object($this->db->query("select coalesce(max(card)+1,1) as next_card from archy_memberships"))->next_card;
	}
	
    // Sessioni (Eventi/Attività) a cui ha partecipato un soggetto
	public static function activities($registry_id){
		global $app;
		$db=$app->get_db();
		$response=[];
		$accounted_events=$db->query("select event_id from archy_events_subscriptions where subscriber_id=".$registry_id." and accounted!=''");
		while($events=mysqli_fetch_object($accounted_events)):
            //var_dump('events',$events);
			$event_id=$events->event_id;
            $response[]=mysqli_fetch_object($db->query("
                select 
                    ev.total_credits, ev.title, ss.title as session_title, ss.start_date
                from
                    archy_events as ev
                left join 
                    archy_events_sessions as ss
                on 
                    ss.event_id=ev.id
                where
                    ev.id=".$event_id."
            "));
		endwhile;
        //var_dump('response',$response);
		return $response;
	}

	public function get_subscription_fee_list(){
		
		$settings=$this->app->get_settings($domain='association_2');
		
		$response=new stdClass();
		$response->categories=[];
		
		$results=$this->db->query("select id,category,subscription_fee from archy_registry_categories where domain='memberships' order by category");
		
		while($row=mysqli_fetch_object($results)):
			$tmp=new stdClass();
			$tmp->category=$row->category;
			$tmp->subscription_fee=$row->subscription_fee;
			$response->categories[$row->id]=$tmp;
		endwhile;
		
		return $response;
		
	}
	
	public function get_subscription_fee($category_id){
        $obj=mysqli_fetch_object($this->db->query("select subscription_fee,splitting,splitting_months from archy_registry_categories where id=".$category_id));

        $subscription_fee=$obj->subscription_fee;
        $splitting=$obj->splitting;
        $splitting_months=unserialize($obj->splitting_months);

        if($this->app->config['association_splitting'] && $splitting==1):
            $mese=intval(date('m'));
            if(isset($_POST['data']) && $_POST['data']!=''):
                $time=strtotime(str_replace('/', '-',$_POST['data']));
                $mese=intval(date("m",$time));
            endif;
            return $splitting_months[$this->app->months[$mese-1]];
        else:
            return $subscription_fee;
        endif;
	}

        public function get_eps_fee($eps){
		return mysqli_fetch_object($this->db->query("select sum(amount) as eps_fee from archy_eps where ID in (".$eps.")"))->eps_fee;
	}
	
	public function get_first_membership_end_date(){
		
		$settings=$this->app->get_settings($domain='association_2');

		$today=new DateTime();
		if(@$settings->first_membership_length=='custom_date'):
			$end_day=$settings->first_membership_custom_day;
			if($end_day<10):
				$end_day='0'.$end_day;
			endif;
			$end_month=$settings->first_membership_custom_month;
			if($end_month<10):
				$end_month='0'.$end_month;
			endif;
			$year_end=date('m-d')>=$end_month.'-'.$end_day? date('Y')+1 : date('Y');
			return $year_end.'-'.$end_month.'-'.$end_day;
		else:// 'one_year'
                        $next_year = date('Y', strtotime("+12 months ".$today->format('Y-m-d').""));
                        $next_year_type = date('L', mktime(0,0,0, 1, 1, $next_year));
                        $days_to_add = '+364 days';
                        if($next_year_type == 1){ $days_to_add = '+365 days'; }
			$today->modify($days_to_add);
			return $today->format('Y-m-d');
		endif;
		
	}
	
	
	public function approve_candidate($candidate_id,$notify,$tmp_folder){
		
        $ret=$this->db->query("update archy_memberships_examinee set request_status='1', response_date='".date('Y-m-d')."', acceptance_meeting_id=".$this->app->insert_id." where id=".$candidate_id);
        show_query_error($ret,$this->db);
		
		$candidate=mysqli_fetch_object($this->db->query("select * from archy_memberships_examinee where id=".$candidate_id));
		
		$found=$this->db->query("select id from archy_memberships where registry_id=".$candidate->registry_id);
		
		if($found->num_rows==0):
		
			//status: In attesa prima quota associativa
			$ret=$this->db->query("
				insert into archy_memberships set 
				status='5',
				card='".$this->app->next_card_number($this->app->name)."',
				registry_id='".$candidate->registry_id."',
				category_id='".$candidate->category_id."',
				signatory='".addslashes($candidate->signatory)."',
				membership_begin='".date('Y-m-d')."',
				membership_end='".$this->get_first_membership_end_date()."',
				picture='".addslashes($candidate->picture)."',
				documents='".addslashes($candidate->documents)."',
				request_date='".$candidate->request_date."',
				acceptance_date='".date('Y-m-d')."',
				acceptance_meeting_id=".$this->app->insert_id."
            ");
            show_query_error($ret,$this->db);
		
			$member_id=$this->db->insert_id;
			
		else:
			
			$member_id=mysqli_fetch_object($found)->id;
			
			//status: In attesa prima quota associativa
			$ret=$this->db->query("
				update archy_memberships set 
				status='5',
				card='".$this->app->next_card_number($this->app->name)."',
				category_id='".$candidate->category_id."',
				signatory='".addslashes($candidate->signatory)."',
				membership_begin='".date('Y-m-d')."',
				membership_end='".$this->get_first_membership_end_date()."',
				picture='".addslashes($candidate->picture)."',
				documents='".addslashes($candidate->documents)."',
				request_date='".$candidate->request_date."',
				acceptance_date='".date('Y-m-d')."',
				resignation_date='',
				resignation_reason='',
				acceptance_meeting_id=".$this->app->insert_id."
				where id=".$member_id
            );
            show_query_error($ret,$this->db);
			
		endif;
		
		if($candidate->fee==1)://deposito quota associativa
            $date_time=explode(' ',$_POST['main']['date_time']); // DATA ADUNANZA

            $ret=$this->db->query("update archy_memberships set status='1' where id=".$member_id);//status: Associato
            show_query_error($ret,$this->db);
		
			$receipt=new Receipt();
			$new_receipt=[
				'firm_id'=>$candidate->firm_id,
				'number'=>$this->app->next_document_number_by_year_and_firm('archy_tax_receipts',$candidate->firm_id,date('Y'),'first_receipt_number',$ajax=false),
				'date'=>"'".App::db_date($date_time[0])."'",
				'customer_id'=>$candidate->registry_id,
				'receipt'=>"'".addslashes(serialize([
					'description'=>['Versamento quota associativa','Storno deposito quota associativa'],
					'quantity'=>['1','1'],
					'price'=>[$candidate->fee_category_amount,-$candidate->fee_category_amount],
					'amount'=>[$candidate->fee_category_amount,-$candidate->fee_category_amount]
				]))."'",
				'payment_method_id'=>$candidate->payment_method_id,
				'total'=>'0',
				'paid'=>'0',
				'payment_status'=>"'paid'"
			];
			$new_receipt=$receipt->add($new_receipt);
			
			$ret=$this->db->query("update archy_memberships_examinee set balance_receipt_id=".$new_receipt->id." where id=".$candidate_id);
            show_query_error($ret,$this->db);

            //error_log("@@@@@@@@@@@@@@@ INSERT archy_subscriptions receipt_id:".$new_receipt->id);
            $ret=$this->db->query("
                insert into archy_subscriptions set
                date='".App::db_date($date_time[0])."',
                member_id=".$member_id.",
                amount=".$candidate->fee_category_amount.",
                firm_id=".$candidate->firm_id.",
                payment_method_id=".$candidate->payment_method_id.",
                petty_cash_type_id=".$candidate->petty_cash_type_id.",
                receipt_id=".$new_receipt->id."
            ");
            show_query_error($ret,$this->db);

            //error_log("@@@@@@@@@@@@@@@ RIGA 1: Storno deposito");
            $petty_cash=new PettyCash();
            $new_petty_cash=[
                'date'=>"'".App::db_date($date_time[0])."'",
                'petty_cash'=>"'Storno deposito quota associativa, ricevuta numero: ".$new_receipt->number." - Candidato: ".addslashes($new_receipt->customer->label)."'",
                'petty_cash_purpose_id'=>'5',
                'petty_cash_type_id'=>$candidate->petty_cash_type_id,
                'amount'=>-$candidate->fee_category_amount,
                'bulk_payment'=>'0',
                'type'=>'0',
                'document_table'=>"'archy_tax_receipts'",
                'document_id'=>$new_receipt->id
            ];
            $new_petty_cash=$petty_cash->add($new_petty_cash);
            //error_log("@@@@@@@@@@@@@@@ id=".print_r($new_petty_cash,true));

            //error_log("@@@@@@@@@@@@@@@ RIGA 2: Versamento quota");
            $petty_cash=new PettyCash();
            $new_petty_cash=[
                'date'=>"'".App::db_date($date_time[0])."'",
                'petty_cash'=>"'Versamento quota associativa, ricevuta numero: ".$new_receipt->number." - Candidato: ".addslashes($new_receipt->customer->label)."'",
                'petty_cash_purpose_id'=>'2',
                'petty_cash_type_id'=>$candidate->petty_cash_type_id,
                'amount'=>$candidate->fee_category_amount,
                'bulk_payment'=>'0',
                'type'=>'0',
                'document_table'=>"'archy_tax_receipts'",
                'document_id'=>$new_receipt->id
            ];
            $new_petty_cash=$petty_cash->add($new_petty_cash);
            //error_log("@@@@@@@@@@@@@@@ id=".print_r($new_petty_cash,true));

		endif;
		
		$new_member=new Membership($member_id);
		$new_member->add_history_item();
		
		$member=$this->get_member($member_id);
		$this->store_pdf_and_notify(
			$member,
			$notify,
			$notice=$this->settings->candidate_accepted,
			$subject=$this->settings->candidate_accepted_subject,
			$tmp_folder=$this->app->subdomain_root.'/assets/documents/temps/accepted_candidates/'.$tmp_folder.'/'
		);
		
        $this->create_card($member_id);
		
		return $new_member->member->registry->label.' ('.$new_member->member->registry->tax_code.')';
		
	}
	
	public function refuse_candidate($candidate_id,$notify,$tmp_folder){
		
		$this->db->query("update archy_memberships_examinee set request_status='0', response_date='".date('Y-m-d')."', rejection_meeting_id=".$this->app->insert_id." where id=".$candidate_id);
		
		$candidate=$this->get_examinee($candidate_id);
		
		$this->store_pdf_and_notify(
			$candidate,
			$notify,
			$notice=$this->settings->candidate_refused,
			$subject=$this->settings->candidate_refused_subject,
			$tmp_folder=$this->app->subdomain_root.'/assets/documents/temps/refused_candidates/'.$tmp_folder.'/'
		);
		
		return $candidate->label.' ('.$candidate->tax_code.')';
		
	}
	
	public function store_pdf_and_notify(&$member,$notify,$notice,$subject,$tmp_folder){
		
		$message=new Message();
		$shortcodes=$message->shortcodes;
		$replaces=$message->get_shortcodes_replaces($member);
		
		$notice=str_replace($shortcodes,$replaces,$notice);
		
		require_once $this->app->main_root.'/private/_common/classes/mpdf/vendor/autoload.php';
		
		$list = get_html_translation_table(HTML_ENTITIES);
		unset($list['"']);
		unset($list['<']);
		unset($list['>']);
		unset($list['&']);
		$entities=strtr($notice, $list);

		$pdf='
			<html>
			<head>
			<style type="text/css">
			body{background:#FFF;color:#000;font-family:Verdana,Arial,Helvetica,sans-serif;font-size:11px;}
			</style>
			</head>
			<body>'.$entities.'</body>
			</html>';
		
		if(!is_dir($tmp_folder)):
			mkdir($tmp_folder,0775,true);
		endif;
		
		$filename=$tmp_folder.$member->member_id.'.pdf';
		
		$mpdf = new \mPDF('utf-8', 'A4-P');
		$mpdf->WriteHTML($pdf);
		$mpdf->Output($filename,'F');
		
		if($notify===true):
			
            // Questa arriva bene
			$email=[
				'recipient_name'=>$member->label,
				'recipient_mail'=>$member->email,
				'sender_name'=>$this->settings->sender_name,
				'sender_mail'=>$this->settings->sender_email,
				'subject'=>str_replace($shortcodes,$replaces,$subject),
				'message'=>$notice
			];

			$message->email($email);
			
		endif;
		
	}
	
	public function cease_member($member_id,$notify){
		
        // CESSATO
		$this->db->query("
			update archy_memberships set
			status=4,
			resignation_date='".date('Y-m-d')."',
			resignation_reason='Mancato rinnovo tessera',
			resignation_meeting_id=".$this->app->insert_id."
			where id=".$member_id
		);
		
		$member=$this->get_member($member_id);
		
		$this->db->query("
			update archy_members_history set
			status=4,
			resignation_date='".date('Y-m-d')."',
			resignation_reason='Mancato rinnovo tessera'
			where card='".$member->card."'"
		);
		
		if($notify===true):
			
			$message=new Message();
			$shortcodes=$message->shortcodes;
			$replaces=$message->get_shortcodes_replaces($member);
			
            // verificare mittente e corpo messaggio 
			$email=[
				'recipient_name'=>$member->label,
				'recipient_mail'=>$member->email,
				'sender_name'=>$this->settings->sender_name,
				'sender_mail'=>$this->settings->sender_email,
				'subject'=>str_replace($shortcodes,$replaces,$this->settings->cessation_occurred_subject),
				'message'=>str_replace($shortcodes,$replaces,$this->settings->cessation_occurred)
			];

			$message->email($email);
			
		endif;
		
		return $member->label.' ('.$member->tax_code.')';
		
	}
        
        public function send($id) {
             
            $member=$this->get_member($id);

            if ($member->email != ''):
                
                // card 
                $file_name_member="card_".$id.".pdf";              
                
                $file_path_member="/assets/documents/memberships/".$file_name_member;
                $physical_path_member=$this->app->subdomain_root.$file_path_member;
                
                $message = new Message();

                $mail_subject = $this->settings->membership_card_subject;
          
                $mail_body = $this->settings->membership_card_body;
                $shortcodes=$message->shortcodes;
                $replaces=$message->get_shortcodes_replaces($member);
     
                $email=[
                    'recipient_name'=>$member->label,
                    'recipient_mail'=>$member->email,
                    'sender_name'=>$this->settings->sender_name,
                    'sender_mail'=>$this->settings->sender_email,
                    'subject'=>str_replace($shortcodes,$replaces,$mail_subject),
                    'message'=>str_replace($shortcodes,$replaces,$mail_body),
                    'attachments' => [$file_name_member => file_get_contents($physical_path_member)]
                ];
                
                $message->email($email);
            
                $sent_on=mysqli_fetch_object($this->db->query("SELECT sent_on FROM archy_memberships WHERE id=".$subscription_id))->sent_on;
                if($this->app->is_serialized($sent_on)):
                        $sent_on=unserialize($sent_on);
                else:
                        $sent_on=[];
                endif;                
                
                $sent_on[]=['id' => $id];               
                
                $this->db->query("update archy_memberships set sent_on='".addslashes(serialize($sent_on))."' where id=" . $id);
    
                set_message("success", "Tessera inviata correttamente.");

            else:

                set_message("danger", "Non è stato possibile inviare la tessera, al cliente non è associato un indirizzo e-mail.");

            endif;

            header('location: ' . $_SERVER['HTTP_REFERER']);

        }
	
	public function store_examinee(){
		
        $examinee_id=$this->app->insert_id;

        //var_dump($examinee_id,$_POST['action'],$receipt_tmp,$fee_category_amount,$fee_eps_amount,$fee_amount);
        //var_dump($_POST['main']);
        //exit;
	
		if(@$_POST['main']['fee']=='1') {
            
            // VF: Attenzione! ...se era fee==1 e l'ha tolta ???  
            // ........... => andrebbero cancellate/modificate ricevuta e prima nota ???

            $fee_category_amount=isset($_POST['main']['fee_category_amount'])?$_POST['main']['fee_category_amount']:0;
            $fee_eps_amount=isset($_POST['main']['fee_eps_amount'])?$_POST['main']['fee_eps_amount']:0;
            $fee_amount=$fee_category_amount+$fee_eps_amount;

            if($fee_eps_amount==0) {
                $receipt_tmp=addslashes(serialize([
                    'description'=>['Versamento deposito quota associativa'],
                    'quantity'=>['1'],
                    'price'=>[$fee_category_amount],
                    'amount'=>[$fee_category_amount]
                ]));    
            } else {
                $receipt_tmp=addslashes(serialize([
                    'description'=>['Versamento deposito quota associativa','Versamento deposito tesseramento EPS o altre quote'],
                    'quantity'=>['1','1'],
                    'price'=>[$fee_category_amount,$fee_eps_amount],
                    'amount'=>[$fee_category_amount,$fee_eps_amount]
                ]));
            }

            if($_POST['action']=='update') {
                // recupero dati per modifiche
                $examinee=$this->get_examinee($examinee_id);
                $deposit_receipt=$examinee->deposit_receipt;
                $deposit_receipt_id=$examinee->deposit_receipt_id;
                $deposit_petty_cash_id=$examinee->deposit_petty_cash_id;
            }

			if($_POST['action']=='update' && !($deposit_receipt_id=="" || $deposit_petty_cash_id=="")) {    // update
				
                // documents override

                error_log("@@@@@@@@@@@@@@@@@@@@: modifica candidato associato: ".
                    "ricevuta:".$deposit_receipt." - ".
                    "ricevuta_id:".$deposit_receipt_id." - ".
                    "primanota_id:".$deposit_petty_cash_id);
              
                // emetto nuova ricevuta
                error_log("@@@@@@@@@@@@@@@@@@@@: emetto nuova ricevuta... ");
                $receipt=new Receipt();
                $number=$this->app->next_document_number_by_year_and_firm('archy_tax_receipts',$_POST['main']['firm_id'],date('Y'),'first_receipt_number',$ajax=false);
                $new_receipt=[
                    'firm_id'=>$_POST['main']['firm_id'],
                    'number'=>$number,
                    'date'=>"'".App::db_date($_POST['main']['fee_date'])."'",
                    'customer_id'=>$_POST['main']['registry_id'],
                    'receipt'=>"'".$receipt_tmp."'",
                    'payment_method_id'=>$_POST['main']['payment_method_id'],
                    'total'=>$fee_amount,
                    'paid'=>$fee_amount,
                    'payment_status'=>"'paid'"
                ];
                $new_receipt=$receipt->add($new_receipt);

                // aggiorno prima nota
                error_log("@@@@@@@@@@@@@@@@@@@@: modifico prima nota... ");
                $q="update archy_petty_cash_book set 
                        date = '".App::db_date($_POST['main']['fee_date'])."',
                        petty_cash = 'Incasso ricevuta numero: ".$new_receipt->number." - Candidato: ".addslashes($new_receipt->customer->label)."',
                        petty_cash_purpose_id = '1',
                        petty_cash_type_id = ".$_POST['main']['petty_cash_type_id'].",
                        amount = ".$fee_amount.",
                        type = '0',
                        document_table='archy_tax_receipts',
                        document_id=".$new_receipt->id."
                    where id=".$deposit_petty_cash_id;
                $ret=$this->db->query($q);
                show_query_error($ret,$this->db,$q);

                // annullo vecchia ricevuta
                error_log("@@@@@@@@@@@@@@@@@@@@: annnullo vecchia ricevuta... ");
                $q="update archy_tax_receipts set 
                        receipt='".addslashes(serialize([
                            'description'=>['Ricevuta annullata, emessa nuova ricevuta N.'.$new_receipt->receipt_number.' id='.$new_receipt->id],
                            'quantity'=>['1'],
                            'price'=>'0.0',
                            'amount'=>'0.0',
                        ]))."',
                        total=0.0,
                        paid=0.0
                    where id=".$examinee->deposit_receipt_id;
                $ret=$this->db->query($q);
                show_query_error($ret,$this->db,$q);

                // aggiorno con nuovi riferimenti
                error_log("@@@@@@@@@@@@@@@@@@@@: aggiorno riferimenti nel record del candiadato... ");
                $q="update archy_memberships_examinee set 
                        deposit_receipt='".$new_receipt->receipt_number."', 
                        deposit_receipt_id=".$new_receipt->id.", 
                        deposit_petty_cash_id=".$deposit_petty_cash_id." 
                    where id=".$examinee_id;
                $ret=$this->db->query($q);
                show_query_error($ret,$this->db,$q);

                // aggiorno pdf ricevuta annnullata...
                $updated_receipt = new Receipt($examinee->deposit_receipt_id);
                $updated_receipt->store();

			} else {    // insert

                $receipt=new Receipt();
                $new_receipt=[
                    'firm_id'=>$_POST['main']['firm_id'],
                    'number'=>$this->app->next_document_number_by_year_and_firm('archy_tax_receipts',$_POST['main']['firm_id'],date('Y'),'first_receipt_number',$ajax=false),
                    'date'=>"'".App::db_date($_POST['main']['fee_date'])."'",
                    'customer_id'=>$_POST['main']['registry_id'],
                    'receipt'=>"'".$receipt_tmp."'",
                    'payment_method_id'=>$_POST['main']['payment_method_id'],
                    'total'=>$fee_amount,
                    'paid'=>$fee_amount,
                    'payment_status'=>"'paid'"
                ];
                $new_receipt=$receipt->add($new_receipt);

                $petty_cash=new PettyCash();
                $new_petty_cash=[
                    'date'=>"'".App::db_date($_POST['main']['fee_date'])."'",
                    'petty_cash'=>"'Incasso ricevuta numero: ".$new_receipt->number.", Candidato: ".addslashes($new_receipt->customer->label)."'",
                    'petty_cash_purpose_id'=>'1',
                    'petty_cash_type_id'=>$_POST['main']['petty_cash_type_id'],
                    'amount'=>$fee_amount,
                    'bulk_payment'=>'0',
                    'type'=>'0',
                    'document_table'=>"'archy_tax_receipts'",
                    'document_id'=>$new_receipt->id
                ];
                $new_petty_cash=$petty_cash->add($new_petty_cash);
                
                $this->db->query("
                    update archy_memberships_examinee set 
                        deposit_receipt='".$new_receipt->receipt_number."', 
                        deposit_receipt_id=".$new_receipt->id.", 
                        deposit_petty_cash_id=".$new_petty_cash->id." 
                    where id=".$examinee_id
                );
            }
		}
		
		$message=new Message();
		$shortcodes=$message->shortcodes;
		$examinee=$this->get_examinee($examinee_id);
		$examinee->card='';
		$examinee->membership_begin='';
		$examinee->membership_end='';
		$replaces=$message->get_shortcodes_replaces($examinee);
		
		$forms=[
			'application.pdf'=>str_replace($shortcodes,$replaces,$this->settings->membership_application),
			'privacy.pdf'=>str_replace($shortcodes,$replaces,$this->settings->privacy_policy)
		];
		
		$examinee_folder=$this->app->subdomain_root.'/assets/documents/examinee/'.$examinee_id.'/';
		
		require_once $this->app->main_root.'/private/_common/classes/mpdf/vendor/autoload.php';
		
		foreach($forms as $file_name=>$form):
			$pdf='
				<html>
				<head>
				<style type="text/css">
				body{background:#FFF;color:#000;font-family:Verdana,Arial,Helvetica,sans-serif;font-size:11px;}
				</style>
				</head>
				<body>'.$form.'</body>
				</html>';

			if(!is_dir($examinee_folder)):
				mkdir($examinee_folder,0775,true);
			endif;

			$mpdf = new \mPDF('utf-8', 'A4-P');
			$mpdf->WriteHTML($pdf);
			$mpdf->Output($examinee_folder.$file_name,'F');
		endforeach;
		
	}	
		
    // Generate PDF Card
    public function create_card($member_id) {
        return $this->create_all_card($member_id,"","","","");      
    }
   
    public function create_all_card($member_id,$from_card,$to_card,$from_date,$to_date) {

        // Esce se mancano i settings della card
        if($this->settings->card_template == ""
            || !isset($this->settings->card_width)
            || !is_numeric($this->settings->card_width)
            || !isset($this->settings->card_height)
            || !is_numeric($this->settings->card_height)
        ) {
            return false;
        } else {

            $physical_path=$this->app->subdomain_root.'/assets/documents/memberships/';
            if(!is_dir($physical_path)):
                mkdir($physical_path, 0775,true);
            endif;
            $sfondo=$this->app->subdomain_root.$this->settings->card_background_image;
            $template=$this->settings->card_template;
            $css_custom=strip_tags($this->settings->card_css);
            $pdf_styles='
                <style type="text/css">
                body{font-size:8px;font-family:Verdana,Arial,Helvetica,sans-serif;maring:0;padding:0;}
                
                '.$css_custom.'
                </style>';
            $width=$this->settings->card_width;  
            $height=$this->settings->card_height;
            $pageLayout = array($width,$height); 
            $orientation = ($height>$width) ? 'P' : 'L';
            if ($member_id!=0) { // Stampa tessera singola
                $filename=$physical_path."card_".$member_id.".pdf";
                $sql="SELECT ".$member_id." AS id FROM dual";
            } elseif ($from_date=="" && $to_date=="") { // Stampa tessere per numero tessera
                $filename=$physical_path."card_all.pdf";
                $sql="SELECT id FROM archy_memberships WHERE status IN (1,5) AND card BETWEEN '".$from_card."' AND '".$to_card."'";   /* ['1'=>'Associato','2'=>'In attesa di rinnovo','3'=>'Dimesso','4'=>'Cessato','5'=>'In attesa prima quota'];*/
            } elseif ($from_card=="" && $to_card=="") { // Stampa tessere per data rinnovo
                $filename=$physical_path."card_all.pdf";
                $sql="SELECT DISTINCT member_id as id FROM archy_subscriptions WHERE date BETWEEN '".$from_date."' AND '".$to_date."'";
            } else { // Stampa tessere per numero tessera e data rinnovo
                $filename=$physical_path."card_all.pdf";
                $sql="SELECT DISTINCT m.id FROM archy_memberships m, archy_subscriptions s WHERE m.id=s.member_id AND m.status IN (1,5) and m.card BETWEEN '".$from_card."' AND '".$to_card."' AND s.date BETWEEN '".$from_date."' AND '".$to_date."'";
            }

            require_once $this->app->main_root.'/private/_common/classes/mpdf/vendor/autoload.php';
            require_once $this->app->main_root.'/private/_common/classes/tcpdf/tcpdf.php';
            $tcpdf = new TCPDF($orientation, 'mm', $pageLayout, true, 'UTF-8', false);
            $tcpdf->SetAuthor('Eteria Cloud');
            $tcpdf->SetTitle("Membership card title");
            //$tcpdf->SetSubject('Card '.$member->card." valida dal ".$member->membership_begin." al ".$member->membership_end);
            $tcpdf->SetSubject('Membership card subject...');

            $tcpdf->setPrintHeader(false);
            $tcpdf->setPrintFooter(false);
            $tcpdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
            $tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

            /* DEBUG 
            file_put_contents($filename.".html","");
            file_put_contents($filename."-raw.html","");
            */

            $rows=$this->db->query($sql);
            while($row=mysqli_fetch_object($rows)):
                $member_id=$row->id;

                $membership=new Membership($member_id);
                $member=$membership->get_member($member_id);

                $message=new Message();           
                $replaces=$message->get_shortcodes_replaces($member);
                
                $template2=str_replace($message->shortcodes,$replaces,$template);          
                $tcpdf->AddPage();

                $tcpdf->setJPEGQuality(75);
                $tcpdf->SetAutoPageBreak(false, 0);

                if(file_exists($sfondo)){
                    $tcpdf->Image($sfondo, 0, 0, $width, $height, '', '', '', false, 300, '', false, false, 0);
                }
                $pdf='<html><head>'.$pdf_styles.'</head><body>';
                $pdf.=$template2;
                $pdf.='</body></html>';
                $tcpdf->writeHTML($pdf, true, false, true, false, '');
               
                $tcpdf->setPageMark();
  
            endwhile;
            
            $tcpdf->Output($filename, 'F');

            return true;
        }
    }


}
