<?php
// Servizi, Abbonamenti ai servizi e quote abbonati.
class ServiceMembership {
	
	public $app, $db, $notifications, $association_settings, $recessions;
	
	public $membership, $statuses;
	
	public function __construct(){
		
		global $app;
		$this->db=$app->get_db();
		$this->app=$app;
		
		$settings=$app->get_settings($domain='association_1');
		$settings2=$app->get_settings($domain='association_2');
		$this->settings= (object) array_merge((array) $settings, (array) $settings2);
		
		$this->statuses=['5'=>'In attesa primo versamento','1'=>'Servizio attivo','2'=>'In attesa di rinnovo','3'=>'Servizio scaduto',''=>'','4'=>'Servizio cessato','6'=>'In aggiornamento...'];
		
		$params=func_get_args();
		$params_count=func_num_args();
		if(method_exists($this,$f='__construct'.$params_count)):
			call_user_func_array(array($this,$f),$params);
		endif;

        $this->shortcodes=[
			'[name]',
			'[surname]',
			'[RagioneSociale/Nominativo]',
			'[DataNascita]',
			'[LuogoNascita]',
			'[PartitaIVA]',
			'[CodiceFiscale]',
			'[Indirizzo]',
			'[zip]',
			'[Comune]',
			'[Provincia]',
			'[SiglaProvincia]',
			'[Regione]',
			'[NazioneIT]',
			'[NazioneEN]',
			'[Telefono]',
			'[Fax]',
			'[Cellulare]',
			'[EMail]',
			'[PEC]',
			'[SitoWeb]',
            '[Foto]',

            '[NomeServizio]',
            '[InizioServizio]',
            '[ScadenzaServizio]',
            '[DataVersamentoServizio]',
            '[Prezzo]',
            '[DataOdierna]',
        ];
	}
	
	public function __construct1($membership_id){
        $this->membership=mysqli_fetch_object($this->db->query("select * from archy_services_memberships where id=".$membership_id));
        $this->membership->service=mysqli_fetch_object($this->db->query("select * from archy_services where id=".$this->membership->services_id));
        $this->membership->registry=mysqli_fetch_object($this->db->query("select * from archy_registry where id=".$this->membership->registry_id));
        $this->membership->member=mysqli_fetch_object($this->db->query("select * from archy_memberships where registry_id=".$this->membership->registry_id));
        $this->membership->subscription=mysqli_fetch_object($this->db->query("select * from archy_services_subscriptions where services_memberships_id=".$membership_id." order by date desc limit 0,1"));
        if($this->membership->registry->city_id!='' and $this->membership->registry->city_id!=0):
			$this->membership->city=mysqli_fetch_object($this->db->query("select * from archy_cities where id=".$this->membership->registry->city_id));
			$this->membership->country=mysqli_fetch_object($this->db->query("select * from archy_countries where id=".$this->membership->city->country_id));
		endif;
        $this->membership->status_label=$this->statuses[$this->membership->status];
	}

    // $service_membership è un oggetto ServiceMembership
    public function get_shortcodes_replaces($service_membership){

        $membership=$service_membership->membership; 
        $registry=$membership->registry;
        $member=$membership->member;
        $service=$membership->service;
        $subscription=$membership->subscription;

        $city=$membership->city;
        $country=$membership->country;

        // var_dump($registry); die;
        
		$replaces=[
			$registry->name,
			$registry->surname,
			$registry->label,
			App::latin_date($registry->birth_date),
			$registry->birth_place,
			$registry->vat_id,
			strtoupper($registry->tax_code),
			$registry->address,
			$registry->zip,
			$city->city_label,
			$city->province_district,
			$city->province_district_code,
			$city->state_region,
			$country->country_it,
			$country->country_en,
			$registry->telephone,
			$registry->fax,
			$registry->mobile,
			$registry->email,
			$registry->certified_email,
			$registry->website,
            '<img src="'.stripslashes($registry->main_picture).'"/>',
            $service->description, // [NomeServizio]
            App::latin_date($membership->membership_begin), // [InizioServizio]
            App::latin_date($membership->membership_end), // [ScadenzaServizio]
            App::latin_date($subscription->date), // [DataVersamentoServizio]
            $subscription->amount, // [Prezzo]
            date('d-m-Y'), // [Data odierna]
		];
		return $replaces;
	}

