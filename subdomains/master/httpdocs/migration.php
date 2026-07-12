<?php

ini_set('memory_limit', '512M');
set_time_limit(0);

/*
|--------------------------------------------------------------------------
| CONFIG DB
|--------------------------------------------------------------------------
*/

$oldDbConfig = [
    'host'     => 'localhost',
    'dbname'   => 'archyart_archiviobcm',
    'username' => 'hbKgqcpD9K',
    'password' => 'r8B6emV+e4e7gAC',
];

$newDbConfig = [
    'host'     => 'localhost',
    'dbname'   => 'archiviobcm_archyart',
    'username' => 'bTE5cBBGCY',
    'password' => 'ah6eNEB2NT-c77E',
];

 /*
|--------------------------------------------------------------------------
| FUNZIONI AGGIUNTIVE 
|--------------------------------------------------------------------------
*/

function normalizePlaceholders($text)
{
    return preg_replace_callback('/\[([^\]]+)\]/u', function ($matches) {

        $placeholder = $matches[1];

        /*
        |--------------------------------------------------------------------------
        | TOGLIE ACCENTI
        |--------------------------------------------------------------------------
        */

        $normalized = str_replace(
            ['à', 'á', 'è', 'é', 'ì', 'í', 'ò', 'ó', 'ù', 'ú'],
            ['a', 'a', 'e', 'e', 'i', 'i', 'o', 'o', 'u', 'u'],
            $placeholder
        );

        /*
        |--------------------------------------------------------------------------
        | EVENTUALE NORMALIZZAZIONE EXTRA
        |--------------------------------------------------------------------------
        */

        $normalized = preg_replace('/[^a-zA-Z0-9_]/', '_', $normalized);

        /*
        |--------------------------------------------------------------------------
        | RIMETTE LE PARENTESI
        |--------------------------------------------------------------------------
        */

        return '[' . $normalized . ']';

    }, $text);
}

function transformSettingsFields($value)
{
    $data = @unserialize($value);

    if (!is_array($data)) {
        return $value;
    }

    /*
    |--------------------------------------------------------------------------
    | TOGLIE PRIMA OPZIONE
    |--------------------------------------------------------------------------
    */

    unset($data[0]);

    /*
    |--------------------------------------------------------------------------
    | TRASFORMA I VALORI
    |--------------------------------------------------------------------------
    */

    $newData = [];

    foreach ($data as $key => $field) {

        $field = normalizePlaceholders("[$field]");

        $newData[$key] = $field;

    }

    return serialize($newData);
}

function cleanEncoding($value)
{
    if (!is_string($value)) {
        return $value;
    }

    /*
    |--------------------------------------------------------------------------
    | DECODIFICA HTML ENTITIES
    |--------------------------------------------------------------------------
    */

    $value = html_entity_decode(
        $value,
        ENT_QUOTES | ENT_HTML5,
        'UTF-8'
    );

    /*
    |--------------------------------------------------------------------------
    | CONVERTE DA UTF8MB3 / LATIN
    |--------------------------------------------------------------------------
    */

    $value = mb_convert_encoding(
        $value,
        'UTF-8',
        'UTF-8, ISO-8859-1, Windows-1252'
    );

    /*
    |--------------------------------------------------------------------------
    | NBSP
    |--------------------------------------------------------------------------
    */

    $value = str_replace(
        [
            "\xc2\xa0",
            chr(160),
            '&nbsp;'
        ],
        ' ',
        $value
    );

    /*
    |--------------------------------------------------------------------------
    | SPAZI STRANI UNICODE
    |--------------------------------------------------------------------------
    */

    $value = preg_replace('/\x{00A0}/u', ' ', $value);

    /*
    |--------------------------------------------------------------------------
    | CARATTERI INVALIDI
    |--------------------------------------------------------------------------
    */

    $value = iconv(
        'UTF-8',
        'UTF-8//IGNORE',
        $value
    );

    /*
    |--------------------------------------------------------------------------
    | NORMALIZZA SPAZI
    |--------------------------------------------------------------------------
    */

    $value = preg_replace('/[ \t]+/', ' ', $value);

    return trim($value);
}

