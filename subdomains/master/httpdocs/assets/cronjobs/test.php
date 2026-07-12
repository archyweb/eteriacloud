<?php

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

$main_root = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));
$subdomain_root = dirname(dirname(dirname(__FILE__)));
require_once $main_root . '/private/_common/classes/load_classes.php';

$app = new App($subdomain_root);

$settings=$app->get_settings($domain='association_1');

$subdomain=explode('/',$subdomain_root);

$dir=$subdomain[5];
$exp=explode('.',$dir);
$name=array_shift($exp);

// Aggiorna stato servizi
//$service_membership=new ServiceMembership();
//$service_membership->check_status();

// Aggiorna stato membership
//$membership=new Membership();
//$membership->check_status();

//Aggiorna notifiche eventi
/*$reminders=new Reminder();
$reminders->check_reminders_status();*/

/*$document = ['content' => 'Company name : [name]. Address: [address], [zip_code] [city].'];
$documentText = Placeholder::load($document['content'])->checkReplace('services');

echo $documentText;
 
 */

function ciao($origin_id){
   // $origin_id=6;
    global  $app;
    $date=new DateTime();
    $rows = $app->db->query("select * from archy_events_subscriptions");
    $subscriptions = [];
    while ($event_subscription = mysqli_fetch_object($rows)) :
        $session_ids=array_keys(unserialize($event_subscription->sessions));
        if(in_array($origin_id,$session_ids)):
            $subscriptions[]=$event_subscription->subscriber_id;
        endif;
    endwhile;

    print_r($subscriptions);
    
    $q=$app->db->query("select sub.*, s.*, r.*, m.*, e.*, l.*, spc.*
    from archy_events_subscriptions as sub
    left join archy_events_sessions as s on (s.id=".$origin_id.")
    left join archy_events_speeches as spc on (s.id=spc.session_id)
    left join archy_events_speakers as spk on (spk.id REGEXP CONCAT_WS('','.s:[0-9];s:', spc.speakers ,';.'))
    left join archy_events as e on (sub.event_id=e.id)
    left join archy_events_locations as l on (s.location_id=l.id)
    left join archy_registry as r on (sub.subscriber_id=r.id)
    left join archy_memberships as m on (sub.subscriber_id=m.registry_id)
    where sub.subscriber_id IN(".implode(",", $subscriptions).") and s.start_date='".$date->format('Y-m-d')."'");

    while($item=mysqli_fetch_object($q)):

        echo $item->label.'('.$item->card.')<br>';

    endwhile;
}

$origin_id= array(6,7);
foreach($origin_id as $id){
    ciao($id);
    
}

/*
left join archy_registry as r on (sub.subscriber_id=r.id)
left join archy_memberships as m on (sub.subscriber_id=m.registry_id)
left join archy_events as e on (sub.event_id=e.id)
left join archy_events_locations as l on (s.location_id=l.id)
left join archy_events_speeches as sp on (s.id=sp.session_id)
 */