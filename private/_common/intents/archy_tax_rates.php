<?php

$intent=new stdClass();
$intent->table='archy_tax_rates';//nome tabella data-base
$intent->title='Aliquote IVA';//titolo (plurale)
$intent->singular='Aliquota IVA';//titolo (singolare)
$intent->add='Nuova Aliquota';//testo usato nei forms e nei pulsanti
$intent->view_title='archy_tax_rates.name';//Titolo nella visualizzazione/modifica del singolo record
$intent->joins=array();//tabelle collegate
$intent->columns=array(//colonne della tabella principale
	'archy_tax_rates.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''//nasconde il campo nel modulo di creazione/aggiornamento di un record
	),
	'archy_tax_rates.name'=>array(
		'label'=>'Aliquota IVA',
		'type'=>'text',
                'required'=>true,
                'help_block'=>'Inserire un nome per l\'aliquota IVA'
	),
	'archy_tax_rates.percentage'=>array(
		'label'=>'Aliquota',
		'type'=>'text',
		'formatter'=>function($d,$row){return number_format((float)$d,0,',','.').'%';},
		'formatter_hide_in_form'=>'',
		'required'=>true,
        'extra_css_class'=>'numbers_only',
        'help_block'=>'Inserire una percentuale per l\'aliquota IVA',
	),
	'archy_tax_rates.vat_exemption'=>array(
		'label'=>'Natura esenzione (richiesto per aliquota 0%)',
		'type'=>'select', 
		'default_value'=>'',
		'options_type'=>'array',
        'options'=>[''=>'Aliquota diversa da zero',
            'N1'=>'N1',
            'N2.1'=>'N2.1',
            'N2.2'=>'N2.2',
            'N3.1'=>'N3.1',
            'N3.2'=>'N3.2',
            'N3.3'=>'N3.3',
            'N3.4'=>'N3.4',
            'N3.5'=>'N3.5',
            'N3.6'=>'N3.6',
            'N4'=>'N4',
            'N5'=>'N5',
            'N6.1'=>'N6.1',
            'N6.2'=>'N6.2',
            'N6.3'=>'N6.3',
            'N6.4'=>'N6.4',
            'N6.5'=>'N6.5',
            'N6.6'=>'N6.6',
            'N6.7'=>'N6.7',
            'N6.8'=>'N6.8',
            'N6.9'=>'N6.9',
            'N7'=>'N7'
        ],
		'formatter'=>function($d,$row){
            $options=[''=>'Aliquota diversa da zero',
                'N1'=>'N1',
                'N2.1'=>'N2.1',
                'N2.2'=>'N2.2',
                'N3.1'=>'N3.1',
                'N3.2'=>'N3.2',
                'N3.3'=>'N3.3',
                'N3.4'=>'N3.4',
                'N3.5'=>'N3.5',
                'N3.6'=>'N3.6',
                'N4'=>'N4',
                'N5'=>'N5',
                'N6.1'=>'N6.1',
                'N6.2'=>'N6.2',
                'N6.3'=>'N6.3',
                'N6.4'=>'N6.4',
                'N6.5'=>'N6.5',
                'N6.6'=>'N6.6',
                'N6.7'=>'N6.7',
                'N6.8'=>'N6.8',
                'N6.9'=>'N6.9',
                'N7'=>'N7'    
            ];
			return $options[$d];
		},
		'formatter_hide_in_form'=>true
	),
	'archy_tax_rates.created_at'=>array(
		'label'=>'Creato il',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_tax_rates.updated_at'=>array(
		'label'=>'Aggiornato il',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
);

$all_ids=array_column(mysqli_fetch_all($app->db->query(
    "SELECT id 
       FROM archy_tax_rates 
      WHERE readonly=1;
    "),MYSQLI_ASSOC),'id');
$intent->uneditable_ids=$all_ids;
//$intent->undeletable_ids=$all_ids;

$intent->default_columns=array(//colonne del db da visualizzare di default nell'elenco (griglia)
	'archy_tax_rates.id','archy_tax_rates.name','archy_tax_rates.percentage'//inizia sempre con l'id di modo che i nuovi record siano visualizzati in cima alla tabella
);

$app->merge_intents($intent);//crea i collegamenti, a cascata, con le altre tabelle
?>