<?php
/*
|--------------------------------------------------------------------------
| ARCHY ART - VIEW PRESENTATION
|--------------------------------------------------------------------------
|
| Vista grafica/archivistica per scheda catalogo.
| Pensata come alternativa alla view tecnica esistente.
|
| Caratteristiche:
| - hero con foto ufficiale e campi principali;
| - tab dinamiche visibili solo se contengono dati;
| - documenti/allegati in card;
| - riuso della modale upload già presente nel gestionale;
| - query difensive: se una tabella collegata non esiste, non genera errore.
|
*/

$view = $app->load_view($_GET['id']);
$record_id = (int) $_GET['id'];

/*
|--------------------------------------------------------------------------
| HELPERS BASE
|--------------------------------------------------------------------------
*/

if(!function_exists('archy_presentation_to_array')){
    function archy_presentation_to_array($value){
        if(is_array($value)){
            return $value;
        }

        if(!is_string($value)){
            return [$value];
        }

        $trimmed = trim($value);

        if($trimmed === ''){
            return [];
        }

        $unserialized = @unserialize($trimmed);
        if($unserialized !== false || $trimmed === 'b:0;'){
            return is_array($unserialized) ? $unserialized : [$unserialized];
        }

        $json = json_decode($trimmed, true);
        if(json_last_error() === JSON_ERROR_NONE && is_array($json)){
            return $json;
        }

        return [$value];
    }
}

if(!function_exists('archy_presentation_normalize_value')){
    function archy_presentation_normalize_value($value){
        if(is_array($value)){
            $value = array_filter($value, function($v){
                return trim(strip_tags((string)$v)) !== '';
            });
            return implode(', ', $value);
        }

        if(is_string($value)){
            $trimmed = trim($value);

            if($trimmed === ''){
                return '';
            }

            $unserialized = @unserialize($trimmed);
            if($unserialized !== false || $trimmed === 'b:0;'){
                return archy_presentation_normalize_value($unserialized);
            }

            $json = json_decode($trimmed, true);
            if(json_last_error() === JSON_ERROR_NONE && is_array($json)){
                return archy_presentation_normalize_value($json);
            }
        }

        return $value;
    }
}

if(!function_exists('archy_presentation_clean_text')){
    function archy_presentation_clean_text($value){
        $value = archy_presentation_normalize_value($value);

        if(is_string($value)){
            $value = preg_replace('/<\/p>\s*<p[^>]*>/i', "\n", $value);
            $value = preg_replace('/<br\s*\/?>/i', "\n", $value);
            $value = strip_tags($value);
            $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        return trim((string)$value);
    }
}

if(!function_exists('archy_presentation_is_empty')){
    function archy_presentation_is_empty($value){
        $value = trim(strip_tags((string) archy_presentation_normalize_value($value)));

        return (
            $value === '' ||
            $value === '[]' ||
            $value === '{}' ||
            $value === 'a:0:{}' ||
            strtolower($value) === 'null'
        );
    }
}

if(!function_exists('archy_presentation_get_value')){
    function archy_presentation_get_value($view, $keys){
        foreach((array)$keys as $key){
            if(isset($view->data[$key]['value']) && !archy_presentation_is_empty($view->data[$key]['value'])){
                return $view->data[$key]['value'];
            }
        }

        return '';
    }
}

if(!function_exists('archy_presentation_get_label')){
    function archy_presentation_get_label($view, $key, $fallback = ''){
        if(isset($view->data[$key]['label']) && trim((string)$view->data[$key]['label']) !== ''){
            return $view->data[$key]['label'];
        }

        return $fallback !== '' ? $fallback : $key;
    }
}

if(!function_exists('archy_presentation_real_field')){
    function archy_presentation_real_field($field){
        if(strpos($field, '.') !== false){
            $parts = explode('.', $field);
            return end($parts);
        }

        return $field;
    }
}

if(!function_exists('archy_presentation_column_config')){
    function archy_presentation_column_config($app, $field){
        $column_config = $app->cols[$field] ?? [];

        if(empty($column_config) && strpos($field, '.') !== false){
            $parts = explode('.', $field);
            $base = end($parts);
            $column_config = $app->cols[$base] ?? [];
        }

        return $column_config;
    }
}

if(!function_exists('archy_presentation_has_uploads')){
    function archy_presentation_has_uploads($db, $table, $record_id, $field){
        if(!$db || trim((string)$table) === '' || trim((string)$field) === '' || (int)$record_id <= 0){
            return false;
        }

        $table = $db->real_escape_string($table);
        $field = $db->real_escape_string($field);
        $record_id = (int)$record_id;

        $sql = "
            SELECT COUNT(*) AS total
            FROM archy_files
            WHERE file_reference_table = '".$table."'
            AND file_reference_id = ".$record_id."
            AND file_reference_field = '".$field."'
        ";

        $res = $db->query($sql);

        if($res && $row = mysqli_fetch_assoc($res)){
            return ((int)$row['total'] > 0);
        }

        return false;
    }
}

if(!function_exists('archy_presentation_first_upload_url')){
    function archy_presentation_first_upload_url($app, $table, $record_id, $field, $file_target = 'storage'){
        if(!$app || !$app->db || (int)$record_id <= 0){
            return '';
        }

        $db = $app->db;
        $table_safe = $db->real_escape_string($table);
        $field_safe = $db->real_escape_string($field);
        $record_id = (int)$record_id;

        $sql = "
            SELECT *
            FROM archy_files
            WHERE file_reference_table = '".$table_safe."'
            AND file_reference_id = ".$record_id."
            AND file_reference_field = '".$field_safe."'
            ORDER BY id ASC
            LIMIT 1
        ";

        $res = $db->query($sql);
        if(!$res || !($row = mysqli_fetch_assoc($res))){
            return '';
        }

        $candidate_keys = ['file_cloud_id','cloud_id','file_name','filename','name','path','file_path','url','file_url'];
        $file_value = '';

        foreach($candidate_keys as $key){
            if(isset($row[$key]) && trim((string)$row[$key]) !== ''){
                $file_value = trim((string)$row[$key]);
                break;
            }
        }

        if($file_value === ''){
            return '';
        }

        if(preg_match('#^https?://#i', $file_value)){
            return $file_value;
        }

        if($file_target === 'cloudImages' && class_exists('StoreCloud')){
            $StoreCloud = new StoreCloud();
            return $StoreCloud->getImageUrl($file_value, 'public');
        }

        if(class_exists('StoreCloud') && preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $file_value)){
            $StoreCloud = new StoreCloud();
            $file_location = $StoreCloud->getImageDetailFromDb($file_value, 'file_location');
            if($file_location === 'cloudImages'){
                return $StoreCloud->getImageUrl($file_value, 'public');
            }
        }

        if(strpos($file_value, '/assets/') === 0){
            return $file_value;
        }

        if(class_exists('StoreR2Adapter') && preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $file_value)){
            $StoreR2Adapter = new StoreR2Adapter();
            return $StoreR2Adapter->getFileUrl($file_value);
        }

        return 'https://demo-storage.archyart.it/uploads/' . rawurlencode(basename($file_value));
    }
}

