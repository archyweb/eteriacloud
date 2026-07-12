<?php
$intent=new stdClass();
$intent->table='archy_credit_notes';
$intent->title='Note di credito';
$intent->singular='Nota di credito';
$intent->add='Nuova Nota di credito';
$intent->view_title='Nota di credito archy_credit_notes.number del archy_credit_notes.date';
$intent->joins=array(
	'archy_firms'=>'archy_credit_notes.firm_id=archy_firms.id',
	'archy_registry'=>'archy_credit_notes.customer_id=archy_registry.id'//,
	//'archy_invoices'=>'archy_credit_notes.invoice_id=archy_invoices.id'
);

$intent->columns=array(
	'archy_credit_notes.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_credit_notes.VAT_collectability'=>array(
		'label'=>'Esigibilità IVA',
		'type'=>'select', 
		'default_value'=>$app->default_VAT_collectability,
		'options_type'=>'array',
		'options'=>['I'=>'IVA ad esigibilità immediata', 'D'=>'IVA ad esigibilità differita', 'S'=>'Scissione dei pagamenti'],
		'formatter'=>function($d,$row){
			$collectability=['I'=>'IVA ad esigibilità immediata', 'D'=>'IVA ad esigibilità differita', 'S'=>'Scissione dei pagamenti'];
			return $collectability[$d];
		},
		'formatter_hide_in_form'=>true,
		'required'=>true
	),

	'archy_credit_notes.firm_id'=>array(
		'label'=>'Registro sezionale',
		'required'=>true,
		'type'=>'search',
		'intent'=>'archy_firms',
		'lookup_text'=>'archy_firms.firm_name',
		'default_value'=>$app->default_firm_id
	),
	'archy_credit_notes.date'=>array(
		'label'=>'Data',
		'type'=>'date',
		'required'=>true,
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'default_value'=>date('d/m/Y')
	),
	'archy_credit_notes.number'=>array(
		'label'=>'Numero',
		'type'=>'text',
		'attributes'=>['readonly'],
		'formatter'=>function($d,$row){
			if(isset($row['id'])):
				global $app;
				$db=$app->get_db();
				$file_data=mysqli_fetch_object($db->query("
					select archy_credit_notes.date, archy_firms.credit_note_prefix 
					from archy_credit_notes 
					left join archy_firms on(archy_credit_notes.firm_id=archy_firms.id) 
					where archy_credit_notes.id=".$row["id"]
				));
				$year=(new DateTime($file_data->date))->format('Y');
				$prefix=$file_data->credit_note_prefix;
				return $prefix.str_repeat("0",4-strlen($d)).$d.'-'.$year;
			else:
				return $d;
			endif;
		},
		'formatter_hide_in_form'=>true,
		'default_value'=>$app->next_document_number_by_year_and_firm('archy_credit_notes',$app->default_firm_id,date('Y'),'first_credit_note_number',$ajax=false)
	),

	'archy_credit_notes.stamp_duty'=>array(
		'label'=>'Imposta di bollo',
		'type'=>'amount',
		'formatter'=>function($d,$row){return App::currency($d);},
		'formatter_hide_in_form'=>true
	),

	'archy_credit_notes.customer_id'=>array(
		'label'=>'Cliente',
		'required'=>true,
		'type'=>'search',
		'intent'=>'archy_registry',
		'lookup_text'=>'archy_registry.firm_name',
		'hidden'=>''
	),
	'archy_credit_notes.credit_note'=>array(
		'label'=>'Corpo nota di credito',
		'type'=>'text',
		'formatter'=>function($d,$row){return unserialize($d);},
		'hidden'=>''
	),
	'archy_credit_notes.taxable_amount'=>array(
		'label'=>'Totale imponibile',
		'type'=>'text',
		'formatter'=>function($d,$row){return App::currency($d);},
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
	'archy_credit_notes.vat'=>array(
		'label'=>'Totale IVA',
		'type'=>'text',
		'formatter'=>function($d,$row){return App::currency($d);},
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
	'archy_credit_notes.total'=>array(
		'label'=>'Totale nota di credito',
		'type'=>'text',
		'formatter'=>function($d,$row){return App::currency($d);},
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
	'archy_credit_notes.paid'=>array(
		'label'=>'Totale pagato',
		'type'=>'text',
		'formatter'=>function($d,$row){return App::currency($d);},
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
	'archy_credit_notes.unpaid'=>array(
		'label'=>'Residuo',
		'type'=>'text',
		'formatter'=>function($d,$row){return App::currency($d);},
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
	'archy_credit_notes.payment_status'=>array(
		'label'=>'Status pagamenti',
		'type'=>'select', 
		'options_type'=>'array',
//		'required'=>true,
		'hidden'=>'',
		'default_value'=>'not_paid',
		'options'=>['not_paid'=>'Aperta','paid'=>'Chiusa','partially_paid'=>'Parzialmente aperta'],
		'formatter'=>function($d,$row){
			$options=['not_paid'=>'Aperta','paid'=>'Chiusa','partially_paid'=>'Parzialmente aperta'];
			return @$options[$d];
		},
		'formatter_hide_in_form'=>''
	),
	'archy_credit_notes.invoice_number'=>array(
		'label'=>'Fattura associata',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_credit_notes.notes'=>array(
		'label'=>'Note da riportare nella nota di credito',
		'type'=>'editor',
		'formatter'=>function($d,$row){return strip_tags($d);},
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
	'archy_credit_notes.sent_on'=>array(
		'label'=>'Data invio nota di credito',
		'type'=>'date',
		'formatter'=>function($d,$row){return $d!=NULL? App::latin_date($d) : 'Non inviata';},
		'hidden'=>true
	),
	'archy_credit_notes.created_at'=>array(
		'label'=>'Data Creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_credit_notes.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
);
$intent->default_columns=array(
	'archy_credit_notes.id',
	'archy_registry.firm_name',
	'archy_credit_notes.number',
	'archy_credit_notes.date',
	'archy_credit_notes.total',
	'archy_credit_notes.paid',
	'archy_credit_notes.unpaid',
	'archy_credit_notes.payment_status',
	'archy_credit_notes.invoice_number'
);

$intent->uneditable_ids=array_column(mysqli_fetch_all($app->db->query("select id from archy_credit_notes where sdi_uuid!=''"),MYSQLI_ASSOC),'id');

$intent->extra_buttons=function($id,$row){
	global $app;
	$db=$app->get_db();
	$file_path=mysqli_fetch_object($db->query("select file_path from archy_credit_notes where id=".$id))->file_path;	
	$file=explode('/',$file_path);
	$file=end($file);
	
	$extra_button=' <a class="btn btn btn-outline-warning" href="'.$file_path.'" data-toggle="tooltip" title="Nota di Credito '.$file.'"><span class="far fa-file-archive"></span></a>';
	$extra_button.='
		<a class="btn btn btn-outline-warning" 
			href="/archy/app.php?do_action=send_credit_note&credit_note_id='.$id.'" 
			data-toggle="tooltip" title="Invia Nota di Credito"
			onClick="return confirm(\'Si è certi di voler inviare la Nota di Credito?\')"
			>span class="far fa-envelope"></span></a>';
	
	if(!in_array($id,$app->intent->uneditable_ids)):
		$extra_button.='
			<a class="btn btn-sm btn-danger" 
				href="/archy/app.php?do_action=send_to_sdi&credit_note_id='.$id.'" 
				data-toggle="tooltip" title="Invia al Sistema di Interscambio"
				onClick="return confirm(\'Inviare al Sistema di Interscambio?\')"
			><span class="fas fa-file-export"></span></a>';
	else:
		$extra_button.='
		<a class="btn btn-sm btn-success" 
			href="/archy/app.php?do_action=download_sdi_xml&credit_note_id='.$id.'" 
			data-toggle="tooltip" title="XML Nota di credito"
		><span class="fas fa-file-import"></span></a>';
	endif;		

	return $extra_button;
};

$intent->include_in_form='intents/includes/credit_note_rows.php';
$intent->extra_action='store_credit_note';
$intent->before_delete='&before_delete=credit_note_before_delete';
$intent->after_delete='&after_delete=credit_note_after_delete';
$intent->before_update='credit_note_before_update';
$intent->after_update='credit_note_after_update';
$app->merge_intents($intent);
?>