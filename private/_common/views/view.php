<?php
$view = $app->load_view($_GET['id']);
$record_id = (int) $_GET['id'];

/*
|--------------------------------------------------------------------------
| ARCHY MEDIA - VISTA RIEPILOGO PULITA
|--------------------------------------------------------------------------
|
| Obiettivi:
| - non mostrare checkbox non valorizzati / false / No;
| - non mostrare pulsanti allegati quando il campo non contiene file;
| - nascondere campi tecnici e campi provenienti da join non utili;
| - rispettare $intent->uneditable_ids per non mostrare il tasto Modifica.
|
*/

if(!function_exists('archy_view_to_array')){
    function archy_view_to_array($value){

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
        if($unserialized !== false || $trimed_false = ($trimmed === 'b:0;')){
            if(is_array($unserialized)){
                return $unserialized;
            }
            return [$unserialized];
        }

        $json = json_decode($trimmed, true);
        if(json_last_error() === JSON_ERROR_NONE && is_array($json)){
            return $json;
        }

        return [$value];
    }
}

if(!function_exists('archy_view_normalize_value')){
    function archy_view_normalize_value($value){

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
                return archy_view_normalize_value($unserialized);
            }

            $json = json_decode($trimmed, true);
            if(json_last_error() === JSON_ERROR_NONE && is_array($json)){
                return archy_view_normalize_value($json);
            }
        }

        return $value;
    }
}

if(!function_exists('archy_view_is_empty_value')){
    function archy_view_is_empty_value($value){

        if(is_array($value)){
            $value = archy_view_normalize_value($value);
        }

        $value = trim(strip_tags((string)$value));

        return (
            $value === '' ||
            $value === '[]' ||
            $value === '{}' ||
            $value === 'a:0:{}' ||
            strtolower($value) === 'null'
        );
    }
}

if(!function_exists('archy_view_is_false_checkbox')){
    function archy_view_is_false_checkbox($value){

        $value = strtolower(trim(strip_tags((string) archy_view_normalize_value($value))));

        return in_array($value, ['', '0', 'no', 'false', 'null', 'n'], true);
    }
}

if(!function_exists('archy_view_has_files')){
    function archy_view_has_files($value){

        $values = archy_view_to_array($value);

        foreach($values as $file_value){

            if(is_array($file_value)){
                if(archy_view_has_files($file_value)){
                    return true;
                }
                continue;
            }

            $file_value = trim((string)$file_value);

            if($file_value === ''){
                continue;
            }

            if(
                strpos($file_value, '/assets/documents/') !== false ||
                strpos($file_value, 'upload_extrafield') !== false ||
                preg_match('/\.(jpg|jpeg|png|gif|webp|pdf|doc|docx|xls|xlsx|zip)$/i', $file_value)
            ){
                return true;
            }
        }

        return false;
    }
}


