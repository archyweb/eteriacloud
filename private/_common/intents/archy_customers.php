<?php
$intent=new stdClass();
$intent->table='archy_customers';
$intent->title='Clienti';
$intent->singular='Cliente';
$intent->add='Nuovo Cliente';
$intent->view_title='Cliente Cliente.label';//to fix
$intent->joins=array(
	'archy_registry as Cliente'=>'archy_customers.registry_id=Cliente.id',
	'archy_registry_categories'=>'archy_customers.category_id=archy_registry_categories.id'
);
$intent->columns=array(
	'archy_customers.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_customers.registry_id'=>array(
		'label'=>'Cliente',
		'type'=>'search',
		'intent'=>'archy_registry',
		'lookup_text'=>'archy_registry.label',
		'required'=>true
	),
	'archy_customers.category_id'=>array(
		'label'=>'Categoria',
		'type'=>'search',
		'intent'=>'archy_registry_categories',
		'domain'=>'customers',
		'lookup_text'=>'category'
	),
	'archy_customers.signatory'=>array(
		'label'=>'Firmatario',
		'type'=>'text'
	),
	'archy_customers.contact_person'=>array(
		'label'=>'Referente',
		'type'=>'text'
	),
	'archy_customers.contact_person_is_legal_representative'=>array(
		'label'=>'Referente è legale rappresentante',
		'type'=>'checkbox',
		'formatter'=>function($d,$row){return App::checkbox($d);},
		'formatter_hide_in_form'=>''
	),
	'archy_customers.notes'=>array(
		'label'=>'Note',
		'type'=>'editor'
	),
	'archy_customers.created_at'=>array(
		'label'=>'Data Creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_customers.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
);
$intent->default_columns=array(
	'archy_customers.id',
	'Cliente.label',
	'archy_registry_categories.category',
	'archy_customers.signatory',
	'archy_customers.contact_person'
);
$app->merge_intents($intent);
?>