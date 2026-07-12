<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/
class Placeholder{
    
    public $app, $db, $config;
    private static $documentText;
    private static $_instance = null;
    
    public function __construct(){

        global $app;
        $this->db=$app->get_db();
        $this->app=&$app;
        $this->config=require $this->app->main_root.'/private/_common/assets/settings/placeholder/config.php';
    }
    
    public static function load($documentText){
        if (self::$_instance === null) {
            self::$_instance = new self;
        }
        self::$documentText = $documentText;
        return self::$_instance;
    }
    
    public static function qv($campo, $default = null) {
        global $q;
        return $q->$campo ?? $default;
    }
    
    public static function replaceAutoPlaceholders(string $template, array $placeHolders): string{
        $map = [];

        foreach ($placeHolders as $key => $item) {

            // 🔥 OPTGROUP
            if (isset($item['content_opt_group'])) {

                foreach ($item['content_opt_group'] as $ph => $data) {
                    $map[$ph] = (string)($data['value'] ?? '');
                }

            }
            // 🔥 FLAT
            elseif (is_array($item) && array_key_exists('value', $item)) {

                $map[$key] = (string)($item['value'] ?? '');
            }
        }
        
        // 🔥 esegue il replace più volte finché trova placeholder
        $maxLoops = 5; // sicurezza per evitare loop infinito

        for ($i = 0; $i < $maxLoops; $i++) {

            $newTemplate = strtr($template, $map);

            if ($newTemplate === $template) {
                break; // nessun altro placeholder sostituito
            }

            $template = $newTemplate;
        }

        return $template;

        //return strtr($template, $map);
    }
    
    public function renderBlockResults($document, $rows, $configPlaceholders, $blockName)
{
    // trova il blocco
    preg_match('/\[block-'.$blockName.'\](.*?)\[\/block-'.$blockName.'\]/s', $document, $match);

    if(!isset($match[1])){
        return $document;
    }

    $blockTemplate = $match[1];
    $result = '';

    foreach($rows as $q){

        $placeHolders = self::buildAutoPlaceholders(
            $configPlaceholders,
            $this->app,
            $q
        );

        $result .= self::replaceAutoPlaceholders(
            $blockTemplate,
            $placeHolders
        );
    }

    // sostituisce il blocco con il risultato
    return str_replace($match[0], $result, $document);
}
    
    public static function buildAutoPlaceholders($config, $app, $q = null) {

        // capisce automaticamente se è grouped
        $isGrouped = isset($config[0]['content_opt_group']);

        return $isGrouped
            ? self::buildGroupedPlaceholders($config, $app, $q)
            : self::buildPlaceholders($config, $app, $q);
    }

    public static function buildPlaceholders($config, $app, $q) {

        $placeHolders = [];
        $joinCache = [];

        foreach ($config as $key => $conf) {

            $label = is_array($conf) ? $conf[0] : $conf;
            //$formatter = is_array($conf) && isset($conf[1]) ? $conf[1] : null;
            $formatter = null;

            if (is_array($conf)) {

                // rimuovo chiavi associative (tipo 'field')
                $numericConf = array_values(array_filter(
                    $conf,
                    fn($k) => is_int($k),
                    ARRAY_FILTER_USE_KEY
                ));

                if (isset($numericConf[1])) {
                    if (count($numericConf) > 2) {
                        $formatter = array_slice($numericConf, 1);
                    } else {
                        $formatter = $numericConf[1];
                    }
                }
            }
            
            //$value = $q->$key ?? null;
            $field = $key;

            if (str_starts_with($key, 'all-')) {
                $field = substr($key, 4);
            }
            
            // ? PRIORITÀ MASSIMA al campo custom
            if (is_array($conf) && isset($conf['field'])) {
                $field = $conf['field'];
            }

            if (is_array($conf) && !empty($conf['join'])) {
                $value = self::resolveJoinedPlaceholderValue(
                    $conf['join'],
                    $field,
                    $q,
                    $app,
                    $joinCache
                );
            } else {
                $value = $q->$field ?? null;
            }
            
            $value = self::applyFormatter($formatter, $value, $app, $field);

            $placeHolders["[$key]"] = [
                'value' => $value,
                'label' => $label
            ];
        }

        return $placeHolders;
    }

