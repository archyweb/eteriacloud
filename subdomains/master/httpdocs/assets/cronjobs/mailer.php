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

global $app;
$app=new App($subdomain_root);
$queue=new Queue();

$settings=$app->get_settings($domain='notifications');

$messageToSend=$settings->smtp_totalmail;
$delaySec=$settings->smtp_delay;
if(!is_numeric($messageToSend)||$messageToSend<0||$messageToSend>10000){
    $messageToSend=10;
}
if(!is_numeric($delaySec)||$delaySec<0||$delaySec>30){
    $delaySec=1;
}

$cnt=0;
while($cnt<$messageToSend):
    $ret=$queue->dequeue('callbackEmail');
    if($ret==0){
        break;
    }
    set_time_limit(0);  // no limit
    sleep($delaySec);
    $cnt++;
endwhile;

function callbackEmail($p) {
    $msg=new Message();
    //error_log("@@@@@@@@@@@@@ callbackEmail: ".print_r($p,true));
    return $msg->email($p);
}