<?php
$intent=new stdClass();
$intent->table='archy_authenticity_certificates';//nome tabella data-base
$intent->title='Certificati di autenticità';//titolo (plurale)
$intent->singular='Certificato di autenticità';//titolo (singolare)
$intent->add='Nuovo Certificato';//testo usato nei forms e nei pulsanti
$intent->view_title='Certificato: archy_authenticity_certificates.name';//Titolo nella visualizzazione/modifica del singolo record
$intent->joins=['authenticity_certificate_requests'=>'archy_authenticity_certificates.request_id=authenticity_certificate_requests.id'];//tabelle collegate
$intent->columns=array(//colonne della tabella principale
	'archy_authenticity_certificates.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''//nasconde il campo nel modulo di creazione/aggiornamento di un record
	),
	'archy_authenticity_certificates.name'=>array(
		'label'=>'Nome certificato',
		'type'=>'text',
		'required'=>true
	),
	'archy_authenticity_certificates.certificate'=>array(
		'label'=>'Certificato',
		'type'=>'editor',
		'required'=>true,
		'default_value'=>$app->get_settings($domain='authenticity_certificate')->certificate
	),
	'archy_authenticity_certificates.request_id'=>array(
		'label'=>'ID Richiesta',
		'type'=>'search',
		'intent'=>'authenticity_certificate_requests',
		'lookup_text'=>'id'
	),
	'archy_authenticity_certificates.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_authenticity_certificates.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);
$intent->default_columns=array(//colonne del db da visualizzare di default nell'elenco (griglia)
	'archy_authenticity_certificates.id','archy_authenticity_certificates.name'//inizia sempre con l'id di modo che i nuovi record siano visualizzati in cima alla tabella
);

$intent->extra_buttons=function($id,$row){
	global $app;
	$pdf_path='/assets/documents/authenticity_certificates/'.$id.'.pdf';
	$extra_button=' <a class="btn btn-outline-secondary" href="'.$pdf_path.'" data-toggle="tooltip" title="Certificato '.$id.'"><span class="far fa-file-archive"></span></a>';
	return $extra_button;
};

//$intent->extra_action='store_report';
//$intent->include_in_form='intents/includes/report.php';
$app->merge_intents($intent);//crea i collegamenti, a cascata, con le altre tabelle
?>