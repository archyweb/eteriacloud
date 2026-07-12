<?php

$intent=new stdClass();
$intent->table='archy_events';
$intent->title='Eventi, corsi, seminari';
$intent->singular='Evento, corso, seminario';
$intent->add='Nuovo evento';
$intent->view_title='archy_events.title';
$intent->joins=[
	'archy_events_categories'=>'archy_events.category_id=archy_events_categories.id',
    'archy_cost_centres'=>'archy_events.cost_centre_id=archy_cost_centres.id',
	'archy_tax_rates'=>'archy_events.tax_rate_id=archy_tax_rates.id'
];
$intent->columns=array(
	'archy_events.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_events.title'=>array(
		'label'=>'Titolo evento',
		'type'=>'text',
		'required'=>true,
		'hidden'=>'',
		'grid_search_type'=>'search'
	),
	'archy_events.description'=>array(
		'label'=>'Descrizione',
		'type'=>'editor',
		'hidden'=>''
	),
	'archy_events.category_id'=>array(
		'label'=>'Categoria',
		'type'=>'search',
		'intent'=>'archy_events_categories',
		'lookup_text'=>'category',
        'required'=>true,
		'hidden'=>''
	),
	'archy_events.total_credits'=>array(
		'label'=>'Crediti totali',
		'type'=>'text',
		'extra_css_class'=>'integers_only',
		'hidden'=>''
	),
	'archy_events.minimum_credits'=>array(
		'label'=>'Crediti min. (attestato)',
		'type'=>'text',
		'extra_css_class'=>'integers_only',
		'default_value'=>0,
		'hidden'=>''
	),
	'archy_events.max_subscribers'=>array(
		'label'=>'Numero max. iscritti',
		'type'=>'text',
		'extra_css_class'=>'integers_only',
		'hidden'=>''
	),
    'archy_events.cost_centre_id'=>array(
		'label'=>'Voce di costo',
		'type'=>'search',
		'intent'=>'archy_cost_centres',
        'lookup_text'=>'cost_centre',
        //'required'=>true,
        'hidden'=>''
    ),
    'archy_events.accounting_reason'=>array(
		'label'=>'Causale ricevuta/fattura',
        'type'=>'text',
		//'required'=>true,
        'hidden'=>''
    ),
	'archy_events.tax_rate_id'=>array(
		'label'=>'Aliquota IVA',
		'type'=>'search',
		'intent'=>'archy_tax_rates',
        'lookup_text'=>'archy_tax_rates.name',
		'hidden'=>''
	),
	'archy_events.image'=>array(
		'label'=>'Immagine',
		'type'=>'file',
		'formatter'=>function($d,$row){
			return $d!=''? 
				'<a href="'.$d.'"><img src="'.$d.'" alt="Immagine" class="tt" title="Download" style="max-height:44px"></a>':
				'';
		},
		'formatter_hide_in_form'=>true,
		'formatter_hide_in_excel'=>true,
		'hidden'=>''
	),
    'archy_events.docs'=>array(
		'label'=>'Documenti',
		'type'=>'file',
		'formatter'=>function($d,$row){
			return $d!=''? '<a href="'.$d.'">Documenti</a>': '';
		},
		'formatter_hide_in_form'=>true,
		'formatter_hide_in_excel'=>true,
		'hidden'=>''
	),
    'archy_events.model_width'=>array(
		'label'=>'Larghezza (mm)',
		'type'=>'text',
        'extra_css_class'=>'integers_only',
		'hidden'=>''
	),
    'archy_events.model_height'=>array(
		'label'=>'Altezza (mm)',
		'type'=>'text',
        'extra_css_class'=>'integers_only',
		'hidden'=>''
	),
    'archy_events.model_template'=>array(
		'label'=>'Modello attestato',
		'type'=>'plain_editor',
		'editor_height'=>'400',
		'editor_purpose'=>'events',
		'hidden'=>''
	),
    'archy_events.model_background'=>array(
		'label'=>'Layout grafico di sfondo',
		'type'=>'file',
		'formatter'=>function($d,$row){
			return $d!=''? 
				'<a href="'.$d.'"><img src="'.$d.'" alt="Layout" class="tt" title="Download" style="max-height:44px"></a>':
				'';
		},
		'formatter_hide_in_form'=>true,
		'formatter_hide_in_excel'=>true,
		'hidden'=>''
	),
        'archy_events.model_css'=>array(
		'label'=>'CSS personalizzato',
		'type'=>'plain_editor',
		'editor_height'=>'400',
		'editor_purpose'=>'css',
		'hidden'=>''
	),                
	'archy_events.contact_mail'=>array(
		'label'=>'E-mail di contatto',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_events.contact_phone'=>array(
		'label'=>'Contatto telefonico',
		'type'=>'text',
		'hidden'=>''
	),
        'archy_events.event_mail_subject'=>array(
		'label'=>'Oggetto mail attestato',
		'default_value'=>'Attestato [titolo_evento] [nominativo]',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_events.event_mail_body'=>array(
		'label'=>'Corpo mail attestato',
		'default_value'=>'<p>Spett.le [nominativo],<br>In allegato attestato [titolo_evento].</p><p>Saluti,<br></p>',
		'type'=>'plain_editor',
		'editor_height'=>'200',
		'editor_purpose'=>'events',
		'hidden'=>''
	),
	'archy_events.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_events.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);
$intent->default_columns=array(
	'archy_events.id',
	'archy_events.title',
	'archy_events_categories.category',
	'archy_events.image'
);

// L'evento non va più modificato/cancellato se ha iscritti
//$all_ids=array_column(mysqli_fetch_all($app->db->query("SELECT DISTINCT event_id FROM archy_events_subscriptions"),MYSQLI_ASSOC),'event_id');
//$intent->uneditable_ids=$all_ids;

$intent->include_in_form='intents/includes/events.php';
$intent->extra_action='store_event';

$intent->before_delete='&before_delete=events_before_delete';
$intent->after_delete='&after_delete=events_after_delete';

$app->merge_intents($intent);
?>