<?php
// Scheda Catalogo
class ManagePdf {
	
    public $app, $db, $pdfclass;
    
    //private $settings, $settings2;
    
    private $data, $data_id, $table_intent, $table_main_intent, $data_type, $custom_data;
    
    private $model, $event, $subscriber_id, $current_session_id;
    
    public function __construct(){

        global $app;
        $this->db=$app->get_db();
        $this->app=$app;
        $this->pdfclass=require_once $this->app->main_root.'/private/_common/classes/tcpdf/tcpdf.php';
       /* $settings=$app->get_settings($domain='association_1');
        $settings2=$app->get_settings($domain='association_2');        
        $this->settings= (object) array_merge((array) $settings, (array) $settings2);*/
 
    }

    // Generate PDF
    public function init_pdf($id,$data_type='standard',$custom_data=array()) {
	
        $this->data_id=$id;
        $this->data_type=$data_type;
        $styles='';
        $background='';
        //require_once $this->app->main_root.'/private/_common/classes/tcpdf/tcpdf.php';
        
        if($this->data_type == 'standard'){
            $this->table_intent=isset($_POST['intent'])? $_POST['intent'] : $custom_data['data_intent'];
            $this->data=mysqli_fetch_object($this->db->query("select * from ".$this->table_intent." where id=".$this->data_id));
           // $this->model=mysqli_fetch_object($this->db->query("select * from archy_models where id=".$this->data->model_id)); 
		   $model_id = isset($this->data->model_id) ? (int)$this->data->model_id : 0;

if ($model_id <= 0) {
    error_log('ManagePdf: model_id mancante. Intent='.$this->table_intent.' ID='.$this->data_id);
    return false;
}

$this->model = mysqli_fetch_object(
    $this->db->query("select * from archy_models where id=".$model_id)
);

if (!$this->model) {
    error_log('ManagePdf: modello non trovato. model_id='.$model_id.' Intent='.$this->table_intent.' ID='.$this->data_id);
    return false;
}
        }elseif($this->data_type == 'custom_event'){
            $this->table_intent=$custom_data['data_intent']; // setto la tabella corrente
            $this->table_main_intent='archy_events'; // setto la tabella principale
            $this->current_session_id=$custom_data['session_id'];
            $this->subscriber_id=$custom_data['subscriber_id'];
        }elseif($this->data_type == 'custom_event_all'){
            $this->table_intent='archy_events_subscriptions';
            $this->table_main_intent='archy_events';   
            $this->custom_data=$custom_data;
        }      
		
        if($this->data_type == 'standard'){ $config_pdf_intent = $this->table_intent; }
	elseif($this->data_type == 'custom_event'){ $config_pdf_intent = $this->table_main_intent; }
        elseif($this->data_type == 'custom_event_all'){ $config_pdf_intent = $this->table_main_intent.'_all'; }
	else{ $config_pdf_intent = 'archy_'.$this->data_type; }
		                  
        if($this->data_type=='standard'){
            
            require $this->app->main_root.'/private/_common/assets/settings/manage-pdf/config/c_'.$config_pdf_intent.'.php'; // richiama il file di configurazione
            //require $this->app->main_root.'/private/_common/assets/settings/manage-pdf/shortcodes/sc_'.$config_pdf_intent.'.php'; // richiama il file di shortcode
            
            $model_pdf_id = $this->data_id; // assegna un id al nome del pdf
            $model_width = $this->model->model_width; // assegna la larghezza del pdf
            $model_height = $this->model->model_height; // assegna l'altezza del pdf
            $model_template = $this->model->model_template; // assegna il template del pdf

            /*
            |--------------------------------------------------------------------------
            | TEMPLATE MODELLO - COMPORTAMENTO STORICO RIPRISTINATO
            |--------------------------------------------------------------------------
            |
            | La scheda catalogo deve usare il modello centrale archy_models collegato
            | tramite model_id. In questo modo, se aggiorno il modello, le schede già
            | esistenti riprendono automaticamente il template aggiornato alla nuova
            | generazione del PDF, senza dover riselezionare il modello nella scheda.
            |
            | Il campo model_template salvato sulla singola scheda viene usato solo
            | come override manuale quando non esiste un model_id valido.
            |
            | Questo ripristina anche il corretto comportamento di "Rigenera allegati",
            | che deve rigenerare tutte le schede usando l'ultima versione dei modelli.
            |
            */
            if(
                isset($this->data->model_template) &&
                trim((string)$this->data->model_template) != '' &&
                (!isset($this->data->model_id) || (int)$this->data->model_id <= 0)
            ){
                $model_template = $this->data->model_template;
            }

            $model_background = $this->model->model_background; // assegna il background del pdf
            $model_css = $this->model->model_css; // assegna il css personalizzato del pdf
            $model_archive = $this->model->model_archive; // assegna il secondo template del pdf di archiviazione
            
        }elseif($this->data_type == 'custom_event'){
	    
            require $this->app->main_root.'/private/_common/assets/settings/manage-pdf/config/c_'.$config_pdf_intent.'.php'; // richiama il file di configurazione
            //require $this->app->main_root.'/private/_common/assets/settings/manage-pdf/shortcodes/sc_'.$config_pdf_intent.'.php'; // richiama il file di shortcode
            
            $model_pdf_id = $this->data_id; // assegna un id al nome del pdf
            $model_event_width = $this->event->model_width; // assegna la larghezza del pdf
            $model_event_height = $this->event->model_height; // assegna l'altezza del pdf
            $model_event_template = $this->event->model_template; // assegna il template del pdf
            $model_event_background = $this->event->model_background; // assegna il background del pdf
            $model_event_css = $this->event->model_css; // assegna il css personalizzato del pdf
            
            $model_width = $this->session->model_width; // assegna la larghezza del pdf
            $model_height = $this->session->model_height; // assegna l'altezza del pdf
            $model_template = $this->session->model_template; // assegna il template del pdf
            $model_background = $this->session->model_background; // assegna il background del pdf
            $model_css = $this->session->model_css; // assegna il css personalizzato del pdf
                        
            $model_archive = ''; // assegna il secondo template del pdf di archiviazione
        }elseif($this->data_type == 'custom_event_all'){
            
            $events_sessions=$this->custom_data;
            $events_keys=array_keys($events_sessions);
            
            foreach($events_keys as $event_key){
                //error_log('@@@@@@KEY'.$event_key.'@@@@@----');             
                $event_id=$event_key;
                $events_q=$this->db->query('select * from '.$this->table_main_intent.' where id='.$event_id);
                $this->event=mysqli_fetch_object($events_q);
  
                $t=0;
                foreach($events_sessions[$event_key] as $session){
                    //error_log('#####SESSION'.$session.'#####----'); 
                    $session_id=$session[$t];
                    $sessions_q=$this->db->query('select * from archy_events_sessions where event_id='.$event_id.' AND id='.$session_id);
                    $session_row=mysqli_fetch_object($sessions_q);
                    
                    $model_pdf_id = '';
                    $model_width = $session_row->model_width; // assegna la larghezza del pdf
                    $model_height = $session_row->model_height; // assegna l'altezza del pdf
                    $model_template = $session_row->model_template; // assegna il template del pdf
                    $model_background = $session_row->model_background; // assegna il background del pdf
                    $model_css = $session_row->model_css; // assegna il css custom del pdf
                    $model_archive = '';                    
                    
                    $pageLayout = array($model_width,$model_height);                                   
                    
                    $orientation = ($pageLayout[1]>$pageLayout[0]) ? 'P' : 'L';
                    $tcpdf = new TCPDF($orientation, 'mm', $pageLayout, true, 'UTF-8', false);
                    
                    $rows = $this->db->query("select * from archy_events_subscriptions where event_id=" . $event_id);
                    while ($event_subscription = mysqli_fetch_object($rows)) :
                        $subscriber_id=$event_subscription->subscriber_id;
                        $session_ids=array_keys(unserialize($event_subscription->sessions));
                        if(in_array($session_id,$session_ids)):
                            
                            require $this->app->main_root.'/private/_common/assets/settings/manage-pdf/config/c_'.$config_pdf_intent.'.php'; // richiama il file di configurazione
                            //require $this->app->main_root.'/private/_common/assets/settings/manage-pdf/shortcodes/sc_'.$this->table_main_intent.'.php'; // richiama il file di shortcode
                            $template=str_replace(array_keys($shortcodes),$shortcodes,$model_template);
                            $background=$this->app->subdomain_root.$model_background;
                            $filename=$physical_path.$physical_filename_with_background.".pdf";
                            $this->generatePdf('all',$template,$background,$filename,$pageLayout,$styles,$tcpdf);
                            
                        endif;
                        
                    endwhile;
                    $tcpdf->Output($filename, 'F');
                    
                    $t++;
                }
               
            }
            
        }
        
        if(isset($physical_path)):
            if(!is_dir($physical_path)):
                mkdir($physical_path, 0775,true);
            endif;        
         endif;
            
        // SET PDF STYLE        
        if(isset($model_css) && $model_css != ''){ 
            $styles='<style type="text/css">'.strip_tags($model_css).'</style>';
        }
        if(isset($model_event_css) && $model_event_css != ''){ 
            $styles='<style type="text/css">'.strip_tags($model_event_css).'</style>';
        }
        
        // CUSTOM EVENT
        if(isset($model_event_template) && $model_event_template != "") { 
            if($this->data_type == 'custom_event'){
                if($this->accrued_credits>=$this->event_credits){
                    $template=str_replace(array_keys($shortcodes),$shortcodes,$model_event_template);
                    if($model_event_background!= '' && file_exists($this->app->subdomain_root.$model_event_background)){
                        $background=$this->app->subdomain_root.$model_event_background;                         
                    }
                    $filename=$physical_path.$model_pdf_id."_".$physical_filename_standard.".pdf";
                    $pageLayout = array($model_event_width,$model_event_height);                                   
                    $this->generatePdf('standard',$template,$background,$filename,$pageLayout,$styles,'');
                }
            }
        }
        
        if($model_width == ''){ $model_width = 210; }
        if($model_height == ''){ $model_height = 297; }
        
        if(isset($model_template) && $model_template == "" && isset($model_archive) && $model_archive == ""){ return false; } // se non c'è nessun template esce
        elseif($this->data_type == 'custom_event_all'){ return false; } // se chiamo la funzione all non viene fatto nulla
        else{
            if(isset($model_template) && $model_template != "") { 
            
                // GENERAZIONE 1° DOCUMENTO CON SFONDO
                $storeR2 = new StoreR2Adapter();
                //$template=str_replace(array_keys($shortcodes),$shortcodes,$model_template);
                $template=Placeholder::load($model_template)->checkReplace($check_replace_placeholder,$this->data); // replace placeholder
                if($model_background!=''){
                    if(strpos($model_background, 'assets') === false){
                        $background=$storeR2->getFileUrl($model_background);
                    }else{
                        $background=$this->app->subdomain_root.$model_background;
                    }
                }
                
                //$filename=$physical_path.$model_pdf_id."_".$physical_filename_with_background.".pdf";

                /*
                |--------------------------------------------------------------------------
                | NOME FILE DINAMICO - PDF CON SFONDO
                |--------------------------------------------------------------------------
                |
                | Se il modello contiene model_filename_template, il nome viene generato
                | sostituendo i placeholder configurati dal cliente.
                | Se il template non è presente, resta attiva la logica storica.
                |
                */
                $filename = $this->buildPdfPhysicalFilename(
                    $physical_path,
                    $physical_filename_with_background,
                    $model_pdf_id,
                    isset($physical_filename_append) ? $physical_filename_append : ''
                );

                /*
                | Path fisico reale del PDF con sfondo appena generato.
                | Serve per aggiornare correttamente file_path con il nuovo
                | nome dinamico, evitando che la scheda resti collegata al
                | PDF generato in precedenza.
                */
                $generated_pdf_with_background = $filename;
				
                $pageLayout = array($model_width,$model_height);                                   
                $this->generatePdf('standard',$template,$background,$filename,$pageLayout,$styles,'');
                
            }

            if(isset($model_archive) && $model_archive != "") {                
                
                // GENERAZIONE 2° DOCUMENTO STANDARD SENZA SFONDO
                //$template=str_replace(array_keys($shortcodes),$shortcodes,$model_archive);  
                $template=Placeholder::load($model_archive)->checkReplace($check_replace_placeholder,$this->data); // replace placeholder
                //$filename=$physical_path.$this->data_id."_".$physical_filename_standard.".pdf";

                /*
                |--------------------------------------------------------------------------
                | NOME FILE DINAMICO - PDF STANDARD / ARCHIVIAZIONE
                |--------------------------------------------------------------------------
                |
                | Usa lo stesso template dinamico del PDF con sfondo, ma mantiene
                | il prefisso fisico specifico della scheda standard/archiviazione.
                |
                */
                $filename = $this->buildPdfPhysicalFilename(
                    $physical_path,
                    $physical_filename_standard,
                    $model_pdf_id,
                    isset($physical_filename_append) ? $physical_filename_append : ''
                );

                /*
                | Manteniamo il path fisico del PDF standard/archiviazione.
                | Utile per eventuali estensioni future, senza interferire
                | con il PDF principale scaricato dalla scheda.
                */
                $generated_pdf_standard = $filename;
                
                $this->generatePdf('base',$template,'',$filename,'',$styles,'');
				
            }          
            
            
            if(isset($this->data->file_path)){  

                /*
                |--------------------------------------------------------------------------
                | AGGIORNAMENTO FILE COLLEGATO ALLA SCHEDA
                |--------------------------------------------------------------------------
                |
                | La funzione originale salvava sempre il vecchio naming:
                |
                |     $relative_path.$model_pdf_id."_".$physical_filename_with_background.".pdf"
                |
                | Con i nomi dinamici questo lasciava il record collegato al PDF
                | generato in precedenza, anche se il nuovo file veniva creato
                | correttamente.
                |
                | Ora salviamo invece il basename reale del PDF principale appena
                | generato, mantenendo invariato il percorso relativo.
                |
                */
                if(isset($generated_pdf_with_background) && $generated_pdf_with_background != ''):
                    $file_name_path = $relative_path.basename($generated_pdf_with_background);
                else:
                    // Fallback storico, usato solo se non è stato generato il PDF con sfondo.
                    $file_name_path = $relative_path.$model_pdf_id."_".$physical_filename_with_background.".pdf";
                endif;

                $this->db->query('update '.$this->table_intent.' set file_path="'.addslashes($file_name_path).'" where id='.$this->data_id);

            }
            
            return true;

        }
               
       
    }



