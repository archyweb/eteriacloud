<?php

$intent=new stdClass();
$intent->table='archy_art_collections';//nome tabella data-base
$intent->title='Collezioni';//titolo (plurale)
$intent->singular='Collezione';//titolo (singolare)
$intent->add='Nuova collezione';//testo usato nei forms e nei pulsanti
$intent->view_title='Collezioni: archy_art_collections.nome_collezione';//Titolo nella visualizzazione/modifica del singolo record
$intent->joins=array();//tabelle collegate
$intent->columns=array(//colonne della tabella principale
	'archy_art_collections.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''//nasconde il campo nel modulo di creazione/aggiornamento di un record
	),
	'archy_art_collections.nome_collezione'=>array(
		'label'=>'Collezione',
		'type'=>'text',
		'required'=>true,
		'grid_search_type'=>'search'
	),
	'archy_art_collections.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_art_collections.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);
$intent->default_columns=array(//colonne del db da visualizzare di default nell'elenco (griglia)
	'archy_art_collections.id','archy_art_collections.nome_collezione'//inizia sempre con l'id di modo che i nuovi record siano visualizzati in cima alla tabella
);

$app->merge_intents($intent);//crea i collegamenti, a cascata, con le altre tabelle
