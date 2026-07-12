<?php
$intent=new stdClass();
$intent->table='archy_tax_receipts';
$intent->title='Ricevute';
$intent->singular='Ricevuta';
$intent->add='Nuova Ricevuta';
$intent->view_title='Ricevuta archy_tax_receipts.number del archy_tax_receipts.date';
$intent->joins=array(
	'archy_firms'=>'archy_tax_receipts.firm_id=archy_firms.id',
	'archy_registry'=>'archy_tax_receipts.customer_id=archy_registry.id',
	'archy_payment_methods'=>'archy_tax_receipts.payment_method_id=archy_payment_methods.id'
);

$intent->columns=array(
	'archy_tax_receipts.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_tax_receipts.firm_id'=>array(
		'label'=>'Registro sezionale',
		'required'=>true,
		'type'=>'search',
		'intent'=>'archy_firms',
		'lookup_text'=>'archy_firms.firm_name',
		'default_value'=>$app->default_firm_id
	),
	'archy_tax_receipts.date'=>array(
		'label'=>'Data',
		'type'=>'date',
		'required'=>true,
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'default_value'=>date('d/m/Y')
	),
	'archy_tax_receipts.number'=>array(
		'label'=>'Numero',
		'type'=>'text',
		'attributes'=>['readonly'],
		'default_value'=>$app->next_document_number_by_year_and_firm('archy_tax_receipts',$app->default_firm_id,date('Y'),'first_receipt_number',$ajax=false)
	),	
	'archy_tax_receipts.customer_id'=>array(
		'label'=>'Cliente',
		'required'=>true,
		'type'=>'search',
		'intent'=>'archy_registry',
		'lookup_text'=>'archy_registry.label'
	),
	'archy_tax_receipts.receipt'=>array(
		'label'=>'Corpo ricevuta',
		'type'=>'text',
		'formatter'=>function($d,$row){return unserialize($d);},
		'hidden'=>''
	),
	'archy_tax_receipts.payment_method_id'=>array(
		'label'=>'Modalità di pagamento',
		'required'=>true,
		'type'=>'search',
		'intent'=>'archy_payment_methods',
		'lookup_text'=>'method'
	),
	'archy_tax_receipts.due_date'=>array(
		'label'=>'Scadenza',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date($d);}
	),
	'archy_tax_receipts.total'=>array(
		'label'=>'Totale Ricevuta',
		'type'=>'text',
		'formatter'=>function($d,$row){return App::currency($d);},
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
	'archy_tax_receipts.paid'=>array(
		'label'=>'Totale Riscosso',
		'type'=>'text',
		'formatter'=>function($d,$row){return App::currency($d);},
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
	'archy_tax_receipts.unpaid'=>array(
		'label'=>'Residuo da riscuotere',
		'type'=>'text',
		'formatter'=>function($d,$row){return App::currency($d);},
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
	'archy_tax_receipts.payment_status'=>array(
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
	'archy_tax_receipts.notes'=>array(
		'label'=>'Note da riportare nella ricevuta',
		'type'=>'editor',
		'formatter'=>function($d,$row){return strip_tags($d);},
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
	'archy_tax_receipts.created_at'=>array(
		'label'=>'Data Creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_tax_receipts.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
);
$intent->default_columns=array(
	'archy_tax_receipts.id',
	'archy_firms.firm_name',
	'archy_registry.label',
	'archy_tax_receipts.number',
	'archy_tax_receipts.date',
	'archy_tax_receipts.total',
	'archy_tax_receipts.paid',
	'archy_tax_receipts.unpaid',
	'archy_tax_receipts.payment_status'
);

$intent->extra_buttons=function($id,$row){
	global $app;
	$db=$app->get_db();
	$receipt=mysqli_fetch_object($db->query("select file_path,sent_on from archy_tax_receipts where id=".$id));	
	$file=explode('/',$receipt->file_path);
    $file=end($file);
    
    /*
    modificare per tutte: glyphicon-envelope
    se già inviata cambiare colore/testo alla busta
    */
    $btn_class="btn-warning";
    $title="Invia";
    if($receipt->sent_on) {
        $btn_class="btn-default";
        $title="Reinvia";
    }

	$extra_button=' <a class="btn btn-sm btn-success" href="'.$receipt->file_path.'" download data-toggle="tooltip" title="Ricevuta '.$file.'"><i class="glyphicon glyphicon-file"></i></a>';
	$extra_button.='
		<a class="btn btn-sm '.$btn_class.'" 
			href="/archy/app.php?do_action=send_receipt&receipt_id='.$id.'" 
			data-toggle="tooltip" title="'.$title.' ricevuta"
			onClick="return confirm(\'Si è certi di voler '.strtolower($title).'re la ricevuta?\')"
			><i class="glyphicon glyphicon-envelope"></i></a>';
	
	$extra_button.=' <a class="btn btn-sm btn-default" href="/archy/app.php?do_action=duplicate_receipt&receipt_id='.$id.'" data-toggle="tooltip" title="Duplica Ricevuta"><i class="glyphicon glyphicon-paste"></i></a>';
	
	return $extra_button;
};

$intent->include_in_form='intents/includes/receipt_rows.php';
$intent->extra_action='store_receipt';
$app->merge_intents($intent);
?>