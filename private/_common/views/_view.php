<?php
$view = $app->load_view($_GET['id']);
$record_id = (int) $_GET['id'];

function archy_view_value($value){

    if(is_array($value)){
        return implode(', ', array_filter($value));
    }

    if(is_string($value)){
        $unserialized = @unserialize($value);

        if($unserialized !== false || $value === 'b:0;'){
            if(is_array($unserialized)){
                return implode(', ', array_filter($unserialized));
            }

            return (string) $unserialized;
        }
    }

    return $value;
}

function archy_view_is_file_value($value){

    if(!is_string($value)){
        return false;
    }

    return (
        strpos($value, '/assets/documents/') !== false ||
        strpos($value, 'upload_extrafield') !== false ||
        preg_match('/\.(jpg|jpeg|png|gif|webp|pdf|doc|docx|xls|xlsx)$/i', $value)
    );
}
?>

<div class="row g-3">
    <div class="col-12 mb-3">
        <div class="d-flex justify-content-end">
            <div class="btn-toolbar" role="toolbar">
                <div class="btn-group me-2 mb-2" role="group">

                    <a class="btn btn-info" href="/archy/app.php?intent=<?php echo $app->intent->table; ?>&view=grid">
                        <span class="fas fa-list"></span> Elenco
                    </a>

                    <a class="btn btn-primary" href="/archy/app.php?intent=<?php echo $app->intent->table; ?>&view=edit&id=<?php echo $record_id; ?>">
                        <span class="fas fa-edit"></span> Modifica
                    </a>

                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <h4><?php echo $view->title; ?></h4>
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
$value = $item['value'] ?? '';

$type = '';

if(isset($app->cols[$field]['type'])){
    $type = $app->cols[$field]['type'];
}

/*
|--------------------------------------------------------------------------
| MULTIPLE SELECT
|--------------------------------------------------------------------------
*/
if($type == 'multiple'){

    $arr = @unserialize($value);

    if(is_array($arr)){
        $value = implode(', ', array_filter($arr));
    }
}

/*
|--------------------------------------------------------------------------
| ARRAY NATIVI
|--------------------------------------------------------------------------
*/
if(is_array($value)){
    $value = implode(', ', array_filter($value));
}

if(trim(strip_tags((string)$value)) === ''){
    continue;
}

/*
|--------------------------------------------------------------------------
| FILE
|--------------------------------------------------------------------------
*/
$is_file = (
    $type == 'file'
    ||
    $type == 'bulk_upload'
);
                        ?>

                        <div class="col-12 col-md-6 col-xl-4">
                            <div class="border rounded p-3 h-100 bg-light">

                                <div class="text-muted small mb-1">
                                    <?php echo htmlspecialchars($label); ?>
                                </div>

                                <div class="fw-semibold">

                                    <?php if($is_file): ?>

                                        <button type="button"
                                                class="btn btn-sm btn-info upload-btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#uploads_modal"
                                                data-table="<?php echo htmlspecialchars($app->intent->table); ?>"
                                                data-id="<?php echo $record_id; ?>"
                                                data-field="<?php echo htmlspecialchars($field); ?>"
                                                data-location="">
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