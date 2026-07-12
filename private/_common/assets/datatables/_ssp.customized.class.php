<?php

/*
 * Helper functions for building a DataTables server-side processing SQL query
 *
 * The static functions in this class are just helper functions to help build
 * the SQL used in the DataTables demo server-side processing scripts. These
 * functions obviously do not represent all that can be done with server-side
 * processing, they are intentionally simple to show how it works. More complex
 * server-side processing operations will likely require a custom script.
 *
 * See http://datatables.net/usage/server-side for full details on the server-
 * side processing requirements of DataTables.
 *
 * @license MIT - http://datatables.net/license_mit
 */


// REMOVE THIS BLOCK - used for DataTables test environment only!
// $file = $_SERVER['DOCUMENT_ROOT'].'/datatables/mysql.php';
// if ( is_file( $file ) ) {
//     include( $file );
// }



require_once '/var/www/vhosts/archyart.it/private/_common/classes/ArchySearch/QueryBuilder.php';
require_once '/var/www/vhosts/archyart.it/private/_common/classes/ArchyCore/Schema/ArchySchema.php';
require_once '/var/www/vhosts/archyart.it/private/_common/classes/ArchyCore/Schema/FieldSearchResolver.php';
require_once '/var/www/vhosts/archyart.it/private/_common/classes/ArchySearch/Semantic/SemanticAnalyzer.php';

class SSP {

    /**
     * Create the data output array for the DataTables rows
     *
     * @param array $columns Column information array
     * @param array $data    Data from the SQL get
     * @param bool  $isJoin  Determine the the JOIN/complex query or simple one
     *
     * @return array Formatted data in a row based format
     */

    static function data_output ( $columns, $data, $isJoin = false )
    {
        $out = array();
		
        for ( $i=0, $ien=count($data) ; $i<$ien ; $i++ ) {
            $row = array();

            for ( $j=0, $jen=count($columns) ; $j<$jen ; $j++ ) {
                $column = $columns[$j];
					 // Is there a formatter?
                if ( isset( $column['formatter'] ) ) {
                    $row[ $column['dt'] ] = ($isJoin) ? $column['formatter']( $data[$i][ $j ], $data[$i] ) : $column['formatter']( $data[$i][ $column['db'] ], $data[$i] );
                }
                else {
                    $row[ $column['dt'] ] = ($isJoin) ? $data[$i][ $j] : $data[$i][ $columns[$j]['db'] ];
                }
            }

            $out[] = $row;
        }

        return $out;
    }


    /**
     * Paging
     *
     * Construct the LIMIT clause for server-side processing SQL query
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array $columns Column information array
     *  @return string SQL limit clause
     */
    static function limit ( $request, $columns )
    {
        $limit = '';

        if ( isset($request['start']) && $request['length'] != -1 ) {
            $limit = "LIMIT ".intval($request['start']).", ".intval($request['length']);
        }

        return $limit;
    }


    /**
     * Ordering
     *
     * Construct the ORDER BY clause for server-side processing SQL query
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array $columns Column information array
     *  @param bool  $isJoin  Determine the the JOIN/complex query or simple one
     *
     *  @return string SQL order by clause
     */
static function order ( $request, $columns, $isJoin = false )
{
    $orderBy = array();

    if ( isset($request['order']) && count($request['order']) ) {
        $dtColumns = SSP::pluck( $columns, 'dt' );

        for ( $i=0, $ien=count($request['order']) ; $i<$ien ; $i++ ) {

            $columnIdx = intval($request['order'][$i]['column']);

            if (!isset($request['columns'][$columnIdx])) {
                continue;
            }

            $requestColumn = $request['columns'][$columnIdx];

            $realColumnIdx = array_search( $requestColumn['data'], $dtColumns );

            if ($realColumnIdx === false || !isset($columns[$realColumnIdx])) {
                continue;
            }

            $column = $columns[$realColumnIdx];

            if (
                !isset($requestColumn['orderable']) ||
                $requestColumn['orderable'] != 'true' ||
                !isset($column['db']) ||
                trim($column['db']) === ''
            ) {
                continue;
            }

            $dir = $request['order'][$i]['dir'] === 'asc' ? 'ASC' : 'DESC';

            $orderBy[] = ($isJoin)
                ? $column['db'].' '.$dir
                : '`'.$column['db'].'` '.$dir;
        }
    }

    return count($orderBy) ? 'ORDER BY '.implode(', ', $orderBy) : '';
}


