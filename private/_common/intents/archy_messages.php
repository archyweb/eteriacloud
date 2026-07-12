<?php

$intent=new stdClass();
$intent->table='archy_messages';//nome tabella data-base
$intent->title='Messaggi';//titolo (plurale)
$intent->singular='Messaggio';//titolo (singolare)
$intent->add='Nuovo Messaggio';//testo usato nei forms e nei pulsanti
$intent->view_title='Messaggio: archy_messages.subject (archy_messages.dispatch_time)';//Titolo nella visualizzazione/modifica del singolo record
$intent->joins=array();//tabelle collegate
$settings=$app->get_settings($domain='notifications');
$intent->columns=array(//colonne della tabella principale
	'archy_messages.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''//nasconde il campo nel modulo di creazione/aggiornamento di un record
	),
	'archy_messages.media'=>array(
		'label'=>'Canale',
		'type'=>'select', 
		'options_type'=>'array',
		//'options'=>['E-mail'=>'E-mail','SMS'=>'SMS'],
		'options'=>['E-mail'=>'E-mail'],
		'required'=>true,
		'hidden'=>''
	),
	'archy_messages.recipients'=>array(
		'label'=>'Destinatari',
		'type'=>'text',
		'hidden'=>'',
		'formatter'=>function($d,$row){
			return '[Dato Composito]';
		},
		'formatter_hide_in_form'=>''
	),
	'archy_messages.subject'=>array(
		'label'=>'Oggetto',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_messages.message'=>array(
		'label'=>'Messaggio',
		'type'=>'plain_editor',
                'editor_height'=>'400',
                'placeholder'=>true,
                'placeholder_origin' => 'archy_messages',
		'hidden'=>''
	),
	'archy_messages.attachments'=>array(
		'label'=>'Allegati',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_messages.dispatch_time'=>array(
		'label'=>'Data e orario di invio',
		'type'=>'datetime',
		'attributes'=>['placeholder="Immediato"','data-min_date=""'],
		'formatter'=>function($d,$row){return App::latin_date_time($d)==''? 'Non inviato': App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_messages.sender_name'=>array(
		'label'=>'Nome mittente',
		'type'=>'text',
		'hidden'=>'',
		'default_value'=>@$settings->sender_name,
		'required'=>true
	),
	'archy_messages.sender_mail'=>array(
		'label'=>'E-mail mittente',
		'type'=>'text',
		'hidden'=>'',
		'default_value'=>@$settings->sender_email,
		'required'=>true
	),
	'archy_messages.created_at'=>array(
		'label'=>'Data salvataggio',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_messages.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);
$intent->default_columns=array(//colonne del db da visualizzare di default nell'elenco (griglia)
	'archy_messages.id','archy_messages.media','archy_messages.subject','archy_messages.dispatch_time','archy_messages.created_at'
);

//$intent->extra_action='store_message_attachments';
$intent->include_in_form='intents/includes/messages.php';
$app->merge_intents($intent);//crea i collegamenti, a cascata, con le altre tabelle
?>