if(!function_exists('archy_presentation_table_exists')){
    function archy_presentation_table_exists($db, $table){
        if(!$db || trim((string)$table) === ''){
            return false;
        }

        $table = $db->real_escape_string($table);
        $res = $db->query("SHOW TABLES LIKE '".$table."'");

        return ($res && $res->num_rows > 0);
    }
}

if(!function_exists('archy_presentation_count_related')){
    function archy_presentation_count_related($db, $table, $possible_columns, $record_id){
        if(!archy_presentation_table_exists($db, $table)){
            return 0;
        }

        $record_id = (int)$record_id;

        foreach((array)$possible_columns as $column){
            $column_safe = $db->real_escape_string($column);
            $check = $db->query("SHOW COLUMNS FROM `".$table."` LIKE '".$column_safe."'");

            if($check && $check->num_rows > 0){
                $res = $db->query("SELECT COUNT(*) AS total FROM `".$table."` WHERE `".$column_safe."` = ".$record_id);
                if($res && $row = mysqli_fetch_assoc($res)){
                    return (int)$row['total'];
                }
            }
        }

        return 0;
    }
}


if(!function_exists('archy_presentation_get_tables_by_patterns')){
    function archy_presentation_get_tables_by_patterns($db, $patterns){
        $tables = [];

        if(!$db){
            return $tables;
        }

        foreach((array)$patterns as $pattern){
            $pattern_safe = $db->real_escape_string($pattern);
            $res = $db->query("SHOW TABLES LIKE '".$pattern_safe."'");

            if($res){
                while($row = mysqli_fetch_array($res)){
                    if(isset($row[0]) && trim((string)$row[0]) !== ''){
                        $tables[] = $row[0];
                    }
                }
            }
        }

        return array_values(array_unique($tables));
    }
}

if(!function_exists('archy_presentation_get_related_rows')){
    function archy_presentation_get_related_rows($db, $tables, $possible_columns, $record_id, $limit = 20){
        $rows = [];

        if(!$db || (int)$record_id <= 0){
            return $rows;
        }

        $record_id = (int)$record_id;
        $limit = max(1, (int)$limit);

        foreach((array)$tables as $table){
            $table = trim((string)$table);

            if($table === '' || !archy_presentation_table_exists($db, $table)){
                continue;
            }

            $table_safe = str_replace('`', '', $table);

            foreach((array)$possible_columns as $column){
                $column = trim((string)$column);
                if($column === ''){
                    continue;
                }

                $column_safe = $db->real_escape_string($column);
                $check = $db->query("SHOW COLUMNS FROM `".$table_safe."` LIKE '".$column_safe."'");

                if($check && $check->num_rows > 0){
                    $order = 'id';
                    $order_check = $db->query("SHOW COLUMNS FROM `".$table_safe."` LIKE 'id'");
                    if(!$order_check || $order_check->num_rows == 0){
                        $order = $column;
                    }

                    $res = $db->query("SELECT * FROM `".$table_safe."` WHERE `".$column_safe."` = ".$record_id." ORDER BY `".$order."` DESC LIMIT ".$limit);

                    if($res){
                        while($row = mysqli_fetch_assoc($res)){
                            $row['_archy_related_table'] = $table_safe;
                            $row['_archy_related_column'] = $column;
                            $rows[] = $row;
                        }
                    }

                    // Se abbiamo trovato la colonna di relazione in questa tabella,
                    // non serve provare le altre colonne sulla stessa tabella.
                    break;
                }
            }
        }

        return $rows;
    }
}

if(!function_exists('archy_presentation_first_non_empty')){
    function archy_presentation_first_non_empty($row, $keys){
        foreach((array)$keys as $key){
            if(isset($row[$key]) && trim((string)$row[$key]) !== ''){
                return trim((string)$row[$key]);
            }
        }

        return '';
    }
}

if(!function_exists('archy_presentation_document_url_from_row')){
    function archy_presentation_document_url_from_row($row, $fallback_paths = []){
        $value = archy_presentation_first_non_empty($row, [
            'pdf_url', 'pdf', 'file_pdf', 'pdf_file', 'document_url', 'documento_url',
            'file_url', 'url', 'path', 'file_path', 'filename', 'file', 'documento', 'allegato'
        ]);

        if($value !== ''){
            if(preg_match('#^https?://#i', $value)){
                return $value;
            }

            if(strpos($value, '/') === 0){
                return $value;
            }

            if(strpos($value, 'assets/') === 0){
                return '/'.$value;
            }
        }

        foreach((array)$fallback_paths as $path){
            $path = trim((string)$path);
            if($path !== ''){
                return $path;
            }
        }

        return '';
    }
}

