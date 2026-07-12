<?php

//if(isset($_GET['intent_filter'])){ $intent_filter = explode('|', $_GET['intent_filter']); }

$intent=new stdClass();
$intent->table='archy_models';
$intent->title='Modelli';
$intent->singular='Modello';
$intent->add='Nuovo modello';
$intent->joins=[];
$intent->view_title='Modelli';
$intent->filter = isset($_GET['intent_filter']) ? $_GET['intent_filter'] : '';
$intent->configModel=require_once $app->main_root.'/private/_common/assets/settings/models/config.php';
$model_intent_filter='';
if($intent->filter != ''){
    $parts = explode('|', $intent->filter);
    $model_intent_filter = isset($parts[1]) ? $parts[1] : '';
}
//$intent->where="model_category='".$_GET['filter']."'";

$intent->columns=array(
	'archy_models.id'=>array(
		'label'=>'ID',
		'type'=>'hidden',
		'hidden'=>''
	),
	'archy_models.model_intent'=>array(
		'label'=>'Intent modello',
		'type'=>'hidden',
                'default_value'=>$model_intent_filter
	),
	'archy_models.model_title'=>array(
		'label'=>'Nome Modello',
		'type'=>'text',
		'required'=>true,
		'grid_search_type'=>'search',
		'hidden'=>''
	),
	'archy_models.model_template'=>array(
		'label'=>'Template modello con sfondo',
		'type'=>'plain_editor',
		'editor_height'=>'400',
                'placeholder'=>true,
                'placeholder_origin' => $model_intent_filter,
		'hidden'=>''
	),
        'archy_models.model_css'=>array(
		'label'=>'CSS personalizzato',
		'type'=>'plain_editor',
		'editor_height'=>'400',
		'editor_purpose'=>'css',
		'hidden'=>''
	),	
	'archy_models.model_width'=>array(
		'label'=>'Larghezza (mm)',
		'type'=>'text',
                'extra_css_class'=>'integers_only',
		'hidden'=>''
	),
        'archy_models.model_height'=>array(
		'label'=>'Altezza (mm)',
		'type'=>'text',
                'extra_css_class'=>'integers_only',
		'hidden'=>''
	),	
        'archy_models.model_background'=>array(
		'label'=>'Layout grafico di sfondo',
		'type'=>'file',
                'help_block'=>'Sono ammessi file con estensione JPG, JPEG, PNG, GIF',
                'file_control' => '.jpg, .jpeg, .gif, .png',
                'formatter' => function ($d, $row) {
                    $return='<a href="'.$d.'" download><img src="'.$d.'" alt="Layout" class="tt" title="Download" style="max-height:44px"></a>';
                    return $d != '' ? $return : '';
                },
                'excel_formatter'=>function($d,$row){
                    $return=App::protocol()."://".$_SERVER['HTTP_HOST'].$d;
                    return $d != '' ? $return : '';
                },
		'formatter_hide_in_form'=>true,
		'formatter_hide_in_excel'=>true,
		'hidden'=>''
	),	
        'archy_models.model_archive'=>array(
		'label'=>'Template modello standard',
		'type'=>'plain_editor',
		'editor_height'=>'400',
                'placeholder'=>true,
                'placeholder_origin' => $model_intent_filter,
		'hidden'=>''
	),                
        /*
        |--------------------------------------------------------------------------
        | TEMPLATE NOME FILE PDF
        |--------------------------------------------------------------------------
        |
        | Permette di personalizzare il nome fisico dei PDF generati dal modello.
        |
        | Esempi:
        | - [numero_archiviazione]_[titolo_opera]
        | - [titolo_opera]_[anno]
        | - [extra:stato_conservazione]_[titolo_opera]
        |
        | L\'ID record viene sempre aggiunto automaticamente alla fine dal sistema,
        | quindi non va inserito manualmente nel template.
        |
        */
        'archy_models.model_filename_template'=>array(
                'label'=>'Nome file PDF dinamico',
                'type'=>'text',
                'placeholder'=>true,
                'placeholder_origin' => $model_intent_filter,
                'help_block'=>'Esempio: [numero_archiviazione]_[titolo_opera]. L\'ID record viene aggiunto automaticamente alla fine del file.',
                'hidden'=>''
        ),
	'archy_models.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_models.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);
                
if($app->config['arts']):
   
	$intent->uneditable_ids=range(1,17);

endif;
                
$intent->default_columns=array(
	'archy_models.id','archy_models.model_title'
);
$intent->include_in_form='intents/includes/models.php';

$app->merge_intents($intent);
?>