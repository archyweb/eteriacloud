<?php
class Reminder{
    
    public $app, $db;
    public $reminders, $settings;
    
    public function __construct(){
        
        global $app;
        $this->db=$app->get_db();
        $this->app=$app;
        $this->reminders=array();
        
        $settings=$app->get_settings($domain='association_1');
        $settings2=$app->get_settings($domain='association_2');
        $this->settings= (object) array_merge((array) $settings, (array) $settings2);
    }
    
    public function get($origin,$origin_id){ // Es. session, session_id
        $items=$this->db->query("select * from archy_reminders where reminder_origin='".$origin."' AND reminder_origin_id='".$origin_id."'");
        
        $response = '';
                
        if ($items->num_rows > 0) :       
            while($item=mysqli_fetch_object($items)):
                $this->load_reminder_layout($item->id,$origin,$origin_id);
            endwhile;
        endif;
        
        //$response .= $this->load_reminder_layout($id,$visibility);
        $response .= '<button type="button" class="btn btn-falcon-default btn-sm me-1 mb-1 add_reminder" data-origin="'.$origin.'" data-origin-id="'.$origin_id.'"><span class="fas fa-plus me-1" data-fa-transform="shrink-3"></span>Aggiungi promemoria</button>';
      
        return $response;
        
    }
    
    public function load_reminder_layout($id=NULL,$origin='',$origin_id=''){
        $new_load=false;
        $global_id=$_GET['id'];
        if($id==NULL):
            $now=new DateTime();
            $id=$now->format('Ymdhisv');
            $new_load=true;
        endif;
        $origin=isset($_POST['reminder_origin'])? $_POST['reminder_origin'] : $origin;
        $origin_id=isset($_POST['reminder_origin_id'])? $_POST['reminder_origin_id'] : $origin_id;
        $_GET['id']=$id;
        $data=$this->app->load_extra_intent_form('archy_reminders',$prefix='reminder',$suffix='',$id_suffix=$id,'',$new_load);
        
        $Placeholder = new Placeholder();
        
        echo '<div class="accordion-item">';
        echo $this->load_reminder_layout_heading_panel($id);
        //echo '<div id="reminders_accordion_'.$id.'" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading_reminders_accordion_'.$id.'"><div class="panel-body"><div class="row">';
        echo '
        <div class="accordion-collapse collapse" id="reminders_accordion_'.$id.'" aria-labelledby="heading_reminders_accordion_'.$id.'" data-bs-parent="#eteria_reminder">
            <div class="accordion-body">
                <div class="row g-3">';
        //echo '<form class="reminder_form">';
        echo App::form_element($data['hidden_columns']['archy_reminders.id'],'col-md-1',$prefix,$suffix,$id_suffix);
        echo App::form_element($data['hidden_columns']['archy_reminders.reminder_origin'],'col-md-1',$prefix,$suffix,$id_suffix,$origin);
        echo App::form_element($data['hidden_columns']['archy_reminders.reminder_origin_id'],'col-md-1',$prefix,$suffix,$id_suffix,$origin_id);
        echo App::form_element($data['columns']['archy_reminders.reminder_title'],'col-md-12',$prefix,$suffix,$id_suffix);
        echo App::form_element($data['columns']['archy_reminders.reminder_days'],'col-md-4',$prefix,$suffix,$id_suffix);
        echo App::form_element($data['columns']['archy_reminders.reminder_delay'],'col-md-4',$prefix,$suffix,$id_suffix);
        echo App::form_element($data['columns']['archy_reminders.reminder_subject'],'col-md-6',$prefix,$suffix,$id_suffix);
        echo App::form_element($data['columns']['archy_reminders.reminder_description'],'col-md-8',$prefix,$suffix,$id_suffix);
        //echo App::form_element($data['hidden_columns']['archy_reminders.sms'],'col-md-12',$prefix,$suffix,$id_suffix);
        //echo '<i class="glyphicon glyphicon-trash trash"></i>';
        //echo '</form>';
        echo '<div class="col-md-4">';
        echo '<div class="placeholder_container" data-rel="'.$id.'">';
        echo '<p><strong>Placeholder</strong></p>';
        echo '<p><label>Seleziona dove inserire il placeholder</label> <select name="placeholder_target_'.$id.'" class="placeholder_target_'.$id.'"><option value="1" data-rel="'.$id.'">Oggetto</option><option value="2" selected>Editor</option></select></p>';
        echo $Placeholder->getAvailablePlaceholders($origin);
        echo '</div></div>';
        echo '</div></div></div>';
        echo '</div>';
        $this->app->restore();
        $_GET['id']=$global_id;
    }
    
