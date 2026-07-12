<?php

ini_set('display_errors',0);
error_reporting(E_ERROR | E_PARSE);

header('Content-Type: application/json');


$config = require dirname(dirname(__DIR__)) . '/config-local.php';

$db = new mysqli(
    $config['db']['host'],
    $config['db']['user'],
    $config['db']['pass'],
    $config['db']['name']
);

if($db->connect_error){
    die(json_encode([
        'success'=>false,
        'message'=>'Errore connessione DB'
    ]));
}


/*
|--------------------------------------------------------------------------
| RECUPERO ID
|--------------------------------------------------------------------------
*/

$ids=[];

if(!empty($_POST['ids'])){

    $ids=array_filter(
        array_map(
            'intval',
            explode(',',$_POST['ids'])
        )
    );

}

if(empty($ids)){

    echo json_encode([
        'success'=>false,
        'message'=>'Nessun ID ricevuto.'
    ]);

    exit;
}

$intent='archy_art_catalog';

if(!empty($_POST['intent'])){
    $intent=$db->real_escape_string($_POST['intent']);
}

$payload=json_encode([
    'intent'=>$intent,
    'ids'=>array_values($ids),
    'batch_size'=>25
]);

$total=count($ids);

/*
|--------------------------------------------------------------------------
| CREA JOB
|--------------------------------------------------------------------------
*/

$db->query("
INSERT INTO archy_jobs
(
job_type,
status,
payload,
progress,
total,
created_at,
log
)
VALUES
(
'regenerate_attachments',
'processing',
'".$db->real_escape_string($payload)."',
0,
".$total.",
NOW(),
'Avvio rigenerazione allegati'
)
");

$jobId=$db->insert_id;

/*
|--------------------------------------------------------------------------
| AVVIO WORKER
|--------------------------------------------------------------------------
*/

echo json_encode([
    'success'=>true,
    'job_id'=>$jobId,
    'total'=>$total
]);

exit;