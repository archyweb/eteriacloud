<?php

$intent=new stdClass();
$intent->table='archy_cost_centres';//nome tabella data-base
$intent->title='Voci contabili';//titolo (plurale)
$intent->singular='Voce contabile';//titolo (singolare)
$intent->add='Nuova voce contabile';//testo usato nei forms e nei pulsanti
$intent->view_title='Voce contabile: archy_cost_centres.cost_centre';//Titolo nella visualizzazione/modifica del singolo record
$intent->joins=['archy_cost_centres_categories'=>'archy_cost_centres.category_id=archy_cost_centres_categories.id'];//tabelle collegate
$intent->columns=array(//colonne della tabella principale
	'archy_cost_centres.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''//nasconde il campo nel modulo di creazione/aggiornamento di un record
	),
    'archy_cost_centres.codice'=>array(
		'label'=>'Codice',
		'type'=>'text',
        'attributes'=>['maxlength="12"'],
	),
	'archy_cost_centres.cost_centre'=>array(
		'label'=>'Voce contabile',
		'type'=>'text',
		'required'=>true,
		'grid_search_type'=>'search'
	),   
	'archy_cost_centres.cost_centre_type'=>array(
		'label'=>'Tipologia',
		'type'=>'select',
		'options_type'=>'array',
		'options'=>['0'=>'Istituzionale','1'=>'Commerciale','2'=>'Tecnica','3'=>'Neutra'],
		'formatter'=>function($d,$row){
			$options=['0'=>'Istituzionale','1'=>'Commerciale','2'=>'Tecnica','3'=>'Neutra'];
			return $d!=''? $options[$d] : '';
		},
		'formatter_hide_in_form'=>true
	),
    'archy_cost_centres.voce_bilancio'=>array(
		'label'=>'Descrizione voce bilancio (Civilistico/Ifrs)',
		'type'=>'text',
	),
	'archy_cost_centres.category_id'=>array(
		'label'=>'Categoria contabile',
		'type'=>'search',
		'intent'=>'archy_cost_centres_categories',
		'lookup_text'=>'category'
	),
	'archy_cost_centres.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_cost_centres.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);

if($app->config['association']):
    $intent->undeletable_ids=array_column(mysqli_fetch_all($app->db->query(
        "SELECT DISTINCT petty_cash_purpose_id FROM archy_petty_cash_book WHERE petty_cash_purpose_id IS NOT NULL;"
    ),MYSQLI_ASSOC),'petty_cash_purpose_id');
        
    if($app->is_admin==true) {
        //$intent->uneditable_ids=range(1,5);
    } else {
        $intent->uneditable_ids=range(1,5);
    }
endif;

$intent->default_columns=array(//colonne del db da visualizzare di default nell'elenco (griglia)
	'archy_cost_centres.id','archy_cost_centres.cost_centre','archy_cost_centres_categories.category','archy_cost_matrice.matrice'//inizia sempre con l'id di modo che i nuovi record siano visualizzati in cima alla tabella
);

$app->merge_intents($intent);//crea i collegamenti, a cascata, con le altre tabelle
?>