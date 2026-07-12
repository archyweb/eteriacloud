<?php
$intent=new stdClass();
$intent->table='archy_reports';//nome tabella data-base
$intent->title='Verbali';//titolo (plurale)
$intent->singular='Verbale';//titolo (singolare)
$intent->add='Nuovo Verbale';//testo usato nei forms e nei pulsanti
$intent->view_title='Verbale: archy_reports.name';//Titolo nella visualizzazione/modifica del singolo record
$intent->joins=array();//tabelle collegate
$intent->columns=array(//colonne della tabella principale
	'archy_reports.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''//nasconde il campo nel modulo di creazione/aggiornamento di un record
	),
	'archy_reports.name'=>array(
		'label'=>'Nome verbale',
		'type'=>'text',
		'required'=>true,
		'hidden'=>''//,
		//'grid_search_type'=>'search'
	),
	'archy_reports.report'=>array(
		'label'=>'Verbale',
		'type'=>'editor',
		'required'=>true,
		'hidden'=>''
	),
	'archy_reports.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_reports.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);
$intent->default_columns=array(//colonne del db da visualizzare di default nell'elenco (griglia)
	'archy_reports.id','archy_reports.name'//inizia sempre con l'id di modo che i nuovi record siano visualizzati in cima alla tabella
);

$intent->extra_buttons=function($id,$row){
	global $app;
	$name=$row['name'].'.pdf';
	$extra_button=' <a class="btn btn-sm btn-success" href="'.$app->abs_root.'assets/documents/reports/'.$id.'.pdf" download="'.$name.'" data-toggle="tooltip" title="Verbale '.$name.'"><i class="glyphicon glyphicon-file"></i></a>';
	return $extra_button;
};

$intent->extra_action='store_report';
$intent->include_in_form='intents/includes/report.php';
$app->merge_intents($intent);//crea i collegamenti, a cascata, con le altre tabelle
?>