/*
|--------------------------------------------------------------------------
| CONFIG TABELLE
|--------------------------------------------------------------------------
|
| old_table => [
|     'table' => 'new_table',
|     'mapping' => [
|         'vecchio_campo' => 'nuovo_campo'
|     ]
| ]
|
*/
$idMaps = [];
$tables = [

    'archy_author' => [
        'table' => 'archy_art_authors'
    ],
    'archy_certificati_model' => [
        'table' => 'archy_models',

        'mapping' => [
            'category' => 'model_title' // funzione per mappare un campo che ha cambiato nome
        ],
        'fixed_values' => [
            'model_intent' => 'archy_art_certificates' // funzione per inserire nel campo di destinazione un valore fisso
        ],
        'register_id_map' => 'archy_certificati_model',
        'ignore_fields' => [
            'id'
        ],
        'min_id' => 4 // funzione per caricare solo i modelli nuovi creati
    ],
    'archy_cessione_immagine_model' => [
        'table' => 'archy_models',

        'mapping' => [
            'category' => 'model_title',
            'modello' => 'model_archive'
        ],
        'fixed_values' => [
            'model_intent' => 'archy_art_cessione_immagine'
        ],
        'register_id_map' => 'archy_cessione_immagine_model',
        'ignore_fields' => [
            'id'
        ],
        'min_id' => 1
    ],
    'archy_cessione_opera_model' => [
        'table' => 'archy_models',

        'mapping' => [
            'category' => 'model_title',
            'modello' => 'model_archive'
        ],
        'fixed_values' => [
            'model_intent' => 'archy_art_cessione_opera'
        ],
        'register_id_map' => 'archy_cessione_opera_model',
        'ignore_fields' => [
            'id'
        ],
        'min_id' => 1
    ],
    'archy_condition_report_model' => [
        'table' => 'archy_models',

        'mapping' => [
            'category' => 'model_title',
            'modello' => 'model_archive'
        ],
        'fixed_values' => [
            'model_intent' => 'archy_art_condition_report'
        ],
        'register_id_map' => 'archy_condition_report_model',
        'ignore_fields' => [
            'id'
        ],
        'min_id' => 2
    ],
    'archy_contratto_valutazione_model' => [
        'table' => 'archy_models',

        'mapping' => [
            'category' => 'model_title',
            'modello' => 'model_archive'
        ],
        'fixed_values' => [
            'model_intent' => 'archy_art_contratto_valutazione'
        ],
        'register_id_map' => 'archy_contratto_valutazione_model',
        'ignore_fields' => [
            'id'
        ],
        'min_id' => 1
    ],
    'archy_diritti_riproduzione_model' => [
        'table' => 'archy_models',

        'mapping' => [
            'category' => 'model_title',
            'modello' => 'model_archive'
        ],
        'fixed_values' => [
            'model_intent' => 'archy_art_diritto_riproduzione_immagine'
        ],
        'register_id_map' => 'archy_diritti_riproduzione_model',
        'ignore_fields' => [
            'id'
        ],
        'min_id' => 1
    ],
    'archy_donazione_model' => [
        'table' => 'archy_models',

        'mapping' => [
            'category' => 'model_title',
            'modello' => 'model_archive'
        ],
        'fixed_values' => [
            'model_intent' => 'archy_art_donazione'
        ],
        'register_id_map' => 'archy_donazione_model',
        'ignore_fields' => [
            'id'
        ],
        'min_id' => 1
    ],
    'archy_noleggio_model' => [
        'table' => 'archy_models',

        'mapping' => [
            'category' => 'model_title',
            'modello' => 'model_archive'
        ],
        'fixed_values' => [
            'model_intent' => 'archy_art_noleggio'
        ],
        'register_id_map' => 'archy_noleggio_model',
        'ignore_fields' => [
            'id'
        ],
        'min_id' => 1
    ],
    'archy_prestito_model' => [
        'table' => 'archy_models',

        'mapping' => [
            'category' => 'model_title',
            'modello' => 'model_archive'
        ],
        'fixed_values' => [
            'model_intent' => 'archy_art_prestito'
        ],
        'register_id_map' => 'archy_prestito_model',
        'ignore_fields' => [
            'id'
        ],
        'min_id' => 1
    ],
    'archy_prestito_definitivo_model' => [
        'table' => 'archy_models',

        'mapping' => [
            'category' => 'model_title',
            'modello' => 'model_archive'
        ],
        'fixed_values' => [
            'model_intent' => 'archy_art_prestito_definitivo'
        ],
        'register_id_map' => 'archy_prestito_definitivo_model',
        'ignore_fields' => [
            'id'
        ],
        'min_id' => 1
    ],
    'archy_prestito_promessa_model' => [
        'table' => 'archy_models',

        'mapping' => [
            'category' => 'model_title',
            'modello' => 'model_archive'
        ],
        'fixed_values' => [
            'model_intent' => 'archy_art_prestito_promessa'
        ],
        'register_id_map' => 'archy_prestito_promessa_model',
        'ignore_fields' => [
            'id'
        ],
        'min_id' => 1
    ],
    'archy_privacy_model' => [
        'table' => 'archy_models',

        'mapping' => [
            'category' => 'model_title',
            'modello' => 'model_archive'
        ],
        'fixed_values' => [
            'model_intent' => 'archy_art_privacy'
        ],
        'register_id_map' => 'archy_privacy_model',
        'ignore_fields' => [
            'id'
        ],
        'min_id' => 1
    ],
    'archy_scheda_catalogo_model' => [
        'table' => 'archy_models',

        'mapping' => [
            'category' => 'model_title'
        ],
        'fixed_values' => [
            'model_intent' => 'archy_art_catalog'
        ],
        'register_id_map' => 'archy_scheda_catalogo_model',
        'ignore_fields' => [
            'id'
        ],
        'min_id' => 1
    ],
    'archy_scheda_catalogo' => [
        'table' => 'archy_art_catalog',

        'mapping' => [
            'modello_id' => 'model_id',
            'unsigned_report' => 'model_template'
        ],
        'foreign_key_mapping' => [
            'model_id' => [
                'map' => 'archy_scheda_catalogo_model'
            ]

        ]
    ],
    'archy_certificati' => [
        'table' => 'archy_art_certificates',

        'mapping' => [
            'modello_id' => 'model_id'
        ],
        'foreign_key_mapping' => [
            'model_id' => [
                'map' => 'archy_certificati_model'
            ]

        ]
    ],
    // 'archy_cessione_immagine' => ['table' => 'archy_art_cessione_immagine']
    'archy_cessione_opera' => [
        'table' => 'archy_art_cessione_opera',

        'mapping' => [
            'modello_id' => 'model_id',
            'unsigned_report' => 'model_template'
        ],
        'foreign_key_mapping' => [
            'model_id' => [
                'map' => 'archy_cessione_opera_model'
            ]

        ]
    ],
    'archy_collezioni' => [
        'table' => 'archy_art_collections'
    ],
    'archy_condition_report' => [
        'table' => 'archy_art_condition_report',

        'mapping' => [
            'modello_id' => 'model_id',
            'unsigned_report' => 'model_template'
        ],
        'foreign_key_mapping' => [
            'model_id' => [
                'map' => 'archy_condition_report_model'
            ]

        ]
    ],
    'archy_contratto_valutazione' => [
        'table' => 'archy_art_contratto_valutazione',

        'mapping' => [
            'modello_id' => 'model_id',
            'unsigned_report' => 'model_template'
        ]
    ],
    // 'archy_diritto_riproduzione_immagine' => ['table' => 'archy_art_diritto_riproduzione_immagine']
    // 'archy_donazione' => ['table' => 'archy_art_donazione']
    'archy_esposizione' => [
        'table' => 'archy_art_exhibitions'
    ],
    'archy_noleggio' => [
        'table' => 'archy_art_noleggio',

        'mapping' => [
            'modello_id' => 'model_id',
            'unsigned_report' => 'model_template'
        ],
        'foreign_key_mapping' => [
            'model_id' => [
                'map' => 'archy_noleggio_model'
            ]

        ]
    ],
    // 'archy_prestito' => ['table' => 'archy_art_prestito']
    // 'archy_prestito_definitivo' => ['table' => 'archy_art_prestito_definitivo']
    // 'archy_prestito_promessa' => ['table' => 'archy_art_prestito_promessa']
    // 'archy_privacy' => ['table' => 'archy_art_privacy']
    'archy_pubblicazione' => [
        'table' => 'archy_art_publications'
    ],
    'archy_serie' => [
        'table' => 'archy_art_series'
    ],
    'archy_impostazione_scheda_catalogo' => [
        'table' => 'archy_art_settings',
        'fixed_values' => [
            'table_name' => 'archy_art_catalog',
            'table_filter' => 'archy_art_catalog_tipologia_scheda'
        ],
        'custom_transform' => [

            'table_fields' => 'transformSettingsFields' // funzione per trasformare campi durante la migrazione

        ]
    ],
    'archy_stats' => [
        'table' => 'archy_stats',

        'mapping' => [
            'stats_total_video' => 'stats_total_cloud_video',
            'stats_total_docs' => 'stats_total_cloud_attachments',
            'stats_weight_docs' => 'stats_weight_cloud_attachments'
        ]
    ],
    'archy_settings' => [
        'table' => 'archy_settings'
    ],
    'archy_users' => [
        'table' => 'archy_users'
    ],
    'archy_registry' => [
        'table' => 'archy_registry'
    ],
    'archy_registry_categories' => [
        'table' => 'archy_registry_categories'
    ],
    'phpauth_users' => [
        'table' => 'phpauth_users'
    ],
    'archy_files' => [
        'table' => 'archy_files',
        'mapping' => [
            'file_extension' => 'file_media_type'
        ],
        'value_mapping' => [
            'file_media_type' => [
                'jpg' => 'image/jpeg',
                'JPG' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'PNG' => 'image/png'
            ],
        ],
        'fixed_values' => [
            'file_location' => 'cloudImages',
            'file_reference_table' => 'archy_art_catalog',
            'file_extension' => ''
        ]
    ]    

];

