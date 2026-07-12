<?php

$intent=new stdClass();
$intent->table='archy_art_series';//nome tabella data-base
$intent->title='Serie';//titolo (plurale)
$intent->singular='Serie';//titolo (singolare)
$intent->add='Nuova Serie';//testo usato nei forms e nei pulsanti
$intent->view_title='Serie: archy_art_series.nome_serie';//Titolo nella visualizzazione/modifica del singolo record
$intent->joins=array();//tabelle collegate
$intent->columns=array(//colonne della tabella principale
	'archy_art_series.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''//nasconde il campo nel modulo di creazione/aggiornamento di un record
	),
	'archy_art_series.nome_serie'=>array(
		'label'=>'Serie',
		'type'=>'text',
		'required'=>true,
		'grid_search_type'=>'search'
	),
	'archy_art_series.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_art_series.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);
$intent->default_columns=array(//colonne del db da visualizzare di default nell'elenco (griglia)
	'archy_art_series.id','archy_art_series.nome_serie'//inizia sempre con l'id di modo che i nuovi record siano visualizzati in cima alla tabella
);

$app->merge_intents($intent);//crea i collegamenti, a cascata, con le altre tabelle
?>