<?php

// Feed per pubblicazione su web (plugin Joomla e Wordpress)

// Chiave di accesso all'esportazione si trova in Impostazioni Associazione / Opzioni di pubblicazione

// URL: http://dev.archy.test/public/feed/?archy_id=dev&intent=memberships&key=a4e73712b9c8b4752bf47df7e345b41bdd85de3c
// URL: http://localhost/public/feed/?archy_id=dev&intent=events&key=df3bcb8645657d6cdfdd219f9735e5a6337f01c6

/*
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
*/

$main_root = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));
$subdomain_root = dirname(dirname(dirname(__FILE__)));
require_once $main_root . '/private/_common/classes/load_classes.php';

$app = new App($subdomain_root);
$settings=$app->get_settings($domain='association_1');

if(@$_GET['archy_id']!=''):
    if(@$_GET['intent']=='memberships'):
        if($settings->public_memberships and @$_GET['key']==hash('ripemd160', 'FrateIndovino2021_'.$_SERVER['HTTP_HOST'])):
            $membership=new Membership();
            $membership->feed();
        else:
            echo json_encode(['error'=>'Not allowed']);
        endif;
    endif;
    if(@$_GET['intent']=='events'):
        if($settings->public_activities and @$_GET['key']==hash('ripemd160', 'FrateIndovino2021_'.$_SERVER['HTTP_HOST'])):
            $event=new Event();
            $event->feed();
        else:
            echo json_encode(['error'=>'Not allowed']);
        endif;
    endif;
else:
	echo json_encode(['error'=>'Bad call']); // Bad archy_id
endif;