    /*
    |--------------------------------------------------------------------------
    | NOMI FILE PDF DINAMICI
    |--------------------------------------------------------------------------
    |
    | Sistema introdotto per permettere ai clienti di personalizzare il nome
    | fisico dei PDF generati dai modelli tramite placeholder.
    |
    | Campo modello previsto:
    |
    |     archy_models.model_filename_template
    |
    | Esempi di template:
    |
    |     [numero_archiviazione]_[titolo_opera]
    |     [titolo_opera]_[anno]
    |     [extra:stato_conservazione]_[titolo_opera]
    |
    | Regole:
    | - l'ID record viene SEMPRE aggiunto alla fine del nome file;
    | - i placeholder vuoti vengono saltati;
    | - se il risultato è vuoto si usa il fallback storico;
    | - il nome viene ripulito da caratteri non validi per filesystem/URL;
    | - il merge PDF continua a funzionare perché l'ID resta sempre finale.
    |
    */
    private function sanitizePdfFilenamePart($value, $fallback='0'){

        $value = html_entity_decode((string)$value, ENT_QUOTES, 'UTF-8');
        $value = strip_tags($value);
        $value = trim($value);

        if($value == ''):
            return $fallback;
        endif;

        // Normalizza accenti quando possibile.
        if(function_exists('iconv')):
            $converted = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
            if($converted !== false):
                $value = $converted;
            endif;
        endif;

        // Evita path traversal e caratteri problematici in nomi file.
        $value = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], ' ', $value);
        $value = preg_replace('/\s+/', '-', $value);
        $value = preg_replace('/[^A-Za-z0-9_\-\.]/', '', $value);
        $value = preg_replace('/\-+/', '-', $value);
        $value = trim($value, '-_.');

        return $value != '' ? $value : $fallback;
    }

    private function getAdditionalFieldValueForFilename($placeholder){

        $placeholder = trim((string)$placeholder);

        if($placeholder == '' || !$this->data_id || !$this->table_intent):
            return '';
        endif;

        /*
        |--------------------------------------------------------------------------
        | Compatibilità
        |--------------------------------------------------------------------------
        |
        | Il campo additional_fields.placeholder è stato introdotto nel nuovo
        | sistema dei placeholder dinamici. Se la colonna non esiste ancora,
        | non blocchiamo la generazione PDF: restituiamo semplicemente vuoto.
        |
        */
        $hasPlaceholderColumn = false;
        $check = @$this->db->query("SHOW COLUMNS FROM additional_fields LIKE 'placeholder'");
        if($check && $check->num_rows > 0):
            $hasPlaceholderColumn = true;
        endif;

        if(!$hasPlaceholderColumn):
            return '';
        endif;

        $placeholder = addslashes($placeholder);
        $table = addslashes($this->table_intent);
        $record_id = (int)$this->data_id;

        $q = "
            SELECT afv.value
            FROM additional_fields_values afv
            INNER JOIN additional_fields_domain afd ON afd.id = afv.domain_id
            INNER JOIN additional_fields af ON af.id = afd.field_id
            WHERE afd.table_name = '".$table."'
            AND af.placeholder = '".$placeholder."'
            AND afv.record_id = '".$record_id."'
            LIMIT 1
        ";

        $res = @$this->db->query($q);

        if($res && $res->num_rows > 0):
            $row = mysqli_fetch_object($res);
            return isset($row->value) ? $row->value : '';
        endif;

        return '';
    }

    private function replaceFilenameTemplatePlaceholders($template){

        $template = (string)$template;

        if($template == ''):
            return '';
        endif;

        $self = $this;

        $template = preg_replace_callback('/\[extra:([A-Za-z0-9_\-]+)\]/', function($matches) use ($self){
            return $self->getAdditionalFieldValueForFilename($matches[1]);
        }, $template);

        $template = preg_replace_callback('/\[([A-Za-z0-9_\.\-]+)\]/', function($matches) use ($self){

            $field = $matches[1];
            $field = str_replace($self->table_intent.'.', '', $field);

            if(isset($self->data->{$field})):
                return $self->data->{$field};
            endif;

            return '';

        }, $template);

        // Elimina separatori doppi derivati da placeholder vuoti.
        $template = preg_replace('/[_\-\s]+/', '_', $template);
        $template = trim($template, '_- ');

        return $template;
    }

    private function buildPdfPhysicalFilename($physical_path, $physical_base_name, $model_pdf_id, $physical_filename_append=''){

        /*
        |--------------------------------------------------------------------------
        | COSTRUZIONE NOME FILE PDF
        |--------------------------------------------------------------------------
        |
        | Funzione originale:
        |
        |     scheda_catalogo_0_2457.pdf
        |
        | Nuova logica con template dinamico:
        |
        |     [numero_archiviazione]_[titolo_opera]
        |
        | diventa:
        |
        |     LV-2009-019_Senza-Titolo_2457.pdf
        |
        | ATTENZIONE
        | -------------------------------------------------------------------------
        | Se il template dinamico è compilato, NON anteponiamo più automaticamente
        | "scheda_catalogo_" al PDF graficato, perché il cliente sta già scegliendo
        | consapevolmente il nome del file.
        |
        | Per il PDF di archiviazione/uso interno, invece, aggiungiamo il prefisso
        | "archiviazione_" per evitare sovrascritture tra:
        |
        | - PDF graficato
        | - PDF interno/generico
        |
        | In ogni caso l'ID record resta sempre alla fine del nome file, così:
        |
        | - il merge PDF può continuare a trovare i file;
        | - non ci sono collisioni tra record diversi;
        | - il download può recuperare l'ultima versione corretta.
        |
        */

        $model_pdf_id = (int)$model_pdf_id;
        $template = '';

        if(isset($this->model->model_filename_template)):
            $template = trim((string)$this->model->model_filename_template);
        endif;

        if($template != ''):

            $dynamicName = $this->replaceFilenameTemplatePlaceholders($template);
            $dynamicName = $this->sanitizePdfFilenamePart($dynamicName, '0');

            /*
            | Se l'utente ha già inserito l'ID nel template evitiamo
            | di duplicarlo. Altrimenti lo aggiungiamo sempre.
            */
            if(!preg_match('/(^|[_\-])'.$model_pdf_id.'$/', $dynamicName)):
                $dynamicName .= '_'.$model_pdf_id;
            endif;

            /*
            | Se il template contiene già un nome tecnico completo,
            | lo rispettiamo.
            */
            if(preg_match('/^scheda_catalogo(_|$)/i', $dynamicName)):
                return $physical_path.$dynamicName.'.pdf';
            endif;

            if(preg_match('/^archiviazione(_|$)/i', $dynamicName)):
                return $physical_path.$dynamicName.'.pdf';
            endif;

            /*
            | PDF generico / uso interno:
            | usa il prefisso chiaro scheda_generica_.
            |
            | Esempio:
            |
            |     scheda_generica_LV-2009-019_Senza-Titolo_2457.pdf
            |
            | Questo sostituisce il precedente prefisso tecnico
            | "archiviazione_", mantenendo più leggibile il file per il cliente.
            */
            if(stripos($physical_base_name, 'archiviazione') !== false):
                return $physical_path.'scheda_generica_'.$dynamicName.'.pdf';
            endif;

            /*
            | PDF graficato:
            | nessun prefisso automatico scheda_catalogo_.
            */
            return $physical_path.$dynamicName.'.pdf';

        endif;

        /*
        |--------------------------------------------------------------------------
        | FALLBACK STORICO
        |--------------------------------------------------------------------------
        |
        | Se il template nome file non è compilato, resta invariata la vecchia
        | logica:
        |
        |     scheda_catalogo_0_ID.pdf
        |
        */

        $filename = $physical_path.$physical_base_name;

        if(isset($physical_filename_append) && $physical_filename_append != ''):
            $appendValue = isset($this->data->{$physical_filename_append}) ? $this->data->{$physical_filename_append} : '';
            $filename .= '_'.$this->sanitizePdfFilenamePart($appendValue, '0');
        endif;

        $filename .= '_'.$model_pdf_id.'.pdf';

        return $filename;
    }


    /*
    |--------------------------------------------------------------------------
    | OTTIMIZZAZIONE IMMAGINI HTML PER PDF
    |--------------------------------------------------------------------------
    |
    | MOTIVO DELLA MODIFICA
    | -------------------------------------------------------------------------
    | Le schede catalogo possono diventare molto pesanti quando nei template HTML
    | vengono inserite immagini delle opere ad alta risoluzione.
    |
    | TCPDF, quando riceve l'HTML tramite writeHTML(), incorpora le immagini così
    | come sono. Questo può portare ogni singola scheda PDF a pesare anche 2 MB
    | o più. Su cataloghi accorpati da centinaia di opere il PDF finale può
    | superare facilmente 1 GB.
    |
    | COSA FA QUESTA FUNZIONE
    | -------------------------------------------------------------------------
    | - Cerca tutte le immagini presenti nell'HTML del template.
    | - Se l'immagine è locale, crea una versione JPEG ottimizzata.
    | - Ridimensiona la larghezza massima a 1400 px.
    | - Salva la cache in /assets/documents/pdf_image_cache/.
    | - Sostituisce nell'HTML il percorso originale con quello ottimizzato.
    |
    | NOTE DI SICUREZZA
    | -------------------------------------------------------------------------
    | - Se un'immagine non esiste o non è leggibile, viene lasciata invariata.
    | - Se GD non supporta un formato, l'immagine viene saltata.
    | - La cache usa md5(path + filemtime), quindi viene rigenerata quando cambia
    |   il file originale.
    | - La funzione è volutamente conservativa: non modifica background PDF e non
    |   altera immagini remote non riconducibili al sottodominio.
    |
    */




