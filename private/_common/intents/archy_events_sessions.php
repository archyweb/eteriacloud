<?php

$intent=new stdClass();
$intent->table='archy_events_sessions';
$intent->title='Sessioni';
$intent->singular='Sessione';
//$intent->add=null;
$intent->restore_button_in_edit=true;

$intent->view_title='Sessione: archy_events_sessions.title';
$intent->joins=[
	'archy_events'=>'archy_events_sessions.event_id=archy_events.id',
	'archy_events_locations'=>'archy_events_sessions.location_id=archy_events_locations.id',
	'archy_ratings as RatingRequired'=>'archy_events_sessions.required_rating_id=RatingRequired.id',
        'archy_ratings as RatingEarned'=>'archy_events_sessions.earned_rating_id=RatingEarned.id'
	//'archy_tax_rates'=>'archy_events_sessions.tax_rate_id=archy_tax_rates.id'
];
$intent->columns=array(
	'archy_events_sessions.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>'',
                'attributes'=>['readonly']
	),
	'archy_events_sessions.event_id'=>array(
		'label'=>'Evento, corso, seminario',
		'type'=>'search',
		'intent'=>'archy_events',
		'lookup_text'=>'title',
		'required'=>true,
		'hidden'=>''
	),
	'archy_events_sessions.title'=>array(
		'label'=>'Titolo sessione',
		'type'=>'text',
		'required'=>true,
		'hidden'=>''
	),
        'archy_events_sessions.activity_code'=>array(
		'label'=>'Codice attività',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_events_sessions.description'=>array(
		'label'=>'Descrizione',
		'type'=>'editor',
		'hidden'=>''
	),
        'archy_events_sessions.note'=>array(
		'label'=>'Note',
		'type'=>'editor',
		'hidden'=>''
	),
	'archy_events_sessions.location_id'=>array(
		'label'=>'Location',
		'type'=>'search',
		'intent'=>'archy_events_locations',
		'lookup_text'=>'name',
		'hidden'=>''
	),
        'archy_events_sessions.start_date'=>array(
		'label'=>'Data inizio',
		'type'=>'date',
                //'default_value'=>date('Y-m-d'),
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'hidden'=>''
	),
        'archy_events_sessions.end_date'=>array(
		'label'=>'Data fine',
		'type'=>'date',
                //'default_value'=>date('Y-m-d'),
		'formatter'=>function($d,$row){return App::latin_date($d);},
		'hidden'=>''
	),
	'archy_events_sessions.price'=>array(
		'label'=>'Prezzo iva compresa',
		'type'=>'amount',
		//'default_value'=>'0.00',
		//'required'=>true,
		'formatter'=>function($d,$row){return App::currency($d);},
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
	/*'archy_events_sessions.tax_rate_id'=>array(
		'label'=>'Aliquota IVA',
		'type'=>'search',
		'intent'=>'archy_tax_rates',
        'lookup_text'=>'archy_tax_rates.name',
		'hidden'=>''
	),*/
	'archy_events_sessions.credits'=>array(
		'label'=>'Crediti',
		'type'=>'text',
		'extra_css_class'=>'integers_only',
                'default_value'=>'0',
		'hidden'=>''
	),
        'archy_events_sessions.training_hours'=>array(
		'label'=>'Ore formative',
		'type'=>'text',
		'extra_css_class'=>'integers_only',
		'hidden'=>''
	), 
        'archy_events_sessions.required_rating_id'=>array(
		'label'=>'Rating necessario',
		'type'=>'search',
		'intent'=>'archy_ratings',
		'lookup_text'=>'archy_ratings.title',
                'hidden'=>''
	),
        'archy_events_sessions.earned_rating_id'=>array(
		'label'=>'Rating guadagnato',
		'type'=>'search',
		'intent'=>'archy_ratings',
		'lookup_text'=>'archy_ratings.title',
                'hidden'=>''
	),
        'archy_events_sessions.manager_id'=>array(
		'label'=>'Organizzatore',
		'type'=>'search',
		'intent'=>'archy_registry',
		'lookup_text'=>'label',
		'hidden'=>''
	),  
        'archy_events_sessions.model_width'=>array(
		'label'=>'Larghezza (mm)',
		'type'=>'text',
                'extra_css_class'=>'integers_only',
                'default_value'=>'0',
		'hidden'=>''
	),
        'archy_events_sessions.model_height'=>array(
		'label'=>'Altezza (mm)',
		'type'=>'text',
                'extra_css_class'=>'integers_only',
                'default_value'=>'0',
		'hidden'=>''
	),
        'archy_events_sessions.model_template'=>array(
		'label'=>'Modello attestato',
		'type'=>'editor',
                 'editor_height'=>'400',
		'editor_purpose'=>'events',
		'hidden'=>''
	),
        'archy_events_sessions.model_background'=>array(
		'label'=>'Layout grafico di sfondo',
		'type'=>'file',
                'formatter' => function ($d, $row) {
                    $return='<a href="'.$d.'" download><img src="'.$d.'" alt="Layout" class="tt" title="Download" style="max-height:44px"></a>';
                    return $d != '' ? $return : '';
                },
		'formatter_hide_in_form'=>true,
		'formatter_hide_in_excel'=>true,
		'hidden'=>''
	),
        'archy_events_sessions.model_css'=>array(
		'label'=>'CSS personalizzato',
		'type'=>'editor',
		'editor_height'=>'400',
		'editor_purpose'=>'css',
		'hidden'=>''
	),                
	'archy_events_sessions.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_events_sessions.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);
$intent->default_columns=array(
	'archy_events_sessions.id',
	'archy_events.title',
	'archy_events_sessions.title',
	'archy_events_sessions.price'
);

if($app->config['association']):
	$intent->columns['archy_events_sessions.members_price']=[
		'label'=>'Prezzo per associati',
		'type'=>'amount',
		//'default_value'=>'0.00',
		//'required'=>true,
		'formatter'=>function($d,$row){return App::currency($d);},
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	];
endif;

// L'evento non va più modificato/cancellato se ha iscritti
$all_ids=array_column(mysqli_fetch_all($app->db->query("SELECT ese.id AS session_id FROM archy_events_sessions ese, archy_events_subscriptions esu WHERE ese.event_id = esu.event_id"),MYSQLI_ASSOC),'session_id');
$intent->uneditable_ids=$all_ids;

$intent->include_in_form='intents/includes/events_sessions.php';
//$intent->extra_action='store_events_sessions';

$intent->after_delete='&after_delete=events_sessions_after_delete';

$app->merge_intents($intent);
