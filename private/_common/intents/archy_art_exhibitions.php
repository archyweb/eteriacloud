<?php

$intent=new stdClass();
$intent->table='archy_art_exhibitions';//nome tabella data-base
$intent->title='Esposizione';//titolo (plurale)
$intent->singular='Esposizione';//titolo (singolare)
$intent->add='Nuova Esposizione';//testo usato nei forms e nei pulsanti
$intent->view_title='Esposizione: archy_art_exhibitions.nome_esposizione';//Titolo nella visualizzazione/modifica del singolo record
$intent->joins=array();//tabelle collegate
$intent->columns=array(//colonne della tabella principale
	'archy_art_exhibitions.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''//nasconde il campo nel modulo di creazione/aggiornamento di un record
	),
	'archy_art_exhibitions.nome_esposizione'=>array(
		'label'=>'Nome Esposizione',
		'type'=>'text',
		'required'=>true,
		'hidden'=>''
	),
	'archy_art_exhibitions.curatore_esposizione'=>array(
		'label'=>'Curatore',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_art_exhibitions.data_esposizione'=>array(
		'label'=>'Data Esposizione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_art_exhibitions.data_fine_esposizione'=>array(
		'label'=>'Data fine Esposizione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_art_exhibitions.anno_esposizione'=>array(
		'label'=>'Anno esposizione',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_art_exhibitions.label_esposizione'=>array(
		'label'=>'Label esposizione',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_art_exhibitions.luogo_esposizione'=>array(
		'label'=>'Luogo Esposizione',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_art_exhibitions.descrizione_esposizione'=>array(
		'label'=>'Descrizione Esposizione',
		'type'=>'plain_editor',
                'editor_height'=>'400',
		'hidden'=>''
	),
    'archy_art_exhibitions.foto_esposizione' => array(
        'label'=>'Foto esposizione',
        'type'=>'file',
        'formatter' => function ($d, $row) {
            return App::format_grid_img($d);
        },
        'excel_formatter'=>function($d,$row){
            $return=App::protocol()."://".$_SERVER['HTTP_HOST'].$d;
			return $d != '' ? $return : '';
        },
        'formatter_hide_in_form' => true,
        'file_control' => 'image/png, image/jpeg, image/gif, image/webp',
        'file_target' => 'cloudImages',
        'hidden'=>''
    ),
	'archy_art_exhibitions.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_art_exhibitions.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);
$intent->default_columns=array(//colonne del db da visualizzare di default nell'elenco (griglia)
	'archy_art_exhibitions.id','archy_art_exhibitions.nome_esposizione'//inizia sempre con l'id di modo che i nuovi record siano visualizzati in cima alla tabella
);

$intent->include_in_form='intents/includes/art_exhibitions.php';

$app->merge_intents($intent);//crea i collegamenti, a cascata, con le altre tabelle
?>