    private static function resolveJoinedPlaceholderValue($joinConfig, $field, $source, $app, &$joinCache) {

        if (!is_object($source)) {
            return null;
        }

        $steps = isset($joinConfig['table']) ? [$joinConfig] : $joinConfig;
        $currentRow = $source;
        $db = $app->db ?? $app->get_db();

        foreach ($steps as $step) {

            if (!is_array($step) || empty($step['table']) || empty($step['local_field'])) {
                return null;
            }

            $table = $step['table'];
            $localField = $step['local_field'];
            $foreignField = $step['foreign_field'] ?? 'id';

            if (
                !self::isSafeSqlIdentifier($table) ||
                !self::isSafeSqlIdentifier($localField) ||
                !self::isSafeSqlIdentifier($foreignField)
            ) {
                return null;
            }

            $localValue = $currentRow->$localField ?? null;

            if ($localValue === null || $localValue === '') {
                return null;
            }

            $cacheKey = $table.'|'.$foreignField.'|'.(string)$localValue;

            if (!array_key_exists($cacheKey, $joinCache)) {
                $sql = "SELECT * FROM `{$table}` WHERE `{$foreignField}` = ? LIMIT 1";
                $stmt = $db->prepare($sql);

                if (!$stmt) {
                    $joinCache[$cacheKey] = null;
                } else {
                    $lookupValue = (string)$localValue;
                    $stmt->bind_param('s', $lookupValue);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $joinCache[$cacheKey] = $result ? $result->fetch_object() : null;
                    $stmt->close();
                }
            }

            $currentRow = $joinCache[$cacheKey];

            if (!is_object($currentRow)) {
                return null;
            }
        }

        return $currentRow->$field ?? null;
    }

