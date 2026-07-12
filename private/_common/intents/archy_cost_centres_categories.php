<?php

$intent=new stdClass();
$intent->table='archy_cost_centres_categories';//nome tabella data-base
$intent->title='Categorie contabili';//titolo (plurale)
$intent->singular='Categoria contabile';//titolo (singolare)
$intent->add='Nuova categoria contabile';//testo usato nei forms e nei pulsanti
$intent->view_title='Categoria: archy_cost_centres_categories.category';//Titolo nella visualizzazione/modifica del singolo record
$intent->joins=['archy_cost_matrice'=>'archy_cost_centres_categories.cost_matrice_id=archy_cost_matrice.id'];//tabelle collegate
$intent->columns=array(//colonne della tabella principale
	'archy_cost_centres_categories.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''//nasconde il campo nel modulo di creazione/aggiornamento di un record
	),
    'archy_cost_centres_categories.codice'=>array(
		'label'=>'Codice',
		'type'=>'text',
        'attributes'=>['maxlength="8"'],
	),
	'archy_cost_centres_categories.category'=>array(
		'label'=>'Categoria contabile',
		'type'=>'text',
		'required'=>true,
		'grid_search_type'=>'search'
	),
	'archy_cost_centres_categories.cost_matrice_id'=>array(
		'label'=>'Sezione contabile',
		'type'=>'search',
		'intent'=>'archy_cost_matrice',
		'lookup_text'=>'matrice'
	),
	'archy_cost_centres_categories.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_cost_centres_categories.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);

if($app->config['association']):
    if($app->is_admin==true) {
        //$intent->uneditable_ids=range(1,2);
    } else {
        $intent->uneditable_ids=range(1,2);
    }
endif;

$intent->default_columns=array(//colonne del db da visualizzare di default nell'elenco (griglia)
	'archy_cost_centres_categories.id','archy_cost_centres_categories.category','archy_cost_matrice.matrice'//inizia sempre con l'id di modo che i nuovi record siano visualizzati in cima alla tabella
);

$app->merge_intents($intent);//crea i collegamenti, a cascata, con le altre tabelle
?>