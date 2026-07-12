<?php

$intent=new stdClass();
$intent->table='archy_cities';
$intent->title='Comuni';
$intent->singular='Comune';
$intent->add='Nuovo Comune';
$intent->view_title='Comune archy_cities.city';
$intent->joins=array(
	'archy_countries'=>'archy_cities.country_id=archy_countries.id'
);
$intent->columns=array(
	'archy_cities.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_cities.city'=>array(
		'label'=>'Città',
		'type'=>'text',
		'required'=>true
	),
	'archy_cities.province_district'=>array(
		'label'=>'Provincia/Distretto',
		'type'=>'text'
	),
	'archy_cities.province_district_code'=>array(
		'label'=>'Sigla Provincia/Distretto',
		'type'=>'text',
		'attributes'=>['maxlength="3"']
	),
	'archy_cities.zip'=>array(
		'label'=>'CAP',
		'type'=>'text',
		'attributes'=>['maxlength="5"']
	),
	'archy_cities.state_region'=>array(
		'label'=>'Regione/Stato',
		'type'=>'text'
	),
	'archy_cities.country_id'=>array(
		'label'=>'Nazione',
		'type'=>'search',
		'intent'=>'archy_countries',
		'lookup_text'=>'archy_countries.country_it',
		'required'=>''
	),
	'archy_cities.city_label'=>array(
		'label'=>'Comune',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_cities.created_at'=>array(
		'label'=>'Data Creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_cities.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
);
$intent->default_columns=array(
	'archy_cities.id','archy_cities.city','archy_cities.province_district','archy_cities.state_region','archy_countries.country_it'
);
$intent->extra_action='store_city_label';
$app->merge_intents($intent);
?>