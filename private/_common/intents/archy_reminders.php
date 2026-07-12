<?php

$intent=new stdClass();
$intent->table='archy_reminders';//nome tabella data-base
$intent->title='Promomeria';//titolo (plurale)
//$intent->singular='Banca';//titolo (singolare)
$intent->add='Nuovo promemoria';//testo usato nei forms e nei pulsanti
$intent->view_title='Promomeria: archy_reminders.reminder_title';//Titolo nella visualizzazione/modifica del singolo record
$intent->joins=array();//tabelle collegate
$intent->columns=array(//colonne della tabella principale
	'archy_reminders.id'=>array(
		'label'=>'ID',
		'type'=>'hidden',
		'hidden'=>''//nasconde il campo nel modulo di creazione/aggiornamento di un record
	),
	'archy_reminders.reminder_title'=>array(
		'label'=>'Titolo promemoria',
		'type'=>'text',
		'required'=>true
	),
	'archy_reminders.reminder_days'=>array(
		'label'=>'Numero giorni',
		'type'=>'text',
		'required'=>true
	),
        'archy_reminders.reminder_delay'=>array(
		'label'=>'Quando?',
		'type'=>'select',
                'options_type'=>'array',
		'options'=>array(
			'before'=>'Prima',
			'after'=>'Dopo',
		),
		'formatter_hide_in_form'=>'',
		'required'=>true
	),
	'archy_reminders.reminder_origin'=>array(
		'label'=>'Origine',
		'type'=>'hidden',
                'formatter_hide_in_form'=>'',
		'hidden'=>''
	),
        'archy_reminders.reminder_origin_id'=>array(
		'label'=>'ID origine',
		'type'=>'hidden',
                'formatter_hide_in_form'=>'',
		'hidden'=>''
	),
        'archy_reminders.reminder_subject'=>array(
		'label'=>'Oggetto',
		'type'=>'text',
		'required'=>true
	),
        'archy_reminders.reminder_description'=>array(
		'label'=>'Testo',
		'type'=>'plain_editor',
                'editor_height'=>'400'
	),
        'archy_reminders.reminder_sms'=>array(
		'label'=>'Testo sms',
		'type'=>'editor'
	),
	'archy_reminders.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_reminders.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);

// $intent->include_in_form='intents/includes/certificati_model.php';
$app->merge_intents($intent);//crea i collegamenti, a cascata, con le altre tabelle
?>