<?php

$intent=new stdClass();
$intent->table='archy_services_subscriptions';//nome tabella data-base
$intent->title='Quote servizi';//titolo (plurale)
$intent->singular='Quote Servizio';//titolo (singolare)
$intent->add='Nuova quota Servizio';//testo usato nei forms e nei pulsanti
$intent->view_title='Quota Servizio';//Titolo nella visualizzazione/modifica del singolo record
$intent->joins=[
    'archy_firms'=>'archy_services_subscriptions.firm_id=archy_firms.id',
    'archy_services_memberships'=>'archy_services_subscriptions.services_memberships_id=archy_services_memberships.id',
    'archy_payment_methods'=>'archy_services_subscriptions.payment_method_id=archy_payment_methods.id',
    'archy_banks'=>'archy_services_subscriptions.bank_id=archy_banks.id',
    'archy_petty_cash_types'=>'archy_services_subscriptions.petty_cash_type_id=archy_petty_cash_types.id',
];//tabelle collegate
$intent->joins_query=" from archy_services_subscriptions 
    left join archy_firms on (archy_services_subscriptions.firm_id=archy_firms.id)  
    left join archy_services_memberships on (archy_services_subscriptions.services_memberships_id=archy_services_memberships.id)  
    left join archy_payment_methods on (archy_services_subscriptions.payment_method_id=archy_payment_methods.id)  
    left join archy_banks on (archy_services_subscriptions.bank_id=archy_banks.id)  
    left join archy_petty_cash_types on (archy_services_subscriptions.petty_cash_type_id=archy_petty_cash_types.id)  
    left join archy_cities on (archy_firms.city_id=archy_cities.id)  
    left join archy_services on (archy_services_memberships.services_id=archy_services.id)  
    left join archy_tax_rates on (archy_services.tax_rate_id=archy_tax_rates.id)  
    left join archy_countries on (archy_cities.country_id=archy_countries.id)  
    left join archy_registry on (archy_services_memberships.registry_id=archy_registry.id)  
    left join archy_cost_centres on (archy_services.cost_centre_id=archy_cost_centres.id)  
    left join archy_cost_centres_categories on (archy_cost_centres.category_id=archy_cost_centres_categories.id)  
    left join archy_cost_matrice on (archy_cost_centres_categories.cost_matrice_id=archy_cost_matrice.id)  
    left join archy_registry_categories as Cliente on (archy_registry.customer_category_id=Cliente.id)  
    left join archy_registry_categories as Fornitore on (archy_registry.supplier_category_id=Fornitore.id)
	left join archy_registry_categories as Generica on (archy_registry.category_id=Generica.id)
    left join archy_cities as Anagrafica on (archy_registry.city_id=Anagrafica.id)  
    left join archy_cities as Anagraficapubblica on (archy_registry.member_public_city_id=Anagraficapubblica.id)  
    left join archy_countries as archy_countries__Anagrafica on (Anagrafica.country_id=archy_countries__Anagrafica.id)  
    left join archy_countries as archy_countries__Anagraficapubblica on (Anagraficapubblica.country_id=archy_countries__Anagraficapubblica.id) 
    ";

$service_membership=new ServiceMembership();
$status_options=$service_membership->statuses;

$intent->columns=array(//colonne della tabella principale
	'archy_services_subscriptions.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''//nasconde il campo nel modulo di creazione/aggiornamento di un record
    ),
    'archy_services_subscriptions.date'=>array(
		'label'=>'Data versamento',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'required'=>true,
		'default_value'=>date('d/m/Y')
	),
	'archy_services_subscriptions.services_memberships_id'=>array(
        'label'=>'Abbonamento al servizio',
		'type'=>'search',
        'intent'=>'archy_services_memberships',
        'intent_joins'=>['archy_registry'=>'archy_services_memberships.registry_id=archy_registry.id'],
		'lookup_text'=>'archy_registry.label',
        'required'=>true
    ),
    'archy_services_subscriptions.amount'=>array(
		'label'=>'Importo',
		'type'=>'amount',
		'formatter'=>function($d,$row){return App::currency($d);},
		'formatter_hide_in_form'=>'',
		'required'=>true
    ),
    'archy_services_subscriptions.firm_id'=>array(
		'label'=>'Registro sezionale',
		'type'=>'search',
		'intent'=>'archy_firms',
		'lookup_text'=>'archy_firms.firm_name',
		'default_value'=>$app->default_firm_id,
		'required'=>true,
    ),
	'archy_services_subscriptions.payment_method_id'=>array(
		'label'=>'Modalità di pagamento',
		'type'=>'search',
		'intent'=>'archy_payment_methods',
		'lookup_text'=>'archy_payment_methods.method',
		'required'=>true,
	),
	'archy_services_subscriptions.bank_id'=>array(
		'label'=>'Banca',
		'type'=>'search',
		'intent'=>'archy_banks',
		'lookup_text'=>'archy_banks.nome',
	),    
	'archy_services_subscriptions.petty_cash_type_id'=>array(
		'label'=>'Libro prima nota',
		'type'=>'search',
		'intent'=>'archy_petty_cash_types',
		'lookup_text'=>'archy_petty_cash_types.type',
		'required'=>true
    ),
    'archy_services_subscriptions.receipt_id'=>array(
		'label'=>'Ricevuta collegata',
		'type'=>'text',
		'hidden'=>''
	),
    'archy_services_subscriptions.invoice_id'=>array(
		'label'=>'Fattura collegata',
		'type'=>'text',
		'hidden'=>''
	),
    'archy_services_subscriptions.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_services_subscriptions.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);
$intent->default_columns=array(
	'archy_services_subscriptions.id',
	'archy_services_subscriptions.date',
	'archy_services_subscriptions.amount',
);

$intent->exclude_columns=array(//colonne del db da escludere nell'elenco di "selezione colonne"
    'archy_services.certificates_model',
    'archy_services.certificates_background_image',
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

$intent->include_in_form='intents/includes/services_subscriptions.php';
$intent->extra_action='store_services_subscriptions';
//$intent->before_update='services_subscriptions_before_update';
$app->merge_intents($intent);//crea i collegamenti, a cascata, con le altre tabelle
