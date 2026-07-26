<?php

$main_root = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$subdomain_root = dirname(dirname(__FILE__));
require_once $main_root . '/private/_common/classes/load_classes.php';
$app = new App($subdomain_root);

$calendar=new Calendar();

if(isset($_GET['do'])){
    
    if($_GET['do']=='get'){
        echo $calendar->get();
    }elseif($_GET['do']=='add'){
        //print_r($_POST);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['id' => $calendar->add($_POST)]);
    }
    
}