if(!function_exists('archy_view_upload_has_attachments')){
    function archy_view_upload_has_attachments($db, $table, $record_id, $field){

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


if(!function_exists('archy_view_get_column_options')){
    function archy_view_get_column_options($column_config, $db){

        $options = [];

        if(!is_array($column_config) || !isset($column_config['options'])){
            return $options;
        }

        if(isset($column_config['options_type']) && $column_config['options_type'] == 'array'){
            if(is_array($column_config['options'])){
                $options = $column_config['options'];
            }
        }
        elseif(isset($column_config['options_type']) && $column_config['options_type'] == 'sql'){
            if($db && trim((string)$column_config['options']) !== ''){
                $res = $db->query($column_config['options']);
                if($res){
                    while($row = mysqli_fetch_object($res)){
                        $options[(string)$row->option_value] = (string)$row->option_text;
                    }
                }
            }
        }

        return $options;
    }
}

if(!function_exists('archy_view_select_looks_like_full_options')){
    function archy_view_select_looks_like_full_options($raw_value, $options){

        if(empty($options)){
            return false;
        }

        $option_labels = array_values(array_filter(array_map(function($v){
            return trim(strip_tags((string)$v));
        }, $options)));

        $option_keys = array_values(array_filter(array_map(function($v){
            return trim(strip_tags((string)$v));
        }, array_keys($options))));

        if(empty($option_labels)){
            return false;
        }

        if(is_array($raw_value)){
            $raw_clean = array_values(array_filter(array_map(function($v){
                return trim(strip_tags((string)$v));
            }, $raw_value)));

            sort($raw_clean);
            $labels_sorted = $option_labels;
            sort($labels_sorted);
            $keys_sorted = $option_keys;
            sort($keys_sorted);

            return ($raw_clean === $labels_sorted || $raw_clean === $keys_sorted);
        }

        $raw_string = trim(strip_tags((string)$raw_value));
        if($raw_string === ''){
            return false;
        }

        $labels_join = implode(', ', $option_labels);
        $keys_join = implode(', ', $option_keys);

        return ($raw_string === $labels_join || $raw_string === $keys_join);
    }
}

if(!function_exists('archy_view_format_select_value')){
    function archy_view_format_select_value($raw_value, $type, $options){

        if($type == 'multiple' || $type == 'multiple_checkbox'){
            $values = archy_view_to_array($raw_value);
            $values = array_values(array_filter($values, function($v){
                return trim(strip_tags((string)$v)) !== '';
            }));

            if(empty($values)){
                return '';
            }

            $labels = [];
            foreach($values as $selected_value){
                $selected_value = (string)$selected_value;
                $labels[] = isset($options[$selected_value]) ? $options[$selected_value] : $selected_value;
            }

            return implode(', ', array_filter($labels));
        }

        $selected_value = trim(strip_tags((string)archy_view_normalize_value($raw_value)));

        if($selected_value === ''){
            return '';
        }

        return isset($options[$selected_value]) ? $options[$selected_value] : $selected_value;
    }
}

if(!function_exists('archy_view_should_hide_field')){
    function archy_view_should_hide_field($field, $label){

        $field_lower = strtolower((string)$field);
        $label_lower = strtolower((string)$label);
        $field_base = $field_lower;

        if(strpos($field_base, '.') !== false){
            $parts = explode('.', $field_base);
            $field_base = end($parts);
        }

        /* Campi tecnici standard da non mostrare nella scheda riepilogo */
        $hidden_exact = [
            'id',
            'created',
            'updated',
            'created_at',
            'updated_at',
            'date_created',
            'date_updated',
            'created_by',
            'updated_by',
            'modified',
            'modified_at',
            'ordering',
            'checked_out',
            'checked_out_time',
            'access',
            'asset_id',
            'params',
            'metakey',
            'metadesc'
        ];

        if(in_array($field_base, $hidden_exact, true)){
            return true;
        }

        /* Nasconde i campi tecnici/di join della tabella modelli */
        if(
            strpos($field_lower, 'archy_models.') !== false ||
            strpos($field_lower, 'models.') !== false ||
            strpos($label_lower, 'modello') !== false && in_array($field_base, $hidden_exact, true)
        ){
            return true;
        }

        return false;
    }
}

$uneditable_ids = [];
if(isset($app->intent->uneditable_ids) && is_array($app->intent->uneditable_ids)){
    $uneditable_ids = array_map('intval', $app->intent->uneditable_ids);
}

$can_edit = !in_array($record_id, $uneditable_ids, true);
?>

<div class="row g-3 archy-view-page">
    <div class="col-12 mb-3">
        <div class="d-flex justify-content-end">
            <div class="btn-toolbar" role="toolbar">
                <div class="btn-group me-2 mb-2" role="group">

                    <a class="btn btn-info" href="/archy/app.php?intent=<?php echo htmlspecialchars($app->intent->table); ?>&view=grid">
                        <span class="fas fa-list"></span> Elenco
                    </a>

                    <?php if($can_edit): ?>
                        <a class="btn btn-primary" href="/archy/app.php?intent=<?php echo htmlspecialchars($app->intent->table); ?>&view=edit&id=<?php echo $record_id; ?>">
                            <span class="fas fa-edit"></span> Modifica
                        </a>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <h4><?php echo htmlspecialchars($view->title); ?></h4>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                Riepilogo dati
            </div>

            <div class="card-body">
                <div class="row g-3">

                    <?php foreach($view->data as $field => $item): ?>

                        <?php
                        $label = $item['label'] ?? $field;
                        $raw_value = $item['value'] ?? '';
                        $type = '';

                        $column_config = $app->cols[$field] ?? [];

                        if(empty($column_config) && strpos($field, '.') !== false){
                            $field_parts_for_config = explode('.', $field);
                            $field_base_for_config = end($field_parts_for_config);
                            $column_config = $app->cols[$field_base_for_config] ?? [];
                        }

                        if(isset($column_config['type'])){
                            $type = $column_config['type'];
                        }

                        if(archy_view_should_hide_field($field, $label)){
                            continue;
                        }

                        $is_file = in_array($type, ['file', 'bulk_upload'], true);
                        $is_checkbox = in_array($type, ['checkbox', 'check', 'boolean', 'bool'], true);
                        $is_select = in_array($type, ['select', 'select_standard', 'multiple', 'multiple_checkbox'], true);

                        $real_field = $field;
                        if(strpos($real_field, '.') !== false){
                            $real_field_parts = explode('.', $real_field);
                            $real_field = end($real_field_parts);
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | LOCATION ALLEGATI
                        |--------------------------------------------------------------------------
                        | La modale get_cloud_attachments si aspetta un valore logico:
                        | - storage     => file su storage/R2
                        | - cloudImages => immagini cloud
                        | - local       => vecchi file locali
                        |
                        | Non va passato il valore del campo, altrimenti App.php entra nel ramo
                        | local e genera link relativi sbagliati.
                        */
                        $file_location = 'storage';
                        if(isset($column_config['file_target']) && trim((string)$column_config['file_target']) !== ''){
                            $file_location = $column_config['file_target'];
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | FILE / BULK UPLOAD
                        |--------------------------------------------------------------------------
                        | Ora i campi upload vengono mostrati solo se esiste almeno un record
                        | collegato nella tabella archy_files per tabella + id + campo reale.
                        | Non usiamo $raw_value perché nella vista può essere vuoto anche quando
                        | la modale recupera correttamente gli allegati da archy_files.
                        */

                        if($is_file && !archy_view_upload_has_attachments($app->db, $app->intent->table, $record_id, $real_field)){
                            continue;
                        }

                        if($is_checkbox && archy_view_is_false_checkbox($raw_value)){
                            continue;
                        }

                        if($is_select){
                            $select_options = archy_view_get_column_options($column_config, $app->db);

                            /*
                            |--------------------------------------------------------------------------
                            | SELECT NON VALORIZZATE
                            |--------------------------------------------------------------------------
                            | In alcuni casi la view riceve l'elenco completo delle opzioni invece del
                            | valore salvato. Nella scheda riepilogo non dobbiamo stampare tutte le
                            | opzioni se il campo non è realmente valorizzato.
                            */
                            if(
                                archy_view_is_empty_value($raw_value) ||
                                archy_view_select_looks_like_full_options($raw_value, $select_options)
                            ){
                                continue;
                            }

                            $value = archy_view_format_select_value($raw_value, $type, $select_options);

                            if(archy_view_is_empty_value($value)){
                                continue;
                            }
                        } else {
                            $value = archy_view_normalize_value($raw_value);
                        }

                        if($is_checkbox){
                            $value = 'Sì';
                        }

                        if(!$is_file && archy_view_is_empty_value($value)){
                            continue;
                        }
                        ?>

                        <div class="col-12 col-md-6 col-xl-4">
                            <div class="border rounded p-3 h-100 bg-light">

                                <div class="text-muted small mb-1">
                                    <?php echo htmlspecialchars($label); ?>
                                </div>

                                <div class="fw-semibold">

                                    <?php if($is_file): ?>

                                        <button type="button"
                                                class="btn btn-sm btn-info upload-btn archy-view-upload-btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#uploads_modal"
                                                data-table="<?php echo htmlspecialchars($app->intent->table); ?>"
                                                data-id="<?php echo $record_id; ?>"
                                                data-field="<?php echo htmlspecialchars($real_field); ?>"
                                                data-location="<?php echo htmlspecialchars($file_location); ?>">
                                            Vedi allegati
                                        </button>

                                    <?php else: ?>

                                        <?php echo $value; ?>

                                    <?php endif; ?>

                                </div>

                            </div>
                        </div>

                    <?php endforeach; ?>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
/*
|--------------------------------------------------------------------------
| ARCHY MEDIA - MODALE ALLEGATI IN SOLA LETTURA NELLA VIEW
|--------------------------------------------------------------------------
| La stessa modale viene usata anche nella form di modifica, dove il pulsante
| Elimina deve restare disponibile. Nella vista riepilogo lo rimuoviamo solo
| lato interfaccia, senza modificare la logica globale della modale.
*/
(function(){
    function cleanViewUploadsModal(){
        if(!document.querySelector('.archy-view-page')){
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

    document.addEventListener('ajaxComplete', cleanViewUploadsModal);

    if(window.jQuery){
        jQuery(document).ajaxComplete(cleanViewUploadsModal);
    }
})();
</script>
