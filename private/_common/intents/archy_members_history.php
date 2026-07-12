<?php
$intent=new stdClass();
$intent->table='archy_members_history';
$intent->title='Libro Soci';
$intent->singular='Voce libro soci';
//$intent->add='Nuova voce';
$intent->view_title='Libro Soci: archy_members_history.full_name';
$intent->joins=[];

$gender_options=[''=>'Non definito','1'=>'Maschile','0'=>'Femminile'];

$membership=new Membership();
$status_options=$membership->statuses;

$intent->columns=array(
	'archy_members_history.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''
	),
	'archy_members_history.card'=>array(
		'label'=>'N° Tessera',
        'type'=>'text',
        'attributes'=>['readonly'],
		//'required'=>true
	),
	'archy_members_history.request_date'=>array(
		'label'=>'Data richiesta iscrizione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date($d);},
        'default_value'=>date('d/m/Y'),
        'attributes'=>['readonly'],
		//'required'=>true
	),
	'archy_members_history.acceptance_date'=>array(
		'label'=>'Data ammissione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date($d);},
        'default_value'=>date('d/m/Y'),
        'attributes'=>['readonly'],
		//'required'=>true
	),
	'archy_members_history.full_name'=>array(
		'label'=>'Nominativo',
        'type'=>'text',
        'attributes'=>['readonly'],
		//'required'=>true
	),
	'archy_members_history.birth_date'=>array(
		'label'=>'Data nascita',
		'type'=>'date',
        'formatter'=>function($d,$row){return App::latin_date($d);},
        'attributes'=>['readonly'],
		//'required'=>true
	),
	'archy_members_history.birth_place'=>array(
		'label'=>'Luogo nascita',
        'type'=>'text',
        'attributes'=>['readonly'],
		//'required'=>true
	),
	'archy_members_history.tax_code'=>array(
		'label'=>'Codice Fiscale',
        'type'=>'text',
        'attributes'=>['readonly'],
		//'required'=>true
	),
	'archy_members_history.address'=>array(
		'label'=>'Indirizzo',
        'type'=>'textarea',
        'attributes'=>['readonly'],
		//'required'=>true
	),
	'archy_members_history.zip'=>array(
		'label'=>'CAP',
        'type'=>'text',
        'attributes'=>['readonly'],
		//'required'=>true
	),
	'archy_members_history.city'=>array(
		'label'=>'Città',
        'type'=>'text',
        'attributes'=>['readonly'],
		//'required'=>true
	),
	'archy_members_history.status'=>array(
		'label'=>'Status (cessato)',
		'type'=>'select', 
		'options'=>$status_options,
		'options_type'=>'array',
		'formatter'=>function($d,$row){
			global $status_options;
			switch($d){
				case '1': $class='text-success'; break;
				case '2':
				case '5': $class='text-warning'; break;
				case '3': $class='text-danger'; break;
				case '4':
				case '':  $class=''; break;
			}
			return $d!=''? '<span class="glyphicon glyphicon-tag '.$class.'"></span> '.$status_options[$d] : '';
		},
		'formatter_hide_in_form'=>true,
        'formatter_hide_in_excel'=>true,
        'attributes'=>['readonly'],
	),
	'archy_members_history.category'=>array(
		'label'=>'Categoria associato',
        'type'=>'text',
        'attributes'=>['readonly'],
		//'required'=>true
	),
	'archy_members_history.resignation_date'=>array(
		'label'=>'Data cessazione',
		'type'=>'date',
        'formatter'=>function($d,$row){return App::latin_date($d);},
        'attributes'=>['readonly'],
	),
	'archy_members_history.resignation_reason'=>array(
		'label'=>'Motivo cessazione',
        'type'=>'textarea',
        'attributes'=>['readonly'],
	),
	'archy_members_history.created_at'=>array(
		'label'=>'Data Creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_members_history.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
);

// Il libro soci non va modificato/cancellato
$all_ids=array_column(mysqli_fetch_all($app->db->query("SELECT id FROM archy_members_history"),MYSQLI_ASSOC),'id');
$intent->uneditable_ids=$all_ids;
$intent->noinsert=true; // Serve per togliere + nella view (lentina)
//$intent->undeletable_ids=$all_ids;

$intent->default_columns=array(//colonne del db da visualizzare di default nell'elenco (griglia)
	'archy_members_history.card',
	'archy_members_history.full_name',
	'archy_members_history.tax_code',
	'archy_members_history.address',
	'archy_members_history.city',
	'archy_members_history.acceptance_date',
	'archy_members_history.resignation_date'
);
$intent->include_in_form='intents/includes/members_history.php';
$app->merge_intents($intent);
?>