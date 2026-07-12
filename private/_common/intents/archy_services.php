<?php

$intent=new stdClass();
$intent->table='archy_services';//nome tabella data-base
$intent->title='Servizi';//titolo (plurale)
$intent->singular='Servizio';//titolo (singolare)
$intent->add='Nuovo Servizio';//testo usato nei forms e nei pulsanti
$intent->view_title='Servizio: archy_services.description';//Titolo nella visualizzazione/modifica del singolo record
$intent->joins=[
    'archy_tax_rates'=>'archy_services.tax_rate_id=archy_tax_rates.id',
    'archy_cost_centres'=>'archy_services.cost_centre_id=archy_cost_centres.id',
];//tabelle collegate
$intent->columns=array(//colonne della tabella principale
	'archy_services.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''//nasconde il campo nel modulo di creazione/aggiornamento di un record
	),
	'archy_services.description'=>array(
		'label'=>'Servizio',
		'type'=>'text',
        'grid_search_type'=>'search',
        'required'=>true,
    ),
    'archy_services.amount'=>array(
		'label'=>'Imponibile',
		'type'=>'amount',
		'formatter'=>function($d,$row){return App::currency($d);},
		'formatter_hide_in_form'=>'',
		'required'=>true,
    ),
    'archy_services.accounting_type'=>array(
		'label'=>'Tiplogia servizio',
		'type'=>'select', 
		'default_value'=>'1',
		'options_type'=>'array',
		'options'=>['1'=>'Commerciale (emette fattura)','0'=>'Istituzionale (emette ricevuta)'],
		'formatter'=>function($d,$row){
			$options=['1'=>'Commerciale (emette fattura)','0'=>'Istituzionale (emette ricevuta)'];
			return $options[$d];
		},
        'formatter_hide_in_form'=>true,
        'required'=>true,
    ),
    'archy_services.tax_rate_id'=>array(
		'label'=>'Aliquota IVA',
		'type'=>'search',
		'intent'=>'archy_tax_rates',
        'lookup_text'=>'archy_tax_rates.name',
	),
    'archy_services.cost_centre_id'=>array(
		'label'=>'Voce di costo',
		'type'=>'search',
		'intent'=>'archy_cost_centres',
        'lookup_text'=>'cost_centre',
        'required'=>true,
    ),
    'archy_services.accounting_reason'=>array(
		'label'=>'Causale ricevuta/fattura',
        'type'=>'text',
		'required'=>true,
    ),
	'archy_services.renew_type'=>array(
		'label'=>'Modalità rinnovo',
		'type'=>'select', 
		'default_value'=>'1',
		'options_type'=>'array',
		'options'=>['1'=>'Una tantum','0'=>'365 giorni','2'=>'Annuale (Data specifica)','3'=>'Mensile (Data specifica)'],
		'formatter'=>function($d,$row){
			$options=['1'=>'Una tantum','0'=>'365 giorni','2'=>'Annuale (Data specifica)','3'=>'Mensile (Data specifica)'];
			return $options[$d];
        },
        'formatter_hide_in_form'=>true,
        'required'=>true,
	),
    'archy_services.expire_date'=>array(
		'label'=>'Data scadenza',
		'type'=>'date',
        'formatter'=>function($d,$row){return App::latin_date_time($d);},
    ),
    'archy_services.splitting'=>array(
		'label'=>'Frazionamento imponibile del servizio',
		'type'=>'checkbox',
		'formatter'=>function($d,$row){return App::checkbox($d);},
		'formatter_hide_in_form'=>''
    ),
    'archy_services.splitting_months'=>array(
		'label'=>'Frazionamento mese/importo',
		'type'=>'text',
		'hidden'=>''
	),
    'archy_services.module_model'=>array(
        'label'=>'Modello extra',
		'type'=>'plain_editor',
		'editor_height'=>'400',
		'placeholder'=>true,
                'placeholder_origin' => 'archy_services',
		'hidden'=>''
	),
    'archy_services.certificates_width'=>array(
		'label'=>'Larghezza (mm)',
		'type'=>'text',
        'extra_css_class'=>'integers_only',
		'hidden'=>''
	),
    'archy_services.certificates_height'=>array(
		'label'=>'Altezza (mm)',
		'type'=>'text',
        'extra_css_class'=>'integers_only',
		'hidden'=>''
	),
    'archy_services.certificates_model'=>array(
		'label'=>'Modello attestato',
		'type'=>'plain_editor',
		'editor_height'=>'400',
		'placeholder'=>true,
                'placeholder_origin' => 'archy_services',
		'hidden'=>''
	),
    'archy_services.certificates_background_image'=>array(
		'label'=>'Layout grafico di sfondo',
		'type'=>'file',
        'formatter' => function ($d, $row) {
            $return='<a href="'.$d.'" download><img src="'.$d.'" alt="Layout" class="tt" title="Download" style="max-height:44px"></a>';
            return $d != '' ? $return : '';
        },
        'excel_formatter'=>function($d,$row){
            $return=App::protocol()."://".$_SERVER['HTTP_HOST'].$d;
			return $d != '' ? $return : '';
        },
		'formatter_hide_in_form'=>true,
		'formatter_hide_in_excel'=>true,
		'hidden'=>''
	),
    'archy_services.service_mail_subject'=>array(
		'label'=>'Oggetto',
		'type'=>'text',
		'hidden'=>''
	),
    'archy_services.service_mail_body'=>array(
		'type'=>'plain_editor',
		'editor_height'=>'400',
		'placeholder'=>true,
                'placeholder_origin' => 'archy_services',
		'hidden'=>''
	),
	'archy_services.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_services.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);

$intent->default_columns=array(//colonne del db da visualizzare di default nell'elenco (griglia)
    'archy_services.id',
    'archy_services.description',
    'archy_services.amount',
    'archy_services.accounting_type',
    'archy_tax_rates.name',
    'archy_cost_centres.cost_centre',
    'archy_services.accounting_reason',
    'archy_services.renew_type',
    'archy_services.expire_date',
    //inizia sempre con l'id di modo che i nuovi record siano visualizzati in cima alla tabella
);

$intent->exclude_columns=array(//colonne del db da escludere nell'elenco di "selezione colonne"
    'archy_services.certificates_model',
    'archy_services.certificates_background_image',
    'archy_services.module_model',
    'archy_services.first_expiring_subject',
    'archy_services.first_expiring_model',
    'archy_services.first_expiring_SMS',
    'archy_services.first_expiring_days',
    'archy_services.first_expiring_days_delay',
    'archy_services.second_expiring_subject',
    'archy_services.second_expiring_model',
    'archy_services.second_expiring_SMS',
    'archy_services.second_expiring_days',
    'archy_services.second_expiring_days_delay',
    'archy_services.third_expiring_subject',
    'archy_services.third_expiring_model',
    'archy_services.third_expiring_SMS',
    'archy_services.third_expiring_days',
    'archy_services.third_expiring_days_delay',
    'archy_services.expired_subject',
    'archy_services.expired_model',
);

$intent->include_in_form='intents/includes/services_splitting.php';
$intent->extra_action='store_services_splitting';
$app->merge_intents($intent);//crea i collegamenti, a cascata, con le altre tabelle
