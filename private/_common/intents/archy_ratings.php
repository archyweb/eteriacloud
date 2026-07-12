<?php

$intent=new stdClass();
$intent->table='archy_ratings';
$intent->title='Ratings';
$intent->singular='Rating';
$intent->add='Nuovo rating';
$intent->view_title='archy_ratings.title';
$intent->joins=[];
$intent->columns=array(
	'archy_ratings.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''
	),
    'archy_ratings.title'=>array(
		'label'=>'Nome rating (qualifica/requisito)',
		'type'=>'text',
        'attributes'=>['maxlength="20"'],
		'required'=>true,
		'grid_search_type'=>'search',
	),
	'archy_ratings.description'=>array(
		'label'=>'Descrizione rating',
		'type'=>'text',
	),
    // Per i rating NON è previsto il rinnovo, solo scadenza
    'archy_ratings.due_type'=>array(
		'label'=>'Modalità scadenza',
		'type'=>'select', 
		'default_value'=>'1',
		'options_type'=>'array',
        'options'=>['1'=>'Una tantum','0'=>'365 giorni','2'=>'Data specifica'],
		'formatter'=>function($d,$row){
            $options=['1'=>'Una tantum','0'=>'365 giorni','2'=>'Data specifica'];
			return $options[$d];
        },
        'formatter_hide_in_form'=>true,
        'required'=>true,
	),
    'archy_ratings.due_date'=>array(
		'label'=>'Data di scadenza',
		'type'=>'date',
        'formatter'=>function($d,$row){return App::latin_date_time($d);},
    ),
    'archy_ratings.parent_rating_id'=>array(
		'label'=>'Rating principale',
		'type'=>'search',
		'intent'=>'archy_ratings',
        'lookup_text'=>'title',
    ),
    'archy_ratings.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_ratings.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);

$intent->noinsert=true; // Serve per togliere + nella view (lentina)

$intent->default_columns=array(
	'archy_ratings.id',
	'archy_ratings.title',
    'archy_ratings.due_type',
    'archy_ratings.due_date',
);

$app->merge_intents($intent);
