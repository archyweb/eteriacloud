<?php

/*
|--------------------------------------------------------------------------
| ARCHY MEDIA - INTENT TAG CONTROLLATI
|--------------------------------------------------------------------------
|
| Anagrafica centrale dei termini controllati usati dai campi type => tags.
| La categoria permette di usare lo stesso motore per Tag, Tecnica,
| Materiali, Supporto, Stile, ecc.
|
*/

$intent=new stdClass();
$intent->table='archy_tag';
$intent->title='Tag / Parole chiave';
$intent->singular='Tag';
$intent->add='Nuovo Tag';
$intent->view_title='Tag: archy_tag.tag';
$intent->joins=array();
$intent->columns=array(
    'archy_tag.id'=>array(
        'label'=>'ID',
        'type'=>'text',
        'hidden'=>''
    ),
    'archy_tag.tag'=>array(
        'label'=>'Tag',
        'type'=>'text',
        'required'=>true,
        'grid_search_type'=>'search'
    ),
    'archy_tag.slug'=>array(
        'label'=>'Slug',
        'type'=>'text',
        'help_block'=>'Valore normalizzato usato dal sistema per evitare duplicati.',
        'grid_search_type'=>'search'
    ),
    'archy_tag.categoria'=>array(
		'label'=>'Categoria',
		'type'=>'select',
		'options_type'=>'array',
		'options'=>array(
			'Tag'=>'Tag',
			'Tecnica'=>'Tecnica',
			'Materiali'=>'Materiali',
			'Supporto'=>'Supporto'
		),
        'default_value'=>'Tag',
        'required'=>true,
        'grid_search_type'=>'select'
    ),
    'archy_tag.status'=>array(
        'label'=>'Attivo',
        'type'=>'checkbox',
        'default_value'=>'1',
        'formatter'=>function($d,$row){return App::checkbox($d);},
        'formatter_hide_in_form'=>true,
        'grid_search_type'=>'checkbox'
    ),
    'archy_tag.created_at'=>array(
        'label'=>'Data creazione',
        'type'=>'date',
        'formatter'=>function($d,$row){return App::latin_date_time($d);},
        'hidden'=>''
    ),
    'archy_tag.updated_at'=>array(
        'label'=>'Ultima modifica',
        'type'=>'date',
        'formatter'=>function($d,$row){return App::latin_date_time($d);},
        'hidden'=>''
    )
);

$intent->default_columns=array(
    'archy_tag.id',
    'archy_tag.tag',
    'archy_tag.categoria',
    'archy_tag.status'
);

/*
|--------------------------------------------------------------------------
| AUTOCOMPILAZIONE SLUG
|--------------------------------------------------------------------------
|
| Se lo slug viene lasciato vuoto, viene generato lato extra_action.
|
*/
$intent->extra_action='store_tag_slug';

$app->merge_intents($intent);
?>
