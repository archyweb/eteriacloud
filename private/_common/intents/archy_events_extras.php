<?php

$intent=new stdClass();
$intent->table='archy_events_extras';
$intent->title='Extra';
$intent->singular='Extra';
//$intent->add='Nuovo extra';
$intent->view_title='archy_events_extras.description';
$intent->joins=[];
$intent->columns=array(
	'archy_events_extras.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>'',
            'attributes'=>['readonly']
	),
	'archy_events_extras.description'=>array(
		'label'=>'Descrizione costo extra',
		'type'=>'text',
		'required'=>true
	),
	'archy_events_extras.price'=>array(
		'label'=>'Prezzo',
                'help_block'=>'Aliquota iva già compresa in caso di emissione di fattura',
		'type'=>'amount',
		'formatter'=>function($d,$row){
			return App::currency($d);
		},
		'formatter_hide_in_form'=>true,
		'required'=>true
	),
    'archy_events_extras.domain'=>array(
		'label'=>'Domain',
		'type'=>'select',
		'options_type'=>'array',
		'options'=>['event'=>'Evento','session'=>'Sessione'],
		'formatter'=>function($d,$row){
			$options=['event'=>'Evento','session'=>'Sessione'];
			return $options[$d];
		},
        'formatter_hide_in_form'=>true,
        'attributes'=>['readonly'],
		'required'=>true,
	),
	'archy_events_extras.domain_id'=>array(
		'label'=>'Domain ID',
		'type'=>'text',
        'attributes'=>['readonly'],
        'required'=>true,
	),
	'archy_events_extras.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_events_extras.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);

//$all_ids=array_column(mysqli_fetch_all($app->db->query("SELECT id FROM archy_queue"),MYSQLI_ASSOC),'id');
//$intent->uneditable_ids=$all_ids;
//$intent->noinsert=true; // Serve per togliere + nella view (lentina)

$intent->default_columns=array(
	'archy_events_extras.id',
	'archy_events_extras.description',
	'archy_events_extras.price',
    //'archy_events_extras.domain',
	//'archy_events_extras.domain_id',
);

// L'extra non va più modificato/cancellato se ha iscritti
$all_extras=mysqli_fetch_all($app->db->query("
SELECT event_extras as extra
FROM archy_events_sessions ese, archy_events_subscriptions esu, archy_events_speeches esp
WHERE ese.event_id = esu.event_id and esp.session_id = ese.id
UNION
SELECT session_extras as extra
FROM archy_events_sessions ese, archy_events_subscriptions esu, archy_events_speeches esp
WHERE ese.event_id = esu.event_id and esp.session_id = ese.id
"),MYSQLI_ASSOC);
$all_ids=[];
foreach($all_extras as $el) {
    $rec=unserialize($el['extra']);
    foreach($rec as $id => $el2) {
        $all_ids[]=$id;
    }
}
$intent->uneditable_ids=$all_ids;

$app->merge_intents($intent);
?>