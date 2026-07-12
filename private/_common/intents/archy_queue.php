<?php

$intent=new stdClass();
$intent->table='archy_queue';//nome tabella data-base
$intent->title='Coda invio SMTP';//titolo (plurale)
$intent->singular='Coda invio SMTP';//titolo (singolare)
//$intent->add='Nuovo Ente';//testo usato nei forms e nei pulsanti
$intent->view_title='Messaggio: archy_queue.subject';//Titolo nella visualizzazione/modifica del singolo record
$intent->joins=array('archy_messages'=>'archy_messages.id=archy_queue.msgid');//tabelle collegate
$intent->columns=array(//colonne della tabella principale
	'archy_queue.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''//nasconde il campo nel modulo di creazione/aggiornamento di un record
	),
	'archy_queue.email'=>array(
		'label'=>'E-mail destinatario',
		'type'=>'text',
        'hidden'=>'',
        'attributes'=>['readonly']
    ),
	'archy_queue.status'=>array(
		'label'=>'Stato',
		'type'=>'text',
        'hidden'=>'',
        'attributes'=>['readonly']
    ),
	'archy_queue.msgid'=>array(
		'label'=>'ID messaggio',
		'type'=>'text',
        'hidden'=>'',
        'attributes'=>['readonly']
    ),
    'archy_queue.datesent'=>array(
		'label'=>'Data invio',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
        'hidden'=>'',
        'attributes'=>['readonly']
    ),
	'archy_queue.subject'=>array(
		'label'=>'Oggetto',
		'type'=>'text',
        'hidden'=>'',
        'attributes'=>['readonly']
    ),
    'archy_queue.retry'=>array(
		'label'=>'retry',
		'type'=>'text',
        'hidden'=>'',
        'attributes'=>['readonly']
    ),
    'archy_queue.serializedmsg'=>array(
		'label'=>'Messaggio',
		'type'=>'text',
        'hidden'=>'',
        'attributes'=>['readonly']
    ),
    'archy_queue.created_at'=>array(
		'label'=>'Data schedulazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
        'hidden'=>'',
	),
	'archy_queue.updated_at'=>array(
		'label'=>'Data ultimo tentativo di invio',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);

/*$intent->extra_buttons=function($id,$row){
	global $app;
    $db=$app->get_db();
    
    $extra_button='';

    $url="/archy/app.php?intent=archy_queue&do_action=delete&id=".$id;
	$extra_button.=' <a class="btn btn-falcon-danger me-1 mb-1" href="'.$url.'" data-toggle="tooltip" title="Elimina"><span class="fas fa-trash-alt"></span></a>';
		
	return $extra_button;
};*/

// Il libro soci non va modificato/cancellato
$all_ids=array_column(mysqli_fetch_all($app->db->query("SELECT id FROM archy_queue"),MYSQLI_ASSOC),'id');
$intent->uneditable_ids=$all_ids;
$intent->noinsert=true; // Serve per togliere + nella view (lentina)

$intent->default_columns=array(//colonne del db da visualizzare di default nell'elenco (griglia)
	'archy_queue.id','archy_queue.email','archy_queue.subject','archy_queue.status','archy_queue.datesent'  //inizia sempre con l'id di modo che i nuovi record siano visualizzati in cima alla tabella
);

$intent->include_in_grid='intents/includes/queue_launch_mailer.php';
$app->merge_intents($intent);//crea i collegamenti, a cascata, con le altre tabelle
