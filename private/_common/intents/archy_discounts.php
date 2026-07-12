<?php

$intent=new stdClass();
$intent->table='archy_discounts';//nome tabella data-base
$intent->title='Sconti';//titolo (plurale)
$intent->singular='Sconto';//titolo (singolare)
$intent->add='Nuovo sconto';//testo usato nei forms e nei pulsanti
$intent->view_title='Sconto: archy_discounts.title';//Titolo nella visualizzazione/modifica del singolo record
$intent->joins=array();//tabelle collegate
$intent->columns=array(//colonne della tabella principale
	'archy_discounts.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''//nasconde il campo nel modulo di creazione/aggiornamento di un record
	),
	'archy_discounts.title'=>array(
		'label'=>'Nome sconto',
		'type'=>'text',
        'attributes'=>['maxlength="20"'],
		'required'=>true,
		'grid_search_type'=>'search',
	),
	'archy_discounts.description'=>array(
		'label'=>'Descrizione sconto',
		'type'=>'text',
	),
    'archy_discounts.type'=>array(
		'label'=>'Tipo sconto',
		'type'=>'select',
		'options_type'=>'array',
		'options'=>array(
            'fixed'=>'Fisso',
            'percentage'=>'Percentuale',
        ),
        'formatter'=>function($d,$row){
			$options=array(
                'fixed'=>'Fisso',
                'percentage'=>'Percentuale'
            );
			return $options[$d];
		},
        'formatter_hide_in_form'=>'',
        'required'=>true,
	),
    'archy_discounts.amount'=>array(
		'label'=>'Importo',
		'type'=>'text',
		//'formatter'=>function($d,$row){return App::currency($d);},
		//'formatter_hide_in_form'=>'',
		'required'=>true
	),
 	'archy_discounts.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_discounts.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);

$intent->default_columns=array(//colonne del db da visualizzare di default nell'elenco (griglia)
	'archy_discounts.id',//inizia sempre con l'id di modo che i nuovi record siano visualizzati in cima alla tabella
    'archy_discounts.title',
    'archy_discounts.type',
    'archy_discounts.amount',
);
// $intent->include_in_form='intents/includes/certificati_model.php';
$app->merge_intents($intent);//crea i collegamenti, a cascata, con le altre tabelle
?>