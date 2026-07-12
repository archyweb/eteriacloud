<?php
$intent=new stdClass();
$intent->table='archy_petty_cash_purposes';//nome tabella data-base
$intent->title='Causali';//titolo (plurale)
$intent->singular='Causale';//titolo (singolare)
$intent->add='Nuova Causale';//testo usato nei forms e nei pulsanti
$intent->view_title='Causale: archy_petty_cash_purposes.purpose';//Titolo nella visualizzazione/modifica del singolo record
$intent->joins=array();//tabelle collegate
$intent->columns=array(//colonne della tabella principale
	'archy_petty_cash_purposes.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''//nasconde il campo nel modulo di creazione/aggiornamento di un record
	),
	'archy_petty_cash_purposes.purpose'=>array(
		'label'=>'Causale Prima Nota',
		'type'=>'text',
		'required'=>true,
		'grid_search_type'=>'search'
	),
	'archy_petty_cash_purposes.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_petty_cash_purposes.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);
$intent->default_columns=array(//colonne del db da visualizzare di default nell'elenco (griglia)
	'archy_petty_cash_purposes.id','archy_petty_cash_purposes.purpose'//inizia sempre con l'id di modo che i nuovi record siano visualizzati in cima alla tabella
);

$app->merge_intents($intent);//crea i collegamenti, a cascata, con le altre tabelle
?>