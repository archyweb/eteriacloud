<?php

$intent=new stdClass();
$intent->table='archy_users';
$intent->title='Utenti';
$intent->singular='Utente';
$intent->add='Nuovo utente';
$intent->view_title='Utente: archy_users.name archy_users.surname';
$intent->joins=[];
$intent->where="archy_users.id > 2";
$intent->columns=array(
    'archy_users.id'=>array(
            'label'=>'ID',
            'type'=>'text',
            'hidden'=>''
    ),
    'archy_users.email'=>array(
            'label'=>'E-mail',
            'type'=>'email',
            'required'=>true
    ),
    'archy_users.password'=>array(
            'label'=>'Password',
            'type'=>'password',
            'required'=>true,
            'formatter'=>function($d,$row){return "";}
    ),
    'archy_users.name'=>array(
            'label'=>'Nome',
            'type'=>'text',
            'required'=>true
    ),	
    'archy_users.surname'=>array(
            'label'=>'Cognome',
            'type'=>'text',
            'required'=>true
    ),    
    'archy_users.created_at'=>array(
            'label'=>'Data Creazione',
            'type'=>'date',
            'formatter'=>function($d,$row){return App::latin_date_time($d);},
            'hidden'=>''
    ),
    'archy_users.updated_at'=>array(
            'label'=>'Ultima modifica',
            'type'=>'date',
            'formatter'=>function($d,$row){return App::latin_date_time($d);},
            'hidden'=>''
    )
);
$intent->default_columns=array(
	'archy_users.id',
	'archy_users.name',
	'archy_users.surname',
	'archy_users.email',
);
$intent->include_in_form='intents/includes/users.php';
$intent->extra_action='store_credentials';
$app->merge_intents($intent);