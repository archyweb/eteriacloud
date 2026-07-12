<?php

$intent=new stdClass();
$intent->table='admin_additional_fields';//nome tabella data-base
$intent->title='Tabelle Espandibili';//titolo (plurale)
$intent->singular='Tabella Espandibile';//titolo (singolare)
$intent->add='Nuova Tabella Espandibile';//testo usato nei forms e nei pulsanti
$intent->view_title='Tabella Espandibile: admin_additional_fields.body';//Titolo nella visualizzazione/modifica del singolo record
$intent->joins=array();//tabelle collegate
$intent->columns=array(//colonne della tabella principale
	'admin_additional_fields.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''//nasconde il campo nel modulo di creazione/aggiornamento di un record
	),
	'admin_additional_fields.table_name'=>array(
		'label'=>'Tabella',
		'type'=>'select', 
		'required'=>true,
		'options_type'=>'sql',
		'options'=>"SELECT TABLE_NAME as option_value, TABLE_NAME as option_text FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA='Gest_GE' and TABLE_NAME like 'archy_%'"
	),
	'admin_additional_fields.alias'=>array(
		'label'=>'Alias',
		'type'=>'text',
		'required'=>true
	),
	'admin_additional_fields.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'admin_additional_fields.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);
$intent->default_columns=array(//colonne del db da visualizzare di default nell'elenco (griglia)
	'admin_additional_fields.id','admin_additional_fields.table_name','admin_additional_fields.alias'//inizia sempre con l'id di modo che i nuovi record siano visualizzati in cima alla tabella
);

$app->merge_intents($intent);//crea i collegamenti, a cascata, con le altre tabelle
?>