<?php

$intent=new stdClass();
$intent->table='archy_events_locations';
$intent->title='Locations';
$intent->singular='Location';
$intent->add='Nuova Location';
$intent->view_title='archy_events_locations.name';
$intent->joins=array(
	'archy_cities'=>'archy_events_locations.city_id=archy_cities.id'
);
$intent->columns=array(
	'archy_events_locations.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_events_locations.name'=>array(
		'label'=>'Nome location',
		'type'=>'text',
		'required'=>true
	),
	'archy_events_locations.representative'=>array(
		'label'=>'Referente',
		'type'=>'text'
	),
	'archy_events_locations.city_id'=>array(
		'label'=>'Città',
		'type'=>'search',
		'intent'=>'archy_cities',
		'lookup_text'=>'archy_cities.city_label',
        'required'=>true
	),
	'archy_events_locations.address'=>array(
		'label'=>'Indirizzo',
		'type'=>'text',
        'required'=>true
	),
	'archy_events_locations.zip'=>array(
		'label'=>'CAP',
		'type'=>'text',
        'required'=>true
	),
	'archy_events_locations.telephone'=>array(
		'label'=>'Telefono',
		'type'=>'text'
	),
	'archy_events_locations.fax'=>array(
		'label'=>'Fax',
		'type'=>'text'
	),
	'archy_events_locations.mobile'=>array(
		'label'=>'Cellulare',
		'type'=>'text'
	),
	'archy_events_locations.email'=>array(
		'label'=>'E-mail',
		'type'=>'text',
		'formatter'=>function($d,$row){return App::email($d);},
		'formatter_hide_in_form'=>true,
		'formatter_hide_in_excel'=>true
	),
	'archy_events_locations.created_at'=>array(
		'label'=>'Data Creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_events_locations.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
);
$intent->default_columns=array(//colonne del db da visualizzare di default nell'elenco (griglia) 
	'archy_events_locations.id','archy_events_locations.name','archy_events_locations.representative','archy_events_locations.telephone','archy_events_locations.email'
);
$intent->include_in_form='intents/includes/locations.php';

$all_ids=array_column(mysqli_fetch_all($app->db->query("SELECT DISTINCT ese.location_id AS location_id FROM archy_events_sessions ese, archy_events_subscriptions esu, archy_events_speeches esp WHERE ese.event_id = esu.event_id and esp.session_id = ese.id"),MYSQLI_ASSOC),'location_id');
$intent->uneditable_ids=$all_ids;

$app->merge_intents($intent);
?>