    /**
     * Searching / Filtering
     *
     * Construct the WHERE clause for server-side processing SQL query.
     *
     * NOTE this does not match the built-in DataTables filtering which does it
     * word by word on any field. It's possible to do here performance on large
     * databases would be very poor
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array $columns Column information array
     *  @param  array $bindings Array of values for PDO bindings, used in the sql_exec() function
     *  @param  bool  $isJoin  Determine the the JOIN/complex query or simple one
     *
     *  @return string SQL where clause
     */
    static function filter ( $request, $columns, &$bindings, $isJoin = false, $db = null )
    {
        $globalSearch = array();
        $columnSearch = array();
        $dtColumns = SSP::pluck( $columns, 'dt' );
		$archySchema = null;

		if (class_exists('ArchySchema')) {

			$schemaIntent = new stdClass();
			$schemaIntent->columns = array();

			foreach ($columns as $schemaColumn) {
				if (!isset($schemaColumn['db'])) {
					continue;
				}

				$columnSql = $schemaColumn['db'];

				$schemaIntent->columns[$columnSql] = array(
					'label' => isset($schemaColumn['label']) ? $schemaColumn['label'] : '',
					'type' => isset($schemaColumn['type']) ? $schemaColumn['type'] : 'text',
					'search_type' => isset($schemaColumn['search_type']) ? $schemaColumn['search_type'] : '',
					'tag_category' => isset($schemaColumn['tag_category']) ? $schemaColumn['tag_category'] : '',
					'lookup_text' => isset($schemaColumn['lookup_text']) ? $schemaColumn['lookup_text'] : '',
					'intent' => isset($schemaColumn['intent']) ? $schemaColumn['intent'] : ''
				);
				if ($columnSql == 'archy_art_catalog.data' || $columnSql == 'data') {
					$schemaIntent->columns[$columnSql]['search_alias'] = array('anno', 'year');
				}
			}

			$archySchema = new ArchySchema('datatable', $schemaIntent);
			
			
			if ($db && method_exists($db, 'query')) {
    $res = $db->query("SELECT tag, categoria FROM archy_tag WHERE status = 1");

    while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
        foreach ($archySchema->fields() as $field) {
            if (
                $field->searchType() === 'tags' &&
                $field->tagCategory() === $row['categoria']
            ) {
                $archySchema->valueIndex()->add($row['tag'], $field->name());
            }
        }
    }
}
		}