    public function load_reminder_layout_heading_panel($id){
        $reminder_title = $this->getReminderTitle($id);
        
        return '<h2 class="accordion-header panel-heading-reminders" id="heading_reminders_accordion_'.$id.'" data-id="'.$id.'">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#reminders_accordion_'.$id.'" aria-expanded="true" aria-controls="reminders_accordion_'.$id.'">'.$reminder_title.'</button>
              <span class="reminder_actions">
                                <button class="saveReminder btn btn-success"><span class="fas fa-save" title="Salva promemoria"></span> SALVA</button>
                                <button class="trashReminder btn btn-warning"><span class="far fa-trash-alt" title="Elimina promemoria"></span> CANCELLA</button>
                        </span>
</h2>';
        
        /*return '<div class="panel-heading panel-heading-reminders" role="tab" data-id="'.$id.'" id="heading_reminders_accordion_'.$id.'">
                <h4 class="panel-title"> 
                        <a role="button" data-toggle="collapse" data-parent="#reminders_accordion" href="#reminders_accordion_'.$id.'" aria-expanded="true" aria-controls="reminders_accordion_'.$id.'">'.$reminder_title.'</a>
                        
                        <span class="reminder_actions">
                                <button class="save btn btn-success"><i class="glyphicon glyphicon-floppy-save" title="Salva promemoria"></i> SALVA</button>
                                <button class="trash btn btn-warning"><i class="glyphicon glyphicon-trash" title="Elimina promemoria"></i> CANCELLA</button>
                        </span>
                        
                </h4>
        </div>';*/
    }
    
    public function getReminderTitle($id){
        $q = mysqli_fetch_object($this->db->query("select reminder_title from archy_reminders where id=".$id));
        if($q == null){
            return 'Promemoria';
        }else{ return $q->reminder_title; }
    }
    
