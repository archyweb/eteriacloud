<?php

$intent=new stdClass();
$intent->table='archy_art_authors';//nome tabella data-base
$intent->title='Autore';//titolo (plurale)
$intent->singular='Autore';//titolo (singolare)
$intent->add='Nuovo Autore';//testo usato nei forms e nei pulsanti
$intent->view_title='Autore: archy_art_authors.id';
$intent->joins=array();//tabelle collegate
$intent->columns=array(
		'archy_art_authors.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_art_authors.name_author'=>array(
		'label'=>'Autore',
		'type'=>'text',
		
	),
	'archy_art_authors.date_birth_author'=>array(
		'label'=>'Data di nascita dell\'Autore',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date($d);},
	),
	'archy_art_authors.place_birth_author'=>array(
		'label'=>'Luogo di nascita dell\'Autore',
		'type'=>'text',
	),
	'archy_art_authors.date_death_author'=>array(
		'label'=>'Data di morte dell\'Autore',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date($d);},
	),
	'archy_art_authors.place_death_author'=>array(
		'label'=>'Luogo di morte dell\'Autore',
		'type'=>'text',
	),
	'archy_art_authors.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_art_authors.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);

$intent->default_columns=array(//colonne del db da visualizzare di default nell'elenco (griglia)
	'archy_art_authors.id', 'archy_art_authors.name_author'//inizia sempre con l'id di modo che i nuovi record siano visualizzati in cima alla tabella
);

//$intent->include_in_form='intents/includes/author.php';
//$intent->extra_action='store_author';

$app->merge_intents($intent);
