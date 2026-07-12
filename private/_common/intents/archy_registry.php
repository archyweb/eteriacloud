<?php

$intent=new stdClass();
$intent->table='archy_registry';
$intent->title='Anagrafiche';
$intent->singular='Anagrafica';
$intent->add='Nuova Anagrafica';
$intent->view_title='Anagrafica archy_registry.label';
$intent->joins=[   
    'archy_registry_categories as Cliente'=>'archy_registry.customer_category_id=Cliente.id',
	'archy_registry_categories as Fornitore'=>'archy_registry.supplier_category_id=Fornitore.id',
    'archy_registry_categories as Generica'=>'archy_registry.category_id=Generica.id',
    'archy_cities as Anagrafica'=>'archy_registry.city_id=Anagrafica.id',
    'archy_cities as Anagraficapubblica'=>'archy_registry.member_public_city_id=Anagraficapubblica.id',
];
$intent->columns=array(
	'archy_registry.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_registry.type'=>array(
		'label'=>'Tipologia',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_registry.legal_personality'=>array(
		'label'=>'Personalità giuridica',
		'type'=>'select', 
		'default_value'=>'0',
		'options_type'=>'array',
		'options'=>['1'=>'Persona giuridica','0'=>'Persona fisica','2'=>'ONLUS e assimilabili','3'=>'Nominativo'],
		'formatter'=>function($d,$row){
			$options=['1'=>'Persona giuridica','0'=>'Persona fisica','2'=>'ONLUS e assimilabili','3'=>'Nominativo'];
			return $options[$d];
		},
		'formatter_hide_in_form'=>true,
        'hidden'=>'',
        'attributes'=>['readonly'], // VEDERE IN registry.php : $('#archy_registry_legal_personality').attr(...);
	),
	'archy_registry.firm_name'=>array(
		'label'=>'Ragione Sociale',
		'type'=>'text',
		'grid_search_type'=>'search',
		'grid_search_column'=>'archy_registry.firm_name',
		'hidden'=>'',
		'attributes'=>['maxlength="250"'],
		'required'=>true
	),
	'archy_registry.name'=>array(
		'label'=>'Nome',
		'type'=>'text',
		'hidden'=>'',
		'attributes'=>['maxlength="250"'],
		'required'=>true
	),
	'archy_registry.surname'=>array(
		'label'=>'Cognome',
		'type'=>'text',
		'hidden'=>'',
		'attributes'=>['maxlength="250"'],
		'required'=>true
	),
	'archy_registry.label'=>array(
		'label'=>'Identificativo Anagr.',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_registry.vat_country_id'=>array(
		'label'=>'Paese IVA',
		'type'=>'search',
		'intent'=>'archy_countries',
		'lookup_text'=>'archy_countries.iso2',
		'required'=>true,
		'default_value'=>1,
		'hidden'=>''
	),
	'archy_registry.vat_id'=>array(
		'label'=>'Partita IVA',
		'type'=>'text',
		'hidden'=>'',
		'attributes'=>['maxlength="11"'],
		'extra_css_class'=>'integers_only',
		'required'=>true
	),
	'archy_registry.tax_code'=>array(
		'label'=>'Codice Fiscale',
		'attributes'=>['maxlength="16"'],
		'type'=>'text',
		'hidden'=>'',
		'required'=>true
    ),
    'archy_registry.category_id'=>array(
		'label'=>'Categoria',
		'type'=>'search',
		'intent'=>'archy_registry_categories',
		'domain'=>'miscellaneous',
		'lookup_text'=>'category',
		'hidden'=>''
    ),
	'archy_registry.contact_person'=>array(
		'label'=>'Referente',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_registry.contact_person_is_legal_representative'=>array(
		'label'=>'Referente è legale rappr.',
		'type'=>'checkbox',
		'formatter'=>function($d,$row){return App::checkbox($d);},
		'formatter_hide_in_form'=>'',
		'hidden'=>''
	),
	'archy_registry.city_id'=>array(
		'label'=>'Città',
		'type'=>'search',
		'intent'=>'archy_cities',
		'lookup_text'=>'archy_cities.city_label',
		'required'=>true,
		'hidden'=>''
	),
	'archy_registry.zip'=>array(
		'label'=>'CAP',
		'type'=>'text',
		'hidden'=>'',
		'required'=>true
	),
	'archy_registry.address'=>array(
		'label'=>'Indirizzo',
		'type'=>'textarea',
		'hidden'=>'',
		'required'=>true
	),
        'archy_registry.latitude'=>array(
		'label'=>'Latitudine',
		'type'=>'text',
		'hidden'=>''
	),
        'archy_registry.longitude'=>array(
		'label'=>'Longitudine',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_registry.telephone'=>array(
		'label'=>'Telefono',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_registry.fax'=>array(
		'label'=>'Fax',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_registry.mobile'=>array(
		'label'=>'Cellulare',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_registry.email'=>array(
		'label'=>'E-mail',
		'type'=>'text',
		'formatter'=>function($d,$row){return App::email($d);},
		'formatter_hide_in_form'=>true,
		'formatter_hide_in_excel'=>true,
		'hidden'=>'',
		'required'=>true
	),
	'archy_registry.certified_email'=>array(
		'label'=>'PEC',
		'type'=>'text',
		'formatter'=>function($d,$row){return App::email($d);},
		'formatter_hide_in_form'=>true,
		'formatter_hide_in_excel'=>true,
		'hidden'=>''
	),
	'archy_registry.website'=>array(
		'label'=>'Sito web',
		'type'=>'text',
		'formatter'=>function($d,$row){return App::website($d);},
		'formatter_hide_in_form'=>true,
		'formatter_hide_in_excel'=>true,
		'hidden'=>''
	),
	'archy_registry.birth_date'=>array(
		'label'=>'Data nascita',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'hidden'=>''
	),
	'archy_registry.birth_place'=>array(
		'label'=>'Luogo nascita',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_registry.gender'=>array(
		'label'=>'Genere',
		'type'=>'select', 
		'default_value'=>'',
		'options_type'=>'array',
		'options'=>[''=>'Non definito','1'=>'Maschile','0'=>'Femminile'],
		'formatter'=>function($d,$row){
			$options=[''=>'Non definito','1'=>'Maschile','0'=>'Femminile'];
			return $options[$d];
		},
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
    'archy_registry.main_picture'=>array(
		'label'=>'Foto',
		'type'=>'file',
        'formatter'=>function($d, $row){
            $return='<a href="' . $d . '" download><img src="' . $d . '" alt="Foto" class="tt" title="Download" style="max-height:75px"></a>';
            return $d != '' ? $return : '';
        },
        'excel_formatter'=>function($d,$row){
            $return=App::protocol()."://".$_SERVER['HTTP_HOST'].$d;
			return $d != '' ? $return : '';
        },
		'formatter_hide_in_form'=>true,
		//'formatter_hide_in_excel'=>true,
		'hidden'=>''
	),
    'archy_registry.notes'=>array(
		'label'=>'Note',
		'type'=>'editor',
		'hidden'=>''
	),
	'archy_registry.is_customer'=>array(
		'label'=>'Filtra Clienti',
		'type'=>'checkbox',
		'formatter'=>function($d,$row){return App::checkbox($d);},
		'formatter_hide_in_form'=>'',
		'hidden'=>''
	),
	'archy_registry.is_supplier'=>array(
		'label'=>'Filtra Fornitori',
		'type'=>'checkbox',
		'formatter'=>function($d,$row){return App::checkbox($d);},
		'formatter_hide_in_form'=>'',
		'hidden'=>''
	),
	'archy_registry.is_member'=>array(
		'label'=>'Filtra Soci',
		'type'=>'checkbox',
		'formatter'=>function($d,$row){return App::checkbox($d);},
		'formatter_hide_in_form'=>'',
		'hidden'=>''
	),
	'archy_registry.customer_category_id'=>array(
		'label'=>'Categoria cliente',
		'type'=>'search',
		'intent'=>'archy_registry_categories',
		'domain'=>'customers',
		'lookup_text'=>'category',
		'hidden'=>''
	),
	'archy_registry.customer_signatory'=>array(
		'label'=>'Firmatario cliente',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_registry.customer_contact_person'=>array(
		'label'=>'Referente cliente',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_registry.customer_contact_person_is_legal_representative'=>array(
		'label'=>'Referente cliente è legale rappr.',
		'type'=>'checkbox',
		'formatter'=>function($d,$row){return App::checkbox($d);},
		'formatter_hide_in_form'=>'',
		'hidden'=>''
	),
	'archy_registry.customer_sending_method'=>array(
		'label'=>'Sistema di invio',
		'type'=>'select', 
		'default_value'=>'2',
		'options_type'=>'array',
		'options'=>['1'=>'PEC','2'=>'Codice Destinatario'],
		'formatter'=>function($d,$row){
			$options=['1'=>'PEC','2'=>'Codice Destinatario'];
			return $d!=''? $options[$d] : '';
		},
		'formatter_hide_in_form'=>true,
		'hidden'=>'',
		'required'=>true
	),
	'archy_registry.customer_recipient_code'=>array(
		'label'=>'Codice Destinatario',
		'type'=>'text',
		'attributes'=>['maxlength="7"'],
		'default_value'=>'0000000',
		'hidden'=>'',
		'required'=>true
	),		
	'archy_registry.customer_sdi_pec'=>array(
		'label'=>'PEC',
		'type'=>'text',
		'formatter'=>function($d,$row){return App::email($d);},
		'formatter_hide_in_form'=>true,
		'formatter_hide_in_excel'=>true,		
		'hidden'=>'',
		'required'=>true
	),	
	'archy_registry.customer_EORI'=>array(
		'label'=>'Codice EORI',
		'type'=>'text',
		'attributes'=>['maxlength="17"'],
		'hidden'=>''
	),
	'archy_registry.customer_private'=>array(
		'label'=>'Privato o P. A.',
		'type'=>'select', 
		'default_value'=>'1',
		'options_type'=>'array',
		'options'=>['1'=>'Privato','2'=>'Pubblica Amministrazione'],
		'formatter'=>function($d,$row){
			$options=['1'=>'Privato','2'=>'Pubblica Amministrazione'];
			return $d!=''? $options[$d] : '';
		},
		'formatter_hide_in_form'=>true,
		'hidden'=>'',
		'required'=>true
	),
	'archy_registry.customer_tax_representative_firm_name'=>array(
		'label'=>'Ragione Sociale',
		'type'=>'text',
		'grid_search_type'=>'search',
		'grid_search_column'=>'archy_registry.firm_name',
		'hidden'=>'',
		'attributes'=>['maxlength="250"']
	),
	'archy_registry.customer_tax_representative_vat_country_id'=>array(
		'label'=>'Paese IVA',
		'type'=>'search',
		'intent'=>'archy_countries',
		'lookup_text'=>'archy_countries.iso2',
		'default_value'=>1,
		'hidden'=>''
	),
	'archy_registry.customer_tax_representative_vat_id'=>array(
		'label'=>'Partita IVA',
		'type'=>'text',
		'hidden'=>'',
		'attributes'=>['maxlength="11"'],
		'extra_css_class'=>'integers_only'
	),
	'archy_registry.customer_notes'=>array(
		'label'=>'Note cliente',
		'type'=>'editor',
		'hidden'=>''
	),
	'archy_registry.supplier_category_id'=>array(
		'label'=>'Categoria fornitore',
		'type'=>'search',
		'intent'=>'archy_registry_categories',
		'domain'=>'suppliers',
		'lookup_text'=>'category',
		'hidden'=>''
	),
	'archy_registry.supplier_contact_person'=>array(
		'label'=>'Referente fornitore',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_registry.supplier_contact_person_is_legal_representative'=>array(
		'label'=>'Referente fornitore è legale rappr.',
		'type'=>'checkbox',
		'formatter'=>function($d,$row){return App::checkbox($d);},
		'formatter_hide_in_form'=>'',
		'hidden'=>''
	),
	'archy_registry.supplier_notes'=>array(
		'label'=>'Note fornitore',
		'type'=>'editor',
		'hidden'=>''
	),
	'archy_registry.member_published'=>array(
		'label'=>'Rendi pubblica questa anagrafica',
		'type'=>'checkbox',
		'default_value'=>'1',
		'formatter'=>function($d,$row){return App::checkbox($d);},
		'formatter_hide_in_form'=>'',
		'hidden'=>''
	),
	'archy_registry.member_public_name'=>array(
		'label'=>'Nome (Pubblico)',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_registry.member_public_surname'=>array(
		'label'=>'Cognome (Pubblico)',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_registry.member_public_address'=>array(
		'label'=>'Indirizzo (Pubblico)',
		'type'=>'textarea',
		'hidden'=>''
	),
	'archy_registry.member_public_zip'=>array(
		'label'=>'CAP (Pubblico)',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_registry.member_public_city_id'=>array(
		'label'=>'Città (Pubblico)',
		'type'=>'search',
		'intent'=>'archy_cities',
		'lookup_text'=>'archy_cities.city_label',
		'hidden'=>''
	),
	'archy_registry.member_public_phone'=>array(
		'label'=>'Telefono (Pubblico)',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_registry.member_public_mail'=>array(
		'label'=>'E-mail (Pubblico)',
		'type'=>'text',
		'formatter'=>function($d,$row){return App::email($d);},
		'formatter_hide_in_form'=>true,
		'formatter_hide_in_excel'=>true,
		'hidden'=>''
	),
	'archy_registry.member_public_website'=>array(
		'label'=>'Sito web (Pubblico)',
		'type'=>'text',
		'formatter'=>function($d,$row){return App::website($d);},
		'formatter_hide_in_form'=>true,
		'formatter_hide_in_excel'=>true,
		'hidden'=>''
	),
	'archy_registry.member_public_picture'=>array(
		'label'=>'Foto (Pubblico)',
		'type'=>'file',
                'formatter'=>function($d, $row){
                    $return='<a href="' . $d . '" download><img src="' . $d . '" alt="Foto" class="tt" title="Download" style="max-height:75px"></a>';
                    return $d != '' ? $return : '';
                },
                'excel_formatter'=>function($d,$row){
                    $return=App::protocol()."://".$_SERVER['HTTP_HOST'].$d;
                                return $d != '' ? $return : '';
                },
		'formatter_hide_in_form'=>true,
		//'formatter_hide_in_excel'=>true,
		'hidden'=>''
	),
	'archy_registry.created_at'=>array(
		'label'=>'Data Creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_registry.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
);

$intent->default_columns=array(//colonne del db da visualizzare di default nell'elenco (griglia)
    'archy_registry.id','archy_registry.type','archy_registry.label','Anagrafica.city_label','archy_registry.telephone','archy_registry.email'
);

$intent->exclude_columns=array(//colonne del db da escludere nell'elenco di "selezione colonne"
	'archy_registry.city_id', 'archy_registry.member_public_city_id','archy_registry.customer_tax_representative_vat_country_id','archy_registry.vat_country_id'
);

$intent->extra_action='store_registry_types';
$intent->include_in_form='intents/includes/registry.php';
$intent->include_in_grid='intents/includes/registry_filters.php';
$app->merge_intents($intent);
