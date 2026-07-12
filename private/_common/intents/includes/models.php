<?php

echo $app::form_element($data['columns']['archy_models.model_intent'], "col-md-12");
echo $app::form_element($data['hidden_columns']['archy_models.model_title'], "col-md-6");

/*
|--------------------------------------------------------------------------
| CONFIG MODELLO SICURA
|--------------------------------------------------------------------------
|
| Quando si crea un nuovo modello senza intent_filter, $model_intent_filter
| può essere vuoto. In quel caso evitiamo errori PHP 8 e mostriamo comunque
| tutti i campi principali.
|
*/

$modelConfig = [];

if (
    isset($model_intent_filter) &&
    $model_intent_filter != '' &&
    isset($app->intent->configModel[$model_intent_filter]) &&
    is_array($app->intent->configModel[$model_intent_filter])
) {
    $modelConfig = $app->intent->configModel[$model_intent_filter];
}

// Se non ho filtro, mostro tutti i blocchi base per creare il modello.
if (empty($modelConfig)) {
    $modelConfig = [
        'model_sizes',
        'model_template',
        'model_archive'
    ];
}

if (in_array('model_sizes', $modelConfig)) {
    echo $app::form_element($data['hidden_columns']['archy_models.model_width'], "col-md-3");
    echo $app::form_element($data['hidden_columns']['archy_models.model_height'], "col-md-3");
}

if (in_array('model_template', $modelConfig)) {
    echo $app::form_element($data['hidden_columns']['archy_models.model_template'], "col-md-12");
    echo $app::form_element($data['hidden_columns']['archy_models.model_background'], "col-md-8");
    echo $app::form_element($data['hidden_columns']['archy_models.model_css'], "col-md-8");
}

if (in_array('model_archive', $modelConfig)) {
    echo $app::form_element($data['hidden_columns']['archy_models.model_archive'], "col-md-8");
}

/*
|--------------------------------------------------------------------------
| NOME FILE PDF DINAMICO
|--------------------------------------------------------------------------
*/

if (isset($data['hidden_columns']['archy_models.model_filename_template'])) {
    echo $app::form_element($data['hidden_columns']['archy_models.model_filename_template'], "col-md-8");
}