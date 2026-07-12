<?php
$intent=new stdClass();
$intent->table='archy_memberships';
$intent->title='Associati';
$intent->singular='Associato';
//$intent->add='Nuovo Associato';
$intent->restore_button_in_grid=true;
$intent->view_title='Associato archy_registry.label';
$intent->joins=array(
	'archy_registry'=>'archy_memberships.registry_id=archy_registry.id',
	'archy_registry_categories as Associazione'=>'archy_memberships.category_id=Associazione.id',
	'archy_meetings_book as Accettazione'=>'archy_memberships.acceptance_meeting_id=Accettazione.id',
	'archy_meetings_book as Cessazione'=>'archy_memberships.resignation_meeting_id=Cessazione.id'
);

$membership=new Membership();

$status_options=$membership->statuses;

$intent->columns=array(
	'archy_memberships.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_memberships.registry_id'=>array(
		'label'=>'Socio',
		'type'=>'search',
		'intent'=>'archy_registry',
		'lookup_text'=>'archy_registry.label',
        'required'=>true,
        'attributes'=>['readonly'],
		'hidden'=>''
	),
	'archy_memberships.signatory'=>array(
		'label'=>'Tutore',
		'type'=>'search',
		'intent'=>'archy_registry',
		'lookup_text'=>'archy_registry.label',
        'attributes'=>['readonly'],
		'hidden'=>''
	),
	'archy_memberships.category_id'=>array(
		'label'=>'Categoria',
		'type'=>'search',
		'intent'=>'archy_registry_categories',
		'domain'=>'memberships',
		'lookup_text'=>'category',
        'required'=>true,
        'attributes'=>['readonly'],
		'hidden'=>''
	),
	'archy_memberships.card'=>array(
		'label'=>'N° Tessera',
		'type'=>'text',
		//'default_value'=>$membership->get_next_card(),
		'default_value'=>$app->next_card_number($app->name),
		'attributes'=>['readonly'],
		'hidden'=>''
	),
	'archy_memberships.membership_begin'=>array(
		'label'=>'Inizio validità tessera',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'default_value'=>date('d/m/Y'),
        'required'=>true,
        'attributes'=>['readonly'],
		'hidden'=>''
	),
	'archy_memberships.membership_end'=>array(
		'label'=>'Scadenza tessera',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'default_value'=>App::latin_date($membership->get_first_membership_end_date()),
        'required'=>true,
        'attributes'=>['readonly'],
		'hidden'=>''
	),
	'archy_memberships.status'=>array(
		'label'=>'Status',
		'type'=>'select', 
		'options'=>$status_options,
		'default_value'=>'5',
		'options_type'=>'array',
		'formatter'=>function($d,$row){
			global $status_options;
			//$options=['1'=>'Associato','2'=>'In attesa di rinnovo','3'=>'Dimesso','4'=>'Cessato','5'=>'In attesa prima quota'];
			switch($d){
				case '1': $class='text-success'; break;
				case '2':
                case '5': $class='text-warning'; break;
				case '3': $class='text-danger'; break;
				case '4': $class=''; break;
			}
			return '<span class="glyphicon glyphicon-tag '.$class.'"></span> '.$status_options[$d];
		},
		'formatter_hide_in_form'=>true,
		'formatter_hide_in_excel'=>true,
        'required'=>true,
        'attributes'=>['readonly'],
		'hidden'=>''
	),
	'archy_memberships.request_date'=>array(
		'label'=>'Data richiesta ammissione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'default_value'=>date('d/m/Y'),
        'required'=>true,
        'attributes'=>['readonly'],
		'hidden'=>''
	),
	'archy_memberships.acceptance_date'=>array(
		'label'=>'Data ammissione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'default_value'=>date('d/m/Y'),
        'required'=>true,
        'attributes'=>['readonly'],
		'hidden'=>''
	),
	/*'archy_memberships.acceptance_document'=>array(
		'label'=>'Verbale di accettazione',
		'type'=>'file',
		'formatter'=>function($d,$row){return $d!=''? '<a href="'.$d.'" download class="grid_glyphicon"><span class="glyphicon glyphicon-save-file"></span></a>':'';},
		'formatter_hide_in_form'=>true,
		'formatter_hide_in_excel'=>true,
		'required'=>true,
		'hidden'=>''
	),*/
	'archy_memberships.picture'=>array(
		'label'=>'Foto',
		'type'=>'file',
        'formatter'=>function($d, $row){
            $return='<a href="' . $d . '" download><img src="' . $d . '" alt="Foto" class="tt" title="Download" style="max-height:75px"></a>';
            return $d != '' ? $return : '';
        },
        'excel_formatter'=>function($d,$row){
            $return=App::protocol()."://".$_SERVER['HTTP_HOST'].$d;
			return $d != '' ? $return : '';
        },
		'formatter_hide_in_form'=>true,
		//'formatter_hide_in_excel'=>true,
		'hidden'=>''
	),
	'archy_memberships.documents'=>array(
		'label'=>'Documenti allegati',
		'type'=>'bulk_upload',
		'formatter'=>function($d,$row){
			if($d!=''):
				$docs=unserialize($d);
				$return='<ul>';
				foreach($docs as $path):
					$tmp=explode('/',$path);
					$return.='<li><a href="'.$path.'">'.end($tmp).'</a></li>';
				endforeach;
				$return.='</ul>';
				return $return;
			endif;
		},
		'formatter_hide_in_form'=>true,
		'formatter_hide_in_excel'=>true,
		'hidden'=>''
	),
	'archy_memberships.medical_certificate'=>array(
		'label'=>'Certificato medico',
		'type'=>'file',
		'formatter'=>function($d,$row){
			if($d!=''):
				$tmp=explode('/',$d);
				return '<a href="'.$d.'">'.end($tmp).'</a>';
			endif;
		},
		'formatter_hide_in_form'=>true,
        'formatter_hide_in_excel'=>true,
        'hidden'=>''
	),
	'archy_memberships.medical_certificate_begin'=>array(
		'label'=>'Inizio validità certificato medico',
		'type'=>'date',
        'formatter'=>function($d,$row){return App::latin_date($d);},
        'hidden'=>''
	),
	'archy_memberships.medical_certificate_end'=>array(
		'label'=>'Fine validità certificato medico',
		'type'=>'date',
        'formatter'=>function($d,$row){return App::latin_date($d);},
        'hidden'=>''
	),
	'archy_memberships.resignation_date'=>array(
		'label'=>'Data cessazione',
		'type'=>'date',
        'formatter'=>function($d,$row){return App::latin_date($d);},
        'attributes'=>['readonly'],
		'hidden'=>''
	),
	'archy_memberships.resignation_reason'=>array(
		'label'=>'Motivo cessazione',
        'type'=>'textarea',
        'attributes'=>['readonly'],
		'hidden'=>''
	),
	'archy_memberships.notes'=>array(
		'label'=>'Note',
		'type'=>'editor',
		'hidden'=>''
    ),
	'archy_memberships.created_at'=>array(
		'label'=>'Data Creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_memberships.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);

$intent->extra_buttons=function($id,$row){
    global $app;
    $db=$app->get_db();
    $member=mysqli_fetch_object($db->query("select sent_on from archy_memberships where id=".$id));	
    
    $title="Invia";
    $btn_send_class="btn btn-outline-warning";
    if($member->sent_on) {
        $btn_send_class="btn btn-outline-success";
        $title="Reinvia";
    }
    
    $extra_button='';

    $file_path="/assets/documents/memberships/card_".$id.".pdf";
    $physical_path=$app->subdomain_root.$file_path;
   
    if (file_exists($physical_path)) {
        $extra_button.=' <a class="btn btn-outline-secondary" href="'.$file_path.'" data-toggle="tooltip" title="Tessera"><span class="far fa-file-archive"></span></a>';
    
        $extra_button.='<a class="'.$btn_send_class.'"
            href="/archy/app.php?do_action=send_membership_card&id='.$id.'" 
            data-toggle="tooltip" title="'.$title.' tessera"
            onClick="return confirm(\'Si è certi di voler '.strtolower($title).'re la tessera?\')"
            ><span class="far fa-envelope"></span></a>   ';  
        
    }
		
    return $extra_button;
};

$intent->default_columns=array(
	'archy_memberships.id',
	'archy_memberships.status',
	'archy_memberships.card',
	'archy_registry.label',
	'Associazione.category',
	'archy_memberships.membership_begin',
	'archy_memberships.membership_end',
	'Accettazione.signed_report',
	'Cessazione.signed_report'
);

$intent->delete_extra_notice='member_deletion';
$intent->update_extra_notice='member_update';

$intent->extra_action='member_extra_action';
$intent->include_in_form='intents/includes/member_history.php';
$app->merge_intents($intent);
?>