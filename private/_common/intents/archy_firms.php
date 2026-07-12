<?php

$intent=new stdClass();
$intent->table='archy_firms';//nome tabella data-base
$intent->title='Opzioni Contabilità';//titolo (plurale)
$intent->singular='Opzioni Contabilità';//titolo (singolare)
//$intent->add='Nuovo registro';//testo usato nei forms e nei pulsanti
$intent->view_title='archy_firms.firm_name';//Titolo nella visualizzazione/modifica del singolo record
$intent->joins=[
	'archy_cities'=>'archy_firms.city_id=archy_cities.id',
	'archy_tax_rates'=>'archy_firms.tax_rate_id=archy_tax_rates.id',
    'archy_banks'=>'archy_firms.bank_id=archy_banks.id'
];//tabelle collegate

$tax_regime_options=[
	'RF01'=>'Ordinario',
	'RF02'=>'Contribuenti minimi (art. 1, c.96-117, L. 244/2007)',
	'RF03'=>'Nuove iniziative produttive (art.13, L. 388/2000)',
	'RF04'=>'Agricoltura e attività connesse e pesca (artt. 34 e 34-bis, D.P.R. 633/1972)',
	'RF05'=>'Vendita sali e tabacchi (art. 74, c.1, D.P.R. 633/1972)',
	'RF06'=>'Commercio dei fiammiferi (art. 74, c.1, D.P.R. 633/1972)',
	'RF07'=>'Editoria (art. 74, c.1, D.P.R. 633/1972)',
	'RF08'=>'Gestione di servizi di telefonia pubblica (art. 74, c.1, D.P.R. 633/1972)',
	'RF09'=>'Rivendita di documenti di trasporto pubblico e di sosta (art. 74, c.1, D.P.R. 633/1972)',
	'RF10'=>'Intrattenimenti, giochi e altre attività di cui alla tariffa allegata al D.P.R. n. 640/72 (art. 74, c.6, D.P.R. 633/1972)',
	'RF11'=>'Agenzie di viaggi e turismo (art. 74-ter, D.P.R. 633/1972)',
	'RF12'=>'Agriturismo (art. 5, c.2, L. 413/1991)',
	'RF13'=>'Vendite a domicilio (art. 25-bis, c.6, D.P.R. 600/1973)',
	'RF14'=>'Rivendita di beni usati, di oggetti d’arte, d’antiquariato o da collezione (art. 36, D.L. 41/1995)',
	'RF15'=>'Agenzie di vendite all’asta di oggetti d’arte, antiquariato o da collezione (art. 40-bis, D.L. 41/1995)',
	'RF16'=>'IVA per cassa P.A. (art. 6, c.5, D.P.R. 633/1972)',
	'RF17'=>'IVA per cassa (art. 32-bis, D.L. 83/2012)',
	'RF18'=>'Altro',
	'RF19'=>'Forfettario (art.1, c. 54-89, L. 190/2014)'
];

