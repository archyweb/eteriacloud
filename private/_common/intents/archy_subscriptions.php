<?php
//$settings=$app->get_settings($domain='association');

$intent=new stdClass();
$intent->table='archy_subscriptions';//nome tabella data-base
$intent->title='Quote Associative';//titolo (plurale)
$intent->singular='Quota Associativa';//titolo (singolare)
$intent->add='Nuova quota Associativa';//testo usato nei forms e nei pulsanti
$intent->view_title='Quota Associativa: archy_registry.label';//Titolo nella visualizzazione/modifica del singolo record
$intent->joins=[
	'archy_firms'=>'archy_subscriptions.firm_id=archy_firms.id',
	'archy_memberships'=>'archy_subscriptions.member_id=archy_memberships.id',
	'archy_payment_methods'=>'archy_subscriptions.payment_method_id=archy_payment_methods.id',
	'archy_petty_cash_types'=>'archy_subscriptions.petty_cash_type_id=archy_petty_cash_types.id'//,
	//'archy_tax_receipts'=>'archy_subscriptions.receipt_id=archy_tax_receipts.id'
];
$intent->columns=array(//colonne della tabella principale
	'archy_subscriptions.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''//nasconde il campo nel modulo di creazione/aggiornamento di un record
	),
	'archy_subscriptions.date'=>array(
		'label'=>'Data versamento',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'required'=>true,
		'default_value'=>date('d/m/Y')
	),
	'archy_subscriptions.member_id'=>array(
		'label'=>'Socio',
		'type'=>'search',
		'intent'=>'archy_memberships',
		'intent_joins'=>['archy_registry'=>'archy_memberships.registry_id=archy_registry.id'],
		'lookup_text'=>'archy_registry.label',
		'required'=>true
	),
	'archy_subscriptions.amount'=>array(
		'label'=>'Importo',
		'type'=>'amount',
		'formatter'=>function($d,$row){return App::currency($d);},
		'formatter_hide_in_form'=>'',
		'required'=>true//,
		//'default_value'=>$settings->subscription_fee
	),
	'archy_subscriptions.firm_id'=>array(
		'label'=>'Registro sezionale',
		'required'=>true,
		'type'=>'search',
		'intent'=>'archy_firms',
		'lookup_text'=>'archy_firms.firm_name',
		'default_value'=>$app->default_firm_id
	),
	'archy_subscriptions.payment_method_id'=>array(
		'label'=>'Modalità di pagamento',
		'required'=>true,
		'type'=>'search',
		'intent'=>'archy_payment_methods',
		'lookup_text'=>'method',
		'required'=>true,
	),
	'archy_subscriptions.petty_cash_type_id'=>array(
		'label'=>'Libro prima nota',
		'type'=>'search',
		'intent'=>'archy_petty_cash_types',
		'lookup_text'=>'type',
		'required'=>true
	),
	'archy_subscriptions.receipt_id'=>array(
		'label'=>'Ricevuta collegata',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_subscriptions.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_subscriptions.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);
$intent->default_columns=[
	'archy_subscriptions.id',
	'archy_registry.label',
	'archy_subscriptions.date',
	'archy_subscriptions.amount',
	'archy_payment_methods.method',
	'archy_petty_cash_types.type'
];

$intent->extra_notice='&extra_notice=3';
$intent->delete_extra_notice='member_subscription_deletion';
$intent->update_extra_notice='member_subscription_update';

$intent->extra_buttons=function($id,$row){
	global $app;
	$db=$app->get_db();
	$receipt=mysqli_fetch_object($db->query("select r.file_path,r.id,r.sent_on from archy_tax_receipts as r left join archy_subscriptions as s on (s.receipt_id=r.id) where s.id=".$id));
	$file_path=$receipt->file_path;	
	$file=explode('/',$file_path);
    $file=end($file);
    
    /*
    modificare per tutte: glyphicon-envelope
    se già inviata cambiare colore/testo alla busta
    */
    $btn_send_class="btn btn-outline-warning";
    $title="Invia";
    if($receipt->sent_on) {
        $btn_send_class="btn btn-outline-success";
        $title="Reinvia";
    }
	
	$extra_button=' <a class="btn btn-outline-secondary" href="'.$file_path.'" download data-toggle="tooltip" title="Ricevuta '.$file.'"><span class="fas fa-file-archive"></span></a>';
	$extra_button.='
		<a class="'.$btn_send_class.'"
			href="/archy/app.php?do_action=send_receipt&receipt_id='.$receipt->id.'" 
			data-toggle="tooltip" title="'.$title.' ricevuta"
			onClick="return confirm(\'Si è certi di voler '.strtolower($title).'re la ricevuta?\')"
			><span class="fas fa-envelope"></span></a>';	
	
	return $extra_button;
};

$intent->include_in_form='intents/includes/subscriptions.php';
$intent->extra_action='subscription_fee';

$app->merge_intents($intent);//crea i collegamenti, a cascata, con le altre tabelle
?>