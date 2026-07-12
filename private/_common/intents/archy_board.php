<?php
$intent=new stdClass();
$intent->table='archy_board';//nome tabella data-base
$intent->title='Board';//titolo (plurale)
$intent->singular='Dirigente';//titolo (singolare)
$intent->add='Nuovo Dirigente';//testo usato nei forms e nei pulsanti
$intent->view_title='archy_registry.label: archy_bodies.body';//Titolo nella visualizzazione/modifica del singolo record
$intent->joins=array(
	'archy_registry'=>'archy_board.registry_id=archy_registry.id',
	'archy_bodies'=>'archy_board.body_id=archy_bodies.id'
);//tabelle collegate
//$app->organi=array('Consiglio Direttivo','Collegio Revisori','Revisore Unico','Commissione Tecnico Scientifica','Collegio dei probiviri');
//$app->funzioni=array('Presidente','Vice Presidente','Segretario','Tesoriere','Membro');
$intent->columns=array(//colonne della tabella principale
	'archy_board.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''//nasconde il campo nel modulo di creazione/aggiornamento di un record
	),
	'archy_board.registry_id'=>array(
		'label'=>'Dirigente',
		'type'=>'search',
		'intent'=>'archy_registry',
		'lookup_text'=>'archy_registry.label',
		'required'=>true
	),
	'archy_board.body_id'=>array(
		'label'=>'Carica associativa',
		'type'=>'search',
		'intent'=>'archy_bodies',
		'lookup_text'=>'body',
		'required'=>true
	),
	'archy_board.mandate_begin'=>array(
		'label'=>'Inizio Mandato',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date($d);}
	),
	'archy_board.mandate_end'=>array(
		'label'=>'Fine Mandato',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date($d);}
	),			
	'archy_board.created_at'=>array(
		'label'=>'Data Creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_board.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
);
$intent->default_columns=array(//colonne del db da visualizzare di default nell'elenco (griglia)
	'archy_board.id',
	'archy_bodies.body',
	'archy_registry.label',
	'archy_board.mandate_begin',
	'archy_board.mandate_end'
);

$app->merge_intents($intent);//crea i collegamenti, a cascata, con le altre tabelle
?>