        if ( isset($request['search']) && $request['search']['value'] != '' ) {
            $str = $request['search']['value'];
				if(strpos($str,'/')===false){
				}else{
					$arr=explode('/',$str);
					$str=join('-',array_reverse($arr));
				}
	
	if (
    $archySchema &&
    class_exists('SearchQuery') &&
    class_exists('FieldExpression') &&
    class_exists('FieldSearchResolver')
) {
	
	if (class_exists('ArchySearchSemanticAnalyzer')) {
    $semantic = new ArchySearchSemanticAnalyzer($archySchema, $db);
    $str = $semantic->analyze($str);
}
	
    $query = SearchQuery::parse($str);

    foreach ($query->expressions() as $expression) {
        if ($expression instanceof FieldExpression) {
            $field = $archySchema->field($expression->field());

            if ($field) {
                $where = FieldSearchResolver::build(
                    $field,
                    $expression->expression(),
                    $bindings
                );

                if ($where !== false) {
                    $globalSearch[] = $where;
                }
            }
        }
    }
}

if (!empty($globalSearch)) {
    return 'WHERE '.implode(' AND ', $globalSearch);
}

            /*
            |--------------------------------------------------------------------------
            | ARCHY PERFORMANCE - RICERCA GLOBALE CATALOGO
            |--------------------------------------------------------------------------
            |
            | La ricerca globale DataTable, nella versione standard, cerca su TUTTE
            | le colonne visibili. Con il catalogo opere questo significa includere
            | anche campi aggiuntivi, COALESCE(afv_X.value), allegati, testi lunghi e
            | join dinamiche. Su archivi grandi può saturare MySQL.
            |
            | Per archy_art_catalog limitiamo quindi la ricerca globale alle colonne
            | principali leggere. I filtri specifici sotto le singole colonne restano
            | invariati e continuano a funzionare anche sui campi dinamici.
            */
            $intentName = isset($_GET['intent']) ? $_GET['intent'] : '';

            $globalSearchWhitelist = array();


            for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ ) {
                $requestColumn = $request['columns'][$i];
                $columnIdx = array_search( $requestColumn['data'], $dtColumns );

                if ($columnIdx === false || !isset($columns[$columnIdx])) {
                    continue;
                }

                $column = $columns[ $columnIdx ];

                if ( $requestColumn['searchable'] == 'true' ) {

                    if (!empty($globalSearchWhitelist)) {
                        $columnDb = isset($column['db']) ? $column['db'] : '';

                        // Esclude campi virtuali/aggregati tipo COALESCE(afv_12.value,'')
                        if (stripos($columnDb, 'COALESCE(') !== false || stripos($columnDb, 'afv_') !== false) {
                            continue;
                        }

                        $normalizedColumn = str_replace('`', '', $columnDb);
                        if (strpos($normalizedColumn, '.') !== false) {
                            $parts = explode('.', $normalizedColumn);
                            $normalizedColumn = end($parts);
                        }

                        if (!in_array($normalizedColumn, $globalSearchWhitelist)) {
                            continue;
                        }
					}
						/*
|--------------------------------------------------------------------------
| ARCHY SEARCH v1.5 - FRASE ESATTA - RICERCA GLOBALE
|--------------------------------------------------------------------------
*/

if (
    $requestColumn['searchable'] == 'true' &&
    preg_match('/^["“](.+)["”]$/u', trim($str), $matches)
) {

    $columnSql = isset($column['search_db']) && $column['search_db'] != ''
        ? $column['search_db']
        : (($isJoin) ? $column['db'] : "`".$column['db']."`");

    $phrase = trim($matches[1]);

    if ($phrase !== '') {
		$phraseRegex = preg_quote(strtolower($phrase), '/');
		$bindingRegex = SSP::bind(
			$bindings,
			'(^|[^[:alnum:]])'.$phraseRegex.'([^[:alnum:]]|$)',
			PDO::PARAM_STR
		);

		$globalSearch[] = "LOWER(TRIM(CAST(".$columnSql." AS CHAR))) REGEXP ".$bindingRegex;
			}

			continue;
		}
						
						
						
                    

if (isset($column['search_type']) && $column['search_type'] == 'publications') {
    $columnSql = ($isJoin) ? $column['db'] : "`".$column['db']."`";
    $tokens = array_filter(preg_split('/\s+/', trim($str)));
    $publicationExists = [];

    foreach($tokens as $token){
        $bindingToken = SSP::bind($bindings, '%'.trim($token).'%', PDO::PARAM_STR);

        $publicationExists[] = "EXISTS (
            SELECT 1 FROM archy_art_publications ap
            WHERE ap.titolo_pubblicazione LIKE ".$bindingToken."
            AND (
                ".$columnSql." = CAST(ap.id AS CHAR)
                OR ".$columnSql." LIKE CONCAT('%,', ap.id, ',%')
                OR ".$columnSql." LIKE CONCAT(ap.id, ',%')
                OR ".$columnSql." LIKE CONCAT('%,', ap.id)
                OR ".$columnSql." LIKE CONCAT('%\"', ap.id, '\"%')
                OR ".$columnSql." REGEXP CONCAT('s:[0-9]+:\"', ap.id, '\";')
                OR ".$columnSql." REGEXP CONCAT('i:', ap.id, ';')
            )
        )";
    }

    if(!empty($publicationExists)){
        $globalSearch[] = '('.implode(' AND ', $publicationExists).')';
    }

    continue;
}

if (isset($column['search_type']) && $column['search_type'] == 'exhibitions') {
    $columnSql = ($isJoin) ? $column['db'] : "`".$column['db']."`";
    $tokens = array_filter(preg_split('/\s+/', trim($str)));
    $exists = [];

    foreach($tokens as $token){
        $bindingToken = SSP::bind($bindings, '%'.trim($token).'%', PDO::PARAM_STR);

        $exists[] = "EXISTS (
            SELECT 1 FROM archy_art_exhibitions ae
            WHERE ae.nome_esposizione LIKE ".$bindingToken."
            AND (
                ".$columnSql." = CAST(ae.id AS CHAR)
                OR ".$columnSql." LIKE CONCAT('%,', ae.id, ',%')
                OR ".$columnSql." LIKE CONCAT(ae.id, ',%')
                OR ".$columnSql." LIKE CONCAT('%,', ae.id)
                OR ".$columnSql." LIKE CONCAT('%\"', ae.id, '\"%')
                OR ".$columnSql." REGEXP CONCAT('s:[0-9]+:\"', ae.id, '\";')
                OR ".$columnSql." REGEXP CONCAT('i:', ae.id, ';')
            )
        )";
    }

    if(!empty($exists)){
        $globalSearch[] = '('.implode(' AND ', $exists).')';
    }

    continue;
}

if (isset($column['search_type']) && $column['search_type'] == 'authors') {
    $columnSql = ($isJoin) ? $column['db'] : "`".$column['db']."`";
    $tokens = array_filter(preg_split('/\s+/', trim($str)));
    $exists = [];

    foreach($tokens as $token){
        $bindingToken = SSP::bind($bindings, '%'.trim($token).'%', PDO::PARAM_STR);

        $exists[] = "EXISTS (
            SELECT 1 FROM archy_art_authors aa
            WHERE aa.name_author LIKE ".$bindingToken."
            AND (
                ".$columnSql." = CAST(aa.id AS CHAR)
                OR ".$columnSql." LIKE CONCAT('%,', aa.id, ',%')
                OR ".$columnSql." LIKE CONCAT(aa.id, ',%')
                OR ".$columnSql." LIKE CONCAT('%,', aa.id)
                OR ".$columnSql." LIKE CONCAT('%\"', aa.id, '\"%')
                OR ".$columnSql." REGEXP CONCAT('s:[0-9]+:\"', aa.id, '\";')
                OR ".$columnSql." REGEXP CONCAT('i:', aa.id, ';')
            )
        )";
    }

    if(!empty($exists)){
        $globalSearch[] = '('.implode(' AND ', $exists).')';
    }

    continue;
}				
					
if (isset($column['search_type']) && $column['search_type'] == 'tags') {

    $columnSql = ($isJoin) ? $column['db'] : "`".$column['db']."`";
    $tagCategory = isset($column['tag_category']) ? $column['tag_category'] : 'Tag';

    $tokens = preg_split('/[\s\-\/]+/', trim($str));
    $tokens = array_filter($tokens);

    $tagExists = [];

    foreach($tokens as $token){

        $token = trim($token);

        if($token === ''){
            continue;
        }

        $bindingTagToken = SSP::bind($bindings, '%'.$token.'%', PDO::PARAM_STR);
        $bindingCategory = SSP::bind($bindings, $tagCategory, PDO::PARAM_STR);

        $tagExists[] = "EXISTS (
            SELECT 1
            FROM archy_tag atg
            WHERE atg.status = 1
            AND atg.categoria = ".$bindingCategory."
            AND atg.tag LIKE ".$bindingTagToken."
            AND (
                ".$columnSql." = CAST(atg.id AS CHAR)
                OR ".$columnSql." LIKE CONCAT('%,', atg.id, ',%')
                OR ".$columnSql." LIKE CONCAT(atg.id, ',%')
                OR ".$columnSql." LIKE CONCAT('%,', atg.id)
                OR ".$columnSql." LIKE CONCAT('%\"', atg.id, '\"%')
            )
        )";
    }

    if(!empty($tagExists)){
        $globalSearch[] = '('.implode(' AND ', $tagExists).')';
    }

    continue;
}

