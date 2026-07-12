<?php

$intent=new stdClass();
$intent->table='archy_events_speeches';
$intent->title='Interventi';
$intent->singular='Intervento';
$intent->add='Nuovo intervento';
$intent->view_title='Intervento ore archy_events_speeches.begin_time, del archy_events_speeches.date, "archy_events_sessions.title"';
$intent->joins=[
	'archy_events_sessions'=>'archy_events_speeches.session_id=archy_events_sessions.id' /*,
	'archy_events_speakers'=>'archy_events_speeches.speaker_id=archy_events_speakers.id' */
];
$intent->columns=array(
	'archy_events_speeches.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>'',
                'attributes'=>['readonly']
	),
	'archy_events_speeches.session_id'=>array(
		'label'=>'Sessione',
		'type'=>'search',
		'intent'=>'archy_events_sessions',
		'lookup_text'=>'archy_events_sessions.title',
		'required'=>true
	),
    'archy_events_speeches.speech_title'=>array(
		'label'=>'Titolo intervento',
		'type'=>'text'
	),
	'archy_events_speeches.date'=>array(
		'label'=>'Data',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date($d);},
	),
	'archy_events_speeches.begin_time'=>array(
		'label'=>'Dalle ore',
		'type'=>'time',
	),
	'archy_events_speeches.end_time'=>array(
		'label'=>'Alle ore',
		'type'=>'time',
	),
        'archy_events_speeches.speakers'=>array(
		'label'=>'Relatore',
		'type'=>'multiple',
                'options_type'=>'sql',
		'options'=>'select ID as option_value, name as option_text from archy_events_speakers order by option_text',
                'formatter'=>function($d,$row){return App::format_speakers($d,$row);},
                'formatter_hide_in_form' => true
	),
	'archy_events_speeches.speech_topic'=>array(
		'label'=>'Contenuti',
		'type'=>'editor'
	),
	'archy_events_speeches.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_events_speeches.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);
$intent->default_columns=array(
	'archy_events_speeches.id',
	'archy_events_sessions.title',
	'archy_events_speeches.speech_title',
    'archy_events_speeches.date',
	'archy_events_speeches.begin_time',
	'archy_events_speeches.end_time'
);

// Lo speech non va più modificato/cancellato se ha iscritti
$all_ids=array_column(mysqli_fetch_all($app->db->query("SELECT DISTINCT esp.id AS speech_id FROM archy_events_sessions ese, archy_events_subscriptions esu, archy_events_speeches esp WHERE ese.event_id = esu.event_id and esp.session_id = ese.id"),MYSQLI_ASSOC),'speech_id');
$intent->uneditable_ids=$all_ids;

$app->merge_intents($intent);
