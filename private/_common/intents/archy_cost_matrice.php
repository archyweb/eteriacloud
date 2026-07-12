<?php

$intent=new stdClass();
$intent->table='archy_cost_matrice';//nome tabella data-base
$intent->title='Sezioni contabili';//titolo (plurale)
$intent->singular='Sezione contabile';//titolo (singolare)
$intent->add='Nuova Sezione contabile';//testo usato nei forms e nei pulsanti
$intent->view_title='Matrice: archy_cost_matrice.matrice';//Titolo nella visualizzazione/modifica del singolo record
$intent->joins=[];//tabelle collegate
$intent->columns=array(//colonne della tabella principale
	'archy_cost_matrice.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''//nasconde il campo nel modulo di creazione/aggiornamento di un record
	),
	'archy_cost_matrice.codice'=>array(
		'label'=>'Codice',
		'type'=>'text',
        'attributes'=>['maxlength="4"'],
	),
	'archy_cost_matrice.matrice'=>array(
		'label'=>'Matrice di costo',
		'type'=>'text',
		'required'=>true,
		'grid_search_type'=>'search'
	),
	'archy_cost_matrice.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_cost_matrice.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);

if($app->config['association']):
	$intent->uneditable_ids=[1,2,3,4,5];
endif;

$intent->default_columns=array(//colonne del db da visualizzare di default nell'elenco (griglia)
	'archy_cost_matrice.id','archy_cost_matrice.matrice'//inizia sempre con l'id di modo che i nuovi record siano visualizzati in cima alla tabella
);

$app->merge_intents($intent);//crea i collegamenti, a cascata, con le altre tabelle
?>