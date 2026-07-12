<?php
$intent=new stdClass();
$intent->table='archy_art_diary_category';//nome tabella data-base
$intent->title='Categoria';//titolo (plurale)
$intent->singular='Categoria';//titolo (singolare)
$intent->add='Nuova Categoria';//testo usato nei forms e nei pulsanti
$intent->view_title='Categoria: archy_art_diary_category.category_diary';//Titolo nella visualizzazione/modifica del singolo record
$intent->joins=array();//tabelle collegate
$intent->columns=array(//colonne della tabella principale
	'archy_art_diary_category.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''//nasconde il campo nel modulo di creazione/aggiornamento di un record
	),
	'archy_art_diary_category.category_diary'=>array(
		'label'=>'Categoria',
		'type'=>'text',
		'required'=>true,
		'grid_search_type'=>'search'
	),
	'archy_art_diary_category.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_art_diary_category.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);
$intent->default_columns=array(//colonne del db da visualizzare di default nell'elenco (griglia)
	'archy_art_diary_category.id','archy_art_diary_category.category_diary'//inizia sempre con l'id di modo che i nuovi record siano visualizzati in cima alla tabella
);

$app->merge_intents($intent);//crea i collegamenti, a cascata, con le altre tabelle
?>