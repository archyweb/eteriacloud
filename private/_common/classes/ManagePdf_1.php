<?php
// Scheda Catalogo
class ManagePdf {
	
    public $app, $db;
    
    private $settings, $settings2;
    
    private $data, $data_id, $table_intent, $type;

    public function __construct($data_id,$data_type='standard',$custom_data=array()){

        global $app;
        $this->db=$app->get_db();
        $this->app=$app;
        
        $settings=$app->get_settings($domain='association_1');
        $settings2=$app->get_settings($domain='association_2');        
        $this->settings= (object) array_merge((array) $settings, (array) $settings2);
        
        $this->data_id=$data_id;
        $this->data_type=$data_type;
        if($this->data_type == 'standard'){
            $this->table_intent=$_POST['intent'];
            $this->data=mysqli_fetch_object($this->db->query("select * from ".$this->table_intent." where id=".$this->data_id));
            $this->data->model=mysqli_fetch_object($this->db->query("select * from ".$this->table_intent."_model where id=".$this->data->modello_id));  
        }elseif($this->data_type == 'custom_event'){
            $this->table_intent=$custom_data['data_intent']; // setto la tabella corrente
            $this->table_main_intent='archy_events'; // setto la tabella principale
            $this->current_session_id=$custom_data['session_id'];
                        
            $this->data=mysqli_fetch_object($this->db->query("select * from ".$this->table_intent." where id=".$this->data_id)); // prendo i dati del subscriber
            $this->data->event=mysqli_fetch_object($this->db->query("select * from ".$this->table_main_intent." where id=".$this->data->event_id)); // prendo i dati dell'evento
            $this->data->session=mysqli_fetch_object($this->db->query("select * from archy_events_sessions where event_id=".$this->data->event->id." AND id=".$this->current_session_id)); // prendo i dati della sessione corrente
            $this->data->registry=mysqli_fetch_object($this->db->query("select * from archy_registry AS r INNER JOIN archy_cities AS c ON r.city_id=c.id where r.id=".$data_id)); // prendo i dati dell'anagrafica
        
            $this->session_credits=$this->data->event->minimum_credits;
            $this->event_credits=$this->data->session->credits;
        }
    }

