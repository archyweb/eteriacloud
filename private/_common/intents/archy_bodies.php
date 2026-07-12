<?php

$intent=new stdClass();
$intent->table='archy_bodies';//nome tabella data-base
$intent->title='Cariche associative';//titolo (plurale)
$intent->singular='Carica associativa';//titolo (singolare)
$intent->add='Nuova carica associativa';//testo usato nei forms e nei pulsanti
$intent->view_title='Carica associativa: archy_bodies.body';//Titolo nella visualizzazione/modifica del singolo record
$intent->joins=array();//tabelle collegate
$intent->columns=array(//colonne della tabella principale
	'archy_bodies.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''//nasconde il campo nel modulo di creazione/aggiornamento di un record
	),
	'archy_bodies.body'=>array(
		'label'=>'Organo',
		'type'=>'text',
		'required'=>true,
		'grid_search_type'=>'search'
	),
	'archy_bodies.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_bodies.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);

//$intent->uneditable_ids=range(1,1);
$intent->undeletable_ids=range(1,1);

// TODO:
// $intent->uneditable_ids=array_column(mysqli_fetch_all($app->db->query("select id from archy_credit_notes where sdi_uuid!=''"),MYSQLI_ASSOC),'id');




$intent->default_columns=array(//colonne del db da visualizzare di default nell'elenco (griglia)
	'archy_bodies.id','archy_bodies.body'//inizia sempre con l'id di modo che i nuovi record siano visualizzati in cima alla tabella
);

$app->merge_intents($intent);//crea i collegamenti, a cascata, con le altre tabelle
?>