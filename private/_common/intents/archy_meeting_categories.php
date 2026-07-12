<?php

$intent=new stdClass();
$intent->table='archy_meeting_categories';//nome tabella data-base
$intent->title='Categorie Adunanza';//titolo (plurale)
$intent->singular='Categoria Adunanza';//titolo (singolare)
$intent->add='Nuova Categoria';//testo usato nei forms e nei pulsanti
$intent->view_title='Categoria: archy_meeting_categories.category';//Titolo nella visualizzazione/modifica del singolo record
$intent->joins=array();//tabelle collegate
$intent->columns=array(//colonne della tabella principale
	'archy_meeting_categories.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''//nasconde il campo nel modulo di creazione/aggiornamento di un record
	),
	'archy_meeting_categories.category'=>array(
		'label'=>'Categoria',
		'type'=>'text',
		'required'=>true,
		'grid_search_type'=>'search',
		'hidden'=>''
	),
	'archy_meeting_categories.report_model'=>array(
		'label'=>'Modello di verbale',
		'type'=>'plain_editor',
		'editor_height'=>'400',
		'placeholder'=>true,
                'placeholder_origin' => 'archy_meeting_categories',
		'required'=>true,
		'hidden'=>''
	),
	'archy_meeting_categories.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_meeting_categories.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);
$intent->default_columns=array(//colonne del db da visualizzare di default nell'elenco (griglia)
	'archy_meeting_categories.id','archy_meeting_categories.category'//inizia sempre con l'id di modo che i nuovi record siano visualizzati in cima alla tabella
);
$intent->include_in_form='intents/includes/meeting_categories.php';
$app->merge_intents($intent);//crea i collegamenti, a cascata, con le altre tabelle
?>