/* Ricerca descrittiva per token */
if (
    isset($column['db']) &&
    (
        strpos($column['db'], 'titolo') !== false ||
        strpos($column['db'], 'autore') !== false ||
        strpos($column['db'], 'inventario') !== false
    )
) {
    $columnSql = ($isJoin) ? $column['db'] : "`".$column['db']."`";

    $tokens = preg_split('/[\s\-\/]+/', trim($str));
    $tokens = array_filter($tokens);

    $conditions = [];

    foreach($tokens as $token){
        $bindingToken = SSP::bind($bindings, '%'.$token.'%', PDO::PARAM_STR);
        $conditions[] = $columnSql.' LIKE '.$bindingToken;
    }

    if(!empty($conditions)){
        $globalSearch[] = '('.implode(' AND ', $conditions).')';
    }

    continue;
}

/* Ricerca archivistica codici */
if (
    isset($column['db']) &&
    (
        strpos($column['db'], 'numero_archiviazione') !== false ||
        strpos($column['db'], 'codice') !== false
    )
) {
    $columnSql = ($isJoin) ? $column['db'] : "`".$column['db']."`";

    $normalizedSearch = strtoupper(trim($str));
    $normalizedSearch = preg_replace('/[^A-Z0-9]/', '', $normalizedSearch);
    $normalizedSearchNoZeros = preg_replace('/([A-Z]+)0+([0-9]+)/', '$1$2', $normalizedSearch);

    $searchTokens = preg_split('/[\s\-\/]+/', strtoupper(trim($str)));
    $searchTokens = array_filter($searchTokens);

    $normalizedColumn = "
        REPLACE(
            REPLACE(
                REPLACE(
                    REPLACE(UPPER(".$columnSql."), ' ', ''),
                '-', ''),
            '/', ''),
        '.', '')
    ";

    $normalizedColumnNoZeros = "
        REGEXP_REPLACE(
            ".$normalizedColumn.",
            '([A-Z]+)0+([0-9]+)',
            '\\\\1\\\\2'
        )
    ";

    $binding = SSP::bind($bindings, '%'.$normalizedSearch.'%', PDO::PARAM_STR);
    $bindingNoZeros = SSP::bind($bindings, '%'.$normalizedSearchNoZeros.'%', PDO::PARAM_STR);

    $tokenConditions = [];

    foreach($searchTokens as $token){
        $token = preg_replace('/[^A-Z0-9]/', '', $token);

        if($token === ''){
            continue;
        }

        $bindingToken = SSP::bind($bindings, '%'.$token.'%', PDO::PARAM_STR);
        $tokenConditions[] = $normalizedColumnNoZeros.' LIKE '.$bindingToken;
    }

    $tokenSql = !empty($tokenConditions) ? ' OR ('.implode(' AND ', $tokenConditions).')' : '';

    $globalSearch[] = "(
        ".$normalizedColumn." LIKE ".$binding."
        OR ".$normalizedColumnNoZeros." LIKE ".$bindingNoZeros."
        ".$tokenSql."
    )";

    continue;
}

$columnSql = isset($column['search_db']) && $column['search_db'] != ''
    ? $column['search_db']
    : (($isJoin) ? $column['db'] : "`".$column['db']."`");

$tokens = preg_split('/\s+/', trim($str));
$tokens = array_filter($tokens);

$conditions = [];

foreach($tokens as $token){

    $token = trim($token);

    if($token === ''){
        continue;
    }

    $binding = SSP::bind($bindings, '%'.$token.'%', PDO::PARAM_STR);
    $conditions[] = $columnSql." LIKE ".$binding;
}