/*
|--------------------------------------------------------------------------
| CONNESSIONI
|--------------------------------------------------------------------------
*/

try {

    $oldDb = new PDO(
        "mysql:host={$oldDbConfig['host']};dbname={$oldDbConfig['dbname']};charset=utf8",
        $oldDbConfig['username'],
        $oldDbConfig['password']
    );
    $oldDb->exec("SET NAMES utf8");

    $newDb = new PDO(
        "mysql:host={$newDbConfig['host']};dbname={$newDbConfig['dbname']};charset=utf8mb4",
        $newDbConfig['username'],
        $newDbConfig['password']
    );
    $newDb->exec("SET NAMES utf8mb4");

    $oldDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $newDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connessioni OK\n\n";

} catch (Exception $e) {

    die("Errore connessione DB: " . $e->getMessage());

}

/*
|--------------------------------------------------------------------------
| MIGRAZIONE
|--------------------------------------------------------------------------
*/

foreach ($tables as $oldTable => $tableConfig) {

    $newTable = $tableConfig['table'];
    $mapping = $tableConfig['mapping'] ?? [];
    $fixedValues = $tableConfig['fixed_values'] ?? [];
    $valueMapping = $tableConfig['value_mapping'] ?? [];
    $registerIdMap = $tableConfig['register_id_map'] ?? null;
    $foreignKeyMap = $tableConfig['foreign_key_mapping'] ?? [];
    $ignoreFields = $tableConfig['ignore_fields'] ?? [];
    $minId = $tableConfig['min_id'] ?? null;
    
    $customTransform = $tableConfig['custom_transform'] ?? [];
    
    $isModelTable = str_ends_with($oldTable, '_model');
    
    echo "-----------------------------\n";
    echo "Migrazione: {$oldTable} -> {$newTable}\n";
    echo "-----------------------------\n";
    
    
    try {
        
        
        
        /*
        |--------------------------------------------------------------------------
        | COLONNE NUOVA TABELLA
        |--------------------------------------------------------------------------
        */

        $columnsStmt = $newDb->query("SHOW COLUMNS FROM `$newTable`");

        $newColumns = [];

        while ($column = $columnsStmt->fetch(PDO::FETCH_ASSOC)) {

            $newColumns[] = $column['Field'];

        }

        /*
        |--------------------------------------------------------------------------
        | DATI VECCHIA TABELLA
        |--------------------------------------------------------------------------
        */

        $oldStmt = $oldDb->query("SELECT * FROM `$oldTable`");

        $count = 0;

        while ($row = $oldStmt->fetch(PDO::FETCH_ASSOC)) {
            
            $currentId = $row['id'] ?? null;

            if ($minId && $currentId < $minId) {

                echo "SKIPPED ID {$currentId} ({$oldTable})\n";

                continue;

            }
            
            $insertData = [];

            foreach ($row as $oldField => $value) {
                
                if (in_array($oldField, $ignoreFields)) {
                    continue;
                }
                
                /*
                |--------------------------------------------------------------------------
                | MAPPING CAMPI
                |--------------------------------------------------------------------------
                */

                $newField = $mapping[$oldField] ?? $oldField;
                
                
                /*
                |--------------------------------------------------------------------------
                | MAPPING ID MODELLI PER IL NUOVO ID
                |--------------------------------------------------------------------------
                */
                if (isset($foreignKeyMap[$newField])) {

                    $mapName = $foreignKeyMap[$newField]['map'];

                    $oldForeignId = $value;

                    if (isset($idMaps[$mapName][$oldForeignId])) {

                        $value = $idMaps[$mapName][$oldForeignId];

                        echo "FK REMAP {$newField}: {$oldForeignId} -> {$value}\n";

                    } else {

                        echo "FK MAP NOT FOUND: {$mapName} -> {$oldForeignId}\n";

                    }

                }
                
                /*
                |--------------------------------------------------------------------------
                | CONTROLLO ESISTENZA CAMPO
                |--------------------------------------------------------------------------
                */

                if (!in_array($newField, $newColumns)) {

                    continue;

                }

                if (
                    isset($valueMapping[$newField]) &&
                    array_key_exists($value, $valueMapping[$newField])
                ) {

                    $value = $valueMapping[$newField][$value];

                }
                
                if ($isModelTable && is_string($value)) {

                    $value = normalizePlaceholders($value);

                }
                
                if (isset($customTransform[$oldField])) {

                    $functionName = $customTransform[$oldField];

                    if (function_exists($functionName)) {

                        $value = $functionName($value);

                    }

                }

                $insertData[$newField] = $value;

            }
            
            foreach ($fixedValues as $field => $value) {

                if (in_array($field, $newColumns)) {

                    $insertData[$field] = $value;

                }

            }

            /*
            |--------------------------------------------------------------------------
            | SALTA RECORD VUOTI
            |--------------------------------------------------------------------------
            */

            if (empty($insertData)) {

                continue;

            }
            
            if ($uniqueBy && isset($insertData[$uniqueBy])) {

                    $checkStmt = $newDb->prepare("
                        SELECT id
                        FROM `$newTable`
                        WHERE `$uniqueBy` = :value
                        LIMIT 1
                    ");

                    $checkStmt->execute([
                        'value' => $insertData[$uniqueBy]
                    ]);

                    $existingRecord = $checkStmt->fetch(PDO::FETCH_ASSOC);

                    /*
                    |--------------------------------------------------------------------------
                    | RECORD GIÀ ESISTENTE
                    |--------------------------------------------------------------------------
                    */

                    if ($existingRecord) {

                        $existingId = $existingRecord['id'];

                        echo "ALREADY EXISTS: {$insertData[$uniqueBy]} -> ID {$existingId}\n";

                        /*
                        |--------------------------------------------------------------------------
                        | SALVA MAPPA
                        |--------------------------------------------------------------------------
                        */

                        if ($registerIdMap) {

                            $oldId = $row['id'] ?? null;

                            if ($oldId) {

                                $idMaps[$registerIdMap][$oldId] = $existingId;

                            }

                        }

                        continue;

                    }

                }

            /*
            |--------------------------------------------------------------------------
            | INSERT
            |--------------------------------------------------------------------------
            */

            $fields = array_keys($insertData);

            $placeholders = [];
            $executeData  = [];

            foreach ($fields as $field) {

                $placeholder = 'field_' . $field;

                $placeholders[] = ':' . $placeholder;

                $executeData[$placeholder] = $insertData[$field];

            }

            $sql = "
                INSERT INTO `$newTable`
                (`" . implode('`,`', $fields) . "`)
                VALUES
                (" . implode(',', $placeholders) . ")
            ";
            
            if (count($fields) != count($executeData)) {

                echo "Mismatch fields/executeData\n";

                print_r($fields);
                print_r($executeData);

            }

            $insertStmt = $newDb->prepare($sql);

            try {
                
                

                $insertStmt->execute($executeData);
                
                if ($registerIdMap) {

                    $oldId = $row['id'] ?? null;
                    $newId = $newDb->lastInsertId();

                    if ($oldId) {

                        $idMaps[$registerIdMap][$oldId] = $newId;

                        echo "MAP {$registerIdMap}: {$oldId} -> {$newId}\n";

                    }

                }

            } catch (Exception $e) {

                echo "\n";
                echo "================ ERROR ================\n";
                echo "Tabella origine: {$oldTable}\n";
                echo "Tabella destinazione: {$newTable}\n";

                if (isset($row['id'])) {
                    echo "Record ID: {$row['id']}\n";
                }

                echo "\nSQL:\n";
                echo $sql . "\n\n";

                echo "FIELDS:\n";
                print_r($fields);

                echo "\nINSERT DATA:\n";
                print_r($insertData);

                echo "\nEXECUTE DATA:\n";
                print_r($executeData);

                echo "\nERRORE PDO:\n";
                echo $e->getMessage() . "\n";

                echo "=======================================\n\n";

                /*
                |--------------------------------------------------------------------------
                | LOG FILE
                |--------------------------------------------------------------------------
                */

                file_put_contents(
                    'migration_errors.log',
                    "\n================ ERROR ================\n" .
                    "Tabella origine: {$oldTable}\n" .
                    "Tabella destinazione: {$newTable}\n" .
                    "SQL: {$sql}\n" .
                    "Errore: {$e->getMessage()}\n" .
                    "InsertData: " . print_r($insertData, true) . "\n" .
                    "=======================================\n",
                    FILE_APPEND
                );

                continue;

            }   

            $count++;

            if (($count % 100) == 0) {

                echo "{$count} record importati...\n";

            }

        }

        echo "Completato: {$count} record importati\n\n";

    } catch (Exception $e) {

        echo "Errore tabella {$oldTable}: " . $e->getMessage() . "\n\n";

    }

}

echo "=============================\n";
echo "MIGRAZIONE TERMINATA\n";
echo "=============================\n";