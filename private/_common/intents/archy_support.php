<?php

$intent=new stdClass();
$intent->table='archy_support';//nome tabella data-base
$intent->title='Tickets';//titolo (plurale)
$intent->singular='Ticket';//titolo (singolare)
$intent->add='Nuovo Tickets';//testo usato nei forms e nei pulsanti
$intent->view_title='Tickets: archy_support.subject';//Titolo nella visualizzazione/modifica del singolo record
$intent->joins=array();//tabelle collegate
$intent->columns=array(//colonne della tabella principale
	'archy_support.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''//nasconde il campo nel modulo di creazione/aggiornamento di un record
	),
	'archy_support.thread_id'=>array(
		'label'=>'ID Conversazione',
		'type'=>'text',
		'required'=>true
	),
	'archy_support.user'=>array(
		'label'=>'Utente',
		'type'=>'text',
		'required'=>true
	),	
	'archy_support.type'=>array(
		'label'=>'Tipologia',
		'type'=>'select',
		'options_type'=>'array',
		'options'=>['tech'=>'Assistenza tecnica','admin'=>'Assistenza amministrativa'],
		'options_type'=>'array',
		'formatter'=>function($d,$row){
			$options=['tech'=>'Assistenza tecnica','admin'=>'Assistenza amministrativa'];
			return $options[$d];
		},
		'formatter_hide_in_form'=>true,
		'required'=>true
	),
	'archy_support.subject'=>array(
		'label'=>'Oggetto',
		'type'=>'text',
		'required'=>true
	),	
	'archy_support.request'=>array(
		'label'=>'Richiesta',
		'type'=>'editor',
		'required'=>true
	),
	'archy_support.reply_to'=>array(
		'label'=>'E-mail per notifiche',
		'type'=>'text',
		'required'=>true,
		'formatter'=>function($d,$row){return App::email($d);},
		'formatter_hide_in_form'=>true,
	),
	'archy_support.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_support.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);
$intent->default_columns=[
	'archy_support.id','archy_support.user','archy_support.type','archy_support.subject','archy_support.request','archy_support.reply_to'
];

$app->merge_intents($intent);//crea i collegamenti, a cascata, con le altre tabelle
?>