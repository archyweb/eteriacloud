<?php

$intent=new stdClass();
$intent->table='archy_registry_categories';//nome tabella data-base
$intent->title='Categorie';//titolo (plurale)
$intent->singular='Categoria';//titolo (singolare)
$intent->add='Nuova Categoria';//testo usato nei forms e nei pulsanti
$intent->view_title='Categoria archy_registry_categories.category';//Titolo nella visualizzazione/modifica del singolo record
$intent->joins=array();//tabelle collegate
$domain_options=['memberships'=>'Soci','customers'=>'Clienti','suppliers'=>'Fornitori','miscellaneous'=>'Anagrafiche generiche'];
if($app->config['association']==false):
	unset($domain_options['memberships']);
endif;
$intent->columns=array(//colonne della tabella principale
	'archy_registry_categories.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''//nasconde il campo nel modulo di creazione/aggiornamento di un record
	),
	'archy_registry_categories.domain'=>array(
		'label'=>'Anagrafiche',
		'type'=>'select', 
		'options_type'=>'array',
		'required'=>true,
		'options'=>$domain_options,
		'formatter'=>function($d,$row){
			global $domain_options;
			return @$domain_options[$d];
		},
		'formatter_hide_in_form'=>''
	),
	'archy_registry_categories.category'=>array(
		'label'=>'Categoria',
		'type'=>'text',
		'required'=>true
	),
	'archy_registry_categories.created_at'=>array(
		'label'=>'Data Creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_registry_categories.updated_at'=>array(
		'label'=>'Ultimo aggiornamento',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
);
$intent->default_columns=array(//colonne del db da visualizzare di default nell'elenco (griglia)
	'archy_registry_categories.id',
	'archy_registry_categories.domain',
	'archy_registry_categories.category'
);

if($app->config['association']):
	$intent->columns['archy_registry_categories.subscription_fee']=[
		'label'=>'Importo quota associativa',
		'type'=>'amount',
		'formatter'=>function($d,$row){return App::currency($d);},
		'hidden'=>''
	];
    $intent->default_columns[]='archy_registry_categories.subscription_fee';
    if($app->config['association_splitting']):
        $intent->columns['archy_registry_categories.splitting']=[
            'label'=>'Frazionamento prima quota associativa',
            'type'=>'checkbox',
            'formatter'=>function($d,$row){return App::checkbox($d);},
            'formatter_hide_in_form'=>'',
            'hidden'=>''
        ];
        $intent->columns['archy_registry_categories.splitting_months']=[
            'label'=>'Frazionamento mese/importo',
            'type'=>'text',
            'hidden'=>''
        ];
        $intent->default_columns[]='archy_registry_categories.splitting';
        $intent->exclude_columns[]='archy_registry_categories.splitting_months';
    endif;
	
endif;

$intent->extra_action='registry_categories';
$intent->include_in_form='intents/includes/registry_categories_members_options.php';

$app->merge_intents($intent);//crea i collegamenti, a cascata, con le altre tabelle
?>