if(!empty($conditions)){
    $globalSearch[] = '('.implode(' AND ', $conditions).')';
}

     }
            }
        }						
						

				// Individual column filtering
				for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ ) {
					$requestColumn = $request['columns'][$i];
					$columnIdx = array_search( $requestColumn['data'], $dtColumns );

                    if ($columnIdx === false || !isset($columns[$columnIdx])) {
                        continue;
                    }

					$column = $columns[ $columnIdx ];

					$str = $requestColumn['search']['value'];
					$customSearchTypes = array(
    'tags',
    'multiple',
    'publications',
    'exhibitions',
    'authors'
);

$isCustomSearchType = isset($column['search_type']) && in_array($column['search_type'], $customSearchTypes);

$isCodeSearch = isset($column['db']) && (
    strpos($column['db'], 'numero_archiviazione') !== false ||
    strpos($column['db'], 'codice') !== false
);

if (
    class_exists('SearchQuery') &&
    class_exists('ArchySearchQueryBuilder') &&
    $requestColumn['searchable'] == 'true' &&
    trim($str) !== '' &&
    !$isCustomSearchType &&
    !$isCodeSearch
) {
	/*if (class_exists('ArchySearchTranslator')) {
    $translator = new ArchySearchTranslator();
    $translated = $translator->translate($str);

    if ($translated != trim($str)) {
        $str = $translated;
    }
}*/
    $query = SearchQuery::parse($str);

    $columnSql = isset($column['search_db']) && $column['search_db'] != ''
        ? $column['search_db']
        : (($isJoin) ? $column['db'] : "`".$column['db']."`");

    $where = ArchySearchQueryBuilder::buildColumnWhere(
        $query,
        $columnSql,
        $bindings,
        'column'
    );

    if ($where !== false) {
        $columnSearch[] = $where;
        continue;
    }
}
					//$archyParsedSearch = ArchySearch::parseBasic($str);

$cleanSearch = trim(strtolower($str));

if (
    $requestColumn['searchable'] == 'true' &&
    ($cleanSearch == '[empty]' || $cleanSearch == 'empty' || $cleanSearch == 'null')
) {

    $columnSql = ($isJoin) ? $column['db'] : "`".$column['db']."`";

    $cleanColumnSql = "TRIM(
    REPLACE(
        REPLACE(
            REPLACE(
                REPLACE(".$columnSql.", '&nbsp;', ''),
            '<p></p>', ''),
        '<br>', ''),
    '<br />', '')
)";

$emptyConditions = array(
    $columnSql." IS NULL",
    $cleanColumnSql." = ''",
    $columnSql." = '0'",
    $columnSql." = '[]'",
    $columnSql." = 'a:0:{}'"
);

    $columnSearch[] = "(".implode(" OR ", $emptyConditions).")";

    continue;
}

if (
    $requestColumn['searchable'] == 'true' &&
    ($cleanSearch == 'notempty')
) {

    $columnSql = ($isJoin) ? $column['db'] : "`".$column['db']."`";

    $cleanColumnSql = "TRIM(
    REPLACE(
        REPLACE(
            REPLACE(
                REPLACE(".$columnSql.", '&nbsp;', ''),
            '<p></p>', ''),
        '<br>', ''),
    '<br />', '')
)";

$notEmptyConditions = array(
    $columnSql." IS NOT NULL",
    $cleanColumnSql." <> ''",
    $columnSql." <> '0'",
    $columnSql." <> '[]'",
    $columnSql." <> 'a:0:{}'"
);

    $columnSearch[] = "(".implode(" AND ", $notEmptyConditions).")";

    continue;
}



				/*
                |--------------------------------------------------------------------------
                | FILTRO CAMPI TAG
                |--------------------------------------------------------------------------
                */
if (isset($column['search_type']) && $column['search_type'] == 'tags') {

    $columnSql = ($isJoin) ? $column['db'] : "`".$column['db']."`";
    $tagCategory = isset($column['tag_category']) ? $column['tag_category'] : 'Tag';

    $tokens = preg_split('/[\s\-\/]+/', trim($str));
    $tokens = array_filter($tokens);

    $tagExists = [];

    foreach($tokens as $token){

        $token = trim($token);

        if($token === ''){
            continue;
        }

        $bindingTagToken = SSP::bind($bindings, '%'.$token.'%', PDO::PARAM_STR);
        $bindingCategory = SSP::bind($bindings, $tagCategory, PDO::PARAM_STR);

        $tagExists[] = "EXISTS (
            SELECT 1
            FROM archy_tag atg
            WHERE atg.status = 1
            AND atg.categoria = ".$bindingCategory."
            AND atg.tag LIKE ".$bindingTagToken."
            AND (
                ".$columnSql." = CAST(atg.id AS CHAR)
                OR ".$columnSql." LIKE CONCAT('%,', atg.id, ',%')
                OR ".$columnSql." LIKE CONCAT(atg.id, ',%')
                OR ".$columnSql." LIKE CONCAT('%,', atg.id)
                OR ".$columnSql." LIKE CONCAT('%\"', atg.id, '\"%')
            )
        )";
    }

    if(!empty($tagExists)){
        $columnSearch[] = '('.implode(' AND ', $tagExists).')';
    }

    continue;
}
			
				
                /*
                |--------------------------------------------------------------------------
                | FILTRO CAMPI MULTIPLE
                |--------------------------------------------------------------------------
                |
                | I campi "multiple" possono essere salvati in forme diverse:
                |
                | - array PHP serializzato: a:2:{i:0;s:1:"1";i:1;s:1:"3";}
                | - CSV/lista: 1,3
                | - JSON/lista testuale
                |
                | Non possiamo quindi usare:
                |
                |     campo = valore
                |
                | perché funzionerebbe solo per la select singola.
                |
                | Se source.php marca la colonna con search_type = multiple,
                | cerchiamo il valore dentro queste varianti.
                |
                */
				
				if (isset($column['search_type']) && $column['search_type'] == 'publications') {

    $columnSql = ($isJoin) ? $column['db'] : "`".$column['db']."`";

    $tokens = preg_split('/\s+/', trim($str));
    $tokens = array_filter($tokens);

    $publicationExists = [];

    foreach($tokens as $token){

        $token = trim($token);

        if($token === ''){
            continue;
        }

        $bindingToken = SSP::bind($bindings, '%'.$token.'%', PDO::PARAM_STR);

        $publicationExists[] = "EXISTS (
            SELECT 1
            FROM archy_art_publications ap
            WHERE ap.titolo_pubblicazione LIKE ".$bindingToken."
            AND (
                ".$columnSql." = CAST(ap.id AS CHAR)

                OR ".$columnSql." LIKE CONCAT('%,', ap.id, ',%')
                OR ".$columnSql." LIKE CONCAT(ap.id, ',%')
                OR ".$columnSql." LIKE CONCAT('%,', ap.id)

                OR ".$columnSql." LIKE CONCAT('%\"', ap.id, '\"%')

                OR ".$columnSql." REGEXP CONCAT('s:[0-9]+:\"', ap.id, '\";')
                OR ".$columnSql." REGEXP CONCAT('i:', ap.id, ';')
            )
        )";
    }

    if(!empty($publicationExists)){
        $columnSearch[] = '('.implode(' AND ', $publicationExists).')';
    }

    continue;
}

