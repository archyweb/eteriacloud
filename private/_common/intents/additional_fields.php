<?php
$intent=new stdClass();
$intent->table='additional_fields';
$intent->title='Campi aggiuntivi';
$intent->singular='Campo aggiuntivo';
$intent->add='Nuovo campo aggiuntivo';
$intent->view_title='Campo aggiuntivo additional_fields.name';
$intent->joins=array();
$fields_types=[
	'text'=>'Campo di testo',
	'date'=>'Campo Data (gg/mm/aaaa)',
	'checkbox'=>'Casella di controllo',
	//'search'=>'Search (valutare se utilizzarlo)',
	'select'=>'Menu a tendina',
	'multiple'=>'Menu a tendina con scelta multipla',
	'editor'=>'Area di testo',
	'file'=>'Allega un file',
	'bulk_upload'=>'Allega uno o più files'
];
$intent->fields_types=$fields_types;
$intent->columns=array(
	'additional_fields.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''
	),
	'additional_fields.name'=>array(
		'label'=>'Nome campo',
		'type'=>'text',
		'required'=>''
	),
	'additional_fields.type'=>array(
		'label'=>'Tipo di campo',
		'type'=>'select', 
		'options_type'=>'array',
		'options'=>$intent->fields_types,
		'required'=>'',
		'formatter'=>function($d,$row){global $fields_types; return $fields_types[$d];},
		'formatter_hide_in_form'=>true
	),
	'additional_fields.options'=>array(
		'label'=>'Opzioni',
		'type'=>'text',
		'hidden'=>'',
		'formatter'=>function($d,$row){return join(', ',unserialize($d));},
		'formatter_hide_in_form'=>true
	),
	'additional_fields.tables'=>array(
		'label'=>'Applica alle tabelle',
		'type'=>'multiple', 
		'options_type'=>'sql',
		'options'=>'select table_name as option_value, alias as option_text from admin_additional_fields order by option_text',
		'required'=>'',
		'formatter'=>function($d,$row){return App::format_additional_fields_tables(unserialize($d));},
		'formatter_hide_in_form'=>true
	),
	'additional_fields.required'=>array(
		'label'=>'Richiesto',
		'type'=>'checkbox',
		'formatter'=>function($d,$row){return $d=='0'? 'No':'Sì';},
		'formatter_hide_in_form'=>true
	),
	'additional_fields.created_at'=>array(
		'label'=>'Data Creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'additional_fields.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
);
$intent->default_columns=array(
	'additional_fields.id','additional_fields.name','additional_fields.type','additional_fields.required','additional_fields.tables'
);

$intent->extra_action='store_additional_field_domains';
$intent->include_in_form='intents/includes/additional_field_options.php';

$app->merge_intents($intent);
?>