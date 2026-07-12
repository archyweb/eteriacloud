<?php

$intent=new stdClass();
$intent->table='archy_countries';
$intent->title='Nazioni';
$intent->singular='Nazione';
$intent->add='Nuova Nazione';
$intent->view_title='Nazione archy_countries.country_it';
$intent->joins=array();
$intent->columns=array(
	'archy_countries.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''
	),
	/*'archy_countries.codice_istat'=>array(
		'label'=>'Codice ISTAT',
		'type'=>'text'
	),*/
	'archy_countries.country_it'=>array(
		'label'=>'Nazione',
		'type'=>'text',
		'required'=>true
	),
	'archy_countries.country_en'=>array(
		'label'=>'Nazione (eng)',
		'type'=>'text'
	),
	/*'archy_countries.nationality'=>array(
		'label'=>'Nazionalità',
		'type'=>'text'
	),*/
	'archy_countries.in_ue'=>array(
		'label'=>'Membro UE',
		'type'=>'checkbox',
		'formatter'=>function($d,$row){return $d=='0'? 'No':'Sì';},
		'formatter_hide_in_form'=>true
	),
	/*'archy_countries.iso'=>array(
		'label'=>'Codice ISO',
		'type'=>'text'
	),*/
	'archy_countries.iso2'=>array(
		'label'=>'Sigla Nazione',
		'type'=>'text'
	),
	/*'archy_countries.iso3'=>array(
		'label'=>'Codice ISO3',
		'type'=>'text'
	),*/
	'archy_countries.created_at'=>array(
		'label'=>'Data Creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_countries.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
);
$intent->default_columns=array(
	'archy_countries.id','archy_countries.country_it','archy_countries.country_en','archy_countries.in_ue','archy_countries.iso2'
);
$app->merge_intents($intent);
?>