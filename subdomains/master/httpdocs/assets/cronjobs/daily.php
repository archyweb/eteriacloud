#!/usr/bin/php
<?php
ini_set('memory_limit', '-1');

// Disabilita timeout
set_time_limit(0);

if (php_sapi_name() !== 'cli') {
  //die("Must be run from command line");
}

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

$main_root = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));
$subdomain_root = dirname(dirname(dirname(__FILE__)));
require_once $main_root . '/private/_common/classes/load_classes.php';

$app = new App($subdomain_root);
$settings=$app->get_settings($domain='association_1');

$subdomain=explode('/',$subdomain_root);
//print_r($subdomain);
$dir=$subdomain[5];
$exp=explode('.',$dir);
$name=array_shift($exp);

// Aggiorna stato servizi
//$service_membership=new ServiceMembership();
//$service_membership->check_status();

// Aggiorna stato membership
$membership=new Membership();
$membership->check_status();

// mette in coda altri promemoria
$reminders=new Reminder();
$reminders->check_reminders_status();

// ADMIN EMAIL
$message=new Message();
$email=[
    'recipient_name'=>'Webmaster',
    'recipient_mail'=>'andrea.umiliani@gmail.com',
    'sender_name'=>$settings->sender_name,
    'sender_mail'=>$settings->sender_email,
    'subject'=>'Check Status DONE! App: '.$settings->sender_name." (".$name." - ".$dir.")",
    'message'=>'Check Status App: '.$settings->sender_name." (name: ".$name." - url: ".$dir." - dir: ".$subdomain_root.")",
];
$message->email($email);

//$budget=new Budget();
//$budget->reminder();
