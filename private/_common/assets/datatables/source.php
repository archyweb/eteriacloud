<?php

//error_reporting(E_ALL); ini_set('display_errors', TRUE); ini_set('display_startup_errors', TRUE);

session_start();
$main_root=dirname(dirname(dirname(dirname(dirname(__FILE__)))));

//require_once $main_root.'/httpdocs/_common/const.php';
//require_once $_SERVER['DOCUMENT_ROOT'].'_common/const.php';

require_once $main_root.'/private/_common/classes/load_classes.php';
$app=new App($subdomain_root);
require_once $main_root.'/private/_common/intents/'.$_GET['intent'].'.php';
$app->init();
$table=$app->intent->table;
$primaryKey='id';
$columns=[];
$counter=0;
$additionalFieldJoins=[];
$hasAdditionalColumns=false;

// Restituisce una colonna qualificata una sola volta.
// Serve perché in alcuni intent $app->actions_column è 'id',
// in altri è già 'nome_tabella.id'.
function dt_qualify_column($table, $column) {
    return (strpos($column, '.') !== false) ? $column : $table.'.'.$column;
}

// Converte in modo robusto i valori multipli dei campi aggiuntivi.
// Nel DB possono arrivare come array serializzato oppure come stringa separata da virgole.
function dt_parse_multiple_values($value) {
    if ($value === null || $value === '') {
        return [];
    }

    $unserialized = @unserialize($value);
    if (is_array($unserialized)) {
        $values = $unserialized;
    } else {
        $values = explode(',', $value);
    }

    $clean = [];
    foreach ($values as $v) {
        if (is_array($v)) {
            continue;
        }
        $v = trim((string)$v);
        if ($v === '') {
            continue;
        }
        $clean[] = $v;
    }

    return array_values(array_unique($clean));
}

if(!isset($app->intent->uneditable_ids)):
	$app->intent->uneditable_ids=[];
endif;
if(!isset($app->intent->undeletable_ids)):
	$app->intent->undeletable_ids=[];
endif;
if(!isset($app->intent->where)):
	$app->intent->where="";
endif;



