<?php

$intent=new stdClass();
$intent->table='archy_banks';//nome tabella data-base
$intent->title='Banche';//titolo (plurale)
$intent->singular='Banca';//titolo (singolare)
$intent->add='Nuova Banca';//testo usato nei forms e nei pulsanti
$intent->view_title='Banca: archy_banks.nome';//Titolo nella visualizzazione/modifica del singolo record
$intent->joins=array();//tabelle collegate
$intent->columns=array(//colonne della tabella principale
	'archy_banks.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''//nasconde il campo nel modulo di creazione/aggiornamento di un record
	),
	'archy_banks.nome'=>array(
		'label'=>'Nome Banca',
		'type'=>'text',
		'required'=>true,
		'grid_search_type'=>'search',
	),
	'archy_banks.iban'=>array(
		'label'=>'IBAN',
		'type'=>'text',
        'attributes'=>['minlength="27"','maxlength="27"'],
		'required'=>true,
        'grid_search_type'=>'search',
	),
    'archy_banks.swift'=>array(
		'label'=>'SWIFT',
		'type'=>'text',
        'attributes'=>['minlength="8"','maxlength="11"'],
        'grid_search_type'=>'search',
	),
	'archy_banks.sia'=>array(
		'label'=>'Codice SIA',
		'type'=>'text',
        'attributes'=>['minlength="5"','maxlength="5"'],
		'grid_search_type'=>'search',
	),
	'archy_banks.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_banks.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);

$intent->default_columns=array(//colonne del db da visualizzare di default nell'elenco (griglia)
	'archy_banks.id',//inizia sempre con l'id di modo che i nuovi record siano visualizzati in cima alla tabella
    'archy_banks.nome',
    'archy_banks.iban',
    'archy_banks.swift',
);
// $intent->include_in_form='intents/includes/certificati_model.php';
$app->merge_intents($intent);//crea i collegamenti, a cascata, con le altre tabelle
?>