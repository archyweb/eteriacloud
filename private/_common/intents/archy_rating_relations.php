<?php

$intent=new stdClass();
$intent->table='archy_rating_relations';
$intent->title='Qualifiche anagrafiche';
$intent->singular='Qualifica';
$intent->add='Nuovo assegnazione qualifica';
$intent->view_title='Relazioni rating archy_registry.label';
$intent->joins=[
    'archy_registry'=>'archy_rating_relations.registry_id=archy_registry.id',
    'archy_ratings'=>'archy_rating_relations.rating_id=archy_ratings.id',
];
$intent->columns=array(
	'archy_rating_relations.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''
	),
    'archy_rating_relations.registry_id'=>array(
		'label'=>'Anagrafica',
		'type'=>'search',
		'intent'=>'archy_registry',
		'lookup_text'=>'archy_registry.label',
		'required'=>true
	),
    'archy_rating_relations.rating_id'=>array(
		'label'=>'Rating',
		'type'=>'search',
		'intent'=>'archy_ratings',
		'lookup_text'=>'archy_ratings.title',
		'required'=>true
	),
    'archy_rating_relations.rating_children'=>array(
		'label'=>'Rating figli',
		'type'=>'text',
                'formatter'=>function($d,$row){
                    $response = '';
                    if($d!=''):
                        global $app;
                        $db=$app->get_db();                        
                        $children = explode(",", $d);                        
                        foreach($children as $child){
                            if($child != ''){
                                $data=mysqli_fetch_object($db->query("select title from archy_ratings where id=".$child));
                                $response .= $data->title.' | ';
                            }
                        }
                        
                    endif;
                    return $response;
                },
                'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
    'archy_rating_relations.rating_due_date'=>array(
		'label'=>'Data di scadenza aggiornamento',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
    'archy_rating_relations.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_rating_relations.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);

$intent->noinsert=true; // Serve per togliere + nella view (lentina)

$intent->default_columns=array(
	'archy_rating_relations.id',
    'archy_registry.label',
    'archy_ratings.title',
    'archy_rating_relations.rating_due_date',
);

$intent->extra_action='store_rating_relations';
$app->merge_intents($intent);