$intent->columns=array(//colonne della tabella principale
	'archy_firms.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''//nasconde il campo nel modulo di creazione/aggiornamento di un record
	),
	'archy_firms.firm_name'=>array(
		'label'=>'Ragione Sociale ',
		'type'=>'text',
		'required'=>true,
		'hidden'=>''
	),
	'archy_firms.tax_rate_id'=>array(
		'label'=>'Aliquota iva predefinita',
		'type'=>'search',
		'intent'=>'archy_tax_rates',
		'lookup_text'=>'archy_tax_rates.name',
		//'required'=>true,
		'hidden'=>''
	),
	'archy_firms.vat_country_id'=>array(
		'label'=>'Partita IVA (Paese)',
		'type'=>'search',
		'intent'=>'archy_countries',
		'lookup_text'=>'iso2',
		'hidden'=>'',
		'default_value'=>'1',
		//'required'=>true
	),
	'archy_firms.vat_id'=>array(
		'label'=>'Partita IVA (Numero)',
		'type'=>'text',
		'hidden'=>'',
		'attributes'=>['maxlength="11"'],
		//'required'=>true
	),
	'archy_firms.tax_id'=>array(
		'label'=>'Codice Fiscale',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_firms.address'=>array(
		'label'=>'Indirizzo',
		'type'=>'text',
		'hidden'=>'',
		'required'=>true
	),
	'archy_firms.city_id'=>array(
		'label'=>'Città',
		'type'=>'search',
		'intent'=>'archy_cities',
		'lookup_text'=>'archy_cities.city_label',
		'hidden'=>'',
		'required'=>true
	),
	'archy_firms.zip'=>array(
		'label'=>'CAP',
		'type'=>'text',
		'hidden'=>'',
		'required'=>true
	),
	'archy_firms.phone'=>array(
		'label'=>'Telefono',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_firms.fax'=>array(
		'label'=>'Fax',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_firms.mobile'=>array(
		'label'=>'Cellulare',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_firms.email'=>array(
		'label'=>'E-mail',
		'type'=>'text',
		'formatter'=>function($d,$row){return App::email($d);},
		'formatter_hide_in_form'=>true,
		'formatter_hide_in_excel'=>true,
		'hidden'=>''
	),
	'archy_firms.certified_email'=>array(
		'label'=>'PEC',
		'type'=>'text',
		'formatter'=>function($d,$row){return App::email($d);},
		'formatter_hide_in_form'=>true,
		'formatter_hide_in_excel'=>true,
		'hidden'=>''
	),
	'archy_firms.website'=>array(
		'label'=>'Sito web',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_firms.logo'=>array(
		'label'=>'Logo',
		'type'=>'file',
		'formatter'=>function($d,$row){
			if($d!=''):
				$tmp=explode("/",$d);
				return '<a href="'.$d.'" download>'.end($tmp).'</a>';
			endif;
		},
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
	'archy_firms.invoice_prefix'=>array(
		'label'=>'Prefisso numerazione fatture',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_firms.invoice_number_length'=>array(
		'label'=>'Lunghezza numero',
		'default_value'=>4,
		'extra_css_class'=>'integers_only',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_firms.first_invoice_number'=>array(
		'label'=>'Numero prima fattura',
		'type'=>'text',
		'extra_css_class'=>'integers_only',
		'formatter'=>function($d,$row){
			return $d!=0? $d:'';
		},
		'hidden'=>''
	),
	'archy_firms.invoice_template'=>array(
		'label'=>'Template fattura',
		'default_value'=>file_get_contents($app->get_subdomain_url() . '/assets/tinymce/templates/invoice.html'),
		'type'=>'plain_editor',
		'editor_height'=>'250',
		'editor_purpose'=>'invoice',
		'hidden'=>''
	),
	'archy_firms.invoice_footer'=>array(
		'label'=>'Footer fattura',
		'type'=>'plain_editor',
		'default_value'=>file_get_contents($app->get_subdomain_url() . '/assets/tinymce/templates/footer.html'),
		'editor_height'=>'90',
		'editor_purpose'=>'invoice',
		'hidden'=>''
	),
	'archy_firms.invoice_mail_subject'=>array(
		'label'=>'Oggetto mail fattura',
		'default_value'=>'Fattura [NumeroFattura] [RagioneSociale Emittente]',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_firms.invoice_mail_body'=>array(
		'label'=>'Corpo mail fattura',
		'default_value'=>'<p>Spett.le [RagioneSociale/Nominativo Cliente],<br>In allegato fattura [NumeroFattura] del [DataFattura]</p><p>Saluti,<br>[IndirizzoFatturazione Emittente]</p>',
		'type'=>'plain_editor',
		'editor_height'=>'200',
		'editor_purpose'=>'invoice',
		'hidden'=>''
	),
	'archy_firms.invoice_mail_bcc'=>array(
		'label'=>'Copia mail fattura',
		'help_block'=>"Indirizzo e-mail cui inviare copia del messaggio. È possibile usare più indirizzi separandoli con una virgola",
		'type'=>'text',
		'hidden'=>''
	),
	'archy_firms.credit_note_prefix'=>array(
		'label'=>'Prefisso numerazione Note di credito',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_firms.credit_note_number_length'=>array(
		'label'=>'Lunghezza numero',
		'default_value'=>4,
		'extra_css_class'=>'integers_only',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_firms.first_credit_note_number'=>array(
		'label'=>'Numero prima Nota di credito',
		'type'=>'text',
		'extra_css_class'=>'integers_only',
		'formatter'=>function($d,$row){
			return $d!=0? $d:'';
		},
		'hidden'=>''
	),
	'archy_firms.credit_note_template'=>array(
		'label'=>'Template nota di credito',
		'default_value'=>file_get_contents($app->get_subdomain_url() . '/assets/tinymce/templates/credit_note.html'),
		'type'=>'plain_editor',
		'editor_height'=>'250',
		'editor_purpose'=>'credit_note',
		'hidden'=>''
	),
	'archy_firms.credit_note_footer'=>array(
		'label'=>'Footer nota di credito',
		'type'=>'plain_editor',
		'default_value'=>file_get_contents($app->get_subdomain_url() . '/assets/tinymce/templates/footer.html'),
		'editor_height'=>'90',
		'editor_purpose'=>'credit_note',
		'hidden'=>''
	),
	'archy_firms.credit_note_mail_subject'=>array(
		'label'=>'Oggetto mail Nota di credito',
		'default_value'=>'Nota di Credito [NumeroNota] [RagioneSociale Emittente]',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_firms.credit_note_mail_body'=>array(
		'label'=>'Corpo mail Nota di credito',
		'default_value'=>'<p>Spett.le [RagioneSociale/Nominativo Cliente],<br>In allegato nota di credito [NumeroNota] del [DataNota]</p><p>Saluti,<br>[IndirizzoFatturazione Emittente]</p>',
		'type'=>'plain_editor',
		'editor_height'=>'200',
		'editor_purpose'=>'credit_note',
		'hidden'=>''
	),
	'archy_firms.credit_note_mail_bcc'=>array(
		'label'=>'Copia mail Nota di credito',
		'help_block'=>"Indirizzo e-mail cui inviare copia del messaggio. È possibile usare più indirizzi separandoli con una virgola",
		'type'=>'text',
		'hidden'=>''
	),
	'archy_firms.receipt_prefix'=>array(
		'label'=>'Prefisso numerazione ricevute',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_firms.receipt_number_length'=>array(
		'label'=>'Lunghezza numero',
		'default_value'=>4,
		'extra_css_class'=>'integers_only',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_firms.first_receipt_number'=>array(
		'label'=>'Numero prima ricevuta',
		'type'=>'text',
		'extra_css_class'=>'integers_only',
		'formatter'=>function($d,$row){
			return $d!=0? $d:'';
		},
		'hidden'=>''
	),
	'archy_firms.passive_receipt_prefix'=>array(
		'label'=>'Prefisso numerazione storni',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_firms.passive_receipt_number_length'=>array(
		'label'=>'Lunghezza numero storni',
		'default_value'=>4,
		'extra_css_class'=>'integers_only',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_firms.first_passive_receipt_number'=>array(
		'label'=>'Numero primo storno',
		'type'=>'text',
		'extra_css_class'=>'integers_only',
		'formatter'=>function($d,$row){
			return $d!=0? $d:'';
		},
		'hidden'=>''
	),
	'archy_firms.receipt_template'=>array(
		'label'=>'Template ricevuta fiscale',
		'type'=>'plain_editor',
		'default_value'=>file_get_contents($app->get_subdomain_url() . '/assets/tinymce/templates/receipt.html'),
		'editor_purpose'=>'receipt',
		'editor_height'=>'200',
		'hidden'=>''
	),
	'archy_firms.receipt_footer'=>array(
		'label'=>'Footer ricevuta fiscale',
		'type'=>'plain_editor',
		'default_value'=>file_get_contents($app->get_subdomain_url() . '/assets/tinymce/templates/footer.html'),
		'editor_height'=>'90',
		'editor_purpose'=>'receipt',
		'hidden'=>''
	),
	'archy_firms.receipt_mail_subject'=>array(
		'label'=>'Oggetto mail ricevuta',
		'default_value'=>'Ricevuta [NumeroRicevuta] [RagioneSociale Emittente]',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_firms.receipt_mail_body'=>array(
		'label'=>'Corpo mail ricevuta',
		'default_value'=>'<p>Spett.le [RagioneSociale/Nominativo Cliente],<br>In allegato ricevuta [NumeroRicevuta] del [DataRicevuta]</p><p>Saluti,<br>[IndirizzoFatturazione Emittente]</p>',
		'type'=>'plain_editor',
		'editor_height'=>'200',
		'editor_purpose'=>'receipt',
		'hidden'=>''
	),
	'archy_firms.receipt_mail_bcc'=>array(
		'label'=>'Copia mail ricevuta',
		'help_block'=>"Indirizzo e-mail cui inviare copia del messaggio. È possibile usare più indirizzi separandoli con una virgola",
		'type'=>'text',
		'hidden'=>''
	),
	'archy_firms.quote_prefix'=>array(
		'label'=>'Prefisso numerazione preventivi',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_firms.quote_number_length'=>array(
		'label'=>'Lunghezza numero',
		'default_value'=>4,
		'extra_css_class'=>'integers_only',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_firms.first_quote_number'=>array(
		'label'=>'Numero primo preventivo',
		'type'=>'text',
		'extra_css_class'=>'integers_only',
		'formatter'=>function($d,$row){
			return $d!=0? $d:'';
		},
		'hidden'=>''
	),
	'archy_firms.quote_template'=>array(
		'label'=>'Template preventivo',
		'type'=>'plain_editor',
		'default_value'=>file_get_contents($app->get_subdomain_url() . '/assets/tinymce/templates/quote.html'),
		'editor_height'=>'250',
		'editor_purpose'=>'quote',
		'hidden'=>''
	),
	'archy_firms.quote_footer'=>array(
		'label'=>'Footer preventivo',
		'default_value'=>file_get_contents($app->get_subdomain_url() . '/assets/tinymce/templates/footer.html'),
		'type'=>'plain_editor',
		'editor_height'=>'90',
		'hidden'=>''
	),
	'archy_firms.quote_mail_subject'=>array(
		'label'=>'Oggetto mail Preventivo',
		'default_value'=>'Preventivo [NumeroPreventivo] [RagioneSociale Emittente]',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_firms.quote_mail_body'=>array(
		'label'=>'Corpo mail preventivo',
		'default_value'=>'<p>Spett.le [RagioneSociale/Nominativo Cliente],<br>In allegato preventivo [NumeroPreventivo] del [DataPreventivo]</p><p>Saluti,<br>[IndirizzoFatturazione Emittente]</p>',
		'type'=>'plain_editor',
		'editor_height'=>'200',
		'editor_purpose'=>'quote',
		'hidden'=>''
	),
	'archy_firms.quote_mail_bcc'=>array(
		'label'=>'Copia mail preventivo',
		'help_block'=>"Indirizzo e-mail cui inviare copia del messaggio. È possibile usare più indirizzi separandoli con una virgola",
		'type'=>'text',
		'hidden'=>''
	),
	'archy_firms.tax_regime'=>array(
		'label'=>'Regime fiscale',
		'type'=>'select', 
		'default_value'=>'',
		'options_type'=>'array',
		'options'=>$tax_regime_options,
		'formatter'=>function($d,$row){
			global $tax_regime_options;
			return $tax_regime_options[$d];
		},
		'formatter_hide_in_form'=>true,
		'required'=>true,
		'hidden'=>''
	),
	'archy_firms.EORI'=>array(
		'label'=>'Codice EORI',
		'type'=>'text',
		'attributes'=>['maxlength="17"'],
		'hidden'=>''
	),
	'archy_firms.VAT_collectability'=>array(
		'label'=>'Esigibilità IVA predefinita',
		'type'=>'select', 
		'default_value'=>'I',
		'options_type'=>'array',
		'options'=>['I'=>'IVA ad esigibilità immediata', 'D'=>'IVA ad esigibilità differita', 'S'=>'Scissione dei pagamenti'],
		'formatter'=>function($d,$row){
			$collectability=['I'=>'IVA ad esigibilità immediata', 'D'=>'IVA ad esigibilità differita', 'S'=>'Scissione dei pagamenti'];
			return $collectability[$d];
		},
		'formatter_hide_in_form'=>true,
		'required'=>true,
		'hidden'=>''
	),

	'archy_firms.stable_org_address'=>array(
		'label'=>'Indirizzo',
		'type'=>'text',
		'attributes'=>['maxlength="250"'],
		'hidden'=>''
	),
	'archy_firms.stable_org_city_id'=>array(
		'label'=>'Città (Italiana)',
		'type'=>'search',
		'intent'=>'archy_cities',
		'lookup_text'=>'archy_cities.city_label',
		'hidden'=>''
	),
	'archy_firms.stable_org_zip'=>array(
		'label'=>'CAP',
		'type'=>'text',
		'attributes'=>['maxlength="5"'],
		'hidden'=>''
	),

	'archy_firms.rea_office'=>array(
		'label'=>'Ufficio',
		'type'=>'select',
		'options_type'=>'sql',
		'options'=>'select distinct province_district_code as option_value, province_district as option_text from archy_cities order by option_text',
		'hidden'=>''
	),
	'archy_firms.rea_number'=>array(
		'label'=>'Numero',
		'type'=>'text',
		'attributes'=>['maxlength="250"'],
		'hidden'=>''
	),
	'archy_firms.rea_share_capital'=>array(
		'label'=>'Capitale Sociale',
		'type'=>'amount',
		'hidden'=>''
	),
	'archy_firms.rea_sole_shareholder'=>array(
		'label'=>'Socio Unico',
		'type'=>'select', 
		'default_value'=>'',
		'options_type'=>'array',
		'options'=>['SU'=>'Socio Unico','SM'=>'Più Soci'],
		'formatter'=>function($d,$row){
			$options=['SU'=>'Socio Unico','SM'=>'Più Soci'];
			return $d!=''? $options[$d]: $d;
		},
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
	'archy_firms.rea_in_liquidation'=>array(
		'label'=>'Stato Liquidazione',
		'type'=>'select', 
		'default_value'=>'',
		'options_type'=>'array',
		'options'=>['LS'=>'In Liquidazione','Ln'=>'Non In Liquidazione'],
		'formatter'=>function($d,$row){
			$options=['LS'=>'In Liquidazione','Ln'=>'Non In Liquidazione'];
			return $d!=''? $options[$d]: $d;
		},
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	),

	'archy_firms.professional_register_name'=>array(
		'label'=>'Albo Professionale',
		'type'=>'text',
		'attributes'=>['maxlength="250"'],
		'hidden'=>''
	),
	'archy_firms.professional_register_province'=>array(
		'label'=>'Provincia Albo',
		'type'=>'select',
		'options_type'=>'sql',
		'options'=>'select distinct province_district_code as option_value, province_district as option_text from archy_cities order by option_text',
		'hidden'=>''
	),
	'archy_firms.professional_register_subscription_number'=>array(
		'label'=>'Numero Iscrizione',
		'type'=>'text',
		'attributes'=>['maxlength="250"'],
		'hidden'=>''
	),
	'archy_firms.professional_register_subscription_date'=>array(
		'label'=>'Data Iscrizione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'hidden'=>''
	),
	'archy_firms.bank_id'=>array(
		'label'=>'Banca predefinita',
		'type'=>'search',
		'intent'=>'archy_banks',
		'lookup_text'=>'archy_banks.nome',
		'hidden'=>''
	),

	'archy_firms.created_at'=>array(
		'label'=>'Data Creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_firms.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
);
$intent->default_columns=array(//colonne del db da visualizzare di default nell'elenco (griglia)
	'archy_firms.id',
	'archy_firms.firm_name',
	'archy_cities.city_label',
	'archy_firms.vat_id',
    // 'archy_firms.invoice_prefix'
    //inizia sempre con l'id di modo che i nuovi record siano visualizzati in cima alla tabella
);
$intent->extra_action='register_firm';
$intent->include_in_form='intents/includes/firm.php';
$app->merge_intents($intent);//crea i collegamenti, a cascata, con le altre tabelle
?>