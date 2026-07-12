<?php

// VF 11-gen-2021 - SEMBRA NON USATA, GIUSTO ?
die();

/*
$intent=new stdClass();
$intent->table='archy_suppliers';
$intent->title='Fornitori';
$intent->singular='Fornitore';
$intent->add='Nuovo Fornitore';
$intent->view_title='Fornitore Fornitori.label';
$intent->joins=array(
	'archy_registry as Fornitori'=>'archy_suppliers.registry_id=Fornitori.id',
	'archy_registry_categories'=>'archy_suppliers.category_id=archy_registry_categories.id'
);
$intent->columns=array(
	'archy_suppliers.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_suppliers.registry_id'=>array(
		'label'=>'Fornitore',
		'type'=>'search',
		'intent'=>'archy_registry',
		'lookup_text'=>'archy_registry.label',
		'required'=>true
	),
	'archy_suppliers.category_id'=>array(
		'label'=>'Categoria',
		'type'=>'search',
		'intent'=>'archy_registry_categories',
		'domain'=>'suppliers',
		'lookup_text'=>'category'
	),	
	'archy_suppliers.contact_person'=>array(
		'label'=>'Referente',
		'type'=>'text'
	),
	'archy_suppliers.contact_person_is_legal_representative'=>array(
		'label'=>'Referente è legale rappresentante',
		'type'=>'checkbox',
		'formatter'=>function($d,$row){return App::checkbox($d);},
		'formatter_hide_in_form'=>''
	),
	'archy_suppliers.notes'=>array(
		'label'=>'Note',
		'type'=>'editor'
	),
	'archy_suppliers.created_at'=>array(
		'label'=>'Data Creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_suppliers.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
);
$intent->default_columns=array(
	'archy_suppliers.id',
	'Fornitori.firm_name',
	'archy_registry_categories.category',
	'archy_suppliers.contact_person'
);
$app->merge_intents($intent);
