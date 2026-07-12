<?php

// VEDERE SE COPIARE QUALCOSA DALLA CLASSE ACTIVITY (Activity.php)
// TIPO le fn sui CERTIFICATI (sono giÃ  qui sotto...)

class Event{
	
	public 
	$app, $db;
	
	public
	$id,
	$title,
	$description,
	$category_id,
	$category_name,
	$total_credits,
	$minimum_credits,
	$max_subscribers,
	$model_width,
        $model_height,
        $model_template,
        $model_background,
	$image,
        $docs,
	$contact_mail,
	$contact_phone,
	$updated_at,
	$created_at;
	
	public $extras;
	
	public $sessions;
	
	public function __construct(){
		global $app;
		$this->db=$app->get_db();
		$this->app=$app;
		
		$this->extras=$this->sessions=[];
		
		$params=func_get_args();
		$params_count=func_num_args();
		if(method_exists($this,$f='__construct'.$params_count)):
			call_user_func_array(array($this,$f),$params);
		endif;
                
	}
	
	public function __construct1($id){
		
		$event=mysqli_fetch_object($this->db->query("select * from archy_events where id=".$id));
		
		foreach($event as $name=>$value):
			$this->$name=$value;
		endforeach;
		$this->category_name=mysqli_fetch_object($this->db->query("select category from archy_events_categories where id=".$this->category_id))->category;
		
		$extras=$this->db->query("select * from archy_events_extras where domain='event' and domain_id=".$id);
		while($extra=mysqli_fetch_object($extras)):
			$this->extras[]=$extra;
		endwhile;
		
		$sessions=$this->db->query("select * from archy_events_sessions where event_id=".$id);
		while($session=mysqli_fetch_object($sessions)):
			$session->location=mysqli_fetch_object($this->db->query("select * from archy_events_locations where id=".$session->location_id));
			$session->speeches=$this->get_session_speeches($session->id);
			$session->extras=$this->get_session_extras($session->id);
		
			$this->sessions[]=$session;
		endwhile;
		
	}
	
	public function get_session_speeches($session_id){
		$speeches=$this->db->query("select * from archy_events_speeches where session_id=".$session_id);
		$response=[];
		while($speech=mysqli_fetch_object($speeches)):
            //var_dump($speech);
            $speech->speaker=[];
            if(unserialize($speech->speakers) != null) {
                $sps=$this->db->query("select * from archy_events_speakers where id in (".implode(',',unserialize($speech->speakers)).")");
                while($sp=mysqli_fetch_object($sps)):
                    $speech->speaker[]=$sp;
                endwhile;
            }
			$response[]=$speech;
		endwhile;
		return $response;
	}
	
        public function get_session_location($location_id){
		$location=mysqli_fetch_object($this->db->query("select * from archy_events_locations where id=".$location_id));		
		return $location->name;
	}
        
	public function get_session_extras($session_id){
		$extras=$this->db->query("select * from archy_events_extras where domain='session' and domain_id=".$session_id);
		$response=[];
		while($extra=mysqli_fetch_object($extras)):
			$response[]=$extra;
		endwhile;
		return $response;
	}
	
	public function load_event_extras($id=NULL){
		$data=$this->app->load_extra_intent_form('archy_events_extras',$prefix='event[extras]',$suffix='[]',$id_suffix=$id);
		echo '<div class="row pt-1 pb-4 event-extras">';
                echo App::form_element($data['hidden_columns']['archy_events_extras.id'],'col-sm-1',$prefix,$suffix,$id_suffix);
		echo App::form_element($data['columns']['archy_events_extras.description'],'col-sm-6',$prefix,$suffix,$id_suffix);
		echo App::form_element($data['columns']['archy_events_extras.price'],'col-auto',$prefix,$suffix,$id_suffix);
		echo '<div class="col-auto">';
		echo '<label class="form-label">Azioni</label>';
                echo '<div class="input-group"><button class="btn btn-danger me-1 mb-1 trash" type="button">Cancella<span class="fas fa-trash ms-1" data-fa-transform="shrink-3"></span></button>';
                echo '<button class="btn btn-info me-1 mb-1 move" type="button">Ordina<span class="fas fa-hand-paper ms-1" data-fa-transform="shrink-3"></span></button></div>';
                echo'</div>';
                echo '</div><hr>';
		$this->app->restore();
	}
	