if (isset($column['search_type']) && $column['search_type'] == 'exhibitions') {

    $columnSql = ($isJoin) ? $column['db'] : "`".$column['db']."`";

    $tokens = preg_split('/\s+/', trim($str));
    $tokens = array_filter($tokens);

    $exists = [];

    foreach($tokens as $token){

        $token = trim($token);

        if($token === ''){
            continue;
        }

        $bindingToken = SSP::bind($bindings, '%'.$token.'%', PDO::PARAM_STR);

        $exists[] = "EXISTS (
            SELECT 1
            FROM archy_art_exhibitions ae
            WHERE ae.nome_esposizione LIKE ".$bindingToken."
            AND (
                ".$columnSql." = CAST(ae.id AS CHAR)
                OR ".$columnSql." LIKE CONCAT('%,', ae.id, ',%')
                OR ".$columnSql." LIKE CONCAT(ae.id, ',%')
                OR ".$columnSql." LIKE CONCAT('%,', ae.id)
                OR ".$columnSql." LIKE CONCAT('%\"', ae.id, '\"%')
                OR ".$columnSql." REGEXP CONCAT('s:[0-9]+:\"', ae.id, '\";')
                OR ".$columnSql." REGEXP CONCAT('i:', ae.id, ';')
            )
        )";
    }

    if(!empty($exists)){
        $columnSearch[] = '('.implode(' AND ', $exists).')';
    }

    continue;
}


