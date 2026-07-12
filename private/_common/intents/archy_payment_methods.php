<?php

$intent=new stdClass();
$intent->table='archy_payment_methods';//nome tabella data-base
$intent->title='Modalità di pagamento';//titolo (plurale)
$intent->singular='Modalità di pagamento';//titolo (singolare)
$intent->add='Nuova Modalità di pagamento';//testo usato nei forms e nei pulsanti
$intent->view_title='Modalità di pagamento: archy_payment_methods.method';//Titolo nella visualizzazione/modifica del singolo record
$intent->joins=array();//tabelle collegate
$intent->columns=array(//colonne della tabella principale
	'archy_payment_methods.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''//nasconde il campo nel modulo di creazione/aggiornamento di un record
	),
    /*
	'archy_payment_methods.payment_code'=>array(
		'label'=>'Codice',
		'type'=>'text',
        'attributes'=>['maxlength="4"'],
		'required'=>true
	),
    */
    'archy_payment_methods.payment_code'=>array(
		'label'=>'Codice',
		'type'=>'select', 
		'default_value'=>'',
		'options_type'=>'array',
		'options'=>[
            ''=>'--- Non gestito ---',
            'MP01'=>'MP01 - Contanti',
            'MP02'=>'MP02 - Assegno',
            'MP03'=>'MP03 - Assegno circolare',
            'MP04'=>'MP04 - Contanti presso Tesoreria',
            'MP05'=>'MP05 - Bonifico',
            'MP06'=>'MP06 - Vaglia cambiario',
            'MP07'=>'MP07 - Bollettino bancario',
            'MP08'=>'MP08 - Carta di pagamento',
            'MP09'=>'MP09 - RID',
            'MP10'=>'MP10 - RID utenze',
            'MP11'=>'MP11 - RID veloce',
            'MP12'=>'MP12 - RIBA',
            'MP13'=>'MP13 - MAV',
            'MP14'=>'MP14 - Quietanza erario',
            'MP15'=>'MP15 - Giroconto su conti di contabilità speciale',
            'MP16'=>'MP16 - Domiciliazione bancaria',
            'MP17'=>'MP17 - Domiciliazione postale',
            'MP18'=>'MP18 - Bollettino di c/c postale',
            'MP19'=>'MP19 - SEPA Direct Debit',
            'MP20'=>'MP20 - SEPA Direct Debit CORE',
            'MP21'=>'MP21 - SEPA Direct Debit B2B',
            'MP22'=>'MP22 - Trattenuta su somme già riscosse',
        ],'formatter'=>function($d,$row){
			$options=[
                ''=>'- Non gestito -',
                'MP01'=>'MP01 - Contanti',
                'MP02'=>'MP02 - Assegno',
                'MP03'=>'MP03 - Assegno circolare',
                'MP04'=>'MP04 - Contanti presso Tesoreria',
                'MP05'=>'MP05 - Bonifico',
                'MP06'=>'MP06 - Vaglia cambiario',
                'MP07'=>'MP07 - Bollettino bancario',
                'MP08'=>'MP08 - Carta di pagamento',
                'MP09'=>'MP09 - RID',
                'MP10'=>'MP10 - RID utenze',
                'MP11'=>'MP11 - RID veloce',
                'MP12'=>'MP12 - RIBA',
                'MP13'=>'MP13 - MAV',
                'MP14'=>'MP14 - Quietanza erario',
                'MP15'=>'MP15 - Giroconto su conti di contabilità speciale',
                'MP16'=>'MP16 - Domiciliazione bancaria',
                'MP17'=>'MP17 - Domiciliazione postale',
                'MP18'=>'MP18 - Bollettino di c/c postale',
                'MP19'=>'MP19 - SEPA Direct Debit',
                'MP20'=>'MP20 - SEPA Direct Debit CORE',
                'MP21'=>'MP21 - SEPA Direct Debit B2B',
                'MP22'=>'MP22 - Trattenuta su somme già riscosse',
                ];
			return $options[$d];
		},
		'formatter_hide_in_form'=>true,
        'required'=>true,
	),

	'archy_payment_methods.method'=>array(
		'label'=>'Modalità di pagamento',
		'type'=>'text',
		'required'=>true
	),
	'archy_payment_methods.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_payment_methods.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);
$intent->default_columns=array( /* colonne del db da visualizzare di default nell'elenco (griglia) */
	'archy_payment_methods.id', /* inizia sempre con l'id di modo che i nuovi record siano visualizzati in cima alla tabella */
    'archy_payment_methods.payment_code',
    'archy_payment_methods.method'
);

$app->merge_intents($intent);//crea i collegamenti, a cascata, con le altre tabelle