	public function load_session_extra($id=NULL){
		$prefix=isset($id)? 'session['.$id.'][extras]' : 'session[extras]';
		$data=$this->app->load_extra_intent_form('archy_events_extras',$prefix,$suffix='[]');
		echo '<div class="row pt-1 pb-4 event-session-extras">';
                echo App::form_element($data['hidden_columns']['archy_events_extras.id'],'col-sm-1',$prefix,$suffix,$id_suffix);
		echo App::form_element($data['columns']['archy_events_extras.description'],'col-sm-6',$prefix,$suffix);
		echo App::form_element($data['columns']['archy_events_extras.price'],'col-auto',$prefix,$suffix);
		echo '<div class="col-auto">';
		echo '<label class="form-label">Azioni</label>';
                echo '<div class="input-group"><button class="btn btn-danger me-1 mb-1 trash" type="button">Cancella<span class="fas fa-trash ms-1" data-fa-transform="shrink-3"></span></button>';
                echo '<button class="btn btn-info me-1 mb-1 move" type="button">Ordina<span class="fas fa-hand-paper ms-1" data-fa-transform="shrink-3"></span></button></div>';
                echo'</div>';
		echo '</div><hr>';
		$this->app->restore();
	}
	
	public function load_event_speech($id=NULL,$id_suffix=NULL){
		$prefix=isset($id)? 'session['.$id.'][speeches]' : 'session[speeches]';
		$data=$this->app->load_extra_intent_form('archy_events_speeches',$prefix,$suffix='[]');
		//$suffix='['.$id_suffix.']';
                //var_dump($data['columns']['archy_events_speeches.speakers']);
                echo '<div class="row pt-1 pb-4 event-speech">';
		//echo "jjjjjj suffix:".$suffix;
                echo App::form_element($data['hidden_columns']['archy_events_speeches.id'],'col-sm-1',$prefix,$suffix,$id_suffix);
                echo App::form_element($data['columns']['archy_events_speeches.speech_title'],'col-sm-5',$prefix,$suffix,$id_suffix);
                echo App::form_element($data['columns']['archy_events_speeches.date'],'col-auto',$prefix,$suffix,$id_suffix);
		echo App::form_element($data['columns']['archy_events_speeches.begin_time'],'col-auto',$prefix,$suffix,$id_suffix);
		echo App::form_element($data['columns']['archy_events_speeches.end_time'],'col-auto',$prefix,$suffix,$id_suffix);
		echo App::form_element($data['columns']['archy_events_speeches.speakers'],'col-auto',$prefix,$suffix,$id_suffix);
                echo App::form_element($data['columns']['archy_events_speeches.speech_topic'],'col-auto',$prefix,$suffix,$id_suffix);
		echo '<div class="col-auto">';
		echo '<label class="form-label">Azioni</label>';
                echo '<div class="input-group"><button class="btn btn-danger me-1 mb-1 trash" type="button">Cancella<span class="fas fa-trash ms-1" data-fa-transform="shrink-3"></span></button>';
                echo '<button class="btn btn-info me-1 mb-1 move" type="button">Ordina<span class="fas fa-hand-paper ms-1" data-fa-transform="shrink-3"></span></button></div>';
                echo'</div>';
		echo '</div><hr>';
		$this->app->restore();
	}
        