    // Generate PDF Certificate of attendance
    public function create_certificate_of_attendance($membership_id) {

        $service_membership=new ServiceMembership($membership_id);

        $certificates_width = $service_membership->membership->service->certificates_width;
        $certificates_height = $service_membership->membership->service->certificates_height;
        $certificates_model = $service_membership->membership->service->certificates_model;
        $certificates_background_image = $service_membership->membership->service->certificates_background_image;

        // Esce se mancano i settings della card
        if($certificates_model == ""
            || !isset($certificates_width)
            || !is_numeric($certificates_width)
            || !isset($certificates_height)
            || !is_numeric($certificates_height)
        ) {
            return false;
        } else {

            // GENERAZIONE 1° DOCUMENTO 
            // Template di frequenza/attestato/certificazione
            $sfondo=$this->app->subdomain_root.$certificates_background_image;
            $member=$service_membership->membership->registry;           
            $replaces=$this->get_shortcodes_replaces($service_membership);
            $template2=str_replace($this->shortcodes,$replaces,$certificates_model);
            
            $styles='<style type="text/css">';
            $styles.='body{font-size:8px;font-family:Verdana,Arial,Helvetica,sans-serif;maring:0;padding:0;}';
            //$styles.='img{width:50px;height:50px}';
            $styles.='</style>';

            $pdf='<html><head>'.$styles.'</head><body>'.$template2.'</body></html>';
            //error_log("@@@@@@@ create_certificate_od_attendance : pdf >".$pdf."<");

            require_once $this->app->main_root.'/private/_common/classes/mpdf/vendor/autoload.php';

            $physical_path=$this->app->subdomain_root.'/assets/documents/services/';
            if(!is_dir($physical_path)):
                mkdir($physical_path, 0775,true);
            endif;
            $filename=$physical_path."membership_".$membership_id.".pdf";

            require_once $this->app->main_root.'/private/_common/classes/tcpdf/tcpdf.php';
            //$tcpdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            
            $width=$certificates_width;  
            $height=$certificates_height;
            $pageLayout = array($width,$height); 
            $orientation = ($height>$width) ? 'P' : 'L';  
            $tcpdf = new TCPDF($orientation, 'mm', $pageLayout, true, 'UTF-8', false);

            $tcpdf->SetAuthor('Eteria Cloud');
            $tcpdf->SetTitle("Certifcato rilasciato a '".$member->label."'");
            $tcpdf->SetSubject('Abbonamento '.$member->description." valido dal ".$member->membership_begin." al ".$member->membership_end);

            $tcpdf->setPrintHeader(false);
            $tcpdf->setPrintFooter(false);
            $tcpdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
            //$tcpdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
            $tcpdf->SetMargins(0,0,0);
            $tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
            //$tcpdf->SetFont('times', 'BI', 20);
            $tcpdf->AddPage();

            $tcpdf->setJPEGQuality(75);

            //$bMargin = $tcpdf->getBreakMargin();
            //$auto_page_break = $tcpdf->getAutoPageBreak();
            $tcpdf->SetAutoPageBreak(false, 0);
            if(file_exists($sfondo)){
                $tcpdf->Image($sfondo, 0, 0, $width, $height, '', '', '', false, 300, '', false, false, 0);
            }
            //$tcpdf->SetAutoPageBreak($auto_page_break, $bMargin);
            $tcpdf->setPageMark();

            $tcpdf->writeHTML($pdf, true, false, true, false, '');
            //$tcpdf->Write(0, $pdf, '', 0, 'C', true, 0, false, false, 0);

            $tcpdf->Output($filename, 'F');




            // GENERAZIONE 2° DOCUMENTO 
            // Template generico
            $template2=str_replace($this->shortcodes,$replaces,$service_membership->membership->service->module_model);

            $pdf_styles='
            <style type="text/css">
                body{background:#FFF;color:#000;font-family:Verdana,Arial,Helvetica,sans-serif;font-size:11px;}
            </style>';
            $pdf_file = '<html><head>'.$pdf_styles.'</head><body>'.$template2.'</body></html>';
            $filename=$physical_path."membership_standard_".$membership_id.".pdf";
            $mpdf = new \mPDF('utf-8', 'A4-P');
            $mpdf->WriteHTML($pdf_file);
            $mpdf->Output($filename, 'F');

            return true;
        }
    }
    
    private function check_notices($key,$subject,$model,$days,$delay) {
        $queue=new Queue();
        $message=new Message();
		$shortcodes=$this->shortcodes;
        //var_dump($shortcodes);
		
        echo "<hr>";
        if(($days=='')||empty($model)||empty($subject)):
            echo "<br>@@@@@@@@@@ key: >".$key."< (FOUND EMPTY FIELD) => SKIPPED!";
        else:
            echo("<br>@@@@@@@@@@ key: >".$key."<");
            echo("<br>@@@@@@@@@@ delay: >".$delay."<");
            echo("<br>@@@@@@@@@@ days: >".$days."<");

            $gg=$delay=='before'? '+':'-';
            $gg.=$days!=''? $days : '0';
            echo "<br>delta (+/-) days: ".$gg;
            $date=new DateTime();
            echo "<br>date now: ".$date->format('Y-m-d');
            $date->modify($gg.' days');
            echo "<br>data now +/- delta: ".$date->format('Y-m-d');

            echo("<br>@@@@@@@@@@ date (membership_end==): >".$date->format('Y-m-d')."<");
            echo"<br><br>";

            $subscriptions=$this->db->query("
                select m.id service_membership_id, m.*, r.*
                  from archy_services_memberships as m                      
                       left join archy_registry as r on (m.registry_id=r.id)
                  where membership_end='".$date->format('Y-m-d')."'
            ");

            while($member=mysqli_fetch_object($subscriptions)):

                // var_dump($member); die;
                echo "<br><b>FOUND: $member->label</b>";

                $service_membership=new ServiceMembership($member->service_membership_id);
                // var_dump($service_membership); die;

                echo "<br>>".$key."<";
                echo("<br>################# mail to: >$member->label ($member->registry_id)<");

                $replaces=$this->get_shortcodes_replaces($service_membership);
                // var_dump($replaces); var_dump($shortcodes); die;

                $email=[
                    'recipient_name'=>$member->label,
                    'recipient_mail'=>$member->email,
                    'sender_name'=>$this->settings->sender_name,
                    'sender_mail'=>$this->settings->sender_email,
                    'subject'=>str_replace($shortcodes,$replaces,$subject),
                    'message'=>str_replace($shortcodes,$replaces,$model),                    
                ];
                /*'bcc'=>['vferri@gmail.com'],*/
                // var_dump($email); die;

                echo("<br>################# enqueue mail msg: >".str_replace($shortcodes,$replaces,$model)."<");
                //$message->email($email);
                $queue->enqueue($email);

            endwhile;


        endif;
    }

    // $this->statuses=['5'=>'In attesa primo versamento','1'=>'Servizio attivo','2'=>'In attesa di rinnovo','3'=>'Servizio scaduto',''=>'','4'=>'Servizio cessato','6'=>'In aggiornamento...'];
	public function check_status(){ /* cronjob, runs once a day at 09:00 AM */	       
        $date=new DateTime();

        echo "<br><br>################### GESTIONE NOTIFICHE ####################<br><br>";

        // Gestione notifiche  per i servizi in scadenza/scaduti
        $service_notices=$this->db->query("
        select id, 
            description,
            first_expiring_subject,
            first_expiring_model,
            first_expiring_days,
            first_expiring_days_delay,
            second_expiring_subject,
            second_expiring_model,
            second_expiring_days,
            second_expiring_days_delay,
            third_expiring_subject,
            third_expiring_model,
            third_expiring_days,
            third_expiring_days_delay,
            expired_subject,
            expired_model,
            expired_days,
            expired_days_delay 
        from archy_services
        ");
        $data['days']=array();
        while($notices=mysqli_fetch_object($service_notices)):

            var_dump($notices);
            $this->check_notices(
                'first',
                $notices->first_expiring_subject,
                $notices->first_expiring_model,
                $notices->first_expiring_days,
                $notices->first_expiring_days_delay
            );

            $this->check_notices(
                'second',
                $notices->second_expiring_subject,
                $notices->second_expiring_model,
                $notices->second_expiring_days,
                $notices->second_expiring_days_delay
            );

            $this->check_notices(
                'third',
                $notices->third_expiring_subject,
                $notices->third_expiring_model,
                $notices->third_expiring_days,
                $notices->third_expiring_days_delay
            );

            $this->check_notices(
                'expired',
                $notices->expired_subject,
                $notices->expired_model,
                $notices->expired_days,
                $notices->expired_days_delay
            );
           
        endwhile;
        



        //echo "<br><br>################### GESTIONE STATUS ####################<br><br>";

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

        return;
    }

	public function get_member($membership_id){
		return mysqli_fetch_object($this->db->query("
            select m.id as member_id, m.membership_begin, m.membership_end, s.description, r.*, city.city_label, country.country_it			
            from archy_services_memberships as m 
            left join archy_services as s on (m.services_id=s.id) 
            left join archy_registry as r on (m.registry_id=r.id)
            left join archy_cities as city on (r.city_id=city.id)
            left join archy_countries as country on (city.country_id=country.id)
            where m.id=".$membership_id
		));
	}	

    public function get_service_subscription_list(){
		$response=new stdClass();
		$response->subscription_list=[];
		
        $id=$this->membership->id;

        //var_dump($this->membership);

		$results=$this->db->query("select * from archy_services_subscriptions where services_memberships_id = ".$id." order by date");
		while($row=mysqli_fetch_object($results)):
			$tmp=new stdClass();
			$tmp->date=$row->date;
			$tmp->amount=$row->amount;
			$response->subscription_list[]=$tmp;
		endwhile;
		
		return $response;
	}
        
        public function send($id,$type) {
             
            $subscription=$this->db->query("
                select m.id service_membership_id, m.*, r.*, s.*
                  from archy_services_memberships as m                      
                       left join archy_registry as r on (m.registry_id=r.id)
                       left join archy_services as s on (m.services_id=s.id)
                  where id='".$id."'
            ");
            $member=mysqli_fetch_object($subscription);
            $service_membership=new ServiceMembership($member->service_membership_id);
            if ($member->email != ''):
                
                // attestato sessione
                if($type == 'standard'):
                    $file_name_service="membership_standard_".$id.".pdf";                    
                else:
                    $file_name_service="membership_".$id.".pdf";
                endif;
                
                //$sent_on[$id]=array($type => 1);
                
                $file_path_service="/assets/documents/services/".$file_name_service;
                $physical_path_service=$this->app->subdomain_root.$file_path_service;
                
                $message = new Message();

                $mail_subject = $member->service_mail_subject;          
                $mail_body = $member->service_mail_body;
                $replaces=$this->get_shortcodes_replaces($service_membership);
                
                $email=[
                    'recipient_name'=>$member->label,
                    'recipient_mail'=>$member->email,
                    'sender_name'=>$this->settings->sender_name,
                    'sender_mail'=>$this->settings->sender_email,
                    'subject'=>str_replace($this->shortcodes,$replaces,$mail_subject),
                    'message'=>str_replace($this->shortcodes,$replaces,$mail_body),
                    'attachments' => [$file_name_service => file_get_contents($physical_path_service)]
                ];
                
                $message->email($email);
                
                $sent_on_q=mysqli_fetch_object($this->db->query("SELECT sent_on FROM archy_services_memberships WHERE id=".$id))->sent_on;
                $sent_on=$this->app->is_serialized($sent_on_q->sent_on)? unserialize($sent_on_q->sent_on):[]; 
                $sent_on[]=['id' => $id,'type' => $type];
                
                $this->db->query("update archy_services_memberships set sent_on='".addslashes(serialize($sent_on))."' where id=" . $id);
    
                set_message("success", "Attestato inviato correttamente.");

            else:

                set_message("danger", "Non è stato possibile inviare l'attestato, al cliente non è associato un indirizzo e-mail.");

            endif;

            header('location: ' . $_SERVER['HTTP_REFERER']);

        }

}