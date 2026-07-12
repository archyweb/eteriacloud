<?php
$intent=new stdClass();
$intent->table='archy_branches';//nome tabella data-base
$intent->title='Sedi Distaccate';//titolo (plurale)
$intent->singular='Sede Distaccata';//titolo (singolare)
$intent->add='Nuova Sede Distaccata';//testo usato nei forms e nei pulsanti
$intent->view_title='archy_branches.firm_name';//Titolo nella visualizzazione/modifica del singolo record
$intent->joins=['archy_cities'=>'archy_branches.city_id=archy_cities.id'];//tabelle collegate
$intent->columns=array(//colonne della tabella principale
	'archy_branches.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''//nasconde il campo nel modulo di creazione/aggiornamento di un record
	),
	'archy_branches.firm_name'=>array(
		'label'=>'Denominazione Sociale ',
		'type'=>'text',
		'required'=>true
	),
	'archy_branches.address'=>array(
		'label'=>'Indirizzo',
		'type'=>'text'
	),
	'archy_branches.city_id'=>array(
		'label'=>'Città',
		'type'=>'search',
		'intent'=>'archy_cities',
		'lookup_text'=>'archy_cities.city_label'
	),
	'archy_branches.zip'=>array(
		'label'=>'CAP',
		'type'=>'text'
	),
	'archy_branches.phone'=>array(
		'label'=>'Telefono',
		'type'=>'text'
	),
	'archy_branches.fax'=>array(
		'label'=>'Fax',
		'type'=>'text'
	),
	'archy_branches.mobile'=>array(
		'label'=>'Cellulare',
		'type'=>'text'
	),
	'archy_branches.email'=>array(
		'label'=>'E-mail',
		'type'=>'text',
		'formatter'=>function($d,$row){return App::email($d);},
		'formatter_hide_in_form'=>true,
		'formatter_hide_in_excel'=>true
	),
	'archy_branches.certified_email'=>array(
		'label'=>'PEC',
		'type'=>'text',
		'formatter'=>function($d,$row){return App::email($d);},
		'formatter_hide_in_form'=>true,
		'formatter_hide_in_excel'=>true
	),
	'archy_branches.created_at'=>array(
		'label'=>'Data Creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_branches.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
);
$intent->default_columns=array(//colonne del db da visualizzare di default nell'elenco (griglia)
	'archy_branches.id',
	'archy_branches.firm_name',
	'archy_cities.city',
	'archy_cities.state_region',
	'archy_branches.phone',
	'archy_branches.email'
	//inizia sempre con l'id di modo che i nuovi record siano visualizzati in cima alla tabella
);
$intent->include_in_form='intents/includes/branches.php';
$app->merge_intents($intent);//crea i collegamenti, a cascata, con le altre tabelle
?>