    public function save(){
        if(isset($_POST['reminder']['id'])){
            $id = $_POST['reminder']['id'];

            if($id!=''){ // update

                $this->db->query("update archy_reminders set 
                        reminder_title='".$_POST['reminder']['reminder_title']."',
                        reminder_days='".$_POST['reminder']['reminder_days']."',
                        reminder_delay='".$_POST['reminder']['reminder_delay']."',
                        reminder_subject='".$_POST['reminder']['reminder_subject']."',
                        reminder_description='".$_POST['reminder_description']."',
                        reminder_sms='".$_POST['reminder']['reminder_sms']."' 
                        where id=".$id);         

            }else{ // insert
                $this->db->query("insert into archy_reminders set 
                        reminder_title='".$_POST['reminder']['reminder_title']."',
                        reminder_days='".$_POST['reminder']['reminder_days']."',
                        reminder_delay='".$_POST['reminder']['reminder_delay']."',
                        reminder_origin='".$_POST['reminder']['reminder_origin']."',
                        reminder_origin_id='".$_POST['reminder']['reminder_origin_id']."',
                        reminder_subject='".$_POST['reminder']['reminder_subject']."',
                        reminder_description='".$_POST['reminder_description']."',
                        reminder_sms='".$_POST['reminder']['reminder_sms']."'");

                //set_message("success", "Promemoria creato correttamente");

            }
        }
    }
    
    public function getAlert(){
        
    }
    
    public function delete(){
	$id = $_POST['id'];
        $this->db->query("delete from archy_reminders where id=".$id);
    }
    
    public function getRemindersGroupedByOrigin(){
        $items=$this->db->query("select * from archy_reminders");
        
        $response = [];
                
        if ($items->num_rows > 0) :       
            while($item=mysqli_fetch_object($items)):
                $response[$item->reminder_origin][$item->reminder_origin_id][]=[
                                'id'=>$item->id,
                                'reminder_title'=>$item->reminder_title,
				'reminder_days'=>$item->reminder_days,
				'reminder_delay'=>$item->reminder_delay,
				'reminder_description'=>$item->reminder_description,
				'reminder_subject'=>$item->reminder_subject,
				'reminder_sms'=>$item->reminder_sms
			];
            endwhile;
        endif;
      
        return $response;
        
    }
    
    public function sendReminder($origin,$origin_id,$days,$delay,$subject,$description){
        $queue=new Queue();
        $message=new Message();
        
        
        if(($days=='')||empty($description)||empty($subject)):
            //echo "<br>@@@@@@@@@@ key: >".$key."< (FOUND EMPTY FIELD) => SKIPPED!";
        else:
            $gg=$delay=='before'? '+':'-';
            $gg.=$days!=''? $days : '0';
            $date=new DateTime();
            $date->modify($gg.' days');
            
            // query by origin
            switch ($origin) {
                case 'archy_services':
                    
                    $q=$this->db->query("select m.id service_membership_id,s.id service_subscription_id, m.*, r.*, s.*
                    from archy_services_memberships as m                      
                    left join archy_registry as r on (m.registry_id=r.id)
                    left join archy_services_subscriptions as s on (m.id=s.services_memberships_id)
                    where membership_end='".$date->format('Y-m-d')."'");                   
                    
                    break;
                case 'archy_events_sessions':
                    
                    $rows = $this->db->query("select * from archy_events_subscriptions");
                    $subscriptions = [];
                    while ($event_subscription = mysqli_fetch_object($rows)) :
                        $session_ids=array_keys(unserialize($event_subscription->sessions));
                        if(in_array($origin_id,$session_ids)):
                            $subscriptions[]=$event_subscription->subscriber_id;
                        endif;
                    endwhile;
                    
                    $q=$this->db->query("select sub.*, s.*, r.*, m.*, e.*, l.*, spc.*
                    from archy_events_subscriptions as sub
                    left join archy_events_sessions as s on (s.id=".$origin_id.")
                    left join archy_events_speeches as spc on (s.id=spc.session_id)
                    left join archy_events_speakers as spk on (spk.id REGEXP CONCAT_WS('','.s:[0-9];s:', spc.speakers ,';.'))
                    left join archy_events as e on (sub.event_id=e.id)
                    left join archy_events_locations as l on (s.location_id=l.id)
                    left join archy_registry as r on (sub.subscriber_id=r.id)
                    left join archy_memberships as m on (sub.subscriber_id=m.registry_id)
                    where sub.subscriber_id IN(".implode(",", $subscriptions).") and s.start_date='".$date->format('Y-m-d')."'");
                    
                    break;
                default:
                    break;
            }
            
            while($item=mysqli_fetch_object($q)):
                
                $email_subject = Placeholder::load($subject)->checkReplace($origin,$item);
                $email_description = Placeholder::load($description)->checkReplace($origin,$item);
                
                $email=[
                    'recipient_name'=>$item->label,
                    'recipient_mail'=>$item->email,
                    'sender_name'=>$this->settings->sender_name,
                    'sender_mail'=>$this->settings->sender_email,
                    'subject'=>$email_subject,
                    'message'=>$email_description,                    
                ];
                $queue->enqueue($email);
                
            endwhile;
            
        endif;
        
    }
    
    public function updateServiceMembership(){
        
        // GESTIONE STATUS
        $date=new DateTime();
        
        // GESTIONE PRIMA QUOTA : '6'=>'In aggiornamento...' => '1'=>'Servizio attivo'
        $service_memberships=$this->db->query("
        select *
        from archy_services_memberships sm 
            left join archy_services s on (s.id = sm.services_id)
            left join archy_services_subscriptions ss on (sm.id = ss.services_memberships_id)
            left join archy_registry r on (r.id = sm.registry_id) 
        where (sm.status='6') and sm.membership_end>='".$date->format('Y-m-d')."' and sm.membership_begin<='".$date->format('Y-m-d')."'
        ");

        while($member=mysqli_fetch_object($service_memberships)):
            $this->db->query("UPDATE archy_services_memberships SET status='1' WHERE registry_id=".$member->registry_id);
        endwhile;

        
        // GESTIONE SCADENZA : '1'=>'Servizio attivo','2'=>'In attesa di rinnovo' => '3'=>'Servizio scaduto'
        $service_memberships=$this->db->query("
        select *
        from archy_services_memberships sm 
            left join archy_services s on (s.id = sm.services_id)
            left join archy_services_subscriptions ss on (sm.id = ss.services_memberships_id)
            left join archy_registry r on (r.id = sm.registry_id) 
        where (sm.status='1' or sm.status='2') and sm.membership_end<'".$date->format('Y-m-d')."'
        ");

        while($member=mysqli_fetch_object($service_memberships)):
            $this->db->query("UPDATE archy_services_memberships SET status='3' WHERE services_id=".$member->services_id." and registry_id=".$member->registry_id);
        endwhile;
        
    }
    
    public function check_reminders_status(){
        
        $reminders = $this->getRemindersGroupedByOrigin();
        
        foreach($reminders as $reminder_type=>$reminder_type_data): // key = reminder type, data = reminder_type_data
            
            foreach($reminder_type_data as $reminder_origin_id=>$reminder_data): // key reminder_origin_id, data = reminder_data
               
                foreach($reminder_data as $key=>$data):
            
                     $this->sendReminder($reminder_type,$reminder_origin_id,$data['reminder_days'],$data['reminder_delay'],$data['reminder_subject'],$data['reminder_description']);
                                
                endforeach;
            endforeach;
            
            // chiamo funzioni specifiche per eventuali update
            switch ($reminder_type) {
                case 'archy_services':
                    $this->updateServiceMembership();
                    break;
                default:
                    break;
            }
           
        endforeach;
        
        return;
    }
    
}

