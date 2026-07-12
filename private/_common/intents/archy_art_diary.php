<?php
$intent = new stdClass();
$intent->table = 'archy_art_diary'; //nome tabella data-base
$intent->title = 'Attività'; //titolo (plurale)
$intent->singular = 'Attività'; //titolo (singolare)
$intent->add = 'Nuova Attività'; //testo usato nei forms e nei pulsanti
$intent->view_title = 'Attività: archy_art_diary.art_diary_title';
$intent->joins = array(
    'archy_art_diary_category'=>'archy_art_diary.art_diary_category=archy_art_diary_category.id',
);
$intent->columns = array(
    'archy_art_diary.id' => array(
        'label'=>'ID',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_art_diary.art_diary_title' => array(
        'label'=>'Titolo attività',
        'type'=>'text',
        'hidden'=>''
    ),
	
	'archy_art_diary.art_diary_place' => array(
        'label'=>'Luogo',
        'type'=>'text',
        'hidden'=>''
    ),
	
    'archy_art_diary.art_diary_participants' => array(
        'label'=>'Collaboratori',
        'type'=>'text',
        'hidden'=>''
    ),
	
    'archy_art_diary.art_diary_description' => array(
        'label'=>'Descrizione',
        'type'=>'plain_editor',
        'hidden'=>''
    ),
   
    /*   'archy_art_diary.art_diary_tag' => array(
        'label'=>'Tag',
        'type'=>'plain_editor',
        'hidden'=>''
    ),*/
   
   
	'archy_art_diary.art_diary_category' => array(
        'label'=>'Categoria',
        'type'=>'select',
        'options_type'=>'sql',
        'options'=>'select ID as option_value, category_diary as option_text from archy_art_diary_category order by option_text',
        'formatter' => function ($d, $row) {
            //return App::format_serie($d, $row);
        },
        'formatter_hide_in_form' => true,
        'formatter_hide_in_view' => true,
        'hidden'=>''
    ),
    
    'archy_art_diary.art_diary_attachments' => array(
        'label'=>'Allegati',
        'type'=>'bulk_upload',
        'formatter' => function ($d, $row) {
            if ($d != '') :
                $docs = unserialize($d);
                $return = '<ul>';
                foreach ($docs as $path) :
                    $tmp = explode('/', $path);
                    $return .= '<li><a href="' . $path . '" download>' . end($tmp) . '</a></li>';
                endforeach;
                $return .= '</ul>';
                return $return;
            endif;
        },
        'formatter_hide_in_form' => true,
        'formatter_hide_in_excel' => true,
        'hidden'=>''
    ),
	
	
    'archy_art_diary.art_diary_date' => array(
        'label'=>'Data',
        'type'=>'date',
        //'default_value' => @$defaults->data_nascita_autore,
        'formatter' => function ($d, $row) {
            return App::latin_date($d);
        },
        'hidden'=>''
    ),
	
	'archy_art_diary.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	
	
	'archy_art_diary.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
);

$intent->default_columns=array(//colonne del db da visualizzare di default nell'elenco (griglia)
	'archy_art_diary.id','archy_art_diary.art_diary_title'//inizia sempre con l'id di modo che i nuovi record siano visualizzati in cima alla tabella
);
$intent->include_in_form = 'intents/includes/art_diary.php';
$app->merge_intents($intent);//crea i collegamenti, a cascata, con le altre tabelle
?>