if (isset($column['search_type']) && $column['search_type'] == 'authors') {

    $columnSql = ($isJoin) ? $column['db'] : "`".$column['db']."`";

    $tokens = preg_split('/\s+/', trim($str));
    $tokens = array_filter($tokens);

    $exists = [];

    foreach($tokens as $token){

        $token = trim($token);

        if($token === ''){
            continue;
        }

        $bindingToken = SSP::bind($bindings, '%'.$token.'%', PDO::PARAM_STR);

        $exists[] = "EXISTS (
            SELECT 1
            FROM archy_art_authors aa
            WHERE aa.name_author LIKE ".$bindingToken."
            AND (
                ".$columnSql." = CAST(aa.id AS CHAR)
                OR ".$columnSql." LIKE CONCAT('%,', aa.id, ',%')
                OR ".$columnSql." LIKE CONCAT(aa.id, ',%')
                OR ".$columnSql." LIKE CONCAT('%,', aa.id)
                OR ".$columnSql." LIKE CONCAT('%\"', aa.id, '\"%')
                OR ".$columnSql." REGEXP CONCAT('s:[0-9]+:\"', aa.id, '\";')
                OR ".$columnSql." REGEXP CONCAT('i:', aa.id, ';')
            )
        )";
    }

    if(!empty($exists)){
        $columnSearch[] = '('.implode(' AND ', $exists).')';
    }

    continue;
}

                if ( isset($column['search_type']) && $column['search_type'] == 'text' ) {

    $columnSql = isset($column['search_db']) && $column['search_db'] != ''
        ? $column['search_db']
        : (($isJoin) ? $column['db'] : "`".$column['db']."`");

    $cleanSearch = trim(strtolower($str));

    if ($cleanSearch == '[empty]' || $cleanSearch == 'empty') {

        $columnSearch[] = "(".$columnSql." IS NULL OR ".$columnSql." = '')";

    } else {

        $tokens = preg_split('/\s+/', trim($str));
        $tokens = array_filter($tokens);

        $conditions = [];

        foreach($tokens as $token){

            $token = trim($token);

            if($token === ''){
                continue;
            }

            $binding = SSP::bind($bindings, '%'.$token.'%', PDO::PARAM_STR);
            $conditions[] = $columnSql." LIKE ".$binding;
        }

        if(!empty($conditions)){
            $columnSearch[] = '('.implode(' AND ', $conditions).')';
        }
    }

}
				
				
				
				elseif ( isset($column['search_type']) && $column['search_type'] == 'multiple' ) {

                    /*
                    |--------------------------------------------------------------------------
                    | FILTRO SELECT MULTIPLA - MATCH PRECISO
                    |--------------------------------------------------------------------------
                    |
                    | La versione precedente cercava anche:
                    |
                    |     LIKE '%i:2;%'
                    |
                    | ma nei serializzati PHP "i:2;" può rappresentare l'INDICE
                    | dell'array, non il valore selezionato.
                    |
                    | Esempio:
                    |
                    |     a:3:{i:0;s:1:"1";i:1;s:1:"3";i:2;s:1:"5";}
                    |
                    | In questo caso i:2 è solo la terza posizione, quindi non deve
                    | far passare il filtro per valore 2.
                    |
                    | Ora cerchiamo:
                    | - valore esatto;
                    | - CSV/lista;
                    | - JSON/stringa quotata;
                    | - PHP serialized string value: i:N;s:L:"VALORE";
                    | - PHP serialized int value:    i:N;i:VALORE;
                    |
                    */

                    $columnSql = ($isJoin) ? $column['db'] : "`".$column['db']."`";

                    $bindingExact = SSP::bind( $bindings, $str, PDO::PARAM_STR );
                    $bindingQuoted = SSP::bind( $bindings, '%"'.$str.'"%', PDO::PARAM_STR );
                    $bindingCsv = SSP::bind( $bindings, '%,'.$str.',%', PDO::PARAM_STR );

                    $bindingSerializedStringRegexp = SSP::bind(
                        $bindings,
                        'i:[0-9]+;s:[0-9]+:"'.preg_quote($str, '/').'";',
                        PDO::PARAM_STR
                    );

                    $bindingSerializedIntRegexp = SSP::bind(
                        $bindings,
                        'i:[0-9]+;i:'.preg_quote($str, '/').';',
                        PDO::PARAM_STR
                    );

                    $columnSearch[] = "(
                        ".$columnSql." = ".$bindingExact."
                        OR ".$columnSql." LIKE ".$bindingQuoted."
                        OR CONCAT(',', ".$columnSql.", ',') LIKE ".$bindingCsv."
                        OR ".$columnSql." REGEXP ".$bindingSerializedStringRegexp."
                        OR ".$columnSql." REGEXP ".$bindingSerializedIntRegexp."
                    )";

                } elseif (is_numeric($str)) {
                   if(in_array($column['db'], $serialized_fields)){ //  $column['db'] == 'archy_scheda_catalogo.esposizione'
                        $binding = SSP::bind( $bindings, '.*;s:[0-9]+:"'.$str.'".*', PDO::PARAM_STR );
                        $columnSearch[] = ($isJoin) ? $column['db']." REGEXP ".$binding : "`".$column['db']."` REGEXP ".$binding;
                    }else{
                        $binding = SSP::bind( $bindings, $str, PDO::PARAM_STR );
                        $columnSearch[] = ($isJoin) ? $column['db']." = ".$binding : "`".$column['db']."` = ".$binding;
                    }
                }else{
                    $binding = SSP::bind( $bindings, '%'.$str.'%', PDO::PARAM_STR );
                    $columnSearch[] = ($isJoin) ? $column['db']." LIKE ".$binding : "`".$column['db']."` LIKE ".$binding;
                }
            }

        // Combine the filters into a single string
        $where = '';

        if ( count( $globalSearch ) ) {
            $where = '('.implode(' OR ', $globalSearch).')';
        }

        if ( count( $columnSearch ) ) {
            $where = $where === '' ?
                implode(' AND ', $columnSearch) :
                $where .' AND '. implode(' AND ', $columnSearch);
        }

        if ( $where !== '' ) {
            $where = 'WHERE '.$where;
        }

        return $where;
    }


    /**
     * Perform the SQL queries needed for an server-side processing requested,
     * utilising the helper functions of this class, limit(), order() and
     * filter() among others. The returned array is ready to be encoded as JSON
     * in response to an SSP request, or can be modified if needed before
     * sending back to the client.
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array $sql_details SQL connection details - see sql_connect()
     *  @param  string $table SQL table to query
     *  @param  string $primaryKey Primary key of the table
     *  @param  array $columns Column information array
     *  @param  array $joinQuery Join query String
     *  @param  string $extraWhere Where query String
     *
     *  @return array  Server-side processing response array
     *
     */
    static function simple ( $request, $sql_details, $table, $primaryKey, $columns, $joinQuery = NULL, $extraWhere = '', $groupBy = '')
    {
        $bindings = array();
        $db = SSP::sql_connect( $sql_details );

        // Build the SQL query string from the request
        $limit = SSP::limit( $request, $columns );
        $order = SSP::order( $request, $columns, $joinQuery );
        $where = SSP::filter( $request, $columns, $bindings, $joinQuery, $db );

		// IF Extra where set then set and prepare query
        if($extraWhere)
            $extraWhere = ($where) ? ' AND '.$extraWhere : ' WHERE '.$extraWhere;
        
        $groupBy = ($groupBy) ? ' GROUP BY '.$groupBy .' ' : '';
        
        // Main query to actually get the data
        if($joinQuery){
            $col = SSP::pluck($columns, 'db', $joinQuery);
            $query =  "SELECT SQL_CALC_FOUND_ROWS ".implode(", ", $col)."
			 $joinQuery
			 $where
			 $extraWhere
			 $groupBy
			 $order
			 $limit";
        }else{
            $query =  "SELECT SQL_CALC_FOUND_ROWS `".implode("`, `", SSP::pluck($columns, 'db'))."`
			 FROM `$table`
			 $where
			 $extraWhere
			 $groupBy
			 $order
			 $limit";
        }
        	  
        $debugSlow = (isset($_REQUEST['debug_slow']) && $_REQUEST['debug_slow'] == '1');
        $debugLogFile = __DIR__.'/datatable_slow_query.log';

        if ($debugSlow) {
            file_put_contents(
                $debugLogFile,
                "\n==============================\n".
                date('Y-m-d H:i:s')."\n".
                "INTENT: ".(isset($_GET['intent']) ? $_GET['intent'] : '')."\n".
                "REQUEST_SEARCH: ".(isset($request['search']['value']) ? $request['search']['value'] : '')."\n".
                "QUERY:\n".$query."\n".
                "BINDINGS:\n".print_r($bindings, true)."\n",
                FILE_APPEND
            );
        }

        $t0 = microtime(true);
        $data = SSP::sql_exec( $db, $bindings,$query);
        $t1 = microtime(true);

        // Data set length after filtering
        $resFilterLength = SSP::sql_exec( $db,
            "SELECT FOUND_ROWS()"
        );
        $t2 = microtime(true);
          
        $recordsFiltered = $resFilterLength[0][0];

        if ($debugSlow) {
            file_put_contents(
                $debugLogFile,
                "TIME_MAIN_QUERY: ".round($t1 - $t0, 4)." sec\n".
                "TIME_FOUND_ROWS: ".round($t2 - $t1, 4)." sec\n".
                "ROWS_RETURNED: ".count($data)."\n".
                "RECORDS_FILTERED: ".$recordsFiltered."\n",
                FILE_APPEND
            );
        }

        // Total data set length
        $resTotalLength = SSP::sql_exec( $db,
            "SELECT COUNT(`{$primaryKey}`)
			 FROM   `$table`"
        );
        $recordsTotal = $resTotalLength[0][0];


        /*
         * Output
         */
        return array(
            "draw"            => intval( $request['draw'] ),
            "recordsTotal"    => intval( $recordsTotal ),
            "recordsFiltered" => intval( $recordsFiltered ),
            "data"            => SSP::data_output( $columns, $data, $joinQuery )
        );
    }


    /**
     * Connect to the database
     *
     * @param  array $sql_details SQL server connection details array, with the
     *   properties:
     *     * host - host name
     *     * db   - database name
     *     * user - user name
     *     * pass - user password
     * @return resource Database connection handle
     */
    static function sql_connect ( $sql_details )
    {
        try {
            $db = @new PDO(
                "mysql:host={$sql_details['host']};dbname={$sql_details['name']}",
                $sql_details['user'],
                $sql_details['pass'],
                array( PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION )
            );
            $db->query("SET NAMES 'utf8'");
        }
        catch (PDOException $e) {
            SSP::fatal(
                "An error occurred while connecting to the database. ".
                "The error reported by the server was: ".$e->getMessage()
            );
        }

        return $db;
    }


    /**
     * Execute an SQL query on the database
     *
     * @param  resource $db  Database handler
     * @param  array    $bindings Array of PDO binding values from bind() to be
     *   used for safely escaping strings. Note that this can be given as the
     *   SQL query string if no bindings are required.
     * @param  string   $sql SQL query to execute.
     * @return array         Result from the query (all rows)
     */
    static function sql_exec ( $db, $bindings, $sql=null )
    {
        // Argument shifting
        if ( $sql === null ) {
            $sql = $bindings;
        }

        $stmt = $db->prepare( $sql );
        //echo $sql;

        // Bind parameters
        if ( is_array( $bindings ) ) {
            for ( $i=0, $ien=count($bindings) ; $i<$ien ; $i++ ) {
                $binding = $bindings[$i];
                $stmt->bindValue( $binding['key'], $binding['val'], $binding['type'] );
            }
        }

        // Execute
        try {
            $stmt->execute();
        }
        catch (PDOException $e) {
            error_log("@@@@@@@@@@@@@: SSP CUSTOM sql_exec - sql: ".$sql);
            SSP::fatal( "An SQL error occurred (error_log): ".$e->getMessage(), $sql );
        }

        // Return all
        return $stmt->fetchAll();
    }


    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Internal methods
     */

    /**
     * Throw a fatal error.
     *
     * This writes out an error message in a JSON string which DataTables will
     * see and show to the user in the browser.
     *
     * @param  string $msg Message to send to the client
     */
    static function fatal ( $msg, $sql )
    {
        echo json_encode( array(
            "error" => $msg,
			  "sql" => $sql
        ) );

        exit(0);
    }

    /**
     * Create a PDO binding key which can be used for escaping variables safely
     * when executing a query with sql_exec()
     *
     * @param  array &$a    Array of bindings
     * @param  *      $val  Value to bind
     * @param  int    $type PDO field type
     * @return string       Bound key to be used in the SQL where this parameter
     *   would be used.
     */
    static function bind ( &$a, $val, $type )
    {
        $key = ':binding_'.count( $a );

        $a[] = array(
            'key' => $key,
            'val' => $val,
            'type' => $type
        );

        return $key;
    }


    /**
     * Pull a particular property from each assoc. array in a numeric array,
     * returning and array of the property values from each item.
     *
     *  @param  array  $a    Array to get data from
     *  @param  string $prop Property to read
     *  @param  bool  $isJoin  Determine the the JOIN/complex query or simple one
     *  @return array        Array of property values
     */
    static function pluck ( $a, $prop, $isJoin = false )
    {
        $out = array();

        for ( $i=0, $len=count($a) ; $i<$len ; $i++ ) {
            $out[] = ($isJoin && isset($a[$i]['as'])) ? $a[$i][$prop]. ' AS '.$a[$i]['as'] : $a[$i][$prop];
        }

        return $out;
    }
}