if(!function_exists('archy_presentation_doc_card')){
    function archy_presentation_doc_card($args){
        $title = $args['title'] ?? 'Documento';
        $subtitle = $args['subtitle'] ?? '';
        $icon = $args['icon'] ?? 'far fa-file-alt';
        $button = $args['button'] ?? '';
        $url = $args['url'] ?? '';
        $badge = $args['badge'] ?? '';

        ob_start();
        ?>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="archy-doc-card h-100">
                <div class="archy-doc-icon"><span class="<?php echo htmlspecialchars($icon); ?>"></span></div>
                <div class="archy-doc-title"><?php echo htmlspecialchars($title); ?></div>
                <?php if($subtitle !== ''): ?>
                    <div class="archy-doc-subtitle"><?php echo htmlspecialchars($subtitle); ?></div>
                <?php endif; ?>
                <?php if($badge !== ''): ?>
                    <div class="archy-doc-badge"><?php echo htmlspecialchars($badge); ?></div>
                <?php endif; ?>
                <div class="mt-3">
                    <?php if($button !== ''): ?>
                        <?php echo $button; ?>
                    <?php elseif($url !== ''): ?>
                        <a class="btn btn-sm btn-outline-primary" href="<?php echo htmlspecialchars($url); ?>" target="_blank">Apri</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}

if(!function_exists('archy_presentation_upload_button')){
    function archy_presentation_upload_button($app, $record_id, $field, $label = 'Apri'){
        $real_field = archy_presentation_real_field($field);
        $column_config = archy_presentation_column_config($app, $field);

        $file_location = 'storage';
        if(isset($column_config['file_target']) && trim((string)$column_config['file_target']) !== ''){
            $file_location = $column_config['file_target'];
        }

        return '<button type="button"
                    class="btn btn-sm btn-outline-primary upload-btn archy-view-upload-btn"
                    data-bs-toggle="modal"
                    data-bs-target="#uploads_modal"
                    data-table="'.htmlspecialchars($app->intent->table, ENT_QUOTES, 'UTF-8').'"
                    data-id="'.(int)$record_id.'"
                    data-field="'.htmlspecialchars($real_field, ENT_QUOTES, 'UTF-8').'"
                    data-location="'.htmlspecialchars($file_location, ENT_QUOTES, 'UTF-8').'">'.htmlspecialchars($label, ENT_QUOTES, 'UTF-8').'</button>';
    }
}

if(!function_exists('archy_presentation_field_card')){
    function archy_presentation_field_card($label, $value){
        if(archy_presentation_is_empty($value)){
            return '';
        }

        ob_start();
        ?>
        <div class="col-12 col-md-6 col-xl-4">
            <div class="archy-field-card h-100">
                <div class="archy-field-label"><?php echo htmlspecialchars($label); ?></div>
                <div class="archy-field-value"><?php echo nl2br(htmlspecialchars(archy_presentation_clean_text($value))); ?></div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}

/*
|--------------------------------------------------------------------------
| CONFIGURAZIONE CAMPI HERO
|--------------------------------------------------------------------------
*/

$title = archy_presentation_clean_text(archy_presentation_get_value($view, [
    'archy_art_catalog.titolo_opera',
    'titolo_opera',
    'archy_art_catalog.titolo',
    'titolo'
]));

if($title === ''){
    $title = archy_presentation_clean_text($view->title ?? 'Scheda opera');
}

$author = archy_presentation_clean_text(archy_presentation_get_value($view, [
    'archy_art_catalog.nominativo_autore',
    'nominativo_autore',
    'archy_art_catalog.autore',
    'autore'
]));

$year = archy_presentation_clean_text(archy_presentation_get_value($view, [
    'archy_art_catalog.data',
    'data',
    'archy_art_catalog.data_anno',
    'data_anno',
    'archy_art_catalog.anno',
    'anno'
]));

$archive_number = archy_presentation_clean_text(archy_presentation_get_value($view, [
    'archy_art_catalog.numero_archiviazione',
    'numero_archiviazione',
    'archy_art_catalog.codice',
    'codice'
]));

$inventory_number = archy_presentation_clean_text(archy_presentation_get_value($view, [
    'archy_art_catalog.numero_inventario',
    'numero_inventario',
    'archy_art_catalog.codice_inventario',
    'codice_inventario'
]));

$technique = archy_presentation_clean_text(archy_presentation_get_value($view, [
    'archy_art_catalog.tecnica',
    'tecnica'
]));

$support = archy_presentation_clean_text(archy_presentation_get_value($view, [
    'archy_art_catalog.supporto',
    'supporto'
]));

/*
|--------------------------------------------------------------------------
| DIMENSIONI FORMATTATE
|--------------------------------------------------------------------------
*/

$lunghezza = archy_presentation_clean_text(
    archy_presentation_get_value($view, [
        'archy_art_catalog.dimensioni_lunghezza',
        'dimensioni_lunghezza'
    ])
);

$altezza = archy_presentation_clean_text(
    archy_presentation_get_value($view, [
        'archy_art_catalog.dimensioni_altezza',
        'dimensioni_altezza'
    ])
);

$profondita = archy_presentation_clean_text(
    archy_presentation_get_value($view, [
        'archy_art_catalog.dimensioni_profondita',
        'dimensioni_profondita'
    ])
);

$unita = archy_presentation_clean_text(
    archy_presentation_get_value($view, [
        'archy_art_catalog.unita_di_misura_altezza',
        'unita_di_misura_altezza'
    ])
);

$dimensions = '';

if($lunghezza !== '' && $altezza !== ''){

    $dimensions = $lunghezza.' × '.$altezza;

    if($profondita !== ''){
        $dimensions .= ' × '.$profondita;
    }

    if($unita !== ''){
        $dimensions .= ' '.$unita;
    }
}

$status = archy_presentation_clean_text(archy_presentation_get_value($view, [
    'archy_art_catalog.status',
    'status',
    'archy_art_catalog.presente_in_archivio',
    'presente_in_archivio'
]));

$location = archy_presentation_clean_text(archy_presentation_get_value($view, [
    'archy_art_catalog.collocazione_geografica',
    'collocazione_geografica',
    'archy_art_catalog.ubicazione',
    'ubicazione'
]));

$official_photo_url = '';
foreach(['archy_art_catalog.foto_ufficiale', 'foto_ufficiale', 'archy_art_catalog.documentazione_fotografica', 'documentazione_fotografica'] as $photo_field){
    $real_photo_field = archy_presentation_real_field($photo_field);
    $photo_config = archy_presentation_column_config($app, $photo_field);
    $target = $photo_config['file_target'] ?? 'cloudImages';

    if(archy_presentation_has_uploads($app->db, $app->intent->table, $record_id, $real_photo_field)){
        $official_photo_url = archy_presentation_first_upload_url($app, $app->intent->table, $record_id, $real_photo_field, $target);
        if($official_photo_url !== ''){
            break;
        }
    }
}

/*
|--------------------------------------------------------------------------
| DOCUMENTI / TAB DINAMICHE
|--------------------------------------------------------------------------
*/

$documentation_fields = [
    'archy_art_catalog.documentazione_fotografica' => ['Documentazione fotografica', 'far fa-images', 'cloudImages'],
    'archy_art_catalog.documentazione_grafica' => ['Documentazione grafica', 'far fa-object-group', 'storage'],
    'archy_art_catalog.documentazione_video' => ['Documentazione video', 'far fa-file-video', 'storage'],
    'archy_art_catalog.documentazione_audio' => ['Documentazione audio', 'far fa-file-audio', 'storage'],
    'archy_art_catalog.file_fotografico' => ['File fotografico', 'far fa-image', 'storage'],
    'archy_art_catalog.condition_report' => ['Condition report allegato', 'far fa-clipboard-check', 'storage'],
    'archy_art_catalog.fonti_documenti' => ['Fonti e documenti', 'far fa-folder-open', 'storage'],
    'archy_art_catalog.documenti_progettuali' => ['Documenti progettuali', 'far fa-folder', 'storage'],
    'archy_art_catalog.certificato_assicurativo' => ['Certificato assicurativo', 'fas fa-umbrella', 'storage'],
    'archy_art_catalog.licenze_attestati_circolazione' => ['Licenze / attestati', 'far fa-file-contract', 'storage']
];

$document_cards = [];
foreach($documentation_fields as $field => $info){
    $real_field = archy_presentation_real_field($field);
    if(archy_presentation_has_uploads($app->db, $app->intent->table, $record_id, $real_field)){
        $document_cards[] = archy_presentation_doc_card([
            'title' => $info[0],
            'subtitle' => 'Allegati collegati alla scheda',
            'icon' => $info[1],
            'button' => archy_presentation_upload_button($app, $record_id, $field, 'Vedi allegati')
        ]);
    }
}

/*
|--------------------------------------------------------------------------
| DOCUMENTI GENERATI / TABELLE COLLEGATE
|--------------------------------------------------------------------------
| Le tabelle possono cambiare tra installazioni; per questo usiamo controlli
| difensivi e mostriamo la card solo se la tabella esiste e ha record collegati.
*/

$related_columns = [
    'catalog_id',
    'art_catalog_id',
    'art_id',
    'opera_id',
    'record_id',
    'scheda_catalogo_id',
    'catalogo_id',
    'art_catalog_record_id',
    'artwork_id',
    'artwork_record_id',
    'id_opera',
    'id_catalogo',
    'id_scheda_catalogo'
];

$related_cards = [];

/*
|--------------------------------------------------------------------------
| CERTIFICATI - link diretto al PDF generato
|--------------------------------------------------------------------------
| Esempio: /assets/documents/certificati/certificato_7.pdf
*/
$certificate_rows = archy_presentation_get_related_rows(
    $app->db,
    ['archy_art_certificates'],
    $related_columns,
    $record_id,
    20
);

foreach($certificate_rows as $certificate_index => $certificate_row){
    $certificate_id = isset($certificate_row['id']) ? (int)$certificate_row['id'] : 0;

    if($certificate_id <= 0){
        continue;
    }

    $certificate_url = '/assets/documents/certificati/certificato_'.$certificate_id.'.pdf';
    $certificate_title = archy_presentation_first_non_empty($certificate_row, ['title','titolo','numero','numero_certificato','codice']);

    $related_cards[] = archy_presentation_doc_card([
        'title' => $certificate_title !== '' ? 'Certificato '.$certificate_title : 'Certificato #'.$certificate_id,
        'subtitle' => 'PDF certificato collegato alla scheda',
        'icon' => 'fas fa-award',
        'url' => $certificate_url,
        'badge' => (string)($certificate_index + 1)
    ]);
}

/*
|--------------------------------------------------------------------------
| CONDITION REPORT
|--------------------------------------------------------------------------
*/
$condition_tables = array_merge(
    [
        'archy_art_condition_report',
        'archy_art_condition_reports',
        'archy_condition_report',
        'archy_condition_reports'
    ],
    archy_presentation_get_tables_by_patterns($app->db, ['%condition%report%'])
);

/*
|--------------------------------------------------------------------------
| CONDITION REPORT - colonne relazione ampliate
|--------------------------------------------------------------------------
| Alcuni intent dei documenti non usano gli stessi nomi campo dei contratti.
| Per questo qui aggiungiamo varianti specifiche oltre a $related_columns.
*/
$condition_related_columns = array_values(array_unique(array_merge($related_columns, [
    'archy_art_catalog_id',
    'archy_art_catalog',
    'catalogo',
    'catalogo_opera',
    'catalogo_id',
    'opera',
    'opera_id',
    'id_opera',
    'scheda',
    'scheda_id',
    'scheda_catalogo',
    'scheda_catalogo_id',
    'id_scheda',
    'id_scheda_catalogo',
    'record',
    'record_id',
    'id_record',
    'artwork',
    'artwork_id',
    'work_id'
])));

$condition_rows = archy_presentation_get_related_rows($app->db, $condition_tables, $condition_related_columns, $record_id, 20);

foreach($condition_rows as $condition_index => $condition_row){
    $condition_id = isset($condition_row['id']) ? (int)$condition_row['id'] : 0;

    if($condition_id <= 0){
        continue;
    }

    $condition_url = archy_presentation_document_url_from_row($condition_row, [
        '/assets/documents/condition_report/condition_report_'.$condition_id.'.pdf',
        '/assets/documents/condition-report/condition_report_'.$condition_id.'.pdf',
        '/assets/documents/condition_report/condition-report_'.$condition_id.'.pdf',
        '/assets/documents/condition-report/condition-report_'.$condition_id.'.pdf',
        '/assets/documents/conditionreport/condition_report_'.$condition_id.'.pdf',
        '/assets/documents/conditionreport/conditionreport_'.$condition_id.'.pdf'
    ]);

    $condition_title = archy_presentation_first_non_empty($condition_row, ['title','titolo','numero','codice','oggetto']);

    $related_cards[] = archy_presentation_doc_card([
        'title' => $condition_title !== '' ? 'Condition report - '.$condition_title : 'Condition report #'.$condition_id,
        'subtitle' => 'PDF condition report collegato alla scheda',
        'icon' => 'fas fa-clipboard-check',
        'url' => $condition_url !== '' ? $condition_url : '/assets/documents/condition_report/condition_report_'.$condition_id.'.pdf',
        'badge' => (string)($condition_index + 1)
    ]);
}

/*
|--------------------------------------------------------------------------
| CONTRATTI / NOLEGGI - link diretto al PDF generato
|--------------------------------------------------------------------------
| Gli intent reali dei contratti Archy Art generano PDF con questa logica:
| /assets/documents/{folder}/{prefix}_{id}.pdf
|
| Esempio noleggio id 2:
| /assets/documents/noleggio/noleggio_2.pdf
*/
$contract_sources = [
    'archy_art_noleggio' => [
        'label' => 'Noleggio',
        'folder' => 'noleggio',
        'prefix' => 'noleggio'
    ],
    'archy_art_donazione' => [
        'label' => 'Donazione',
        'folder' => 'donazione',
        'prefix' => 'donazione'
    ],
    'archy_art_cessione_immagine' => [
        'label' => 'Cessione diritti d\'immagine',
        'folder' => 'cessione_immagine',
        'prefix' => 'cessione_immagine'
    ],
    'archy_art_diritto_riproduzione_immagine' => [
        'label' => 'Cessione diritti patrimoniali',
        'folder' => 'diritto_riproduzione_immagine',
        'prefix' => 'diritto_riproduzione_immagine'
    ],
    'archy_art_cessione_opera' => [
        'label' => 'Cessione opera',
        'folder' => 'cessione_opera',
        'prefix' => 'cessione_opera'
    ],
    'archy_art_contratto_valutazione' => [
        'label' => 'Valutazione opera',
        'folder' => 'contratto_valutazione',
        'prefix' => 'contratto_valutazione'
    ],
    'archy_art_prestito' => [
        'label' => 'Prestito',
        'folder' => 'prestito',
        'prefix' => 'prestito'
    ],
    'archy_art_prestito_promessa' => [
        'label' => 'Promessa di prestito',
        'folder' => 'prestito_promessa',
        'prefix' => 'prestito_promessa'
    ],
    'archy_art_prestito_definitivo' => [
        'label' => 'Prestito definitivo',
        'folder' => 'prestito_definitivo',
        'prefix' => 'prestito_definitivo'
    ],
    'archy_art_privacy' => [
        'label' => 'Modulo privacy',
        'folder' => 'privacy',
        'prefix' => 'privacy'
    ]
];

$contract_tables = array_keys($contract_sources);
$contract_rows = archy_presentation_get_related_rows($app->db, $contract_tables, $related_columns, $record_id, 50);

foreach($contract_rows as $contract_index => $contract_row){
    $contract_id = isset($contract_row['id']) ? (int)$contract_row['id'] : 0;

    if($contract_id <= 0){
        continue;
    }

    $contract_table = isset($contract_row['_archy_related_table']) ? $contract_row['_archy_related_table'] : '';

    if(!isset($contract_sources[$contract_table])){
        continue;
    }

    $contract = $contract_sources[$contract_table];
    $contract_title = archy_presentation_first_non_empty($contract_row, ['title','titolo','oggetto','tipo','tipologia','numero','codice']);
    $contract_pdf_url = '/assets/documents/'.$contract['folder'].'/'.$contract['prefix'].'_'.$contract_id.'.pdf';

    $related_cards[] = archy_presentation_doc_card([
        'title' => $contract_title !== '' ? $contract['label'].' - '.$contract_title : $contract['label'].' #'.$contract_id,
        'subtitle' => 'PDF contratto collegato alla scheda',
        'icon' => 'fas fa-file-signature',
        'url' => $contract_pdf_url,
        'badge' => (string)($contract_index + 1)
    ]);
}

/*
|--------------------------------------------------------------------------
| MOVIMENTAZIONI ED ESPOSIZIONI
|--------------------------------------------------------------------------
*/
$other_related_sources = [
    [
        'tables' => ['archy_art_movements', 'archy_movements'],
        'patterns' => ['%movement%', '%moviment%'],
        'title' => 'Movimentazioni',
        'icon' => 'fas fa-truck',
        'intent' => 'archy_art_movements'
    ],
    [
        'tables' => ['archy_art_exhibitions', 'archy_exhibitions'],
        'patterns' => ['%exhibition%', '%esposiz%'],
        'title' => 'Esposizioni',
        'icon' => 'fas fa-landmark',
        'intent' => 'archy_art_exhibitions'
    ]
];

foreach($other_related_sources as $source){
    $source_tables = array_merge($source['tables'], archy_presentation_get_tables_by_patterns($app->db, $source['patterns']));
    $source_rows = archy_presentation_get_related_rows($app->db, $source_tables, $related_columns, $record_id, 50);
    $count = count($source_rows);

    if($count > 0){
        $related_cards[] = archy_presentation_doc_card([
            'title' => $source['title'],
            'subtitle' => $count === 1 ? '1 record collegato' : $count.' record collegati',
            'icon' => $source['icon'],
            'url' => '/archy/app.php?intent='.rawurlencode($source['intent']).'&view=grid',
            'badge' => (string)$count
        ]);
    }
}

/*
|--------------------------------------------------------------------------
| CAMPI DETTAGLIO ORGANIZZATI IN SEZIONI
|--------------------------------------------------------------------------
*/

$field_groups = [
    'Codici identificativi' => [
        'archy_art_catalog.codice',
        'archy_art_catalog.serie',
        'archy_art_catalog.collezione',
        'archy_art_catalog.numero_elementi',
        'archy_art_catalog.presente_in_archivio',
        'archy_art_catalog.collocazione_geografica',
        'archy_art_catalog.precedenti_archiviazioni',
        'archy_art_catalog.certificato_archiviazione',
        'archy_art_catalog.riferimento_catalogo_ragionato',
        'archy_art_catalog.tags'
    ],
    'Dati identificativi' => [
        'archy_art_catalog.nominativo_autore',
        'archy_art_catalog.data_nascita_autore',
        'archy_art_catalog.luogo_nascita_autore',
        'archy_art_catalog.data_morte_autore',
        'archy_art_catalog.luogo_morte_autore',
        'archy_art_catalog.titolo_opera',
        'archy_art_catalog.altri_titoli',
        'archy_art_catalog.tipologia_opera',
        'archy_art_catalog.data',
        'archy_art_catalog.data_specifica',
        'archy_art_catalog.data_anno',
        'archy_art_catalog.luogo_produzione_opera',
        'archy_art_catalog.opera_edizione',
        'archy_art_catalog.numero_edizione',
        'archy_art_catalog.editore',
        'archy_art_catalog.stampatore'
    ],
    'Descrizione' => [
        'archy_art_catalog.tipo_di_oggetto',
        'archy_art_catalog.supporto',
        'archy_art_catalog.materiali',
        'archy_art_catalog.tecnica',
        'archy_art_catalog.dimensioni',
        'archy_art_catalog.dimensioni_altezza',
        'archy_art_catalog.dimensioni_lunghezza',
        'archy_art_catalog.dimensioni_profondita',
        'archy_art_catalog.dimensioni_spessore',
        'archy_art_catalog.dimensioni_ingombro',
        'archy_art_catalog.dimensioni_diametro',
        'archy_art_catalog.altre_misure',
        'archy_art_catalog.peso',
        'archy_art_catalog.firma',
        'archy_art_catalog.posizione_firma',
        'archy_art_catalog.segni_particolari',
        'archy_art_catalog.descrizione_retro',
        'archy_art_catalog.descrizione_narrativa'
    ],
    'Conservazione e allestimento' => [
        'archy_art_catalog.stato_conservazione',
        'archy_art_catalog.interventi_restauri',
        'archy_art_catalog.diagnostica',
        'archy_art_catalog.indicazioni_conservazione',
        'archy_art_catalog.indicazioni_allestimento',
        'archy_art_catalog.indicazioni_imballaggio',
        'archy_art_catalog.ingombro_imballo',
        'archy_art_catalog.istruzioni_allestimento',
        'archy_art_catalog.indicazioni_condizioni_ambientali'
    ],
    'Provenienza e condizione giuridica' => [
        'archy_art_catalog.titolarita_attuale',
        'archy_art_catalog.storico_provenienza',
        'archy_art_catalog.tipologia_proprieta',
        'archy_art_catalog.titolo_proprieta',
        'archy_art_catalog.data_acquisizione',
        'archy_art_catalog.stima',
        'archy_art_catalog.valuta',
        'archy_art_catalog.data_di_stima',
        'archy_art_catalog.valore_assicurativo',
        'archy_art_catalog.ulteriori_informazioni'
    ],
    'Note' => [
        'archy_art_catalog.note_libere',
        'archy_art_catalog.notizie_storiche',
        'archy_art_catalog.ulteriori_diritti',
        'archy_art_catalog.link_esterni'
    ]
];

$detail_group_html = [];
foreach($field_groups as $group_title => $fields){
    $cards = [];

    foreach($fields as $field){
        if(!isset($view->data[$field])){
            continue;
        }

        $column_config = archy_presentation_column_config($app, $field);
        $type = $column_config['type'] ?? '';

        if(in_array($type, ['file','bulk_upload'], true)){
            continue;
        }

        $label = archy_presentation_get_label($view, $field, $field);
        $value = $view->data[$field]['value'] ?? '';

        if(archy_presentation_is_empty($value)){
            continue;
        }

        $cards[] = archy_presentation_field_card($label, $value);
    }

    if(!empty($cards)){
        $detail_group_html[$group_title] = implode('', $cards);
    }
}

$has_documents = (!empty($document_cards) || !empty($related_cards));
$has_details = !empty($detail_group_html);
$has_notes = isset($detail_group_html['Note']);

$uneditable_ids = [];
if(isset($app->intent->uneditable_ids) && is_array($app->intent->uneditable_ids)){
    $uneditable_ids = array_map('intval', $app->intent->uneditable_ids);
}
$can_edit = !in_array($record_id, $uneditable_ids, true);
?>

<style>
.archy-presentation-page{
    --archy-navy:#06284f;
    --archy-gold:#c58a2a;
    --archy-soft:#f5f7fb;
    --archy-border:#dbe3ee;
    --archy-text:#12233f;
    color:var(--archy-text);
}
.archy-presentation-hero{
    background:linear-gradient(135deg,#ffffff 0%,#f8fafc 100%);
    border:1px solid var(--archy-border);
    border-radius:22px;
    box-shadow:0 18px 45px rgba(6,40,79,.10);
    overflow:hidden;
}
.archy-presentation-topbar{
    background:var(--archy-navy);
    color:#fff;
    padding:18px 22px;
    display:flex;
    justify-content:space-between;
    gap:15px;
    align-items:center;
}
.archy-presentation-brand{
    font-weight:700;
    letter-spacing:.18em;
    text-transform:uppercase;
}
.archy-presentation-body{
    padding:28px;
}
.archy-official-photo{
    width:100%;
    min-height:280px;
    border-radius:18px;
    background:#eef2f7;
    border:1px solid var(--archy-border);
    overflow:hidden;
    display:flex;
    align-items:center;
    justify-content:center;
}
.archy-official-photo img{
    width:100%;
    height:100%;
    object-fit:cover;
    display:block;
}
.archy-photo-placeholder{
    color:#8190a5;
    text-align:center;
    padding:35px;
}
.archy-photo-placeholder span{
    font-size:54px;
    display:block;
    margin-bottom:12px;
}
.archy-title{
    color:var(--archy-navy);
    font-size:2rem;
    font-weight:800;
    line-height:1.1;
    margin-bottom:8px;
}
.archy-author{
    font-size:1.15rem;
    color:#4b5c75;
    margin-bottom:22px;
}
.archy-hero-grid{
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:16px;
}
.archy-hero-item{
    border-bottom:1px solid #edf1f5;
    padding-bottom:12px;
}
.archy-hero-label,
.archy-field-label{
    color:#6b7a90;
    font-size:.78rem;
    text-transform:uppercase;
    letter-spacing:.06em;
    margin-bottom:3px;
}
.archy-hero-value{
    font-weight:700;
    color:var(--archy-text);
}
.archy-status-pill{
    display:inline-flex;
    align-items:center;
    gap:7px;
    background:#eef8f1;
    color:#197544;
    border-radius:999px;
    padding:7px 12px;
    font-weight:700;
    font-size:.86rem;
}
.archy-status-dot{
    width:8px;
    height:8px;
    border-radius:50%;
    background:#20b15a;
}
.archy-presentation-tabs .nav-link{
    color:var(--archy-navy);
    font-weight:700;
    border:0;
    border-bottom:3px solid transparent;
    padding:16px 18px;
}
.archy-presentation-tabs .nav-link.active{
    color:var(--archy-navy);
    background:transparent;
    border-bottom-color:var(--archy-gold);
}
.archy-tab-panel{
    border:1px solid var(--archy-border);
    border-top:0;
    border-radius:0 0 18px 18px;
    padding:24px;
    background:#fff;
}
.archy-doc-card,
.archy-field-card{
    background:#fff;
    border:1px solid var(--archy-border);
    border-radius:16px;
    padding:20px;
    box-shadow:0 10px 25px rgba(6,40,79,.06);
}
.archy-doc-icon{
    width:52px;
    height:52px;
    border-radius:16px;
    background:#f3f6fa;
    color:var(--archy-navy);
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:24px;
    margin-bottom:14px;
}
.archy-doc-title{
    color:var(--archy-navy);
    font-weight:800;
    margin-bottom:4px;
}
.archy-doc-subtitle{
    color:#6b7a90;
    font-size:.9rem;
}
.archy-doc-badge{
    margin-top:10px;
    display:inline-block;
    background:var(--archy-navy);
    color:#fff;
    border-radius:999px;
    padding:2px 9px;
    font-size:.75rem;
    font-weight:700;
}
.archy-field-value{
    font-weight:650;
    color:var(--archy-text);
}
.archy-section-title{
    color:var(--archy-navy);
    font-weight:800;
    margin:22px 0 14px;
}
.archy-actions .btn{
    border-radius:10px;
}
@media (max-width: 767px){
    .archy-hero-grid{grid-template-columns:1fr;}
    .archy-title{font-size:1.55rem;}
    .archy-presentation-body{padding:18px;}
}
</style>

<div class="archy-presentation-page">

    <div class="d-flex justify-content-between align-items-center mb-3 archy-actions">
        <a class="btn btn-outline-secondary" href="/archy/app.php?intent=<?php echo htmlspecialchars($app->intent->table); ?>&view=grid">
            <span class="fas fa-arrow-left"></span> Elenco
        </a>

        <div>
            <a class="btn btn-outline-primary" href="/archy/app.php?intent=<?php echo htmlspecialchars($app->intent->table); ?>&view=view&id=<?php echo $record_id; ?>">
                Vista tecnica
            </a>
            <?php if($can_edit): ?>
                <a class="btn btn-primary" href="/archy/app.php?intent=<?php echo htmlspecialchars($app->intent->table); ?>&view=edit&id=<?php echo $record_id; ?>">
                    <span class="fas fa-edit"></span> Modifica
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="archy-presentation-hero mb-4">
        <div class="archy-presentation-topbar">
            <div class="archy-presentation-brand">Archy Art</div>
            <div class="small opacity-75">Scheda archivistica</div>
        </div>

        <div class="archy-presentation-body">
            <div class="row g-4 align-items-start">
                <div class="col-12 col-lg-4">
                    <div class="archy-official-photo">
                        <?php if($official_photo_url !== ''): ?>
                            <img src="<?php echo htmlspecialchars($official_photo_url); ?>" alt="Foto ufficiale">
                        <?php else: ?>
                            <div class="archy-photo-placeholder">
                                <span class="far fa-image"></span>
                                Foto ufficiale non disponibile
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-12 col-lg-8">
                    <div class="archy-title"><?php echo htmlspecialchars($title); ?></div>
                    <?php if($author !== ''): ?>
                        <div class="archy-author"><?php echo htmlspecialchars($author); ?></div>
                    <?php endif; ?>

                    <div class="archy-hero-grid">
                        <?php
                        $hero_items = [
                            'Anno' => $year,
                            'Numero archiviazione' => $archive_number,
                            'Numero inventario' => $inventory_number,
                            'Tecnica' => $technique,
                            'Supporto' => $support,
                            'Dimensioni' => $dimensions,
                            'Collocazione' => $location
                        ];

                        foreach($hero_items as $label => $value):
                            if($value === ''){
                                continue;
                            }
                        ?>
                            <div class="archy-hero-item">
                                <div class="archy-hero-label"><?php echo htmlspecialchars($label); ?></div>
                                <div class="archy-hero-value"><?php echo htmlspecialchars($value); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if($status !== ''): ?>
                        <div class="mt-4">
                            <span class="archy-status-pill"><span class="archy-status-dot"></span><?php echo htmlspecialchars($status); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <ul class="nav nav-tabs archy-presentation-tabs" id="archyPresentationTabs" role="tablist">
        <?php $first_tab = true; ?>

        <?php if($has_documents): ?>
            <li class="nav-item" role="presentation">
                <button class="nav-link <?php echo $first_tab ? 'active' : ''; ?>" id="docs-tab" data-bs-toggle="tab" data-bs-target="#docs-pane" type="button" role="tab">Documenti collegati</button>
            </li>
            <?php $first_tab = false; ?>
        <?php endif; ?>

        <?php if($has_details): ?>
            <li class="nav-item" role="presentation">
                <button class="nav-link <?php echo $first_tab ? 'active' : ''; ?>" id="details-tab" data-bs-toggle="tab" data-bs-target="#details-pane" type="button" role="tab">Dati scheda</button>
            </li>
            <?php $first_tab = false; ?>
        <?php endif; ?>

        <?php if($has_notes): ?>
            <li class="nav-item" role="presentation">
                <button class="nav-link <?php echo $first_tab ? 'active' : ''; ?>" id="notes-tab" data-bs-toggle="tab" data-bs-target="#notes-pane" type="button" role="tab">Note</button>
            </li>
            <?php $first_tab = false; ?>
        <?php endif; ?>
    </ul>

    <div class="tab-content archy-tab-panel" id="archyPresentationTabsContent">
        <?php $first_pane = true; ?>

        <?php if($has_documents): ?>
            <div class="tab-pane fade <?php echo $first_pane ? 'show active' : ''; ?>" id="docs-pane" role="tabpanel" aria-labelledby="docs-tab">
                <div class="row g-3">
                    <?php echo implode('', $document_cards); ?>
                    <?php echo implode('', $related_cards); ?>
                </div>
            </div>
            <?php $first_pane = false; ?>
        <?php endif; ?>

        <?php if($has_details): ?>
            <div class="tab-pane fade <?php echo $first_pane ? 'show active' : ''; ?>" id="details-pane" role="tabpanel" aria-labelledby="details-tab">
                <?php foreach($detail_group_html as $group_title => $html): ?>
                    <?php if($group_title === 'Note'){ continue; } ?>
                    <h5 class="archy-section-title"><?php echo htmlspecialchars($group_title); ?></h5>
                    <div class="row g-3 mb-2">
                        <?php echo $html; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php $first_pane = false; ?>
        <?php endif; ?>

        <?php if($has_notes): ?>
            <div class="tab-pane fade <?php echo $first_pane ? 'show active' : ''; ?>" id="notes-pane" role="tabpanel" aria-labelledby="notes-tab">
                <div class="row g-3">
                    <?php echo $detail_group_html['Note']; ?>
                </div>
            </div>
            <?php $first_pane = false; ?>
        <?php endif; ?>
    </div>

</div>

<script>
/*
|--------------------------------------------------------------------------
| ARCHY MEDIA - MODALE ALLEGATI IN SOLA LETTURA NELLA VIEW PRESENTATION
|--------------------------------------------------------------------------
*/
(function(){
    function cleanViewUploadsModal(){
        if(!document.querySelector('.archy-presentation-page')){
            return;
        }

        var modal = document.getElementById('uploads_modal');
        if(!modal){
            return;
        }

        modal.querySelectorAll('.delete_attachment').forEach(function(btn){
            btn.remove();
        });
    }

    document.addEventListener('shown.bs.modal', function(event){
        if(event.target && event.target.id === 'uploads_modal'){
            setTimeout(cleanViewUploadsModal, 150);
        }
    });

    if(window.jQuery){
        jQuery(document).ajaxComplete(cleanViewUploadsModal);
    }
})();
</script>