private function optimizeImagesForPdfHtml($html){

    if(trim((string)$html) == ''):
        return $html;
    endif;

    if(!function_exists('getimagesize') || !function_exists('imagejpeg')):
        return $html;
    endif;

    $cacheDir = rtrim($this->app->subdomain_root, '/') .
        '/assets/documents/pdf_image_cache/';

    if(!is_dir($cacheDir)):
        @mkdir($cacheDir, 0775, true);
    endif;

    if(!is_dir($cacheDir) || !is_writable($cacheDir)):
        return $html;
    endif;

    /*
    |--------------------------------------------------------------------------
    | RACCOLTA IMMAGINI DA HTML
    |--------------------------------------------------------------------------
    | Cerca sia:
    | - <img src="...">
    | - background-image:url(...)
    | - background:url(...)
    |--------------------------------------------------------------------------
    */

    $sources = [];

    preg_match_all(
        '/<img[^>]+src=[\"\']([^\"\']+)[\"\']/i',
        $html,
        $imgMatches
    );

    if(!empty($imgMatches[1])):
        foreach($imgMatches[1] as $src):
            $sources[] = $src;
        endforeach;
    endif;

    preg_match_all(
        '/url\((["\']?)([^"\')]+)\1\)/i',
        $html,
        $urlMatches
    );

    if(!empty($urlMatches[2])):
        foreach($urlMatches[2] as $src):
            $sources[] = $src;
        endforeach;
    endif;

    $sources = array_values(array_unique($sources));

    if(empty($sources)):
        return $html;
    endif;

    foreach($sources as $src):

        $srcOriginal = trim($src);

        if($srcOriginal == ''):
            continue;
        endif;

        if(strpos($srcOriginal, '/assets/documents/pdf_image_cache/') !== false):
            continue;
        endif;

        if(strpos($srcOriginal, 'data:image') === 0):
            continue;
        endif;

        $imagePath = $srcOriginal;

        /*
        |--------------------------------------------------------------------------
        | CONVERSIONE URL/PERCORSO IN PATH FISICO
        |--------------------------------------------------------------------------
        */

        if(strpos($imagePath, 'http://') === 0 || strpos($imagePath, 'https://') === 0):

            $parsed = parse_url($imagePath);

            if(!isset($parsed['path'])):
                continue;
            endif;

            $imagePath = rtrim($this->app->subdomain_root, '/') . $parsed['path'];

        elseif(strpos($imagePath, '/') === 0):

            $imagePath = rtrim($this->app->subdomain_root, '/') . $imagePath;

        else:

            $imagePath = rtrim($this->app->subdomain_root, '/') . '/' . ltrim($imagePath, '/');

        endif;

        if(!file_exists($imagePath) || !is_file($imagePath)):
            continue;
        endif;

        $imgInfo = @getimagesize($imagePath);

        if(!$imgInfo || empty($imgInfo['mime'])):
            continue;
        endif;

        $width = (int)$imgInfo[0];
        $height = (int)$imgInfo[1];

        if($width <= 0 || $height <= 0):
            continue;
        endif;

        /*
        |--------------------------------------------------------------------------
        | PARAMETRI OTTIMIZZAZIONE
        |--------------------------------------------------------------------------
        | Per PDF catalogo conviene non superare 1200 px.
        |--------------------------------------------------------------------------
        */

        $maxWidth = 1200;
        $jpegQuality = 68;

        $cacheFile = $cacheDir .
            md5($imagePath . '_' . filemtime($imagePath) . '_w'.$maxWidth.'_q'.$jpegQuality) .
            '.jpg';

        if(!file_exists($cacheFile)):

            $img = null;

            switch($imgInfo['mime']):

                case 'image/jpeg':
                case 'image/jpg':
                    if(function_exists('imagecreatefromjpeg')):
                        $img = @imagecreatefromjpeg($imagePath);
                    endif;
                    break;

                case 'image/png':
                    if(function_exists('imagecreatefrompng')):
                        $img = @imagecreatefrompng($imagePath);
                    endif;
                    break;

                case 'image/webp':
                    if(function_exists('imagecreatefromwebp')):
                        $img = @imagecreatefromwebp($imagePath);
                    endif;
                    break;

                default:
                    $img = null;
                    break;

            endswitch;

            if(!$img):
                continue;
            endif;

            if($width > $maxWidth):

                $newWidth = $maxWidth;
                $newHeight = (int)(($height * $newWidth) / $width);

            else:

                $newWidth = $width;
                $newHeight = $height;

            endif;

            $tmp = imagecreatetruecolor($newWidth, $newHeight);

            $white = imagecolorallocate($tmp, 255, 255, 255);
            imagefilledrectangle($tmp, 0, 0, $newWidth, $newHeight, $white);

            imagecopyresampled(
                $tmp,
                $img,
                0, 0, 0, 0,
                $newWidth,
                $newHeight,
                $width,
                $height
            );

            @imagejpeg($tmp, $cacheFile, $jpegQuality);

            imagedestroy($img);
            imagedestroy($tmp);

        endif;

        if(!file_exists($cacheFile)):
            continue;
        endif;

        $publicPath =
            '/assets/documents/pdf_image_cache/' .
            basename($cacheFile);

        /*
        |--------------------------------------------------------------------------
        | SOSTITUZIONE NELL'HTML
        |--------------------------------------------------------------------------
        */

        $html = str_replace($srcOriginal, $publicPath, $html);

    endforeach;

    return $html;
}











    /*
    |--------------------------------------------------------------------------
    | ARCHY MEDIA - FONT PDF DINAMICI / CLIENTE
    |--------------------------------------------------------------------------
    |
    | Integrazione prudente:
    | - non modifica la logica esistente di generazione PDF;
    | - non interviene sulla pulizia dei caratteri;
    | - registra solo i font disponibili tramite PdfFontManager, se presente;
    | - normalizza eventuali font-family HTML verso i nomi riconosciuti da TCPDF.
    |
    */
    private function preparePdfTemplateFonts($tcpdf, $template){

        if(!class_exists('PdfFontManager')):
            $fontManagerPath = $this->app->main_root.'/private/_common/classes/PdfFontManager.php';
            if(file_exists($fontManagerPath)):
                require_once $fontManagerPath;
            endif;
        endif;

        if(class_exists('PdfFontManager')):
            $fontManager = new PdfFontManager($this->app);
            $fontManager->registerFonts($tcpdf);
            $template = $fontManager->normalizeHtmlFontFamilies($template);
        endif;

        return $template;
    }

    public function generatePdf($type='standard',$template='',$background='',$filename='',$layout='',$css='',$tcpdf=''){
        
        if($type=='standard'){
            
            $orientation = ($layout[1]>$layout[0]) ? 'P' : 'L';
            
            $tcpdf = new TCPDF($orientation, 'mm', $layout, true, 'UTF-8', false);
            $tcpdf->SetAuthor('Eteria Cloud');
            $tcpdf->setPrintHeader(false);
            $tcpdf->setPrintFooter(false);
            //$tcpdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);                    
            $tcpdf->SetMargins(10,10,10);
            $tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
            $tcpdf->setJPEGQuality(75);
            $tcpdf->SetAutoPageBreak(false, 0);

            $tcpdf->AddPage();                        
            if($background!= ''){
                $tcpdf->Image($background, 0, 0, $layout[0], $layout[1], '', '', '', false, 300, '', false, false, 0);
            }                    
            $tcpdf->setPageMark();                    
            /*
            |--------------------------------------------------------------------------
            | OTTIMIZZAZIONE IMMAGINI PRIMA DI writeHTML()
            |--------------------------------------------------------------------------
            |
            | Prima di passare il template a TCPDF, sostituiamo eventuali immagini
            | pesanti con versioni cache ottimizzate. Questo riduce il peso della
            | singola scheda e rende più sostenibile il catalogo accorpato.
            |
            */
            $template = $this->optimizeImagesForPdfHtml($template);


			@file_put_contents(
    $this->app->subdomain_root.'/assets/documents/debug_template_pdf.html',
    $template
);

@file_put_contents(
    $this->app->subdomain_root.'/assets/documents/debug_template_pdf.html',
    $template
);
            $template = $this->preparePdfTemplateFonts($tcpdf, $template);
			$css = $this->preparePdfTemplateFonts($tcpdf, $css);
			if (class_exists('Normalizer')) {
				$template = Normalizer::normalize($template, Normalizer::FORM_C);
			}
			$template = $this->cleanPdfHtmlBeforeWrite($template);
			$pdf_template = '<html><head>'.$css.'</head><body>'.$template.'</body></html>';
            $tcpdf->writeHTML($pdf_template, true, false, true, false, '');

            $tcpdf->Output($filename, 'F');
            
        }elseif($type=='base'){
            
            $tcpdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            $tcpdf->SetAuthor('Eteria Cloud');
            $tcpdf->setPrintHeader(false);
            $tcpdf->setPrintFooter(false);
            $tcpdf->SetMargins(10, 10, 10);

            $tcpdf->AddPage();
            $tcpdf->setPageMark();                
            /*
            |--------------------------------------------------------------------------
            | OTTIMIZZAZIONE IMMAGINI PRIMA DI writeHTML()
            |--------------------------------------------------------------------------
            |
            | Prima di passare il template a TCPDF, sostituiamo eventuali immagini
            | pesanti con versioni cache ottimizzate. Questo riduce il peso della
            | singola scheda e rende più sostenibile il catalogo accorpato.
            |
            */
            $template = $this->optimizeImagesForPdfHtml($template);
			
			
			
			@file_put_contents(
    $this->app->subdomain_root.'/assets/documents/debug_template_pdf.html',
    $template
);

            $template = $this->preparePdfTemplateFonts($tcpdf, $template);
$css = $this->preparePdfTemplateFonts($tcpdf, $css);

$template = $this->cleanPdfHtmlBeforeWrite($template);
$pdf_template = '<html><head>'.$css.'</head><body>'.$template.'</body></html>';
            $tcpdf->writeHTML($pdf_template, true, false, true, false, '');

            $tcpdf->Output($filename, 'F');
            
        }elseif($type=='all'){
            
            $tcpdf->SetAuthor('Eteria Cloud');
            $tcpdf->setPrintHeader(false);
            $tcpdf->setPrintFooter(false);
           
            $tcpdf->SetMargins(10,10,10);
            $tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
            $tcpdf->setJPEGQuality(75);
            $tcpdf->SetAutoPageBreak(false, 0);
           // $fontname = TCPDF_FONTS::addTTFfont($this->app->main_root.'/private/_common/classes/tcpdf/fonts/Lora/Lora-Regular.ttf');
           // $tcpdf->AddFont('Lora', '', 'lora.php');
            $tcpdf->AddPage();                        
            if($background!= ''){
                $tcpdf->Image($background, 0, 0, $layout[0], $layout[1], '', '', '', false, 300, '', false, false, 0);
            }                    
            $tcpdf->setPageMark();                    
            /*
            |--------------------------------------------------------------------------
            | OTTIMIZZAZIONE IMMAGINI PRIMA DI writeHTML()
            |--------------------------------------------------------------------------
            |
            | Prima di passare il template a TCPDF, sostituiamo eventuali immagini
            | pesanti con versioni cache ottimizzate. Questo riduce il peso della
            | singola scheda e rende più sostenibile il catalogo accorpato.
            |
            */
            $template = $this->optimizeImagesForPdfHtml($template);
			
			@file_put_contents(
    $this->app->subdomain_root.'/assets/documents/debug_template_pdf.html',
    $template
);

            $template = $this->preparePdfTemplateFonts($tcpdf, $template);
$css = $this->preparePdfTemplateFonts($tcpdf, $css);

$template = $this->cleanPdfHtmlBeforeWrite($template);
$pdf_template = '<html><head>'.$css.'</head><body>'.$template.'</body></html>';
            $tcpdf->writeHTML($pdf_template, true, false, true, false, '');
            
        }elseif($type=='download'){
            
            $orientation = $layout[0];
            $format = $layout[1];
            $marginLeft = $layout[2];
            $marginTop = $layout[3];
            $marginRight = $layout[4];
            
            $tcpdf = new TCPDF($orientation, 'mm', $format, true, 'UTF-8', false);
            $tcpdf->SetAuthor('Eteria Cloud');
            $tcpdf->setPrintHeader(false);
            $tcpdf->setPrintFooter(false);
            //$tcpdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);                    
            $tcpdf->SetMargins($marginLeft,$marginTop,$marginRight);
            $tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
            $tcpdf->setJPEGQuality(75);
            $tcpdf->SetAutoPageBreak(true, 10);

            $tcpdf->AddPage();                        
            if($background!= ''){
                $tcpdf->Image($background, 0, 0, $layout[0], $layout[1], '', '', '', false, 300, '', false, false, 0);
            }                    
            $tcpdf->setPageMark();                    
            /*
            |--------------------------------------------------------------------------
            | OTTIMIZZAZIONE IMMAGINI PRIMA DI writeHTML()
            |--------------------------------------------------------------------------
            |
            | Prima di passare il template a TCPDF, sostituiamo eventuali immagini
            | pesanti con versioni cache ottimizzate. Questo riduce il peso della
            | singola scheda e rende più sostenibile il catalogo accorpato.
            |
            */
            $template = $this->optimizeImagesForPdfHtml($template);
			
			
			@file_put_contents(
    $this->app->subdomain_root.'/assets/documents/debug_template_pdf.html',
    $template
);

            $template = $this->preparePdfTemplateFonts($tcpdf, $template);
$css = $this->preparePdfTemplateFonts($tcpdf, $css);

$template = $this->cleanPdfHtmlBeforeWrite($template);
$pdf_template = '<html><head>'.$css.'</head><body>'.$template.'</body></html>';
            $tcpdf->writeHTML($pdf_template, true, false, true, false, '');

            $tcpdf->Output($filename, 'D');
            
        }
        
        
        
    }
	
	private function cleanPdfHtmlBeforeWrite($html){

    $html = (string)$html;

    // Converte entità HTML problematiche
    $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    // Converte non-breaking space in spazio normale
    $html = str_replace("\xC2\xA0", ' ', $html);
    $html = preg_replace('/&nbsp;?/i', ' ', $html);

    // Rimuove caratteri invisibili / oggetto incorporato / replacement char
	$html = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}\x{FFFC}\x{FFFD}]/u', '', $html);

	// Rimuove caratteri di controllo non stampabili
	$html = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $html);

	// Rimuove spazi/caratteri sporchi prima della chiusura dei tag
	$html = preg_replace('/[\x{FFFD}\s]+(<\/(?:p|div|span|strong|em|b|i|td|th|li)>)/iu', '$1', $html);

    // Rimuove spazi inutili prima della chiusura dei tag
    $html = preg_replace('/\s+(<\/(?:p|div|span|strong|em|b|i|td|th|li)>)/i', '$1', $html);

    return trim($html);
}
    
}