        public function load_events_for_certificates(){
            $html = '';
            $events=$this->db->query("SELECT * FROM archy_events");
            while($event=mysqli_fetch_object($events)):   
                
                
                $sessions=$this->db->query("SELECT * FROM archy_events_sessions WHERE event_id=".$event->id);
                if ($sessions->num_rows > 0) : 
                    $html.='<div class="panel panel-default">';
                    $html.='
                    <div class="panel-heading" role="tab" id="heading_event_accordion_'.$event->id.'">
                        <h4 class="panel-title"> 
                            <a role="button" data-toggle="collapse" data-parent="#event_accordion" href="#event_accordion_'.$event->id.'" aria-expanded="true" aria-controls="event_accordion_'.$event->id.'">'.$event->title.'</a>

                        </h4>
                    </div> 
                    <div id="event_accordion_'.$event->id.'" class="panel-collapse collapse out" role="tabpanel" aria-labelledby="heading_event_accordion_'.$event->id.'">
                        <div class="panel-body">
                    ';
                    while($session=mysqli_fetch_object($sessions)):
                        $html.='<div class="row"><div class="col-sm-8">';
                        $file_path_events_all='/assets/documents/events/event_'.$event->id.'_session_'.$session->id.'_all.pdf';
                        $physical_path_events_all=$this->app->subdomain_root.$file_path_events_all;
                        
                        $html.='
                            <div class="checkbox">
                                    <label>
                                            <input type="checkbox" 
                                                name="session['.$event->id.'][]" 
                                                value="'.$session->id.'" 
                                                class="event_cbox" '.$session_checked.'>

                                            <strong>SESSIONE: </strong> '.$session->title;
                        if(file_exists($physical_path_events_all)){ $html.=' - <a href="'.$file_path_events_all.'" target="_blank">Scarica PDF</a>'; }
                            $html.='                    
                                    </label>
                            </div>';
                            $html.='</div></div>';
                    endwhile;
                    $html.='</div></div>';
                    $html.='</div>';
                endif;
                
                
            endwhile; 
            
            return $html;
            
        }
	
