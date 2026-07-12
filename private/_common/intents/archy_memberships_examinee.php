<?php

$intent=new stdClass();
$intent->table='archy_memberships_examinee';
$intent->title='Candidati associati';
$intent->singular='Candidato associato';
$intent->add='Nuovo candidato';
$intent->view_title='Candidato: archy_registry.firm_name';
$intent->joins=[
	'archy_firms'=>'archy_memberships_examinee.firm_id=archy_firms.id',
	'archy_registry'=>'archy_memberships_examinee.registry_id=archy_registry.id',
	'archy_registry_categories as Associato'=>'archy_memberships_examinee.category_id=Associato.id',
	'archy_meetings_book as Accettazione'=>'archy_memberships_examinee.acceptance_meeting_id=Accettazione.id',
	'archy_meetings_book as Respingimento'=>'archy_memberships_examinee.rejection_meeting_id=Respingimento.id',
	'archy_payment_methods'=>'archy_memberships_examinee.payment_method_id=archy_payment_methods.id',
    'archy_petty_cash_types'=>'archy_memberships_examinee.petty_cash_type_id=archy_petty_cash_types.id'
];
$intent->columns=array(
	'archy_memberships_examinee.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_memberships_examinee.registry_id'=>array(
		'label'=>'Anagrafica candidato',
		'type'=>'search',
		'intent'=>'archy_registry',
		'lookup_text'=>'archy_registry.label',
		'required'=>true
	),
	'archy_memberships_examinee.signatory'=>array(
		'label'=>'Tutore',
		'type'=>'search',
		'intent'=>'archy_registry',
		'lookup_text'=>'archy_registry.label'
	),
	'archy_memberships_examinee.category_id'=>array(
		'label'=>'Categoria',
		'type'=>'search',
		'intent'=>'archy_registry_categories',
		'domain'=>'memberships',
		'lookup_text'=>'category',
		'required'=>true
    ),
    /*
    'archy_memberships_examinee.eps'=>array(
		'label'=>'Enti esterni e EPS',
		'type'=>'multiple',
        'options_type'=>'sql',
		'options'=>'select ID as option_value, title as option_text from archy_eps order by option_text',
        'formatter'=>function($d,$row){return App::format_eps($d,$row);},
		'formatter_hide_in_form'=>true
	),
    */
	'archy_memberships_examinee.request_date'=>array(
		'label'=>'Data richiesta',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'default_value'=>date('d/m/Y'),
		'required'=>true
	),
	'archy_memberships_examinee.picture'=>array(
		'label'=>'Foto',
		'type'=>'file',
		'formatter'=>function($d,$row){return $d!=''? '<a href="'.$d.'"><img src="'.$d.'" alt="Foto Socio" class="tt" title="Download" style="max-height:44px"></a>':'';},
		'formatter_hide_in_form'=>true,
		'formatter_hide_in_excel'=>true
	),
	'archy_memberships_examinee.documents'=>array(
		'label'=>'Documentazione',
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
		'formatter_hide_in_excel'=>true
	),
	'archy_memberships_examinee.request_status'=>array(
		'label'=>'Status richiesta',
		'type'=>'select', 
		'default_value'=>'2',
		'options_type'=>'array',
		'options'=>['2'=>'Valutazione in corso','1'=>'Approvata','0'=>'Respinta'],
		'formatter'=>function($d,$row){
			$options=['2'=>'Valutazione in corso','1'=>'Approvata','0'=>'Respinta'];
			return $options[$d];
		},
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
	'archy_memberships_examinee.response_date'=>array(
		'label'=>'Data responso',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'default_value'=>date('d/m/Y'),
		'hidden'=>''
	),
	'archy_memberships_examinee.fee'=>array(
		'label'=>'Deposito quota associativa',
		'type'=>'checkbox',
		'formatter'=>function($d,$row){return App::checkbox($d);},
		'formatter_hide_in_form'=>''
	),
	'archy_memberships_examinee.fee_date'=>array(
		'label'=>'Data',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'default_value'=>date('d/m/Y'),
		'hidden'=>'',
		'required'=>true,
	),
	'archy_memberships_examinee.firm_id'=>array(
		'label'=>'Registro sezionale',
		'required'=>true,
		'type'=>'search',
		'intent'=>'archy_firms',
		'lookup_text'=>'archy_firms.firm_name',
		'default_value'=>$app->default_firm_id,
		'hidden'=>''
	),
	'archy_memberships_examinee.fee_category_amount'=>array(
		'label'=>'Importo Categoria',
        'type'=>'amount',
        'attributes'=>['readonly'],
		'formatter'=>function($d,$row){return App::currency($d);},
		'formatter_hide_in_form'=>true,
		'hidden'=>'',
		'required'=>true,
	),
    /*
	'archy_memberships_examinee.fee_eps_amount'=>array(
		'label'=>'Importo EPS',
        'type'=>'amount',
        'default_value'=>'0.00',
        'attributes'=>['readonly'],
		'formatter'=>function($d,$row){return App::currency($d);},
		'formatter_hide_in_form'=>true,
		'hidden'=>'',
	),
    */
	'archy_memberships_examinee.payment_method_id'=>array(
		'label'=>'Modalità di pagamento',
		'required'=>true,
		'type'=>'search',
		'intent'=>'archy_payment_methods',
		'lookup_text'=>'method',
		'hidden'=>'',
	),
	'archy_memberships_examinee.petty_cash_type_id'=>array(
		'label'=>'Libro prima nota',
		'type'=>'search',
		'intent'=>'archy_petty_cash_types',
		'lookup_text'=>'type',
		'required'=>true,
		'hidden'=>''
	),
	'archy_memberships_examinee.deposit_receipt'=>array(
		'label'=>'Ricevuta deposito',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_memberships_examinee.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_memberships_examinee.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);

// Gestione form in base a config (tipologia cliente)
/*
if(!$app->config['association_eps']){
    $intent->columns['archy_memberships_examinee.eps']['hidden']='';
}
*/

$intent->extra_buttons=function($id,$row){
	global $app;
	$db=$app->get_db();
	$deposit_receipt=mysqli_fetch_object($db->query("select r.file_path,r.id,r.sent_on from archy_tax_receipts as r left join archy_memberships_examinee as e on (e.deposit_receipt_id=r.id) where e.id=".$id));
	$balance_receipt=mysqli_fetch_object($db->query("select r.file_path,r.id,r.sent_on from archy_tax_receipts as r left join archy_memberships_examinee as e on (e.balance_receipt_id=r.id) where e.id=".$id));
	
    $extra_button='';

    if($deposit_receipt!=NULL):
        /*
        modificare per tutte: glyphicon-envelope
        se già inviata cambiare colore/testo alla busta
        */
        //$btn_class="btn-warning";
        $title="Invia";
        $btn_send_class="btn btn-outline-warning";
        if($deposit_receipt->sent_on) {
            $btn_send_class="btn btn-outline-success";
            $title="Reinvia";
        }
        
		$file_path=$deposit_receipt->file_path;	
		$file=explode('/',$file_path);
		$file=end($file);
		$extra_button.=' <a class="btn btn-outline-secondary" href="'.$file_path.'" download data-toggle="tooltip" title="Ricevuta deposito quota associativa"><span class="fas fa-file-archive"></span></a>';
		$extra_button.='
			<a class="'.$btn_send_class.'"
				href="/archy/app.php?do_action=send_receipt&receipt_id='.$deposit_receipt->id.'" 
				data-toggle="tooltip" title="'.$title.' ricevuta deposito"
				onClick="return confirm(\'Si è certi di voler '.strtolower($title).'re la ricevuta?\')"
				><span class="fas fa-envelope"></span></a>';
	endif;
		
    if($balance_receipt!=NULL):
        /*
        modificare per tutte: glyphicon-envelope
        se già inviata cambiare colore/testo alla busta
        */
        //$btn_class="btn-warning";
        $title="Invia";
    $btn_send_class="btn btn-outline-warning";
        if($balance_receipt->sent_on) {
            $btn_send_class="btn btn-outline-success";
            $title="Reinvia";
        }

		$file_path=$balance_receipt->file_path;	
		$file=explode('/',$file_path);
		$file=end($file);
		$extra_button.=' <a class="btn btn-outline-secondary" href="'.$file_path.'" download data-toggle="tooltip" title="Ricevuta quota associativa"><span class="fas fa-file-archive"></span></a>';
		$extra_button.='
			<a class="'.$btn_send_class.'" 
				href="/archy/app.php?do_action=send_receipt&receipt_id='.$balance_receipt->id.'" 
				data-toggle="tooltip" title="'.$title.' ricevuta quota associativa"
				onClick="return confirm(\'Si è certi di voler '.strtolower($title).'re la ricevuta?\')"
				><span class="fas fa-envelope"></span></a>';
	endif;
	
	$examinee_folder='/assets/documents/examinee/'.$id.'/';
	if(is_file($app->subdomain_root.$examinee_folder.'application.pdf')):
		$extra_button.=' <a class="btn btn-outline-secondary" href="'.$examinee_folder.'application.pdf'.'" download="Adesione.pdf" data-toggle="tooltip" title="Domanda di adesione"><span class="fas fa-file-archive"></span></a>';
	endif;
	if(is_file($app->subdomain_root.$examinee_folder.'privacy.pdf')):
		$extra_button.=' <a class="btn btn-outline-secondary" href="'.$examinee_folder.'privacy.pdf'.'" download="Privacy.pdf" data-toggle="tooltip" title="Accettazione privacy"><span class="fas fa-file-archive"></span></a>';
	endif;
	
		
	return $extra_button;
};

// Il candidato associato non va cancellato se è dentro a un'adunanza
$all_ids=array_column(mysqli_fetch_all($app->db->query(
    "SELECT id 
       FROM archy_memberships_examinee 
      WHERE acceptance_meeting_id > 0 
         OR rejection_meeting_id > 0;
    "),MYSQLI_ASSOC),'id');
$intent->undeletable_ids=$all_ids;

// Il candidato associato non va neanche modificato se è dentro a un'adunanza (approvato o respinto)
$intent->uneditable_ids=$all_ids;

$intent->default_columns=array(
	'archy_memberships_examinee.id',
	'archy_registry.label',
	'archy_memberships_examinee.signatory',
	'archy_registry.tax_code',
	'Associato.category',
	'archy_memberships_examinee.request_date',
	'archy_memberships_examinee.request_status',
	'archy_memberships_examinee.response_date',
	'archy_memberships_examinee.fee',
	'Accettazione.signed_report',
	'Respingimento.signed_report'
);
$intent->include_in_form='intents/includes/members_examinee.php';
$intent->extra_action='store_examinee';
$app->merge_intents($intent);
?>