<?php

$intent=new stdClass();
$intent->table='archy_services_memberships';//nome tabella data-base
$intent->title='Abbonamenti ai servizi';//titolo (plurale)
$intent->singular='Cliente servizio';//titolo (singolare)
$intent->add='Nuovo abbonamento';//testo usato nei forms e nei pulsanti
$intent->view_title='Cliente: archy_registry.label';//Titolo nella visualizzazione/modifica del singolo record
$intent->joins=array(
    'archy_services'=>'archy_services_memberships.services_id=archy_services.id',
    'archy_registry'=>'archy_services_memberships.registry_id=archy_registry.id',
);//tabelle collegate

$service_membership=new ServiceMembership();
$status_options=$service_membership->statuses;

$intent->columns=array(//colonne della tabella principale
	'archy_services_memberships.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''//nasconde il campo nel modulo di creazione/aggiornamento di un record
    ),
    'archy_services_memberships.registry_id'=>array(
		'label'=>'Anagrafica cliente',
		'type'=>'search',
        'intent'=>'archy_registry',
        //'intent_joins'=>['archy_registry'=>'archy_services_memberships.registry_id=archy_registry.id'],
		'lookup_text'=>'archy_registry.label',
        'required'=>true        
    ),
    'archy_services_memberships.services_id'=>array(
		'label'=>'Servizio',
		'type'=>'search',
		'intent'=>'archy_services',
		'lookup_text'=>'archy_services.description',
        'required'=>true,
    ),
	'archy_services_memberships.membership_begin'=>array(
		'label'=>'Inizio validità servizio',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date($d);},
		//'default_value'=>date('d/m/Y'),
        'attributes'=>['readonly'],
        'hidden'=>''
	),
	'archy_services_memberships.membership_end'=>array(
		'label'=>'Scadenza servizio',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date($d);},
		//'default_value'=>App::latin_date($membership->get_first_membership_end_date()),
        'attributes'=>['readonly'],
        'hidden'=>''
    ),
	'archy_services_memberships.status'=>array(
		'label'=>'Status',
		'type'=>'select', 
		'options'=>$status_options,
		'default_value'=>'5',
		'options_type'=>'array',
		'formatter'=>function($d,$row){
			global $status_options;
			// $this->statuses=['5'=>'In attesa primo versamento','1'=>'Servizio attivo','2'=>'In attesa di rinnovo','3'=>'Servizio scaduto',''=>'','4'=>'Servizio cessato','6'=>'In aggiornamento...'];
			switch($d){
				case '1': $class='text-success'; break;
				case '2':
                case '5':
                case '6': $class='text-warning'; break; // In aggiornamento
				case '3': $class='text-danger'; break;
                case '4': $class=''; break; // cessato
			}
			return '<span class="glyphicon glyphicon-tag '.$class.'"></span> '.$status_options[$d];
		},
		'formatter_hide_in_form'=>true,
		'formatter_hide_in_excel'=>true,
        'attributes'=>['readonly'],
        'hidden'=>''
    ),
           'archy_services_memberships.sent_on'=>array(
		'label'=>'Inviato',
		'type'=>'text',
		'hidden'=>''//nasconde il campo nel modulo di creazione/aggiornamento di un record
    ),             
	'archy_services_memberships.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_services_memberships.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);

$intent->extra_buttons=function($id,$row){
	global $app;
    $db=$app->get_db();
    $service=mysqli_fetch_object($db->query("select sent_on from archy_services_memberships where id=".$id));
    
    $btn_send_class="btn btn-outline-warning";
    $title="Invia";
    if($service->sent_on) {
        $btn_send_class="btn btn-outline-success";
        $title="Reinvia";
    }
    
    $extra_button='';

    // Attestato
    $file_path="/assets/documents/services/membership_".$id.".pdf";
    $physical_path=$app->subdomain_root.$file_path;
    if (file_exists($physical_path)) {
        $extra_button.=' <a class="btn btn-outline-secondary" href="'.$file_path.'" data-toggle="tooltip" title="Attestato"><span class="fas fa-file-archive"></span></a>';
    
        $extra_button.='<a class="'.$btn_send_class.'"
            href="/archy/app.php?do_action=send_services_certificate&id='.$id.'&type=base" 
            data-toggle="tooltip" title="'.$title.' attestato"
            onClick="return confirm(\'Si è certi di voler '.strtolower($title).'re l\'attestato?\')"
            ><span class="fas fa-envelope"></span></a>   ';     
    }

    // Documento
    $file_path="/assets/documents/services/membership_standard_".$id.".pdf";
    $physical_path=$app->subdomain_root.$file_path;
    if (file_exists($physical_path)) {
        $extra_button.=' <a class="btn btn-outline-secondary" href="'.$file_path.'" data-toggle="tooltip" title="Documento"><span class="fas fa-file-archive"></span></a>';
    
        $extra_button.='<a class="btn btn-outline-warning"
            href="/archy/app.php?do_action=send_services_certificate&id='.$id.'&type=standard" 
            data-toggle="tooltip" title="'.$title.' documento"
            onClick="return confirm(\'Si è certi di voler '.strtolower($title).'re il documento?\')"
            ><span class="fas fa-envelope"></span></a>   ';
        
    }
	
	return $extra_button;
};

$intent->default_columns=array(
	'archy_services_memberships.id',
	//'archy_registry.label',
	'archy_services.description',
	'archy_services_memberships.membership_begin',
	'archy_services_memberships.membership_end',
);

$intent->exclude_columns=array(//colonne del db da escludere nell'elenco di "selezione colonne"
    'archy_services.certificates_model',
    'archy_services.certificates_background_image',
    'archy_services.first_expiring_subject',
    'archy_services.first_expiring_model',
    'archy_services.first_expiring_SMS',
    'archy_services.first_expiring_days',
    'archy_services.first_expiring_days_delay',
    'archy_services.second_expiring_subject',
    'archy_services.second_expiring_model',
    'archy_services.second_expiring_SMS',
    'archy_services.second_expiring_days',
    'archy_services.second_expiring_days_delay',
    'archy_services.third_expiring_subject',
    'archy_services.third_expiring_model',
    'archy_services.third_expiring_SMS',
    'archy_services.third_expiring_days',
    'archy_services.third_expiring_days_delay',
    'archy_services.expired_subject',
    'archy_services.expired_model',
);

$intent->include_in_form='intents/includes/services_memberships.php';
$app->merge_intents($intent);//crea i collegamenti, a cascata, con le altre tabelle
