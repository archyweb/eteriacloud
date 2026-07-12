<?php

$intent=new stdClass();
$intent->table='archy_notices';//nome tabella data-base
$intent->title='Notifiche';//titolo (plurale)
$intent->singular='Notifica';//titolo (singolare)
$intent->add='Nuova Notifica';//testo usato nei forms e nei pulsanti
$intent->view_title='Notifica: archy_notices.id';//Titolo nella visualizzazione/modifica del singolo record
$intent->joins=array();//tabelle collegate
$intent->columns=array(//colonne della tabella principale
	'archy_notices.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''//nasconde il campo nel modulo di creazione/aggiornamento di un record
	),	
	'archy_notices.notice'=>array(
		'label'=>'Notifica',
		'type'=>'plain_editor',
		'required'=>true,
		'formatter'=>function($d,$row){
			return strip_tags($d);
		},
		'formatter_hide_in_form'=>true,
		'custom_wrapper'=>'col-md-6',
		'editor_height'=>250
	),
	'archy_notices.seen'=>array(
		'label'=>'Letto',
		'type'=>'checkbox',
		'formatter'=>function($d,$row){return App::checkbox($d);},
		'formatter_hide_in_form'=>''
	),
        'archy_notices.created_at'=>array(
		'label'=>'Data',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_notices.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);
$intent->default_columns=array(//colonne del db da visualizzare di default nell'elenco (griglia)
	'archy_notices.id','archy_notices.created_at','archy_notices.notice','archy_notices.seen'//inizia sempre con l'id di modo che i nuovi record siano visualizzati in cima alla tabella
);
$app->merge_intents($intent);//crea i collegamenti, a cascata, con le altre tabelle
?>