<?php
$intent=new stdClass();
$intent->table='archy_passive_receipts';
$intent->title='Storno Ricevute';
$intent->singular='Storno Ricevuta';
$intent->add='Nuovo Storno';
$intent->view_title='Ricevuta archy_passive_receipts.number del archy_passive_receipts.date';
$intent->joins=array(
	'archy_firms'=>'archy_passive_receipts.firm_id=archy_firms.id',
	'archy_registry'=>'archy_passive_receipts.customer_id=archy_registry.id',
	'archy_payment_methods'=>'archy_passive_receipts.payment_method_id=archy_payment_methods.id'
);

$intent->columns=array(
	'archy_passive_receipts.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_passive_receipts.firm_id'=>array(
		'label'=>'Registro sezionale',
		'required'=>true,
		'type'=>'search',
		'intent'=>'archy_firms',
		'lookup_text'=>'archy_firms.firm_name',
		'default_value'=>$app->default_firm_id
	),
	'archy_passive_receipts.date'=>array(
		'label'=>'Data',
		'type'=>'date',
		'required'=>true,
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'default_value'=>date('d/m/Y')
	),
	'archy_passive_receipts.number'=>array(
		'label'=>'Numero',
		'type'=>'text',
		'attributes'=>['readonly'],
		'default_value'=>$app->next_document_number_by_year_and_firm('archy_passive_receipts',$app->default_firm_id,date('Y'),'first_passive_receipt_number',$ajax=false)
	),	
	'archy_passive_receipts.customer_id'=>array(
		'label'=>'Cliente',
		'required'=>true,
		'type'=>'search',
		'intent'=>'archy_registry',
		'lookup_text'=>'archy_registry.label'
	),
	'archy_passive_receipts.receipt'=>array(
		'label'=>'Corpo ricevuta',
		'type'=>'text',
		'formatter'=>function($d,$row){return unserialize($d);},
		'hidden'=>''
	),
	'archy_passive_receipts.payment_method_id'=>array(
		'label'=>'Modalità di pagamento',
		'type'=>'search',
		'intent'=>'archy_payment_methods',
		'lookup_text'=>'method'
	),
	'archy_passive_receipts.due_date'=>array(
		'label'=>'Scadenza',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date($d);}
	),
	'archy_passive_receipts.total'=>array(
		'label'=>'Totale Storno',
		'type'=>'text',
		'formatter'=>function($d,$row){return App::currency($d);},
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
	'archy_passive_receipts.paid'=>array(
		'label'=>'Totale Pagato',
		'type'=>'text',
		'formatter'=>function($d,$row){return App::currency($d);},
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
	'archy_passive_receipts.unpaid'=>array(
		'label'=>'Residuo da pagare',
		'type'=>'text',
		'formatter'=>function($d,$row){return App::currency($d);},
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
	'archy_passive_receipts.payment_status'=>array(
		'label'=>'Status pagamenti',
		'type'=>'select', 
		'options_type'=>'array',
		'hidden'=>'',
		'default_value'=>'not_paid',
		'options'=>['not_paid'=>'Aperta','paid'=>'Chiusa','partially_paid'=>'Parzialmente aperta'],
		'formatter'=>function($d,$row){
			$options=['not_paid'=>'Aperta','paid'=>'Chiusa','partially_paid'=>'Parzialmente aperta'];
			return @$options[$d];
		},
		'formatter_hide_in_form'=>''
	),
	'archy_passive_receipts.notes'=>array(
		'label'=>'Note da riportare nella ricevuta',
		'type'=>'editor',
		'formatter'=>function($d,$row){return strip_tags($d);},
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
	'archy_passive_receipts.created_at'=>array(
		'label'=>'Data Creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_passive_receipts.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
);
$intent->default_columns=array(
	'archy_passive_receipts.id',
	'archy_firms.firm_name',
	'archy_registry.label',
	'archy_passive_receipts.number',
	'archy_passive_receipts.date',
	'archy_passive_receipts.total',
	'archy_passive_receipts.paid',
	'archy_passive_receipts.unpaid',
	'archy_passive_receipts.payment_status'
);

$intent->extra_buttons=function($id,$row){
	global $app;
	$db=$app->get_db();
	$file_path=mysqli_fetch_object($db->query("select file_path from archy_passive_receipts where id=".$id))->file_path;	
	$file=explode('/',$file_path);
	$file=end($file);

	$extra_button=' <a class="btn btn-outline-secondary" href="'.$file_path.'" download data-toggle="tooltip" title="Ricevuta '.$file.'"><span class="far fa-file-archive"></span></a>';
	/*$extra_button.='
		<a class="btn btn-sm btn-warning" 
			href="'.$app->abs_root.'archy/app.php?do_action=send_receipt&receipt_id='.$id.'" 
			data-toggle="tooltip" title="Invia ricevuta fiscale"
			onClick="return confirm(\'Si è certi di voler inviare la ricevuta fiscale?\')"
			><i class="glyphicon glyphicon-envelope"></i></a>';*/
	
	//$extra_button.=' <a class="btn btn-sm btn-default" href="'.$app->abs_root.'archy/app.php?do_action=duplicate_passive_receipt&receipt_id='.$id.'" data-toggle="tooltip" title="Duplica Ricevuta"><i class="glyphicon glyphicon-paste"></i></a>';
	
	return $extra_button;
};

$intent->include_in_form='intents/includes/receipt_rows.php';
$intent->extra_action='store_passive_receipt';
$app->merge_intents($intent);
?>