<?php
/**
 * Endpoint font PDF per TinyMCE.
 *
 * Restituisce la lista dei font realmente disponibili per i modelli PDF.
 * Il file è volutamente separato da app.php per mantenere la funzione semplice
 * e non introdurre nuove do_action nella classe App.
 */

session_start();

header('Content-Type: application/json; charset=utf-8');

$main_root = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$subdomain_root = dirname(dirname(__FILE__));

try {
    require_once $main_root . '/private/_common/classes/load_classes.php';

    $app = new App($subdomain_root);

    if (!$app->isLogged()) {
        echo json_encode([
            'success' => false,
            'font_formats' => 'Helvetica=helvetica; Arial=arial,helvetica,sans-serif; Times New Roman=times new roman,times; Courier New=courier new,courier',
            'message' => 'not_logged'
        ]);
        exit;
    }

    if(!class_exists('PdfFontManager')){
        require_once $main_root . '/private/_common/classes/PdfFontManager.php';
    }

    $manager = new PdfFontManager($app);

    echo json_encode([
        'success' => true,
        'font_formats' => $manager->getTinyMceFontFormats()
    ]);

} catch (Throwable $e) {
    echo json_encode([
        'success' => false,
        'font_formats' => 'Helvetica=helvetica; Arial=arial,helvetica,sans-serif; Times New Roman=times new roman,times; Courier New=courier new,courier',
        'message' => $e->getMessage()
    ]);
}
