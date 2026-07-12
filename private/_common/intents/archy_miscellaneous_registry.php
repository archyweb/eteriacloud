<?php
$intent=new stdClass();
$intent->table='archy_miscellaneous_registry';
$intent->title='Anagrafiche generiche';
$intent->singular='Anagrafica generica';
$intent->add='Nuova Anagrafica generica';
$intent->view_title='Anagrafica generica archy_registry.label';
$intent->joins=array(
	'archy_registry'=>'archy_miscellaneous_registry.registry_id=archy_registry.id',
	'archy_registry_categories'=>'archy_miscellaneous_registry.category_id=archy_registry_categories.id'
);
$intent->columns=array(
	'archy_miscellaneous_registry.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_miscellaneous_registry.registry_id'=>array(
		'label'=>'Nominativo',
		'type'=>'search',
		'intent'=>'archy_registry',
		'lookup_text'=>'archy_registry.label',
		'required'=>true
	),
	'archy_miscellaneous_registry.category_id'=>array(
		'label'=>'Categoria',
		'type'=>'search',
		'intent'=>'archy_registry_categories',
		'domain'=>'miscellaneous',
		'lookup_text'=>'category'
	),	
	'archy_miscellaneous_registry.contact_person'=>array(
		'label'=>'Referente',
		'type'=>'text'
	),
	'archy_miscellaneous_registry.contact_person_is_legal_representative'=>array(
		'label'=>'Referente è legale rappresentante',
		'type'=>'checkbox',
		'formatter'=>function($d,$row){return App::checkbox($d);},
		'formatter_hide_in_form'=>''
	),
	'archy_miscellaneous_registry.notes'=>array(
		'label'=>'Note',
		'type'=>'editor'
	),
	'archy_miscellaneous_registry.created_at'=>array(
		'label'=>'Data Creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_miscellaneous_registry.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
);
$intent->default_columns=array(
	'archy_miscellaneous_registry.id',
	'archy_registry.label',
	'archy_registry_categories.category',
	'archy_miscellaneous_registry.contact_person'
);
$app->merge_intents($intent);
?>