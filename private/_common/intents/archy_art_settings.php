<?php

$intent=new stdClass();
$intent->table='archy_art_settings';//nome tabella data-base
$intent->title='Impostazione Scheda Catalogo';//titolo (plurale)
$intent->singular='Impostazione Scheda Catalogo';//titolo (singolare)
//$intent->add='Nuovo Impostazione Scheda Catalogo';//testo usato nei forms e nei pulsanti
$intent->view_title='Impostazione Scheda Catalogo';//Titolo nella visualizzazione/modifica del singolo record
$intent->joins=array();//tabelle collegate
$intent->columns=array(//colonne della tabella principale
	'archy_art_settings.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''//nasconde il campo nel modulo di creazione/aggiornamento di un record
	),
	
	'archy_art_settings.table_name' => array(
            'label'=>'Tabella',
            'type'=>'text',
                    //'formatter'=>function($d,$row){return 'Tabella Scheda Catalogo';},
            //'formatter_hide_in_form'=> true,
            'hidden'=>''
        ),
	'archy_art_settings.table_filter' => array(
            'label'=>'Campo filtro',
            'type'=>'text',
                    //'formatter'=>function($d,$row){return 'Tabella Scheda Catalogo';},
            //'formatter_hide_in_form'=> true,
            'hidden'=>''
        ),
        'archy_art_settings.table_filter_value' => array(
            'label'=>'Valore impostazione tabella',
            'type'=>'text',
            'formatter'=>function($d,$row){
                    switch($d){
                            case '1': $response='Opera'; break;
                            case '2': $response='Documento'; break;
                            case '3': $response='Fonte bibliografica'; break;
                    }
                    return $response;
                    },
            'formatter_hide_in_form'=> true,
            'hidden'=>''
        ),
	'archy_art_settings.table_fields' => array(
            'label'=>'Disattavizione campi della scheda catalogo<p style="font-size: 16px; margin-top:8px; font-weight:300">I campi selezionati verranno disabilitati dalla scheda catalogo</p>',
            'type'=>'multiple_checkbox',
            'wrapper_column' => 'col-12',
            'options_type'=>'array',
            'options' => Placeholder::getPlaceholders('archy_art_catalog'),
            'formatter' => function ($d, $row) {            
               return $d;
            }
        ),	
	'archy_art_settings.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_art_settings.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);
$intent->undeletable_ids=range(1,4);    
//$intent->include_additional_fields=true;    
$intent->default_columns=array(//colonne del db da visualizzare di default nell'elenco (griglia)
	'archy_art_settings.id', 
        'archy_art_settings.table_name', 
        'archy_art_settings.table_filter',
        'archy_art_settings.table_filter_value'
);
//$intent->include_in_form='intents/includes/art_settings.php';
$app->merge_intents($intent);//crea i collegamenti, a cascata, con le altre tabelle
?>