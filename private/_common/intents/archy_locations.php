<?php

// NON SI USA, Vedree archy_events_location.php
 
$intent=new stdClass();
$intent->table='archy_locations';
$intent->title='Locations';
$intent->singular='Location';
$intent->add='Nuova Location';
$intent->view_title='Location archy_locations.name';
$intent->joins=array(
	'archy_cities'=>'archy_locations.city_id=archy_cities.id'
);
$intent->columns=array(
	'archy_locations.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_locations.name'=>array(
		'label'=>'Nome location',
		'type'=>'text',
		'required'=>true
	),
	'archy_locations.representative'=>array(
		'label'=>'Referente',
		'type'=>'text'
	),
	'archy_locations.city_id'=>array(
		'label'=>'Città',
		'type'=>'search',
		'intent'=>'archy_cities',
		'lookup_text'=>'archy_cities.city_label'
	),
	'archy_locations.address'=>array(
		'label'=>'Indirizzo',
		'type'=>'text'
	),
	'archy_locations.zip'=>array(
		'label'=>'CAP',
		'type'=>'text'
	),
	'archy_locations.telephone'=>array(
		'label'=>'Telefono',
		'type'=>'text'
	),
	'archy_locations.fax'=>array(
		'label'=>'Fax',
		'type'=>'text'
	),
	'archy_locations.mobile'=>array(
		'label'=>'Cellulare',
		'type'=>'text'
	),
	'archy_locations.email'=>array(
		'label'=>'E-mail',
		'type'=>'text',
		'formatter'=>function($d,$row){return App::email($d);},
		'formatter_hide_in_form'=>true,
		'formatter_hide_in_excel'=>true
	),
	'archy_locations.created_at'=>array(
		'label'=>'Data Creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_locations.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
);
$intent->default_columns=array(//colonne del db da visualizzare di default nell'elenco (griglia) 
	'archy_locations.id','archy_locations.name','archy_locations.representative','archy_locations.telephone','archy_locations.email'
);
$intent->include_in_form='intents/includes/locations.php';
$app->merge_intents($intent);
?>