    // Generate PDF
    public function init_pdf() { // filters ($filters['date']['from'], $filters['date']['to'], $filters['type']['from'], $filters['type']['to'])
	
        require_once $this->app->main_root.'/private/_common/classes/tcpdf/tcpdf.php';
		
        if($this->data_type == 'standard'){ $config_pdf_intent = $this->table_intent; }
		elseif($this->data_type == 'custom_event'){ $config_pdf_intent = $this->table_main_intent; }		
		else{ $config_pdf_intent = 'archy_'.$this->data_type; }
		
            require_once $this->app->main_root.'/private/_common/assets/manage-pdf/config/c_'.$config_pdf_intent.'.php'; // richiama il file di configurazione
        
        if($this->data_type=='standard'){
            
            require_once $this->app->main_root.'/private/_common/assets/manage-pdf/shortcodes/sc_'.$this->table_intent.'.php'; // richiama il file di shortcode
            //error_log("######ID SESSIONE".$this->data_id);
            $model_pdf_id = $this->data_id; // assegna un id al nome del pdf
            $model_width = $this->data->model->model_width; // assegna la larghezza del pdf
            $model_height = $this->data->model->model_height; // assegna l'altezza del pdf
            $model_template = $this->data->model->model_template; // assegna il template del pdf
            $model_background = $this->data->model->model_background; // assegna il background del pdf
            $model_css = $this->data->model->model_css; // assegna il css personalizzato del pdf
            $model_archive = $this->data->model->model_archive; // assegna il secondo template del pdf di archiviazione
            
        }elseif($this->data_type == 'custom_event'){
				
            require_once $this->app->main_root.'/private/_common/assets/manage-pdf/shortcodes/sc_'.$this->table_main_intent.'.php'; // richiama il file di shortcode
            //error_log("######ID SESSIONE".$this->data_id);
            $model_pdf_id = $this->data_id; // assegna un id al nome del pdf
            $model_width = $this->data->event->model_width; // assegna la larghezza del pdf
            $model_height = $this->data->event->model_height; // assegna l'altezza del pdf
            $model_template = $this->data->event->model_template; // assegna il template del pdf
            $model_background = $this->data->event->model_background; // assegna il background del pdf
            $model_css = ''; // assegna il css personalizzato del pdf
            //$model_archive = ''; // assegna il secondo template del pdf di archiviazione
            
        }
        
        if(!is_dir($physical_path)):
            mkdir($physical_path, 0775,true);
        endif;        
        
        // SET PDF STYLE
        $styles='<style type="text/css">';
        $styles.='body{font-size:12px;font-family:Verdana,Arial,Helvetica,sans-serif;margin:0;padding:0;}';
        if($model_css != ''){ 
            $css_custom=strip_tags($model_css);
            $styles.= $css_custom;             
        }
        $styles.='</style>';
        
        // Esce se mancano i settings della card
        if($model_template == "" && $model_archive == ""){ return false; }
        else{
            if($model_template != ""
                    && isset($model_width)
                    && is_numeric($model_width)
                    && isset($model_height)
                    && is_numeric($model_height)
            ) {
               
                    // GENERAZIONE 1° DOCUMENTO             
                
                    if($this->data_type=='standard'){ $template=str_replace(array_keys($shortcodes),$shortcodes,$model_template); }
                    else{ $template=str_replace(array_keys($shortcodes),$shortcodes,$model_template); }
					
                    $template_background=$this->app->subdomain_root.$model_background;
                    $filename=$physical_path.$model_pdf_id."_".$physical_filename.".pdf";
                    //if($membership_get_all_cards){ $filename=$physical_path.$physical_filename.".pdf"; }
                    
                    $pageLayout = array($model_width,$model_height); 
                    $orientation = ($model_height>$model_width) ? 'P' : 'L';  
                                        
                    $tcpdf = new TCPDF($orientation, 'mm', $pageLayout, true, 'UTF-8', false);
                    $tcpdf->SetAuthor('Eteria Cloud');
                    $tcpdf->setPrintHeader(false);
                    $tcpdf->setPrintFooter(false);
                    $tcpdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);                    
                    $tcpdf->SetMargins(10,10,10);
                    $tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
                    $tcpdf->setJPEGQuality(75);
                    $tcpdf->SetAutoPageBreak(false, 0);
                                        
                    $tcpdf->AddPage();                        
                    if($model_background!='' && file_exists($template_background)){
                        $tcpdf->Image($template_background, 0, 0, $model_width, $model_height, '', '', '', false, 300, '', false, false, 0);
                    }                    
                    $tcpdf->setPageMark();                    
                    $pdf_template = '<html><head>'.$styles.'</head><body>'.$template.'</body></html>';
                    $tcpdf->writeHTML($pdf_template, true, false, true, false, '');
                    
                    $tcpdf->Output($filename, 'F');

            }

            if($model_archive != "") {    // GENERAZIONE 2° DOCUMENTO             
                
                $template=str_replace(array_keys($shortcodes),$shortcodes,$model_archive);               
                $filename=$physical_path.$this->data_id."_".$physical_filename_2.".pdf";
                
                $tcpdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                
                $tcpdf->SetAuthor('Eteria Cloud');
                $tcpdf->setPrintHeader(false);
                $tcpdf->setPrintFooter(false);
                $tcpdf->SetMargins(10, 10, 10);
                
                $tcpdf->AddPage();
                $tcpdf->setPageMark();                
                $pdf_template = '<html><head>'.$styles.'</head><body>'.$template.'</body></html>';
                $tcpdf->writeHTML($pdf_template, true, false, true, false, '');
                
                $tcpdf->Output($filename, 'F');

            }

            return true;

        }
    }
    
    public function generatePdf(){
        
    }

}