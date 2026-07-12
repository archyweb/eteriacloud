<?php
$intent=new stdClass();
$intent->table='archy_quotes';
$intent->title='Preventivi';
$intent->singular='Preventivo';
$intent->add='Nuovo Preventivo';
$intent->view_title='Preventivo archy_quotes.number del archy_quotes.date Cliente.label';
$intent->joins=array(
	'archy_firms'=>'archy_quotes.firm_id=archy_firms.id',
	'archy_registry'=>'archy_quotes.customer_id=archy_registry.id'
);
$receipt=new Receipt();
$intent->columns=array(
	'archy_quotes.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_quotes.firm_id'=>array(
		'label'=>'Registro sezionale',
		'required'=>true,
		'type'=>'search',
		'intent'=>'archy_firms',
		'lookup_text'=>'archy_firms.firm_name',
		'default_value'=>$app->default_firm_id
	),
	'archy_quotes.date'=>array(
		'label'=>'Data',
		'type'=>'date',
		'required'=>true,
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'default_value'=>date('d/m/Y')
	),
	'archy_quotes.number'=>array(
		'label'=>'Numero',
		'type'=>'text',
		'attributes'=>['readonly'],
		'default_value'=>$app->next_document_number_by_year_and_firm('archy_quotes',$app->default_firm_id,date('Y'),'first_quote_number',$ajax=false)
	),	
	'archy_quotes.customer_id'=>array(
		'label'=>'Cliente',
		'required'=>true,
		'type'=>'search',
		'intent'=>'archy_registry',
		'lookup_text'=>'archy_registry.label'
	),
	'archy_quotes.quote_table'=>array(
		'label'=>'Tabellla preventivo',
		'type'=>'text',
		'formatter'=>function($d,$row){return unserialize($d);},
		'hidden'=>''
	),
	'archy_quotes.quote'=>array(
		'label'=>'Preventivo',
		'type'=>'editor',
		'formatter'=>function($d,$row){return strip_tags($d);},
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
	'archy_quotes.taxable_amount'=>array(
		'label'=>'Totale imponibile',
		'type'=>'text',
		'formatter'=>function($d,$row){return App::currency($d);},
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
	'archy_quotes.vat'=>array(
		'label'=>'Totale IVA',
		'type'=>'text',
		'formatter'=>function($d,$row){return App::currency($d);},
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
	'archy_quotes.total'=>array(
		'label'=>'Totale Preventivo',
		'type'=>'text',
		'formatter'=>function($d,$row){return App::currency($d);},
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
	'archy_quotes.status'=>array(
		'label'=>'Status',
		'type'=>'select', 
		'options_type'=>'array',
		'default_value'=>'0',
		'options'=>['In attesa','Accettato','Respinto'],
		'formatter'=>function($d,$row){
			$options=['In attesa','Accettato','Respinto'];
			return @$options[$d];
		},
		'formatter_hide_in_form'=>''
	),
	'archy_quotes.created_at'=>array(
		'label'=>'Data Creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_quotes.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
);
$intent->default_columns=array(
	'archy_quotes.id',
	'archy_firms.firm_name',
	'archy_registry.label',
	'archy_quotes.number',
	'archy_quotes.date',
	'archy_quotes.total'
);

$intent->extra_buttons=function($id,$row){
	global $app;
	$db=$app->get_db();
	$file_path=mysqli_fetch_object($db->query("select file_path from archy_quotes where id=".$id))->file_path;	
	$file=explode('/',$file_path);
	$file=end($file);
	
	$extra_button=' <a class="btn btn-sm btn-success" href="'.$file_path.'" download data-toggle="tooltip" title="Preventivo '.$file.'"><i class="glyphicon glyphicon-file"></i></a>';
	$extra_button.='
		<a class="btn btn-sm btn-warning" 
			href="/archy/app.php?do_action=send_quote&quote_id='.$id.'" 
			data-toggle="tooltip" title="Invia preventivo"
			onClick="return confirm(\'Si è certi di voler inviare il preventivo?\')"
			><i class="glyphicon glyphicon-envelope"></i></a>';
	
	return $extra_button;
};

$intent->include_in_form='intents/includes/quote_rows.php';
$intent->extra_action='store_quote';
$app->merge_intents($intent);
?>