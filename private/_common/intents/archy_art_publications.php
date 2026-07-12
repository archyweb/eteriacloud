<?php

$intent=new stdClass();
$intent->table='archy_art_publications';//nome tabella data-base
$intent->title='Pubblicazione';//titolo (plurale)
$intent->singular='Pubblicazione';//titolo (singolare)
$intent->add='Nuova Pubblicazione';//testo usato nei forms e nei pulsanti
$intent->view_title='Pubblicazione: archy_art_publications.titolo_pubblicazione';//Titolo nella visualizzazione/modifica del singolo record
$intent->joins=array();//tabelle collegate
$intent->columns=array(//colonne della tabella principale
	'archy_art_publications.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''//nasconde il campo nel modulo di creazione/aggiornamento di un record
	),
	'archy_art_publications.titolo_pubblicazione'=>array(
		'label'=>'Titolo',
		'type'=>'text',
		'required'=>true,
		'hidden'=>''
	),
	'archy_art_publications.data_pubblicazione'=>array(
		'label'=>'Data',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_art_publications.anno_pubblicazione'=>array(
		'label'=>'Anno pubblicazione',
		'type'=>'text',
		//'required'=>true,
		'hidden'=>''
	),
	'archy_art_publications.editore_pubblicazione'=>array(
		'label'=>'Editore',
		'type'=>'text',
		//'required'=>true,
		'hidden'=>''
	),
	'archy_art_publications.descrizione_pubblicazione'=>array(
		'label'=>'Descrizione',
		'type'=>'plain_editor',
            'editor_height'=>'400',
		'hidden'=>''
	),
    'archy_art_publications.foto_pubblicazione' => array(
        'label'=>'Foto',
        'type'=>'file',
        'formatter' => function ($d, $row) {
            return App::format_grid_img($d);
        },
        'excel_formatter'=>function($d,$row){
            $return=App::protocol()."://".$_SERVER['HTTP_HOST'].$d;
			return $d != '' ? $return : '';
        },
        'formatter_hide_in_form' => true,
        'file_control' => 'image/png, image/jpeg, image/gif',
        'file_target' => 'cloudImages',
        'hidden'=>''
    ),
    'archy_art_publications.foto_copertina_pubblicazione' => array(
        'label'=>'Foto copertina',
        'type'=>'file',
        'formatter' => function ($d, $row) {
            return App::format_grid_img($d);
        },
        'excel_formatter'=>function($d,$row){
            $return=App::protocol()."://".$_SERVER['HTTP_HOST'].$d;
			return $d != '' ? $return : '';
        },
        'formatter_hide_in_form' => true,
        'file_control' => 'image/png, image/jpeg, image/gif',
        'file_target' => 'cloudImages',
        //'formatter_hide_in_excel' => true,
        'hidden'=>''
    ),
	'archy_art_publications.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_art_publications.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);
$intent->default_columns=array(//colonne del db da visualizzare di default nell'elenco (griglia)
	'archy_art_publications.id','archy_art_publications.titolo_pubblicazione'//inizia sempre con l'id di modo che i nuovi record siano visualizzati in cima alla tabella
);

$intent->include_in_form='intents/includes/art_publications.php';

$app->merge_intents($intent);//crea i collegamenti, a cascata, con le altre tabelle
?>