    private static function isSafeSqlIdentifier($identifier) {
        return is_string($identifier) &&
            preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $identifier) === 1;
    }
    
    public static function buildGroupedPlaceholders($config, $app, $q) {

        $placeHolders = [];

        foreach ($config as $group) {

            $optGroup = [
                'title' => $group['title'],
                'content_opt_group' => []
            ];

            foreach ($group['content_opt_group'] as $key => $conf) {

                // Label
                //$label = is_array($conf) ? $conf[0] : $conf;

                // Formatter (può essere null, string o array)
                //$formatter = is_array($conf) && isset($conf[1]) ? $conf[1] : null;
                $label = $conf[0] ?? null;

                $formatter = null;

                if(isset($conf[1])){

                    // se ci sono parametri extra
                    if(is_array($conf) && count($conf) > 2){

                        $formatter = array_slice($conf,1); 
                        // diventa:
                        // ['format_single_select','nome_serie','archy_art_series']

                    } else {

                        $formatter = $conf[1];
                    }
                }

                // Valore dal DB
                $value = $q->$key ?? null;

                // 🔥 QUI CHIAMIAMO APPLYFORMATTER
                $value = self::applyFormatter($formatter, $value, $app,$q, $key);

                $optGroup['content_opt_group']["[$key]"] = [
                    'value' => $value,
                    'label' => $label
                ];
            }

            $placeHolders[] = $optGroup;
        }
       
        return $placeHolders;
    }
    
    public static function applyFormatter($formatter, $value, $app, $q, $key = null){
        if(!$formatter) return $value;
        $StoreCloud = new StoreCloud();
        // Se è array → formatter con parametri
        if(is_array($formatter)){

            $name = array_shift($formatter); // primo = nome funzione
            $params = $formatter;           // resto = parametri extra

        } else {

            $name = $formatter;
            $params = [];
        }
 
        switch($name){

            case 'format_date':
                return $app->format_date($value);
            
            case 'format_tags':
                return $app->format_tags($value);
            
            case 'format_checkbox':
                return $app->htmlCheckboxDef($value);
                
            case 'format_serialize_data':
                return $app->format_serialize_data($value);
                
            case 'format_editor':
                return $app->strip_p_tags($value);

            case 'getArrayValues':
                return $app->getArrayValues($key,$value,$params[0] ?? null);

            case 'format_single_select':
               
                // params[0] = nome campo tabella
                // params[1] = nome tabella
                return $app->format_single_select(
                    $value,
                    $params[0] ?? null,
                    $params[1] ?? null
                );
            
            case 'format_all_fields':
              
                return $app->explode_all_fields(
                    $params[0] ?? null,
                    $value
                );
            
             case 'format_multiple_select':
                return $app->format_multiple_select(
                    $value,
                    $params[0] ?? null,
                    $params[1] ?? null
                );  
            
            case 'getArrayMultipleValues':
                return $app->getArrayMultipleValues($key,$value);
            
            case 'custom_opera_title':
                $opera_id = $q->{$params[0]};
                return $app->getArtCatalogData(
                        $opera_id ?? null,
                        $params[1] ?? null
                );
                
            case 'renderCloudImg':
                return $StoreCloud->renderCloudImg(
                    $value,
                    $params[0] ?? null,
                    $params[1] ?? null
                );
                
            default:
                return $value;
        }
    }
    
    public static function extractPlaceholderList($config){
        $result = [
            'type' => 'flat',
            'data' => []
        ];

        // GROUPED
        if (isset($config[0]) && isset($config[0]['content_opt_group'])) {

            $result['type'] = 'grouped';

            foreach ($config as $group) {

                $groupTitle = $group['title'] ?? 'Altro';

                foreach ($group['content_opt_group'] as $key => $value) {

                    $label = is_array($value) ? $value[0] : $value;

                    $result['data'][$groupTitle]["[$key]"] = $label;
                }
            }

        } else {

            // FLAT
            foreach ($config as $key => $value) {

                $label = is_array($value) ? $value[0] : $value;

                $result['data']["[$key]"] = $label;
            }
        }

        return $result;
    }
    
    public function replaceRegistry($id = null){
        
        include($this->app->main_root.'/private/_common/assets/settings/placeholder/archy_registry.php');
        
        if($id!=null):  
    
            $q = mysqli_fetch_object($this->db->query("select * from archy_registry where id=".$id));
            
            $placeHolders = self::buildAutoPlaceholders($configPlaceholders, $this->app, $q);
            
            static::$documentText = self::replaceAutoPlaceholders(static::$documentText, $placeHolders);

            return $this;
        else:
            return $configPlaceholders;
        endif;
    }
    
    public function replaceMembership($id = null){
        
         // Define placeholders
        include($this->app->main_root.'/private/_common/assets/settings/placeholder/archy_memberships.php');
        
        if($id!=null):  
    
            $q = mysqli_fetch_object($this->db->query("select * from archy_memberships where id=".$id));
            
            $placeHolders = self::buildAutoPlaceholders($configPlaceholders, $this->app, $q);
            
            static::$documentText = self::replaceAutoPlaceholders(static::$documentText, $placeHolders);

            return $this;
        else:
            return $configPlaceholders;
        endif;
        
        /*switch ($q->status) {
            case 2:
                $status = 'In attesa di rinnovo';
                break;
            case 3:
                $status = 'Dimesso';
                break;
            case 4:
                $status = 'Cessato';
                break;
            case 5:
                $status = 'In attesa prima quota';
                break;
            default:
                $status = 'Associato';
                break;
        }*/
        
    }
    
    public function replaceEventsSessions($id = null){
        
        $q=null;
        if($id!=null){
            $q = mysqli_fetch_object($this->db->query("select * from archy_events_sessions where id=".$id));
            $event=new Event();
            $speaker=$event->get_session_speeches($id);
            $location=$event->get_session_location($q->location_id);
        }
        if (!is_object($q)) {
            $q = (object)[];
        }
        // Define placeholders
        include($this->app->main_root.'/private/_common/assets/settings/placeholder/archy_events_sessions.php');
                                
        if($id!=null):   
            $find = array_keys($placeHolders);
            $replace = [];
            foreach($find as $key){$replace[] = $placeHolders[$key]['value'];}
            
            static::$documentText = str_replace($find, $replace, static::$documentText);
            return $this;            
        else:
            return $placeHolders;
        endif;
    }
    
    public function specialReplace($id = null){
        
        $q=null;
        if($id!=null){
            $q = mysqli_fetch_object($this->db->query("select * from archy_art_catalog where id=".$id));
        }
     
        // Define placeholders
        include($this->app->main_root.'/private/_common/assets/settings/placeholder/special.php');
                                
        if($id!=null):   
            $placeHolders = self::buildAutoPlaceholders($configPlaceholders, $this->app, $q);
            
            static::$documentText = self::replaceAutoPlaceholders(static::$documentText, $placeHolders);
            return $this;            
        else:
            return $configPlaceholders;
        endif;
    }
    
    public function contractReplace($id = null,$intent=null){
        
        $q=null;
        if($id!=null){
            $q = mysqli_fetch_object($this->db->query("select * from ".$intent." where id=".$id));
        }
 
        // Define placeholders
        include($this->app->main_root.'/private/_common/assets/settings/placeholder/'.$intent.'.php');        
        
        if($id!=null):   
            
            $placeHolders = self::buildAutoPlaceholders($configPlaceholders, $this->app, $q);
        
            static::$documentText = self::replaceAutoPlaceholders(static::$documentText, $placeHolders);

            return $this;
        else:
            return $configPlaceholders;
        endif;
    }
    
    public function standardReplace($id = null,$intent=null,$params=[]){
        
        // Define placeholders
        include($this->app->main_root.'/private/_common/assets/settings/placeholder/'.$intent.'.php');
        
        if($id!=null):  
            if(count($params)>0){ 
                $rel_intent = $params['rel_table'];
                $rel_field = $params['rel_field'];
                $q_check = mysqli_fetch_object($this->db->query("select $rel_field from ".$rel_intent." where id=".$id));
                $id = $q_check->{$rel_field};
            } 
           
            $q = mysqli_fetch_object($this->db->query("select * from ".$intent." where id=".$id));
            
            $placeHolders = self::buildAutoPlaceholders($configPlaceholders, $this->app, $q);
            
            static::$documentText = self::replaceAutoPlaceholders(static::$documentText, $placeHolders);

            return $this;
        else:
            return $configPlaceholders;
        endif;
    }
    
    public function externalReplace($id = null,$intent=null,$ext_intent=null,$params=''){
        
        // Define placeholders
        include($this->app->main_root.'/private/_common/assets/settings/placeholder/'.$ext_intent.'.php');
        
        if($id!=null):  
           
            if($params!=''){ 
                //$rel_intent = $params['rel_table'];
                $rel_field = $params;
                $q_check = mysqli_fetch_object($this->db->query("select $rel_field from ".$intent." where id=".$id));
                $ids = $q_check->{$rel_field};
            } 
            
            if($ids!= 'null' && $ids!='' && $ids!='a:0:{}'):                
                
                $q = $this->db->query("select * from ".$ext_intent." WHERE id IN (".implode(',',unserialize($ids)).")");
                $rows = [];

                while($row = mysqli_fetch_object($q)){
                    $rows[] = $row;
                }
                static::$documentText = $this->renderBlockResults(
                    static::$documentText,
                    $rows,
                    $configPlaceholders,
                    $rel_field
                );
            
                return $this;
            endif;
        else:
            return $configPlaceholders;
        endif;
    }
    
    public function checkReplace($param=NULL,$object=NULL){
                
        $replaces = $this->checkConfig($param);
        
        if(in_array('archy_registry',$replaces['main_targets'])){ $this->replaceRegistry($object->registry_id); }
        if(in_array('archy_services',$replaces['main_targets'])){ $this->standardReplace($object->services_id,'archy_services'); }
        if(in_array('archy_services_subscriptions',$replaces['main_targets'])){ $this->standardReplace($object->service_subscription_id,'archy_services_subscriptions'); }
        if(in_array('archy_services_memberships',$replaces['main_targets'])){ $this->standardReplace($object->service_membership_id,'archy_services_memberships'); }
        if(in_array('archy_memberships',$replaces['main_targets'])){ $this->replaceMembership($object->membership_id); }
        if(in_array('archy_events_sessions',$replaces['main_targets'])){ $this->replaceEventsSessions($object->id); }
        if(in_array('archy_events',$replaces['main_targets'])){ $this->standardReplace($object->id,'archy_events'); }
        if(in_array('archy_art_catalog',$replaces['main_targets'])){ 
            $params=[];
            if(isset($replaces['params'])){ 
                foreach($replaces['params'] as $key=>$value){ 
                     $params[$key] = $value;
                }
            }
            $this->standardReplace($object->id,'archy_art_catalog',$params);             
        }
        if(in_array('archy_art_certificates',$replaces['main_targets'])){ 
            $params=[];
            if(isset($replaces['params'])){ 
                foreach($replaces['params'] as $key=>$value){ 
                     $params[$key] = $value;
                }
            }
            $this->standardReplace($object->id,'archy_art_certificates');            
        }
        if(in_array('archy_art_privacy',$replaces['main_targets'])){ $this->standardReplace($object->id,'archy_art_privacy'); }
        if(in_array('archy_art_cessione_immagine',$replaces['main_targets'])){ $this->contractReplace($object->id,'archy_art_cessione_immagine'); }
        if(in_array('archy_art_cessione_opera',$replaces['main_targets'])){ $this->contractReplace($object->id,'archy_art_cessione_opera'); }
        if(in_array('archy_art_contratto_valutazione',$replaces['main_targets'])){ $this->standardReplace($object->id,'archy_art_contratto_valutazione'); }
        if(in_array('archy_art_condition_report',$replaces['main_targets'])){ $this->standardReplace($object->id,'archy_art_condition_report'); }
	if(in_array('archy_art_diritto_riproduzione_immagine',$replaces['main_targets'])){ $this->contractReplace($object->id,'archy_art_diritto_riproduzione_immagine'); }
        if(in_array('archy_art_donazione',$replaces['main_targets'])){ $this->contractReplace($object->id,'archy_art_donazione'); }
        if(in_array('archy_art_noleggio',$replaces['main_targets'])){ $this->contractReplace($object->id,'archy_art_noleggio'); }
        if(in_array('archy_art_prestito',$replaces['main_targets'])){ $this->contractReplace($object->id,'archy_art_prestito'); }
        if(in_array('archy_art_prestito_promessa',$replaces['main_targets'])){ $this->contractReplace($object->id,'archy_art_prestito_promessa'); }
        if(in_array('archy_art_prestito_definitivo',$replaces['main_targets'])){ $this->standardReplace($object->id,'archy_art_prestito_definitivo'); }
	if(in_array('archy_meetings_book',$replaces['main_targets'])){ $this->standardReplace($object->id,'archy_meetings_book'); }
        //if(in_array('special',$replaces['main_targets'])){ $this->specialReplace($object->id); }
        
        if(isset($replaces['add_targets'])){
            if(in_array('archy_art_authors',$replaces['add_targets'])){ 
                $this->externalReplace($object->id,'archy_art_catalog','archy_art_authors','autori'); 
            }
            if(in_array('archy_art_exhibitions',$replaces['add_targets'])){ 
                $this->externalReplace($object->id,'archy_art_catalog','archy_art_exhibitions','esposizione'); 
            }
            if(in_array('archy_art_publications',$replaces['add_targets'])){ 
                $this->externalReplace($object->id,'archy_art_catalog','archy_art_publications','pubblicazioni'); 
            }
        }
        return $this->get();
        
    }
    
    public function checkConfig($param){
        return $this->config[$param];
    }
    
    public static function getPlaceholders($intent){
        global $app;
        // Define placeholders
        include($app->main_root.'/private/_common/assets/settings/placeholder/'.$intent.'.php');
        
        $placeholderList = self::extractPlaceholderList($configPlaceholders);
  
        return $placeholderList;
    }

    public function getPlaceholderSelect($label_wrap,$intent,$addClass=''){
        
        // Define placeholders
        include($this->app->main_root.'/private/_common/assets/settings/placeholder/'.$intent.'.php');
        
        $placeholderList = self::extractPlaceholderList($configPlaceholders);
        
        $response = '';
        
        if($placeholderList['type'] === 'grouped') {
            $response .= '<label class="form-label">'.$label_wrap.'</label>';
            $response .= '<select class="form-select selectpicker placeholder_options" data-rel="'.$addClass.'">';
            $response .= '<option value="">--Seleziona--</option>';
            foreach($placeholderList['data'] as $group => $items):

                $response .= '<optgroup label="'.$group.'">';
                    foreach($items as $value => $label):
                        $response .= '<option value="'.$value.'">'.$label.'</option>';
                    endforeach;
                $response .= '</optgroup>';

            endforeach;
            $response .= '</select>';
         } else {
             
            $response .= '<label class="form-label">'.$label_wrap.'</label>';
            $response .= '<select class="form-select selectpicker placeholder_options" data-rel="'.$addClass.'">';
                $response .= '<option value="">--Seleziona--</option>';
                foreach($placeholderList['data'] as $value => $label):
                    $response .= '<option value="'.$value.'">'.$label.'</option>';
                endforeach;
            $response .= '</select>';

         }
        
        return $response;
    }
    
    public function getAvailablePlaceholders($param){
        $placeholders = '';
        if($param == ''){ $param = 'default_placeholders'; }
        if($param == 'settings'){ $param = 'default_settings'; }
        $config = $this->checkConfig($param);
        
        if(in_array('archy_registry',$config['main_targets'])){ $placeholders .= $this->getPlaceholderSelect('Anagrafica','archy_registry'); }
        if(in_array('archy_services',$config['main_targets'])){ $placeholders .= $this->getPlaceholderSelect('Servizio',$this->standardReplace(null,'archy_services')); }
        if(in_array('archy_services_subscriptions',$config['main_targets'])){ $placeholders .= $this->getPlaceholderSelect('Dati Iscrizione servizio',$this->standardReplace(null,'archy_services_subscriptions')); }
        if(in_array('archy_services_memberships',$config['main_targets'])){ $placeholders .= $this->getPlaceholderSelect('Dati Membership',$this->standardReplace(null,'archy_services_memberships')); }
        if($this->app->config['association']){ if(in_array('archy_memberships',$config['main_targets'])){ $placeholders .= $this->getPlaceholderSelect('Membership','archy_memberships'); } }
        if(in_array('archy_events_sessions',$config['main_targets'])){ $placeholders .= $this->getPlaceholderSelect('Sessione evento',$this->replaceEventsSessions(null)); }
        if(in_array('archy_events',$config['main_targets'])){ $placeholders .= $this->getPlaceholderSelect('Evento',$this->standardReplace(null,'archy_events')); }
        if(in_array('archy_meetings_book',$config['main_targets'])){ $placeholders .= $this->getPlaceholderSelect('Adunanza',$this->standardReplace(null,'archy_meetings_book')); }
        
        if(in_array('archy_art_certificates',$config['main_targets'])){ $placeholders .= $this->getPlaceholderSelect('Dati Certificato','archy_art_certificates'); }
        if(in_array('archy_art_catalog',$config['main_targets'])){ $placeholders .= $this->getPlaceholderSelect('Dati Catalogo','archy_art_catalog'); }
        if(in_array('archy_art_cessione_immagine',$config['main_targets'])){ $placeholders .= $this->getPlaceholderSelect('Dati Contratto','archy_art_cessione_immagine'); }
        if(in_array('archy_art_cessione_opera',$config['main_targets'])){ $placeholders .= $this->getPlaceholderSelect('Dati Contratto','archy_art_cessione_opera'); }
        if(in_array('archy_art_contratto_valutazione',$config['main_targets'])){ $placeholders .= $this->getPlaceholderSelect('Dati Contratto','archy_art_contratto_valutazione'); }
        if(in_array('archy_art_condition_report',$config['main_targets'])){ $placeholders .= $this->getPlaceholderSelect('Dati Contratto','archy_art_condition_report'); }
	if(in_array('archy_art_diritto_riproduzione_immagine',$config['main_targets'])){ $placeholders .= $this->getPlaceholderSelect('Dati Contratto','archy_art_diritto_riproduzione_immagine'); }
        if(in_array('archy_art_donazione',$config['main_targets'])){ $placeholders .= $this->getPlaceholderSelect('Dati Contratto','archy_art_donazione'); }
        if(in_array('archy_art_noleggio',$config['main_targets'])){ $placeholders .= $this->getPlaceholderSelect('Dati Contratto','archy_art_noleggio'); }
        if(in_array('archy_art_prestito',$config['main_targets'])){ $placeholders .= $this->getPlaceholderSelect('Dati Contratto','archy_art_prestito'); }
        if(in_array('archy_art_prestito_promessa',$config['main_targets'])){ $placeholders .= $this->getPlaceholderSelect('Dati Contratto','archy_art_prestito_promessa'); }
        if(in_array('archy_art_prestito_definitivo',$config['main_targets'])){ $placeholders .= $this->getPlaceholderSelect('Dati Contratto','archy_art_prestito_definitivo'); }
	if(in_array('archy_art_privacy',$config['main_targets'])){ $placeholders .= $this->getPlaceholderSelect('Privacy','archy_art_privacy'); }
        
        if(isset($config['add_targets'])){
            if(in_array('special',$config['add_targets'])){ $placeholders .= $this->getPlaceholderSelect('Special','special','templateSelector'); }
           // if(in_array('archy_art_authors',$config['add_targets'])){ $placeholders .= $this->getPlaceholderSelect('Autori','archy_art_authors'); }
            //if(in_array('archy_art_exhibitions',$config['add_targets'])){ $placeholders .= $this->getPlaceholderSelect('Esposizioni','archy_art_exhibitions'); }
           // if(in_array('archy_art_publications',$config['add_targets'])){ $placeholders .= $this->getPlaceholderSelect('Pubblicazioni','archy_art_publications'); }
        }
        
        return $placeholders;
    }
    
    
 
    public function get(){
        return static::$documentText;
    }
}

// Use it
// Get documentdata.
//$document = ['content' => 'Company name : [company_name]. Address: [company_address], [zip_code] [city].'];

// Replace text with chaining
//$documentText = Placeholder::load($document['content'])->replaceRegistry();


/* USE AL POSTO DEI FOREACH

 $find = array_keys($shortcodes);
$replace = array_values($shortcodes);

$string = str_replace($find, $replace, $string);
   */
