<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

/*
|--------------------------------------------------------------------------
| ARCHY MEDIA - BOOTSTRAP DINAMICO
|--------------------------------------------------------------------------
|
| Funziona sia su:
| - /var/www/vhosts/archymedia.com/subdomains/archymedia/httpdocs
| - /var/www/vhosts/archyart.it/subdomains/demo/httpdocs
|
*/

$subdomain_root = realpath(__DIR__ . '/../..');

if (!$subdomain_root) {
    echo json_encode([
        'status' => 'error',
        'message' => 'subdomain_root non rilevato',
        'debug' => __DIR__
    ]);
    exit;
}

$main_root = realpath($subdomain_root . '/../../..');

if (!$main_root) {
    echo json_encode([
        'status' => 'error',
        'message' => 'main_root non rilevato',
        'debug' => $subdomain_root
    ]);
    exit;
}

$load_classes = $main_root . '/private/_common/classes/load_classes.php';

if (!is_file($load_classes)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'load_classes.php non trovato',
        'debug' => $load_classes
    ]);
    exit;
}

chdir($subdomain_root);

require_once $load_classes;

$app = new App($subdomain_root);
$db = $app->get_db();

header('Content-Type: application/json');

$recordId = isset($_POST['record_id']) ? (int) $_POST['record_id'] : 0;
$domainId = isset($_POST['domain_id']) ? (int) $_POST['domain_id'] : 0;
$mode     = isset($_POST['mode']) ? $_POST['mode'] : 'file';
$action   = isset($_POST['action']) ? $_POST['action'] : '';

if ($recordId <= 0 || $domainId <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Parametri mancanti.'
    ]);
    exit;
}

$uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/assets/documents/upload_extrafield/';
$publicDir = '/assets/documents/upload_extrafield/';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0775, true);
}

function getCurrentExtraValue($db, $recordId, $domainId)
{
    $result = $db->query("
        SELECT value
        FROM additional_fields_values
        WHERE domain_id = '{$domainId}'
        AND record_id = '{$recordId}'
        LIMIT 1
    ");

    if ($result && $result->num_rows > 0) {
        $row = mysqli_fetch_object($result);
        return $row->value;
    }

    return '';
}

function saveExtraValue($db, $recordId, $domainId, $value)
{
    $value = $db->real_escape_string((string)$value);

    $db->query("
        DELETE FROM additional_fields_values
        WHERE domain_id = '{$domainId}'
        AND record_id = '{$recordId}'
    ");

    if ($value !== '') {
        $db->query("
            INSERT INTO additional_fields_values
            SET
                domain_id = '{$domainId}',
                record_id = '{$recordId}',
                value = '{$value}'
        ");
    }
}

function renderExtraUploadHtml($files, $recordId, $domainId, $mode)
{
    if (!is_array($files)) {
        $files = [];
    }

    $html = '';

    foreach ($files as $path) {
        if (!$path) {
            continue;
        }

        $filename = explode('/', $path);

        $html .= '
            <div class="small mb-2">
                <a href="'.$path.'" target="_blank">'.end($filename).'</a>
                <button 
                    type="button" 
                    class="btn btn-sm btn-outline-danger ms-2 extra-delete-file"
                    data-record-id="'.(int)$recordId.'"
                    data-domain-id="'.(int)$domainId.'"
                    data-mode="'.htmlspecialchars($mode, ENT_QUOTES).'"
                    data-file="'.htmlspecialchars($path, ENT_QUOTES).'">
                    Elimina
                </button>
            </div>
        ';
    }

    return $html;
}

if ($action === 'delete') {
    $fileToDelete = isset($_POST['file']) ? $_POST['file'] : '';
    $oldValue = getCurrentExtraValue($db, $recordId, $domainId);

    if ($mode === 'bulk') {
        $files = @unserialize($oldValue);

        if (!is_array($files)) {
            $files = [];
        }

        $files = array_values(array_filter($files, function($path) use ($fileToDelete) {
            return $path !== $fileToDelete;
        }));

        $newValue = serialize($files);

    } else {
        $files = [];
        $newValue = '';
    }

    $safePath = realpath($_SERVER['DOCUMENT_ROOT'] . $fileToDelete);
    $basePath = realpath($uploadDir);

    if ($safePath && $basePath && strpos($safePath, $basePath) === 0 && file_exists($safePath)) {
        @unlink($safePath);
    }

    saveExtraValue($db, $recordId, $domainId, $newValue);

    echo json_encode([
        'status' => 'success',
        'html' => renderExtraUploadHtml($files, $recordId, $domainId, $mode)
    ]);
    exit;
}

if ($action === 'upload') {
    if (empty($_FILES['extra_file'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Nessun file ricevuto.'
        ]);
        exit;
    }

    if (!is_dir($uploadDir) || !is_writable($uploadDir)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Cartella upload non scrivibile.'
        ]);
        exit;
    }

    $file = $_FILES['extra_file'];
    $savedFiles = [];

    if (is_array($file['name'])) {
        foreach ($file['name'] as $i => $name) {
            if (empty($name) || (int)$file['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }

            $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($name));
            $newName = date('YmdHis') . '_' . uniqid() . '_' . $safeName;

            if (move_uploaded_file($file['tmp_name'][$i], $uploadDir . $newName)) {
                $savedFiles[] = $publicDir . $newName;
            }
        }
    }

    if (empty($savedFiles)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'File non salvato.'
        ]);
        exit;
    }

    if ($mode === 'bulk') {
        $oldValue = getCurrentExtraValue($db, $recordId, $domainId);
        $oldFiles = @unserialize($oldValue);

        if (!is_array($oldFiles)) {
            $oldFiles = [];
        }

        $allFiles = array_merge($oldFiles, $savedFiles);
        $value = serialize($allFiles);
        $filesForHtml = $allFiles;

    } else {
        $value = $savedFiles[0];
        $filesForHtml = [$savedFiles[0]];
    }

    saveExtraValue($db, $recordId, $domainId, $value);

    echo json_encode([
        'status' => 'success',
        'html' => renderExtraUploadHtml($filesForHtml, $recordId, $domainId, $mode)
    ]);
    exit;
}

echo json_encode([
    'status' => 'error',
    'message' => 'Azione non valida.'
]);
exit;