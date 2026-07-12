<?php

$intent=new stdClass();
$intent->table='archy_petty_cash_types';//nome tabella data-base
$intent->title='Strumenti di pagamento';//titolo (plurale)
$intent->singular='Strumento di pagamento';//titolo (singolare)
$intent->add='Nuovo Strumento di pagamento';//testo usato nei forms e nei pulsanti
$intent->view_title='Registrazione contabile: archy_petty_cash_types.type';//Titolo nella visualizzazione/modifica del singolo record
$intent->joins=array();//tabelle collegate
$intent->columns=array(//colonne della tabella principale
	'archy_petty_cash_types.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''//nasconde il campo nel modulo di creazione/aggiornamento di un record
	),
	'archy_petty_cash_types.type'=>array(
		'label'=>'Strumento di pagamento',
		'type'=>'text',
		'required'=>true,
		'grid_search_type'=>'search'
	),
	'archy_petty_cash_types.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_petty_cash_types.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);
$intent->default_columns=array(//colonne del db da visualizzare di default nell'elenco (griglia)
	'archy_petty_cash_types.id','archy_petty_cash_types.type'//inizia sempre con l'id di modo che i nuovi record siano visualizzati in cima alla tabella
);

$app->merge_intents($intent);//crea i collegamenti, a cascata, con le altre tabelle
?>