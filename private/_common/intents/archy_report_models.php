<?php
$intent=new stdClass();
$intent->table='archy_report_models';//nome tabella data-base
$intent->title='Modelli verbale';//titolo (plurale)
$intent->singular='Modello verbale';//titolo (singolare)
$intent->add='Nuovo Modello';//testo usato nei forms e nei pulsanti
$intent->view_title='Modello: archy_report_models.name';//Titolo nella visualizzazione/modifica del singolo record
$intent->joins=array();//tabelle collegate
$intent->columns=array(//colonne della tabella principale
	'archy_report_models.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''//nasconde il campo nel modulo di creazione/aggiornamento di un record
	),
	'archy_report_models.name'=>array(
		'label'=>'Nome modello',
		'type'=>'text',
		'required'=>true,
		'hidden'=>''//,
		//'grid_search_type'=>'search'
	),
	'archy_report_models.model'=>array(
		'label'=>'Modello verbale',
		'type'=>'editor',
		'required'=>true,
		'hidden'=>''
	),
	'archy_report_models.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_report_models.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);
$intent->default_columns=array(//colonne del db da visualizzare di default nell'elenco (griglia)
	'archy_report_models.id','archy_report_models.name'//inizia sempre con l'id di modo che i nuovi record siano visualizzati in cima alla tabella
);
$intent->include_in_form='intents/includes/report_model.php';
$app->merge_intents($intent);//crea i collegamenti, a cascata, con le altre tabelle
?>