	public function event_session($id,$visibility,$speeches=array(),$extras=array(),$scope=NULL){
		?>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading_sessions_accordion_<?php echo $id?>">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#sessions_accordion_<?php echo $id?>" aria-expanded="true" aria-controls="sessions_accordion_<?php echo $id?>">Sessione</button>
                        <?php if(!$scope):?>
                        <div class="session_actions">
                                <!--<i class="glyphicon glyphicon-duplicate duplicate_session tt" title="Duplica sessione"></i>
                                <i class="fas fa-list-ol move_session tt" title="Ordina sessioni"></i> 
                                <i class="fas fa-trash-alt trash_session tt" title="Elimina sessione"></i>-->
                                <a class="btn btn-danger me-1 trash_session" type="button">Elimina<span class="fas fa-trash ms-1" data-fa-transform="shrink-3"></span></a>
                                <a class="btn btn-info me-1 move_session" type="button">Ordina<span class="fas fa-hand-paper ms-1" data-fa-transform="shrink-3"></span></a>
                        </div>
                        <?php endif?>
                    </h2>
                    <div class="accordion-collapse collapse <?php echo $visibility; ?>" id="sessions_accordion_<?php echo $id?>" aria-labelledby="heading_sessions_accordion_<?php echo $id?>" data-bs-parent="#sessions_accordion">
                        <div class="accordion-body">
                            <div class="row g-3">
                                <?php
                                    $session_suffix='';
                                    switch($scope):
                                            case NULL://event form
                                                    $session_prefix='session['.$id.']';
                                            break;
                                            case 'session'://single session form
                                                    $session_prefix='main';
                                            break;
                                    endswitch;

                                    $data=$this->app->load_extra_intent_form('archy_events_sessions',$prefix=$session_prefix,$suffix=$session_suffix,$id_suffix=$id,$scope);

                                    //$title_width=$scope==NULL? '8' : '7';
                                    if($scope=='session'):
                                            echo App::form_element($data['hidden_columns']['archy_events_sessions.event_id'],'col-md-3',$prefix,$suffix,$id_suffix);
                                    endif;
                                    echo App::form_element($data['hidden_columns']['archy_events_sessions.id'],'col-1',$prefix,$suffix,$id_suffix);
                                    echo App::form_element($data['hidden_columns']['archy_events_sessions.title'],'col-sm-7',$prefix,$suffix,$id_suffix);
                                    echo App::form_element($data['hidden_columns']['archy_events_sessions.activity_code'],'col-auto',$prefix,$suffix,$id_suffix);
				
                                    //$location_width=$this->app->config['association']? 4 : 7;
                                    echo App::form_element($data['hidden_columns']['archy_events_sessions.location_id'],'col-sm-5',$prefix,$suffix,$id_suffix);
                                    echo App::form_element($data['hidden_columns']['archy_events_sessions.price'],'col-auto',$prefix,$suffix,$id_suffix);
                                    if($this->app->config['association']):
                                            echo App::form_element($data['hidden_columns']['archy_events_sessions.members_price'],'col-auto',$prefix,$suffix,$id_suffix);
                                    endif;
                                    //echo App::form_element($data['hidden_columns']['archy_events_sessions.tax_rate_id'],'col-md-2',$prefix,$suffix,$id_suffix);

                                    echo App::form_element($data['hidden_columns']['archy_events_sessions.start_date'],'col-auto',$prefix,$suffix,$id_suffix);
                                    echo App::form_element($data['hidden_columns']['archy_events_sessions.end_date'],'col-auto',$prefix,$suffix,$id_suffix);

                                    echo App::form_element($data['hidden_columns']['archy_events_sessions.credits'],'col-auto',$prefix,$suffix,$id_suffix);
                                    echo App::form_element($data['hidden_columns']['archy_events_sessions.training_hours'],'col-auto',$prefix,$suffix,$id_suffix);
                                    echo App::form_element($data['hidden_columns']['archy_events_sessions.manager_id'],'col-auto',$prefix,$suffix,$id_suffix);

                                    echo App::form_element($data['hidden_columns']['archy_events_sessions.required_rating_id'],'col-auto',$prefix,$suffix,$id_suffix);
                                    echo App::form_element($data['hidden_columns']['archy_events_sessions.earned_rating_id'],'col-auto',$prefix,$suffix,$id_suffix);
                                    
                                    echo App::form_element($data['hidden_columns']['archy_events_sessions.description'],'col-auto',$prefix,$suffix,$id_suffix);
                                    echo App::form_element($data['hidden_columns']['archy_events_sessions.note'],'col-auto',$prefix,$suffix,$id_suffix);

                                    echo '<hr><p class="lead text-info-emphasis fw-black text-uppercase">Attestato finale (sessione)</p>';
                                    echo App::form_element($data['hidden_columns']['archy_events_sessions.model_width'],'col-auto',$prefix,$suffix,$id_suffix);
                                    echo App::form_element($data['hidden_columns']['archy_events_sessions.model_height'],'col-auto',$prefix,$suffix,$id_suffix);

                                    echo App::form_element($data['hidden_columns']['archy_events_sessions.model_background'],'col-auto',$prefix,$suffix,$id_suffix);

                                    echo App::form_element($data['hidden_columns']['archy_events_sessions.model_template'],'col-auto',$prefix,$suffix,$id_suffix);
                                    echo App::form_element($data['hidden_columns']['archy_events_sessions.model_css'],'col-auto',$prefix,$suffix,$id_suffix);

                                    $this->app->restore();
                                ?>
                            </div>
                            
                            <hr> <p class="lead text-info-emphasis fw-black text-uppercase">Interventi</p>
                            <div class="row speakers">
                                    <?php
                                    $speeches_session_id=$scope==NULL? $id : NULL;
                                    if(count($speeches)>0):
                                            //$this->load_event_speech($id=$speeches_session_id,$id_suffix=$id,0);
                                    //else:
                                            $global_id=$_GET['id'];
                                            foreach($speeches as $index=>$speech):
                                                    $_GET['id']=$speech->id;
                                                    $this->load_event_speech($id=$speeches_session_id,$id_suffix="_".$index."_".$id,$index);
                                            endforeach;
                                            $_GET['id']=$global_id;
                                    endif;
                                    ?>
                            </div>
                            <?php if(@$_GET['safe']=='') { ?>
                            <div class="row mt-3">
                                <div class="col-2">
                                    <button type="button" class="btn btn-falcon-default btn-sm me-1 mb-1 add_speech" data-session="<?php echo $id?>">Aggiungi intervento</button>
                                </div>
                            </div>
                            <?php } ?>
                                
                            <hr><p class="lead text-info-emphasis fw-black text-uppercase">Extra</p>
                            <div class="row session_extras">
                                    <?php
                                    if(is_array($extras)):
                                            $global_id=$_GET['id'];
                                            $session_extra_id=$scope==NULL? $id : NULL;
                                            foreach($extras as $extra):
                                                    $_GET['id']=$extra->id;
                                                    $this->load_session_extra($id=$session_extra_id);
                                            endforeach;
                                            $_GET['id']=$global_id;
                                    endif;
                                    ?>
                            </div>
                            <?php if(@$_GET['safe']=='') { ?>
                            <div class="row mt-3">
                                <div class="col-2">
                                    <button type="button" class="btn btn-falcon-default btn-sm me-1 mb-1 add_session_extra" data-session="<?php echo $id?>">Aggiungi extra</button>
                                </div>
                            </div>
                            <?php } ?>

                            <?php if(isset($_GET['id']) && $_GET['id']!=''){ ?>                                        
                            <hr><p class="lead text-info-emphasis fw-black text-uppercase">Promemoria automatici</p>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="panel-group reminders_accordion" role="tablist" aria-multiselectable="true">
                                        <div class="panel-placeholder"></div>
                                        <?php 
                                            $reminder = new Reminder();                                                         
                                            echo $reminder->get('archy_events_sessions',$id);                                                        
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
		
		<?php
	}
	
        public function clear_event_add($id,$table,$domain=NULL){
           if($domain!=''){ 
                $this->db->query("delete from ".$table." where domain='".$domain."' and id=".$id);
           }else{
                $this->db->query("delete from ".$table." where id=".$id);
           }
        }
        
        public function clear_session($session_id){
	
		$this->db->query("delete from archy_events_extras where domain='session' and domain_id=".$session_id);
		$this->db->query("delete from archy_events_speeches where session_id=".$session_id);
		$this->db->query("delete from archy_events_sessions where id=".$session_id);
		
	}
        
	public function clear_related_tables($event_id){
		
		$this->db->query("delete from archy_events_extras where domain='event' and domain_id=".$event_id);
		
		$event_sessions_ids=implode(',',array_column(mysqli_fetch_all($this->db->query("select id from archy_events_sessions where event_id=".$event_id),MYSQLI_ASSOC),'id'));
		if($event_sessions_ids==''):
			$event_sessions_ids='0';
		endif;
		
		$this->db->query("delete from archy_events_extras where domain='session' and domain_id in (".$event_sessions_ids.")");
		$this->db->query("delete from archy_events_speeches where session_id in (".$event_sessions_ids.")");
		$this->db->query("delete from archy_events_sessions where id in (".$event_sessions_ids.")");
		
	}
        
        public function send($subscription_id,$session_id) {
                
            $subscription=$this->get_subscription_details($subscription_id);
            $customer = new Registry($subscription->subscriber_id);
            $event = new Event($subscription->event_id);
            $firm = new Firm();
            $firm_details = $firm->getDefaultFirm();
            
            $shortcodes =[
                '[titolo_evento]',
                '[nominativo]'
            ];
            $replaces = [
                $event->title,
                $subscription->subscriber_full_name
            ];
            
            if ($customer->email != ''):
                
                // attestato sessione
                if($session_id != ''):
                    $file_name_event_session=$subscription->id."_event_".$subscription->event_id."_session_".$session_id."_".$subscription->subscriber_id.".pdf"; 
                else:
                    $file_name_event_session=$subscription->id."_event_".$subscription->event_id."_".$subscription->subscriber_id.".pdf";
                endif;
                  
                $file_path_event_session="/assets/documents/events/".$file_name_event_session;
                $physical_path_event_session=$this->app->subdomain_root.$file_path_event_session;
                
                $message = new Message();

                $mail_subject = $event->event_mail_subject;
          
                $mail_body = $event->event_mail_body;
                $mail_body = str_replace($shortcodes, $replaces, $mail_body);
             
                $email = [
                    'recipient_name' => $customer->firm_name,
                    'recipient_mail' => $customer->email,
                    'sender_name' => $firm_details->firm_name,
                    'sender_mail' => $firm_details->email,
                    'subject' => $mail_subject,
                    'message' => $mail_body,
                    'attachments' => [$file_name_event_session => file_get_contents($physical_path_event_session)]
                ];
               
                $message->email($email);
                                                       
                
                $sent_on=mysqli_fetch_object($this->db->query("SELECT sent_on FROM archy_events_subscriptions WHERE id=".$subscription_id))->sent_on;
                if($this->app->is_serialized($sent_on)):
                        $sent_on=unserialize($sent_on);
                else:
                        $sent_on=[];
                endif;                
                if($session_id != ''):
                    $sent_on[]=['session_id' => $session_id];
                else:
                    $sent_on[]=['event_id' => $subscription->event_id];
                endif;
                
                
                $this->db->query("update archy_events_subscriptions set sent_on='".addslashes(serialize($sent_on))."' where id=" . $subscription_id);
              
                set_message("success", "Attestato inviato correttamente.");

            else:

                set_message("danger", "Non è stato possibile inviare l'attestato, al cliente non è associato un indirizzo e-mail.");

            endif;

            header('location: ' . $_SERVER['HTTP_REFERER']);

        }
	
	public function store($event_id){
		//error_log('@@@@FILES STORE@@@@:' .print_r($_FILES,true));
                // GESTIONE EXTRA EVENTI SIA IN INSERT CHE IN UPDATE
		if(is_array($_POST['event']['extras']['description'])):
                    
                    foreach($_POST['event']['extras']['description'] as $index=>$description):
                        $action_to_event_extra = 'insert into';
                        if(isset($_POST['event']['extras']['id'][$index]) && $_POST['event']['extras']['id'][$index]!=''){$action_to_event_extra = 'update';}
                        $q=$action_to_event_extra." archy_events_extras SET
                                domain='event',
                                domain_id=".$event_id.",
                                description='".addslashes($description)."',
                                price=".$_POST['event']['extras']['price'][$index]."
                        ";
                        if($action_to_event_extra=='update'):
                            $q.=" where id=".$_POST['event']['extras']['id'][$index];
                        endif;
                        $ret=$this->db->query($q);
                        show_query_error($ret,$this->db,$q,"event->store() 1");
                    endforeach;
                    
		endif;
		
                // GESTIONE SESSIONI EVENTI SIA IN INSERT CHE IN UPDATE
		if(is_array($_POST['session'])):
                    foreach($_POST['session'] as $id=>$session):
                        $action_to_sessions = 'insert into';
                        if(isset($session['id']) && $session['id']!=''){$action_to_sessions = 'update';}
                    
                        $members_price=(empty($session['members_price'])?'NULL':$session['members_price']);
                        $price=(empty($session['price'])?'NULL':$session['price']);
                        $model_height=(empty($session['model_height'])?'NULL':$session['model_height']);
                        $model_width=(empty($session['model_width'])?'NULL':$session['model_width']);
                        $q=$action_to_sessions." archy_events_sessions SET
                                event_id=".$event_id.",
                                title='".addslashes($session['title'])."',
                                description='".addslashes($session['description'])."',
                                location_id=".(empty($session['location_id'])?'NULL':$session['location_id']).",
                                start_date='".App::db_date($session['start_date'])."',
                                end_date='".App::db_date($session['end_date'])."',
                                price=".$price.",
                                members_price=".$members_price.",
                                credits=".(empty($session['credits'])?'NULL':$session['credits']).",
                                required_rating_id=".(empty($session['required_rating_id'])?'NULL':$session['required_rating_id']).",
                                earned_rating_id=".(empty($session['earned_rating_id'])?'NULL':$session['earned_rating_id']).",
                                model_width=".$model_width.",
                                model_height=".$model_height.",
                                model_template='".addslashes($session['model_template'])."',
                                model_css='".addslashes($session['model_css'])."'
                        ";
                        
                        if($action_to_sessions=='update'):
                            $q.=" where id=".$id;
                        endif;
                        $ret=$this->db->query($q);
                        show_query_error($ret,$this->db,$q,"event->store() 2");
                        
                        if($action_to_sessions=='insert into'):
                            $session_id=$this->db->insert_id;
                        endif;
                        if($action_to_sessions=='update'):
                            $session_id=$id;
                        endif;
                        
                        $this->store_session_files('archy_events_sessions',$session_id);
                        
                        // GESTIONE INTERVENTI SESSIONE SIA IN INSERT CHE IN UPDATE
                        if(is_array($session['speeches']['begin_time'])):
                            $speeches=$session['speeches'];
                                                       
                            foreach($speeches['begin_time'] as $index=>$begin_time):
                                $action_to_speeches = 'insert into'; 
                                if($speeches['id'][$index]>0){$action_to_speeches = 'update';}
                                $q=$action_to_speeches." archy_events_speeches SET
                                        session_id=".$session_id.",
                                        speech_title='".addslashes($speeches['speech_title'][$index])."',
                                        date='".App::db_date($speeches['date'][$index])."',
                                        begin_time='".$begin_time."',
                                        end_time='".$speeches['end_time'][$index]."',
                                        speakers='".serialize($speeches['speakers'][$index])."',
                                        speech_topic='".addslashes($speeches['speech_topic'][$index])."'
                                ";
                                if($action_to_speeches=='update'):
                                    $q.=" where id=".$speeches['id'][$index];
                                endif;
                                $ret=$this->db->query($q);
                                show_query_error($ret,$this->db,$q,"event->store() 3");                               
                            endforeach;
                            
                        endif;
                           
                        // GESTIONE EXTRA SESSIONE SIA IN INSERT CHE IN UPDATE
                        if(is_array($session['extras']['description'])):
                            $session_extras=$session['extras'];
                            
                            foreach($session_extras['description'] as $index=>$description):
                                $action_to_session_extras = 'insert into'; 
                                if($session_extras['id'][$index]>0){$action_to_session_extras = 'update';}
                                $q=$action_to_session_extras." archy_events_extras SET
                                        domain='session',
                                        domain_id=".$session_id.",
                                        description='".addslashes($description)."',
                                        price=".$session_extras['price'][$index]."
                                ";
                                if($action_to_session_extras=='update'):
                                    $q.=" where id=".$session_extras['id'][$index];
                                endif;
                                $ret=$this->db->query($q);
                                show_query_error($ret,$this->db,$q,"event->store() 4");
                            endforeach;
                            
                        endif;

                    endforeach;
		endif;
	
	}
        
     
    public function store_session_files($intent,$session_id){
            
                        
                    foreach($_FILES['session']['name'][$session_id] as $field_name=>$file_name):
                            
                            if(!empty($file_name)):
                                $path='/assets/documents/';
                                $physical_path=$this->app->subdomain_root.$path;
                                if(!is_dir($physical_path)):
                                    mkdir($physical_path, 0775,true);
                                endif;
                                
                                // se e'è, rimuovo file dal fs
                                $ret=mysqli_fetch_object($this->db->query("select model_background from archy_events_sessions where id=".$session_id));
                                //error_log('######################'.$this->app->subdomain_root.$ret->model_background);
                                if(is_file($this->app->subdomain_root.$ret->model_background)):
                                    unlink($this->app->subdomain_root.$ret->model_background);
                                endif;
                         
                                $prefix='';
                                // conflitto nomi uguali
                                $counter=1;
                                while(is_file($physical_path.$prefix.$value)):
                                    $prefix=$counter.'-';
                                    $counter++;
                                endwhile;
                      
                                move_uploaded_file($_FILES['session']['tmp_name'][$session_id][$field_name], $physical_path.$prefix.$file_name);
                          
                                $this->db->query("update ".$intent." set ".$field_name."='".addslashes($path.$file_name)."' where id=".$session_id);
                                   
                            endif;
                    endforeach;
    }

    public function after_delete() {
        $this->clear_related_tables($this->insert_id);
        //$this->db->query("DELETE FROM archy_events_extras WHERE domain = 'event' AND domain_id=" . $this->id);
    }
    
    public function feed(){
		$response=[];
		
		$events=$this->db->query("
			SELECT *
			  FROM archy_events AS a
			  LEFT JOIN archy_events_categories AS c ON	a.category_id=c.id
		");
		while($event=mysqli_fetch_object($events)):
			$event->table='events';
			$response[]=$event;
		endwhile;

		$sessions=$this->db->query("
            SELECT *
              FROM archy_events AS e
              LEFT JOIN archy_events_sessions AS ss ON e.id=ss.event_id
		");
		while($ss=mysqli_fetch_object($sessions)):
			$ss->table='sessions';
			$response[]=$ss;
		endwhile;
		
		echo json_encode($response);
		
	}

    public function get_session($session_id){
        $session=[];
        $rows = $this->db->query("select * from archy_events_sessions where id=" . $session_id);
        if ($rows->num_rows > 0) :
            $r=mysqli_fetch_object($rows);
            $session = $r;
        endif;
        return $session;
    }
    
    public function get_subscription_details($id){
        $subscription=[];
        $rows = $this->db->query("select * from archy_events_subscriptions where id=" . $id);
        if ($rows->num_rows > 0) :
            $r=mysqli_fetch_object($rows);
            $subscription = $r;
        endif;
        return $subscription;
    }
    
    public function update_credits_by_event($credits,$event_id,$subscriber_id){ // aggiorna il conteggio dei crediti maturati dopo la convalida
        $this->db->query("update archy_events_subscriptions set accrued_credits=(accrued_credits+$credits) where event_id=".$event_id." AND subscriber_id=".$subscriber_id);
    }
    
    public function update_earned_rating_id($registry_id,$rating_id,$action='',$id=NULL){ // aggiorna o inserisce il rating associato dopo la convalida
        $check_rating_parent = mysqli_fetch_object($this->db->query("select id,parent_rating_id,due_date from archy_ratings where id=".$rating_id));
        if($check_rating_parent->parent_rating_id != 'NULL'){ $rating_id = $check_rating_parent->parent_rating_id; } 
        
        $rows = $this->db->query("select * from archy_rating_relations where registry_id=" . $registry_id." AND rating_id=".$rating_id);
        if ($rows->num_rows == 0) : // non c'è abbinamento
            if($action == 'update'): // azione quando inserisco o modifico a mano una relazione
                $this->db->query("update archy_rating_relations set rating_id=".$rating_id.",rating_due_date='".$check_rating_parent->due_date."',rating_children=',".$check_rating_parent->id."' where registry_id=".$registry_id." and id=".$id);
            else:    
               //error_log("insert into archy_rating_relations set registry_id=".$registry_id.",rating_id=".$rating_id.",rating_due_date='".$check_rating_parent->due_date."',rating_children=',".$check_rating_parent->id."'");
                $this->db->query("insert into archy_rating_relations set registry_id=".$registry_id.",rating_id=".$rating_id.",rating_due_date='".$check_rating_parent->due_date."',rating_children=',".$check_rating_parent->id."'");
            endif;            
        else:   // c'è già abbinamento
            if($action == 'update'): // azione quando inserisco o modifico a mano una relazione
                //$this->db->query("delete from archy_rating_relations where id=".$id);
                //set_message("danger", "Abbinamento rating/anagrafica già presente");
            else:
                $r=mysqli_fetch_object($rows);
                $rating_children = $r->rating_children;
                $this->db->query("update archy_rating_relations set rating_due_date='".$check_rating_parent->due_date."',rating_children='".$rating_children.",".$check_rating_parent->id."' where registry_id=".$registry_id." AND rating_id=".$rating_id); // aggiorno la data di scadenza
            endif;
        endif;  
        
        
        // controllare se c'è già e cancellarlo??????
        
                  
    }
    
    // Recupera gli iscritti (archy_events_subscriptions) da una sessione (archy_events_sessions) di un evento
    public function get_session_subscriptions($session_id){

        $subscriptions=[];
        $rows = $this->db->query("select * from archy_events_sessions where id=" . $session_id);
        if ($rows->num_rows > 0) :
            $r=mysqli_fetch_object($rows);
            $event_id = $r->event_id;
            $credits = $r->credits;
			
            $rows = $this->db->query("select * from archy_events_subscriptions where event_id=" . $event_id);
            $subscriptions = [];
			
            while ($event_subscription = mysqli_fetch_object($rows)) :
                //var_dump('event_subscription',$event_subscription);
    
                $session_ids=array_keys(unserialize($event_subscription->sessions));
                //var_dump('session_ids',$session_ids);
                
                if(in_array($session_id,$session_ids)):
                    $event_subscription->total_paid=0;
                    $payments=unserialize($event_subscription->payments);
                    //var_dump('payments',$payments);
                    if(is_array(@$payments['paid'])):
                        foreach($payments['paid'] as $index=>$paid):
                            $event_subscription->total_paid+=$paid;
                        endforeach;
                    endif;
                    $event_subscription->balance=$event_subscription->total_paid-$event_subscription->subscription_total;
                    
                    $headcounts=$this->app->is_serialized($event_subscription->accounted)? unserialize($event_subscription->accounted):[]; 
                    $event_subscription->checked=in_array($event_id,array_keys($headcounts))? 'checked':'';
                    //var_dump('headcounts',$headcounts);
                   /* $event_subscription->accrued_credits=0;
                    foreach($headcounts as $accounted_event_id=>$static_one):
						//$event_subscription->accrued_credits+=$credits[$accounted_event_id];
						$event_subscription->accrued_credits=$credits;
                    endforeach;*/
                    $subscriptions[$event_subscription->id]=$event_subscription;   
                    
                endif;
            endwhile;
            
        endif;
        return $subscriptions;
	}

    }