// INTESTAZIONE
foreach($_SESSION[$app->intent->table.'-listing_columns'] as $column_name):

    $columnDefinition = array(
        'db' => '',
        'dt' => $counter
    );

    // V1.4 - Campi aggiuntivi in DataTable
    // Le colonne virtuali additional_{domain_id} vengono lette da additional_fields_values
    // tramite LEFT JOIN dedicata, così possono essere mostrate, filtrate e ordinate.
    // ATTENZIONE: il riconoscimento deve essere rigoroso /^additional_[0-9]+$/
    // per non confondere la tabella nativa additional_fields con i campi extra.
    if (preg_match('/^additional_([0-9]+)$/', $column_name, $additionalMatch)):
        $domain_id = (int) $additionalMatch[1];
        $alias = 'afv_'.$domain_id;

        $hasAdditionalColumns = true;
        $additionalFieldJoins[$alias] = " left join additional_fields_values as ".$alias." on (".$alias.".record_id = ".dt_qualify_column($table, $app->actions_column)." and ".$alias.".domain_id = ".$domain_id.") ";

        $columnDefinition['db'] = "COALESCE(".$alias.".value,'')";
        $columnDefinition['as'] = $column_name;

        /*
        |--------------------------------------------------------------------------
        | TIPO CAMPO AGGIUNTIVO
        |--------------------------------------------------------------------------
        |
        | Serve anche al filtro DataTable. In particolare i campi "multiple"
        | non possono essere filtrati con uguaglianza semplice, perché il valore
        | nel DB può essere serializzato, CSV o lista.
        |
        */
        $type = '';
        $options = [];

        if (isset($app->cols[$column_name]['type'])):
            $type = $app->cols[$column_name]['type'];
            $options = isset($app->cols[$column_name]['options']) ? $app->cols[$column_name]['options'] : [];
        else:
            $fieldRes = $app->get_db()->query("
                SELECT af.type, af.options
                FROM additional_fields_domain afd
                INNER JOIN additional_fields af ON af.id = afd.field_id
                WHERE afd.id = '".$domain_id."'
                LIMIT 1
            ");

            if($fieldRes && $fieldObj = mysqli_fetch_object($fieldRes)):
                $type = $fieldObj->type;

                $rawOptions = @unserialize($fieldObj->options);
                if($rawOptions !== false && is_array($rawOptions)):
                    $options = $rawOptions;
                endif;
            endif;
        endif;

        if($type != ''):
            $columnDefinition['type'] = $type;
            $columnDefinition['search_type'] = $type;
        endif;

        if ($type != ''):

            $columnDefinition['formatter'] = function($d, $row) use ($type, $options) {
                if ($d === null || $d === '') {
                    return '';
                }

                switch ($type) {
                    case 'data':
                    case 'date':
                        $ts = strtotime($d);
                        return $ts ? date('d/m/Y', $ts) : $d;

                    case 'checkbox':
                        return ((int)$d === 1) ? 'Sì' : 'No';

                    case 'select':
                        return isset($options[$d]) ? $options[$d] : $d;

                    case 'multiple':
                        $values = dt_parse_multiple_values($d);
                        $labels = [];
                        foreach ($values as $value) {
                            $label = isset($options[$value]) ? $options[$value] : $value;
                            $label = trim((string)$label);
                            if ($label !== '') {
                                $labels[] = $label;
                            }
                        }
                        return implode('<br>', $labels);

                    case 'file':
                        $filename = basename($d);
                        return '<a href="'.$d.'" target="_blank" download>'.$filename.'</a>';

                    case 'bulk_upload':
                        $files = @unserialize($d);
                        if (!is_array($files)) {
                            return '';
                        }
                        $links = [];
                        foreach ($files as $path) {
                            $links[] = '<a href="'.$path.'" target="_blank" download>'.basename($path).'</a>';
                        }
                        return implode(', ', $links);

                    default:
                        return $d;
                }
            };
        endif;

    else:
        // no joins + joins_query non definita => table.field else field
        $datatables_name=(count($app->intent->joins)==0 && empty($app->intent->joins_query)) ? str_replace($app->intent->table.'.','',$column_name) : $column_name;

        $columnDefinition['db'] = $datatables_name;

        if(isset($app->cols[$column_name]['formatter'])):
            $columnDefinition['formatter']=$app->cols[$column_name]['formatter'];
        endif;
		if(isset($app->cols[$column_name]['type'])):
    $columnDefinition['type'] = $app->cols[$column_name]['type'];
endif;

if(isset($app->cols[$column_name]['tag_category'])):
    $columnDefinition['tag_category'] = $app->cols[$column_name]['tag_category'];
endif;

if(isset($app->cols[$column_name]['search_type'])):
    $columnDefinition['search_type'] = $app->cols[$column_name]['search_type'];
elseif(isset($app->cols[$column_name]['type'])):
    $columnDefinition['search_type'] = $app->cols[$column_name]['type'];
endif;
if(isset($app->cols[$column_name]['search_db'])):
    $columnDefinition['search_db'] = $app->cols[$column_name]['search_db'];
endif;
    endif;

    $columns[]=$columnDefinition;

	$counter++;
endforeach;

// DATI
/*
|--------------------------------------------------------------------------
| COLONNA TECNICA AZIONI
|--------------------------------------------------------------------------
| Per archy_art_catalog i pulsanti extra hanno bisogno anche di
| numero_archiviazione. Prima, se la colonna non era visibile nella DataTable,
| il formatter faceva una SELECT per ogni riga visualizzata.
| Con molti record questo crea rallentamenti inutili.
|
| Qui passiamo id e numero_archiviazione in un unico payload tecnico:
|     id|numero_archiviazione
| così evitiamo la query N+1 senza togliere le colonne dinamiche.
*/
$actionDbColumn = ((count($app->intent->joins)==0 && !empty($app->intent->joins_query)) || $hasAdditionalColumns) ? dt_qualify_column($table, $app->actions_column) : $app->actions_column;

if ($_GET['intent'] === 'archy_art_catalog') {
    $catalogIdColumn = dt_qualify_column($table, $primaryKey);
    $actionDbColumn = "CONCAT(CAST(".$catalogIdColumn." AS CHAR),'|',COALESCE(CAST(".$table.".numero_archiviazione AS CHAR),''))";
}


$columns[]=array(
    'db'=>$actionDbColumn,
    'as'=>'id',
	'dt'=>$counter,
	'formatter'=>function($d,$row){
		global $app;
                // ID tecnico del record: non deve dipendere da nessuna colonna visibile
                // come Numero Archiviazione.
                $record_id = $d;
                $numero_archiviazione_tecnico = '';

                // Per il catalogo, $d può arrivare come payload tecnico: id|numero_archiviazione
                // Questo evita una SELECT aggiuntiva per ogni riga quando numero_archiviazione
                // non è tra le colonne visibili selezionate dall'utente.
                if ($_GET['intent'] === 'archy_art_catalog' && strpos((string)$d, '|') !== false) {
                    $payload_parts = explode('|', (string)$d, 2);
                    $record_id = (int) $payload_parts[0];
                    $numero_archiviazione_tecnico = $payload_parts[1] ?? '';
                }

                $intent_filter_q = $_GET['intent_filter'] ?? '';
                $toolbar_buttons = '<div class="btn-toolbar" role="toolbar" style="display:flex;flex-wrap:nowrap;gap:2px;">';
		
                    $extra_notice=isset($app->intent->delete_extra_notice)? '&extra_notice='.$app->intent->delete_extra_notice : '';		
                    $before_delete=isset($app->intent->before_delete)? $app->intent->before_delete : '';		
                    $after_delete=isset($app->intent->after_delete)? $app->intent->after_delete : '';		



                    /*
                    $common_buttons='
                            <a class="btn btn-sm btn-info" 
                                            data-toggle="tooltip" title="Dettagli" 
                                            data-id="'.$record_id.'" data-intent="'.$_GET['intent'].'" 
                                            href="/archy/app.php?intent='.$_GET['intent'].'&view=view&id='.$d.'">
                                            <span class="glyphicon glyphicon-search"></span>
                                    </a>';
                    */
                    // Sostituito view con edit (safe: senza save)

                    /*$common_buttons = '<div class="btn-group me-2 mb-2" role="group">';    
                    $common_buttons.='
						<a class="btn btn-info btn-view" title="Visualizza" 
						  data-id="'.$record_id.'" data-intent="'.$_GET['intent'].'" 
						  href="/archy/app.php?intent='.$_GET['intent'].'&view=view&id='.$record_id.'&intent_filter='.$intent_filter_q.'">
						  <span class="fas fa-search"></span>
						</a>';
						
						/*
|--------------------------------------------------------------------------
| ARCHY ART - PULSANTE PRESENTAZIONE
|--------------------------------------------------------------------------
*/
if ($_GET['intent'] === 'archy_art_catalog') {

    $common_buttons .= '
        <a class="btn btn-success"
           title="Presentazione"
		   style="margin-right:2px;"
           href="/archy/app.php?intent='.$_GET['intent'].'&view=presentation&id='.$record_id.'&intent_filter='.$intent_filter_q.'">
            <span class="fas fa-image"></span>
        </a>';
}

                    if(!in_array($record_id,$app->intent->uneditable_ids)):
                        $common_buttons.='   
						<a class="btn btn-edit btn-view" title="Modifica"
							data-id="'.$record_id.'" data-intent="'.$_GET['intent'].'"
							style="background:#009fe3;border-color:#009fe3;color:#fff;margin-right:2px;"
							href="/archy/app.php?intent='.$_GET['intent'].'&view=edit&id='.$record_id.'&intent_filter='.$intent_filter_q.'">
							<span class="fas fa-edit"></span>
						</a>';
                        if(!isset($_GET['event'])){ // Nascosti se sono in una modale di selezione          
                            if(!in_array($record_id,$app->intent->undeletable_ids)):
                                $common_buttons .='
                                    <a class="btn btn-danger" title="Elimina" 
									    style="margin-right:2px;"
                                        href="/archy/app.php?intent='.$_GET['intent'].'&do_action=delete&id='.$record_id.$extra_notice.$before_delete.$after_delete.'" 
                                        onClick="return confirm(\'Eliminare definitivamente?\')">
                                        <span class="fas fa-trash-alt"></span>
                                    </a>';
                            endif;
                        }
                    endif;
                   
                    if(isset($app->intent->extra_buttons) && $app->intent->extra_buttons!=''):
                        $common_buttons .= '<div class="btn-group" role="group">'; 
                        $common_buttons .= '<button type="button" class="btn btn-warning dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            ...
                          </button>
                          <ul class="dropdown-menu">';
                                $function=$app->intent->extra_buttons;
                                // Alcuni extra_buttons degli intent usano campi specifici della riga.
                                // Esempio archy_art_catalog usa numero_archiviazione per cercare i PDF.
                                // Se l'utente rimuove quella colonna dalla DataTable, il campo non
                                // esiste in $row e PHP stampa un warning, rompendo il JSON.
                                // Qui passiamo una riga safe con fallback vuoto.
                                $safe_row = is_array($row) ? $row : [];
                                if (!isset($safe_row['numero_archiviazione'])) {
                                    $safe_row['numero_archiviazione'] = '';
                                }
                                if (!isset($safe_row['archy_art_catalog.numero_archiviazione'])) {
                                    $safe_row['archy_art_catalog.numero_archiviazione'] = '';
                                }

                                if ($_GET['intent'] === 'archy_art_catalog' && $numero_archiviazione_tecnico !== '') {
                                    $safe_row['numero_archiviazione'] = $numero_archiviazione_tecnico;
                                    $safe_row['archy_art_catalog.numero_archiviazione'] = $numero_archiviazione_tecnico;
                                }

                                // Per Archy Art Catalog i pulsanti extra cercano i PDF usando
                                // numero_archiviazione. Se la colonna non è visibile in griglia,
                                // il valore non arriva dentro $row: lo recuperiamo in modo tecnico
                                // senza obbligare l'utente a tenere la colonna selezionata.
                                if ($_GET['intent'] === 'archy_art_catalog' && $safe_row['numero_archiviazione'] === '') {
                                    $db_extra = $app->get_db();
                                    $record_id_safe = (int) $record_id;
                                    $res_extra = $db_extra->query("select numero_archiviazione from archy_art_catalog where id=".$record_id_safe." limit 1");
                                    if ($res_extra && $obj_extra = mysqli_fetch_object($res_extra)) {
                                        $safe_row['numero_archiviazione'] = $obj_extra->numero_archiviazione;
                                        $safe_row['archy_art_catalog.numero_archiviazione'] = $obj_extra->numero_archiviazione;
                                    }
                                }

                                $common_buttons.=$function($record_id,$safe_row);
                                $common_buttons .='</ul></div>';
                    else:
                            $common_buttons.='';
                    endif;
                    
                    
                    $common_buttons.='</div>';
                    
                    $toolbar_buttons.=$common_buttons;
                
                $toolbar_buttons.='</div>';
                return $toolbar_buttons;
	}
                
);

$sql_details = $app->config['db'];

$joinQuery='';
if(count($app->intent->joins)):
	$joinQuery.=' from '.$table;
	foreach($app->intent->joins as $join_table=>$on):
		$joinQuery.=" left join ".$join_table." on (".$on.") ";
	endforeach;
endif;

// V1.3 - Se la griglia include campi aggiuntivi, serve una joinQuery
// anche per gli intent che normalmente non hanno join.
if(!empty($additionalFieldJoins)):
    if($joinQuery==''):
        $joinQuery=' from '.$table;
    endif;

    foreach($additionalFieldJoins as $additionalFieldJoin):
        $joinQuery.=$additionalFieldJoin;
    endforeach;
endif;

require('ssp.customized.class.php' );

// GESTIONE EXTRA WHERE
$ew='';
if(@$_GET['domain']!=''):
	$ew="domain='".addslashes($_GET['domain'])."'";
elseif(isset($app->intent->filter) && $app->intent->filter!=''): // filtro attivo già al caricamento della intent
    $intent_filter = explode('|', $app->intent->filter);
    //if($_GET['intent']=='archy_models'):
	$ew=$intent_filter[0]."='".addslashes($intent_filter[1])."'";
    //endif;
elseif(isset($_GET['extrawhere'])):
    // Caso particolare: se filtro su type='Socio' => uso archy_membership
    $ewArr=[];
    $clauses=explode('|',$_GET['extrawhere']);
    foreach($clauses as $clause):
        if($_GET['intent']=='archy_registry' && $clause=='Socio') {
            $ewArr[]="EXISTS (SELECT 1 FROM archy_memberships m WHERE m.status=1 AND m.registry_id=archy_registry.id)";
        } else {
            if($_GET['intent']=='archy_art_catalog'){
                $fn='status';
            } else if ($_GET['intent']=='archy_registry'){
                $fn='type';
            }
            $ewArr[]=$fn." like '%".$clause."%'";
        }
    endforeach;
    $ew=implode(" or ",$ewArr);
endif;

if($ew!='' && $app->intent->where!='') {
    $extraWhere = $app->intent->where . " AND (".$ew.")";
} elseif($ew!='') {
    $extraWhere=$ew;
} else {
    $extraWhere=$app->intent->where;
}

if( isset($app->intent->joins_query) && $app->intent->joins_query!='') {
    $joinQuery = $app->intent->joins_query;
}

/*

echo json_encode(
	SSP::simple( $_REQUEST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere )
);

*/

/*
|--------------------------------------------------------------------------
| EXPORT ID FILTRATI PER PDF SCHEDE CATALOGO ACCORPATE
|--------------------------------------------------------------------------
|
| Questa sezione serve per recuperare tutti gli ID risultanti dai filtri
| attualmente applicati alla DataTable.
|
| Viene usata dal pulsante "PDF Schede filtrate" del catalogo opere.
|
| IMPORTANTE:
| - non restituisce solo le righe visibili nella pagina corrente;
| - forza start = 0 e length = -1 per recuperare tutti i record filtrati;
| - mantiene gli stessi filtri, ordinamenti e ricerche inviati dalla DataTable;
| - restituisce solo un JSON semplice con gli ID.
|
| Esempio risposta:
| {
|   "ids": [22, 21, 20],
|   "count": 3
| }
|
| La generazione/accorpamento dei PDF viene poi gestita dal file:
| /merge_schede_catalogo.php
|
*/

if (isset($_REQUEST['ids_only']) && $_REQUEST['ids_only'] == '1') {

    $bindings = [];
    $db = SSP::sql_connect($sql_details);

    /*
    |--------------------------------------------------------------------------
    | Query leggera solo ID
    |--------------------------------------------------------------------------
    | Usa gli stessi filtri della DataTable, ma NON genera le colonne,
    | NON genera formatter HTML, NON costruisce pulsanti e NON carica immagini.
    */

    $where = SSP::filter($_REQUEST, $columns, $bindings, ($joinQuery != ''));
    $order = SSP::order($_REQUEST, $columns, ($joinQuery != ''));

    if ($extraWhere) {
        $extraWhereSql = ($where) ? ' AND '.$extraWhere : ' WHERE '.$extraWhere;
    } else {
        $extraWhereSql = '';
    }

    $start = isset($_REQUEST['start']) ? (int) $_REQUEST['start'] : 0;
    $length = isset($_REQUEST['length']) ? (int) $_REQUEST['length'] : 100;

    if ($length <= 0 || $length > 500) {
        $length = 100;
    }

    $idColumn = dt_qualify_column($table, $primaryKey);

    if ($joinQuery) {
        $query = "
            SELECT ".$idColumn." AS id
            ".$joinQuery."
            ".$where."
            ".$extraWhereSql."
            ".$order."
            LIMIT ".$start.", ".$length."
        ";

        $countQuery = "
            SELECT COUNT(DISTINCT ".$idColumn.")
            ".$joinQuery."
            ".$where."
            ".$extraWhereSql."
        ";
    } else {
        $query = "
            SELECT `".$primaryKey."` AS id
            FROM `".$table."`
            ".$where."
            ".$extraWhereSql."
            ".$order."
            LIMIT ".$start.", ".$length."
        ";

        $countQuery = "
            SELECT COUNT(`".$primaryKey."`)
            FROM `".$table."`
            ".$where."
            ".$extraWhereSql."
        ";
    }

    $rows = SSP::sql_exec($db, $bindings, $query);

    $ids = [];

    foreach ($rows as $row) {
        if (isset($row['id'])) {
            $ids[] = (int) $row['id'];
        } elseif (isset($row[0])) {
            $ids[] = (int) $row[0];
        }
    }

    $countRows = SSP::sql_exec($db, $bindings, $countQuery);
    $recordsFiltered = isset($countRows[0][0]) ? (int) $countRows[0][0] : count($ids);

    echo json_encode([
        'ids' => array_values(array_unique($ids)),
        'count' => count($ids),
        'recordsFiltered' => $recordsFiltered,
        'recordsTotal' => $recordsFiltered
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| RISPOSTA STANDARD DATATABLE
|--------------------------------------------------------------------------
|
| Se non viene richiesto ids_only=1, il file continua a comportarsi
| normalmente e restituisce il JSON completo della DataTable.
|
*/

echo json_encode(
    SSP::simple(
        $_REQUEST,
        $sql_details,
        $table,
        $primaryKey,
        $columns,
        $joinQuery,
        $extraWhere
    )
);