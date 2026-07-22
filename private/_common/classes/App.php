<?php
/* ARCHY PDF BATCH NO-SPLIT-ROWS VERSION: 2026-07-22-1810 */
/* ARCHY PDF MULTICELL LINK ANNOTATIONS VERSION: 2026-07-22-1745 */
/* ARCHY PDF BATCH ALLEGATI CLICCABILI VERSION: 2026-07-22-1730 */
/* ARCHY PDF BATCH RIGHE-ALLEGATI VERSION: 2026-07-22-1700 */
/* ARCHY PDF BATCH FOTO VERSION: 2026-07-22-1605 */
/*
|--------------------------------------------------------------------------
| NOTE DI MANUTENZIONE - EXPORT DATATABLE
|--------------------------------------------------------------------------
|
| Questo file mantiene la logica aggiornata per:
| - export Excel funzionante con campi aggiuntivi;
| - export PDF tramite generazione file temporaneo TCPDF;
| - campi aggiuntivi dinamici additional_XX;
| - link completi per campi upload / bulk_upload.
|
| Regole adottate:
| - le funzioni originali non devono essere eliminate;
| - quando sostituite, vanno mantenute commentate come riferimento;
| - le nuove funzioni devono essere documentate;
| - prima di commentare una funzione condivisa va verificato se viene usata
|   anche da altri processi del gestionale.
|
| ATTENZIONE
| -------------------------------------------------------------------------
| Non rimuovere funzioni apparentemente obsolete senza verificare le chiamate
| in altri file, intent, viste o processi AJAX.
|
*/
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class App extends CustomActions{

	public $intent, $actions_column, $cols, $g_cols, $main_cols, $data, $extra_notices;

	/*
	|--------------------------------------------------------------------------
	| ARCHY MEDIA - ADDITIONAL FIELDS RENDERING FLAG
	|--------------------------------------------------------------------------
	|
	| Flag usato durante il rendering dei campi aggiuntivi.
	| Dichiarato esplicitamente per compatibilità PHP 8.2+
	| ed evitare deprecated dynamic properties.
	|
	*/
	public $rendering_additional_fields = false;
	public $db, $credentials_dir;
	public $alias;
	public $username, $user_first_name, $user_second_name, $user_full_name, $is_admin, $admin_edit_only;
public $user_id;
	public $months;
	public $default_firm_id, $default_bank_id, $default_VAT_collectability ;
	public $name;
        public $grouped_columns, $grouped_intents;

	function __construct(string $subdomain_root){

            parent::__construct($subdomain_root);
            
            $this->subdomain_root = $subdomain_root;
            
		$this->intent=NULL;
		$this->grouped_columns=$this->grouped_intents=[];
		$this->db=NULL;
		//$this->assets_version='1.7.3';
				
		$this->extra_notices=[
			'member_deletion'=>"Se &egrave; stato cancellato un socio, verificare la correttezza delle informazioni riportate nel Libro Soci.",
			'member_update'=>"&Egrave; stato modificato un socio. Verificare la correttezza delle informazioni riportate nel Libro Soci.",
			'member_subscription_deletion'=>"&Egrave; stata cancellata una quota associativa. Verificare la correttezza delle informazioni riportate nel Libro Soci.",
			'member_subscription_update'=>"&Egrave; stata modificata una quota associativa. Verificare la correttezza delle informazioni riportate nel Libro Soci.",
			'invoice_deletion'=>"Sono state cancellate anche le note di credito e le scritture in prima nota collegate a questa fattura.",
			'invoice_update'=>"Potrebbero essere variati i saldi di eventuali note di credito collegate a questa fattura."
		];
		$this->alias='';
		$this->g_cols=[];
		$this->cols=[];
		$this->main_cols=NULL;
		
		$this->data=new stdClass();
		$this->data->grouped_columns=$this->data->grouped_intents=[];

		$subdomain=explode('.',$_SERVER['HTTP_HOST']);
		$this->name=array_shift($subdomain);
                
        if(isset($_COOKIE[$this->auth->config->cookie_name])){ $session_hash = $_COOKIE[$this->auth->config->cookie_name]; }
        else{ $session_hash = ''; }
        
        $this->db=$this->get_db();
		$user = mysqli_fetch_object($this->db->query("
			SELECT 
				au.id AS user_id,
				u.email,
				au.name AS name,
				au.surname AS surname
			FROM phpauth_sessions s
			LEFT JOIN phpauth_users u ON u.id = s.uid
			LEFT JOIN archy_users au ON u.email = au.email
			WHERE s.hash = '" . addslashes($session_hash) . "'
			LIMIT 1
		"));
		if($user):
			$this->is_admin = false;
			$this->user_id = (int) $user->user_id;
			if (!class_exists('ArchyPermissions')) {
				require_once __DIR__ . '/ArchyPermissions.php';
			}
			$Permissions = new ArchyPermissions($this->db, $this->user_id);
			
			
			$this->is_admin = $Permissions->userHasRole('administrator');
			//$this->is_admin = true;
		
			
			$this->user_first_name=trim((string) $user->name);
			$this->user_second_name=trim((string) $user->surname);
			$this->user_full_name=trim($this->user_first_name.' '.$this->user_second_name);
			if($this->user_full_name == ''):
				$this->user_full_name=$user->email;
			endif;
                        $this->username=$this->generaUsername($this->user_first_name,$this->user_second_name);
		else:
            // FIXME: Se non sessione sempre Admin????????????????????????????????????????????????????????????
			$this->is_admin=false;
			$this->user_first_name='Simple';
			$this->user_second_name='Guest';
                        $this->user_full_name='Simple Guest';
		endif;
		
		$this->admin_edit_only=[];
		
		$this->months=['Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno','Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre'];
		
		$this->default_firm_id=mysqli_fetch_object($this->db->query("select min(id) as default_firm_id from archy_firms"))->default_firm_id;
		$this->default_bank_id=mysqli_fetch_object($this->db->query("select min(bank_id) as default_bank_id from archy_firms"))->default_bank_id;
		
		if($this->default_firm_id):
			$this->default_VAT_collectability=mysqli_fetch_object($this->db->query("select VAT_collectability from archy_firms where id=".$this->default_firm_id))->VAT_collectability;
		endif;
	}

	public function user_can_add_to($table_name){
		$single_record_tables=$this->config['single_record'];
		if(in_array($table_name,$single_record_tables)):
			$table_records=mysqli_fetch_object($this->db->query("select count(id) as num from ".$table_name))->num;
			return $table_records==0;
		endif;
		return true;
	}
	
	/*public function include_navbar_right(){
		require_once $this->admin_template_child.'/html/navbar_right.php';		
	}*/
        
        public function include_custom_navbar_right(){
            if(is_file($this->subdomain_root.'/custom/custom_navbar_right.php')):
                require_once $this->subdomain_root.'/custom/custom_navbar_right.php';	
            endif;
        }

        protected function apply_local_menu_extend($menu)
        {
            if (!isset($this->config['menu_extend']) || !is_array($this->config['menu_extend'])) {
                return $menu;
            }

            foreach ($this->config['menu_extend'] as $menu_key => $menu_extend) {
                if (!is_array($menu_extend)) {
                    continue;
                }

                if (isset($menu[$menu_key]) && is_array($menu[$menu_key])) {
                    $menu[$menu_key] = $this->merge_menu_section($menu[$menu_key], $menu_extend);
                    continue;
                }

                $menu = $this->insert_menu_section($menu, $menu_key, $menu_extend);
            }

            return $menu;
        }

        protected function merge_menu_section($base_section, $extend_section)
        {
            $append_items = array();
            $remove_items = array();

            if (isset($extend_section['items']) && is_array($extend_section['items'])) {
                $append_items = $extend_section['items'];
                unset($extend_section['items']);
            }

            if (isset($extend_section['remove_items']) && is_array($extend_section['remove_items'])) {
                $remove_items = $extend_section['remove_items'];
                unset($extend_section['remove_items']);
            }

            $replace_items = isset($extend_section['replace_items']) && $extend_section['replace_items'] === true;
            unset($extend_section['replace_items']);

            $base_section = array_replace($base_section, $extend_section);

            if ($remove_items && isset($base_section['items']) && is_array($base_section['items'])) {
                $base_section['items'] = $this->remove_menu_items($base_section['items'], $remove_items);
            }

            if ($replace_items) {
                $base_section['items'] = $append_items;
            } elseif ($append_items) {
                if (!isset($base_section['items']) || !is_array($base_section['items'])) {
                    $base_section['items'] = array();
                }
                $base_section['items'] = array_merge($base_section['items'], $append_items);
            }

            return $base_section;
        }

        protected function remove_menu_items($items, $remove_ids)
        {
            $filtered_items = array();

            foreach ($items as $item) {
                if (isset($item['id']) && in_array($item['id'], $remove_ids, true)) {
                    continue;
                }

                if (isset($item['items']) && is_array($item['items'])) {
                    $item['items'] = $this->remove_menu_items($item['items'], $remove_ids);
                }

                $filtered_items[] = $item;
            }

            return $filtered_items;
        }

        protected function insert_menu_section($menu, $menu_key, $menu_section)
        {
            $after = isset($menu_section['after']) ? $menu_section['after'] : null;
            $before = isset($menu_section['before']) ? $menu_section['before'] : null;
            unset($menu_section['after'], $menu_section['before']);

            if (!$after && !$before) {
                $menu[$menu_key] = $menu_section;
                return $menu;
            }

            $new_menu = array();
            $inserted = false;

            foreach ($menu as $key => $section) {
                if (!$inserted && $before === $key) {
                    $new_menu[$menu_key] = $menu_section;
                    $inserted = true;
                }

                $new_menu[$key] = $section;

                if (!$inserted && $after === $key) {
                    $new_menu[$menu_key] = $menu_section;
                    $inserted = true;
                }
            }

            if (!$inserted) {
                $new_menu[$menu_key] = $menu_section;
            }

            return $new_menu;
        }

        protected function resolve_menu_href($href)
        {
            if (!is_string($href) || strpos($href, '{') === false) {
                return $href;
            }

            return preg_replace_callback('/\{([a-zA-Z0-9_]+)\}/', function ($matches) {
                $key = $matches[1];

                if (!array_key_exists($key, $this->config) || is_array($this->config[$key])) {
                    return $matches[0];
                }

                return rawurlencode((string) $this->config[$key]);
            }, $href);
        }
        
       
	
        public function load_menu(){
			
			/*
|--------------------------------------------------------------------------
| ARCHY MEDIA - MENU VISIBILITY FILTER
|--------------------------------------------------------------------------
|
| Carica i menu consentiti per l'utente corrente.
| Gli admin vedono tutto.
|
*/

$forbiddenMenus = [];

if (!$this->is_admin && isset($this->user_id)) {

    $res = $this->db->query("
        SELECT menu_key
        FROM archy_user_menus
        WHERE user_id = ".(int)$this->user_id."
    ");

    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $forbiddenMenus[] = $row['menu_key'];
        }
    }
}

$menu = $this->config_menu['menu'];

$menu['association']['enabled'] = $this->config['association'];
$menu['accounting']['enabled'] = $this->config['accounting'];
$menu['comitato']['enabled'] = $this->config['comitato'];
$menu['arts2']['enabled'] = $this->config['arts2'];
$menu['arts']['enabled'] = $this->config['arts'];
$menu['contract']['enabled'] = $this->config['contract'];
$menu['anagrafiche']['enabled'] = $this->config['anagrafiche'];
$menu['services']['enabled'] = $this->config['services'];
$menu['events']['enabled'] = (
    isset($this->config['events']) &&
    $this->config['events'] === true
);

$menu = $this->apply_local_menu_extend($menu);

$html = '<ul class="navbar-nav" data-top-nav-dropdowns="data-top-nav-dropdowns">
            <li id="dashboard_trigger">
                <a href="archy/app.php?view=dashboard" data-id="dashboard">Dashboard</a>
            </li>
            <li id="notices_trigger">
                <a href="archy/app.php?intent=archy_notices&view=grid" data-id="archy_notices">Notifiche</a>
            </li>';

foreach ($menu as $main => $first_level):
			
			/*
|--------------------------------------------------------------------------
| FILTRO MENU PRINCIPALE
|--------------------------------------------------------------------------
*/

if (
    !$this->is_admin &&
    in_array($main, $forbiddenMenus, true)
) {
    continue;
}
                    if($first_level['enabled']):
                          
                            $dropdown_position = '';
                            //$html.=$this->$method;
                            if(isset($first_level['position'])){ $dropdown_position = $first_level['position']; }
                            $html.='<li class="nav-item dropdown">';

                                $html.='<a class="nav-link dropdown-toggle main-link" href="#'.$main.'" role="button" data-bs-target="#'.$main.'" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-controls="'.$main.'">
                                <span class="nav-link-icon"><span class="'.$first_level['icon'].'"></span></span><span class="nav-link-text ps-1">'.$first_level['text'].'</span>
                                
                              </a><div class="dropdown-menu dropdown-caret dropdown-menu-card border-0 mt-0 '.$dropdown_position.'" aria-labelledby="'.$main.'"><div class="bg-white dark__bg-1000 rounded-3">';
                            $i=1;
                            $count_second_level = 0;
                            
                            foreach($first_level['items'] as $second_level):
						
                                
                                // Se esiste la condition e NON coincide → salta
                                if (isset($second_level['condition']) && $second_level['condition'] != $this->is_admin) {
                                    continue;
                                }
								
								/*
								|--------------------------------------------------------------------------
								| FILTRO SECONDO LIVELLO
								|--------------------------------------------------------------------------
								*/

								if (
									!$this->is_admin &&
									isset($second_level['id']) &&
									in_array($second_level['id'], $forbiddenMenus, true)
								) {
									continue;
								}
                                
                                // CONTROLLO CHE IL MENU NON SIA FILTRATO
                                    if(isset($second_level['filter']) && $this->config[$second_level['filter']]== false):
                                        $second_level['enabled']=false;
                                    endif;

                                    if(isset($second_level['divider'])){ $html.='<hr class="dropdown-divider">'; }

                                    // CONTROLLO CHE IL MENU SIA ABILITATO
                                    if(isset($second_level['enabled']) && $second_level['enabled']):
                                        //if(isset($second_level['condition']) && $second_level['condition']==$this->is_admin):
                                            if(isset($second_level['href'])):
                                                $attr=isset($second_level['anchor_attributes'])? $second_level['anchor_attributes'] : '';
                                                $href=$this->resolve_menu_href($second_level['href']);
                                                $html.='<a class="dropdown-item link-600 fw-medium eteria-target" href="'.$href.'" data-id="'.$second_level['id'].'" '.$attr.'>'.$second_level['text'].'</a>';
                                            else:

                                                if($i==1){ $html.='<div class="card navbar-card-app shadow-none dark__bg-1000"><div class="card-body scrollbar max-h-dropdown"><div class="row">'; }
                                                $html.='<div class="col-6 col-md-6"><div class="nav flex-column">';
                                                $html.='
                                                <p class="nav-link text-700 mb-0 fw-bold">'.$second_level['text'].'</p>';
                                                foreach($second_level['items'] as $third_level):
												/*
|--------------------------------------------------------------------------
| FILTRO TERZO LIVELLO
|--------------------------------------------------------------------------
*/

if (
    !$this->is_admin &&
    isset($third_level['id']) &&
    in_array($third_level['id'], $forbiddenMenus, true)
) {
    continue;
}

                                                    $attr=isset($third_level['anchor_attributes'])? $third_level['anchor_attributes'] : '';
                                                    $href=$this->resolve_menu_href($third_level['href']);
                                                    $html.='<a class="nav-link py-2 link-600 fw-medium eteria-target" href="'.$href.'" data-id="'.$third_level['id'].'" '.$attr.'>
                                                        '.$third_level['text'].'

                                                      </a>
                                                      <!-- more inner pages-->
                                                    ';

                                                endforeach;
                                                $html.='</div></div>';
                                                $count_second_level++;
                                                $i++;
                                                //echo $count_second_level;
                                                if($i==$count_second_level){$html.='</div></div></div>';}
                                            endif;
                                            $html.='';
                                        endif;
                                    //endif;
                                  
                            endforeach;
                            $html.='</div></div></li>';
                    else:
                        /*$html.='<li class="nav-item dropdown">';
                        $html.='<a class="nav-link dropdown-toggle main-link disabled" href="#'.$main.'" role="button" data-bs-target="#'.$main.'" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-controls="'.$main.'">
                        <span class="nav-link-icon"><span class="'.$first_level['icon'].'"></span></span><span class="nav-link-text ps-1">'.$first_level['text'].'</span>

                      </a>';*/
                    endif;
            endforeach;
            $html.='</ul>';

            return $html;
	}
        

	public function loginPage() {
            header('location: '.$this->protocol.'://'.$_SERVER['HTTP_HOST'].'/archy/login.php');
        }
        
        public function maintenancePage() {
            header('location: '.$this->protocol.'://'.$_SERVER['HTTP_HOST'].'/archy/maintenance.php');
        }

	public function dashboardPage() {
            header('location: '.$this->protocol.'://'.$_SERVER['HTTP_HOST'].'/');
        }
       
        
    public function login($username, $password) {
        $res = $this->auth->login($username, $password);
        return $res;
    }

    public function logout(){
	    $this->auth->logoutUser();
		header('location: '.$this->protocol.'://'.$_SERVER['HTTP_HOST'].'?do_action=loginPage');
	}

    public function currentUser(){
        return $this->auth->getCurrentUser();
    }

    public function changePassword() {
	    $oldPassword = $_POST['old_password'];
	    $newPassword = $_POST['new_password'];
	    $newPasswordRepeat = $_POST['new_password_repeat'];
        $curUser = $this->auth->getCurrentUser();
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';

        if (!$curUser || !isset($curUser['uid'])) {
            set_message("error", "Sessione non valida. Effettua nuovamente il login.");
            header('location: '.$referer);
            return;
        }

        if ($newPassword !== $newPasswordRepeat) {
            set_message("error", "Le nuove password non coincidono.");
            header('location: '.$referer);
            return;
        }

        $validation = $this->auth->validateStrictPassword($newPassword, $curUser['email'] ?? '', $oldPassword);

        if (!$validation['valid']) {
            set_message("error", $validation['message']);
            header('location: '.$referer);
            return;
        }

	    $resp = $this->auth->changePassword($curUser['uid'], $oldPassword, $newPassword, $newPasswordRepeat, $captcha_response = null);
        set_message($resp['error'] ? "error" : "success", $resp['message']);
        header('location: '.$referer);
    }

    public function creaCard() {
        $m = new Membership; 
	    $fromCard = $_POST['from_card'];
	    $toCard = $_POST['to_card'];
        $fromDate = $_POST['from_date'];
	    $toDate = $_POST['to_date'];
        $m->create_all_card(0,$fromCard,$toCard,$fromDate,$toDate);
        $resp['error']="success";
        $resp['message']='Stampa terminata! <a href="/assets/documents/memberships/card_all.pdf">Scarica qui il pdf</a>';
        set_message($resp['error'] ? "error" : "success", $resp['message']);
        header('location: '.$_SERVER['HTTP_REFERER']);
    }
    
    public function generate_events_certificates(){
        //$manage_pdf_id=$this->insert_id;
        $sessions = $_POST['session'];
        $manage_pdf=new ManagePdf();
        $manage_pdf->init_pdf('','custom_event_all',$sessions);
        
        $resp['error']="success";
        $resp['message']='Pdf creato, puoi scaricarlo dalla lista sotto';
        set_message($resp['error'] ? "error" : "success", $resp['message']);
        header('location: '.$_SERVER['HTTP_REFERER']);
    }
    
    
    /*
    // FIXME: to be deleted
    public function dologout(){
        header('location: //'.$_SERVER['HTTP_HOST']);
    }
    */
	
	public function get_db(){
		if($this->db==NULL):
			$this->db=new mysqli($this->config['db']['host'],$this->config['db']['user'],$this->config['db']['pass'],$this->config['db']['name']);
			$this->db->set_charset("utf8");
			$this->db->query("set @@session.time_zone='".$this->config['db']['timezone']."'");
		endif;
		return $this->db;
	}
	
	public function init(){
		$main_table=$this->intent->table;
		$main_title=$this->data->grouped_intents[$main_table]->singular;
		$this->g_cols[$main_title]=$this->data->grouped_columns[$main_table];
		$this->main_cols=$this->data->grouped_columns[$main_table];
		
		foreach($this->intent->joins as $table=>$on):
			if(strpos($table," as ")!==false):
				list($table_name,$table_alias)=explode(" as ",$table);
				$temp_array=explode("__",$table_alias);
				$group_title=$this->data->grouped_intents[$table_name]->singular.' '.end($temp_array);
				$tmp=[];
				foreach($this->data->grouped_columns[$table_name] as $column_name=>$column):
					list($col_table_name,$col_column_name)=explode(".",$column_name);
					$temp_array=explode("__",$table_alias);
					$column['label'].=' '.end($temp_array);
					$tmp[$table_alias.'.'.$col_column_name]=$column;
				endforeach;
				$this->g_cols[$group_title]=$tmp;
			else:
				$group_title=$this->data->grouped_intents[$table]->singular;
				$this->g_cols[$group_title]=$this->data->grouped_columns[$table];
			endif;
		endforeach;
		
		foreach($this->g_cols as $columns):
			$this->cols=array_merge($this->cols,$columns);
		endforeach;
		/*
|--------------------------------------------------------------------------
| V1.3 - Campi aggiuntivi nella modale selezione colonne
|--------------------------------------------------------------------------
| Aggiunge un gruppo "Campi aggiuntivi" alla selezione colonne.
| Attivo solo per gli intent che dichiarano:
|
| $intent->enable_additional_fields_columns = true;
|--------------------------------------------------------------------------
*/
if (
    isset($this->intent->enable_additional_fields_columns) &&
    $this->intent->enable_additional_fields_columns &&
    class_exists('AdditionalFields')
) {
    $AdditionalFields = new AdditionalFields($this);

    $additionalColumns = $AdditionalFields->getGridColumnsForTable($this->intent->table);

    if (!empty($additionalColumns)) {
        $this->g_cols['Campi aggiuntivi'] = $additionalColumns;
        $this->cols = array_merge($this->cols, $additionalColumns);
    }
}
	}
	
	public function merge_joins(&$intent){
        global $app;
        
        //x----- merge_joins 000 table:".$intent->table);

        foreach($intent->joins as $table=>$on):
            //error_log("@@@@@xxxxx merge_joins 111 table => on:".$table." => ".$on);

			if($app->alias==''):
				$key=$table;
                //error_log("@@@@@ merge_joins 2222 NO ALIAS key:".$key);
			else:
                if(strpos($table,' as ')>0) {
                    $key=$table;
                } else {
                    $key=$table.' as '.$table.'__'.$app->alias;			
                }
                //error_log("@@@@@ merge_joins 2222 SI ALIAS key:".$key);
                
                //error_log("@@@@@ merge_joins 555 replace on BEFORE:".$on);
                $on=str_replace($table,$table.'__'.$app->alias,$on);
                //error_log("@@@@@ merge_joins 555 replace on AFTER:".$on);
				list($left_table,$right_table)=explode("=",$on);
				list($table_name,$column_name)=explode(".",$left_table);
				$on=$app->alias.'.'.$column_name.'='.$right_table;
			endif;
			
			$app->intent->joins[$key]=$on;
			
			if($app->alias!=''):
                $app->alias=$table.'__'.$app->alias;
                //error_log("@@@@@ merge_joins 333 alias:".$app->alias);
			endif;		
            
        endforeach;
        
        //error_log("@@@@@----- merge_joins 444 joins:".print_r($app->intent->joins,true));

		
	}
	
	public function merge_intents(&$intent){
		global $app;		

        //error_log("@@@@@xxxxx----- merge_intents 000 START table:".$intent->table);

		if(!isset($app->data->grouped_intents[$intent->table])):
			$cloned_intent= clone $intent;
			unset($cloned_intent->columns);
			$app->data->grouped_intents[$intent->table]=$cloned_intent;
			$app->data->grouped_columns[$intent->table]=$intent->columns;
		endif;
		
        if(count($intent->joins)>0):
            //error_log("@@@@@xxxxx merge_intents 111 HAS JOINS...");

			//store $intent variable
			if($app->intent==NULL):
				//store main intent
				$app->intent=$intent;
			else:
				$app->merge_joins($intent);
			endif;
			
            $joins=$intent->joins;
            foreach($joins as $table=>$on):
                //error_log("@@@@@ merge_intents 333 START JOIN TABLE ".$table." ON ".$on);
				if(strpos($table," as ")!==false):
					list($table,$table_alias)=explode(" as ",$table);
					$app->alias=$table_alias;
				endif;
								
				include $this->main_root.'/private/_common/intents/'.$table.'.php';//now $intent is overridden
                $app->merge_joins($intent);
                //error_log("@@@@@ merge_intents 444 END JOIN TABLE ".$table." ON ".$on);
			endforeach;
			
		else:
            //error_log("@@@@@xxxxx merge_intents 111 NO JOINS...");
			if($app->intent==NULL):
				$app->intent=$intent;
				$app->actions_column='id';
            else:
                //error_log("@@@@@ merge_intents 222 DO MERGE_JOINS...");
				$app->merge_joins($intent);
				$app->actions_column=$app->intent->table.'.id';
			endif;
        endif;
        
        //error_log("@@@@@xxxxx----- merge_intents 000 END table:".$intent->table);
	}
	
	public function set_listing_columns(){
		if(isset($_POST['listing_columns'])):
			$_SESSION[$this->intent->table.'-listing_columns']=$_POST['listing_columns'];
			$this->store_listing_columns();
		else:
			if(!isset($_SESSION[$this->intent->table.'-listing_columns'])):
				$_SESSION[$this->intent->table.'-listing_columns']=$this->get_listing_columns();
			endif;
		endif;
	}
	
	public function store_listing_columns(){
		$db=$this->get_db();
		$columns=$db->query("select id from listing_columns where username='".addslashes($this->username)."' and intent='".$this->intent->table."'");
		if($columns->num_rows>0):
			$id=mysqli_fetch_object($columns)->id;
			$db->query("update listing_columns set username='".addslashes($this->username)."', intent='".$this->intent->table."', columns='".addslashes(serialize($_POST['listing_columns']))."' where id=".$id);
		else:
			$db->query("insert into listing_columns (username,intent,columns) values ('".addslashes($this->username)."','".$this->intent->table."','".addslashes(serialize($_POST['listing_columns']))."')");
		endif;
	}
	
	public function get_listing_columns(){
		$db=$this->get_db();
		$columns=$db->query("select columns from listing_columns where username='".addslashes($this->username)."' and intent='".$this->intent->table."'");
		if($columns->num_rows>0):
			return unserialize(mysqli_fetch_object($columns)->columns);
		else:
			return $this->intent->default_columns;
		endif;
	}
	
	public function reset_listing_columns(){
		$db=$this->get_db();
		$db->query("delete from listing_columns where username='".addslashes($this->username)."' and intent='".$_GET['intent']."'");
		unset($_SESSION[$_GET['intent'].'-listing_columns']);
		header('location: '.$_SERVER['HTTP_REFERER']);
		exit;
	}

    // Intestazione colonne GRID
	/*
	|--------------------------------------------------------------------------
	| INTESTAZIONE COLONNE DATATABLE
	|--------------------------------------------------------------------------
	|
	| La funzione originale leggeva tutte le colonne da $this->cols.
	|
	| Con l'introduzione dei campi aggiuntivi dinamici, alcune colonne
	| salvate nella configurazione utente possono avere forma:
	|
	|     additional_32
	|
	| Queste colonne NON esistono dentro $this->cols, perché vengono lette
	| dinamicamente dalle tabelle:
	|
	| - additional_fields_domain
	| - additional_fields
	|
	| Per evitare warning PHP e per mantenere il corretto funzionamento
	| della DataTable, la funzione ora riconosce i campi additional_XX
	| e recupera label, tipo filtro e opzioni direttamente dal DB.
	|
	| ATTENZIONE
	| -------------------------------------------------------------------------
	| Non confondere additional_XX con l'intent additional_fields.
	| Un campo aggiuntivo valido deve essere additional_ seguito da numero.
	|
	*/
	public function grid_table_header(){
		$return='<tr>';
		$db=$this->get_db();

		foreach($_SESSION[$this->intent->table.'-listing_columns'] as $column):

			/*
			|--------------------------------------------------------------------------
			| CAMPO AGGIUNTIVO DINAMICO
			|--------------------------------------------------------------------------
			*/
			if(preg_match('/^additional_[0-9]+$/', $column)):

				$domain_id = (int) str_replace('additional_', '', $column);

				$field = mysqli_fetch_object($db->query("
					SELECT
						afd.id AS domain_id,
						af.name,
						af.type,
						af.options
					FROM additional_fields_domain afd
					INNER JOIN additional_fields af ON af.id = afd.field_id
					WHERE afd.id = '".$domain_id."'
					LIMIT 1
				"));

				if(!$field):
					continue;
				endif;

				$filter = $field->type;
				if($filter == 'multiple'):
					$filter = 'multiple';
				elseif($filter == 'select'):
					$filter = 'select';
				elseif($filter == 'checkbox'):
					$filter = 'checkbox';
				elseif($filter == 'date'):
					$filter = 'date';
				elseif($filter == 'file' || $filter == 'bulk_upload'):
					$filter = 'bulk_upload';
				elseif($filter == 'textarea' || $filter == 'editor' || $filter == 'plain_editor'):
					$filter = 'text';
				else:
					$filter = 'text';
				endif;

				$options = [];

				if(in_array($field->type, ['select','multiple','checkbox']) && trim((string)$field->options) != ''):

					$raw_options = @unserialize($field->options);

					if($raw_options !== false && is_array($raw_options)):
						$options = $raw_options;
					else:
						$rows = preg_split('/\r\n|\r|\n|\|/', (string)$field->options);
						foreach($rows as $row):
							$row = trim($row);
							if($row == ''):
								continue;
							endif;

							if(strpos($row, '=') !== false):
								list($k,$v) = explode('=', $row, 2);
								$options[trim($k)] = trim($v);
							else:
								$options[$row] = $row;
							endif;
						endforeach;
					endif;

				endif;

				asort($options);

				$return.='<th class="filter_'.$filter.'" data-column-filter="'.$this->get_intent_filter().'" data-column="'.$column.'" data-options=\''.htmlspecialchars(json_encode($options), ENT_QUOTES).'\'>'.htmlspecialchars($field->name, ENT_QUOTES).'</th>';

				continue;

			endif;

			/*
			|--------------------------------------------------------------------------
			| COLONNA NATIVA
			|--------------------------------------------------------------------------
			|
			| Se una colonna salvata non esiste più in $this->cols, la saltiamo
			| per evitare warning e rotture della griglia.
			|
			*/
			if(!isset($this->cols[$column])):
				continue;
			endif;

			$filter=isset($this->cols[$column]['grid_search_type'])? $this->cols[$column]['grid_search_type'] : $this->cols[$column]['type'];

			/*
			|--------------------------------------------------------------------------
			| ARCHY MEDIA - FILTRO GRID PER CAMPI TAGS
			|--------------------------------------------------------------------------
			|
			| Il tipo custom tags deve comportarsi nella griglia come un campo search.
			| Non usiamo filter_text perché il frontend DataTables di Archy genera
			| l'input sotto colonna sui campi filter_search.
			|
			*/
			if(isset($this->cols[$column]['type']) && $this->cols[$column]['type'] == 'tags'):
				$filter = isset($this->cols[$column]['grid_search_type'])
					? $this->cols[$column]['grid_search_type']
					: 'search';
			endif;
			$data_column=strpos($column,".")!==false? $column : $this->intent->table.'.'.$column;

			if(isset($this->cols[$column]['grid_search_column'])):
				$data_column=$this->cols[$column]['grid_search_column'];
			endif;

			if(isset($this->cols[$column]['options'])):
				if($this->cols[$column]['options_type']=='array'):
					$options=$this->cols[$column]['options'];
				elseif($this->cols[$column]['options_type']=='sql'):
					$recordset=$db->query($this->cols[$column]['options']);
					$options=[];
					while($row=mysqli_fetch_object($recordset)):
						$options[$row->option_value]=$row->option_text;
					endwhile;
				endif;
			else:
				$options=[];
			endif;

			asort($options);

			$return.='<th class="filter_'.$filter.'" data-column-filter="'.$this->get_intent_filter().'" data-column="'.$data_column.'" data-options=\''.htmlspecialchars(json_encode($options), ENT_QUOTES).'\'>'.$this->cols[$column]['label'].'</th>';

		endforeach;

		$return.='<th class="">Azioni</th></tr>';

		return $return;
	}
	
	private static function normalize_form_wrapper($wrapper){
        if($wrapper === NULL || trim((string)$wrapper) === ''){
            return 'col-12 col-md-6 col-lg-4';
        }

        $classes = preg_split('/\s+/', trim((string)$wrapper));
        $extra_classes = [];
        $has_grid_class = false;
        $small_field = false;
        $wide_field = false;
        $full_field = false;

        foreach($classes as $class){
            if($class === ''){
                continue;
            }

            if(preg_match('/^col-(xs|sm|md|lg|xl|xxl)?-?(\d+)$/', $class, $match)){
                $has_grid_class = true;
                $size = (int)$match[2];

                if($size >= 12){
                    $full_field = true;
                }elseif($size > 6){
                    $wide_field = true;
                }else{
                    $small_field = true;
                }

                continue;
            }

            if($class === 'col-auto'){
                $has_grid_class = true;
                $small_field = true;
                continue;
            }

            $extra_classes[] = $class;
        }

        if(!$has_grid_class){
            array_unshift($extra_classes, 'col-12', 'col-md-6', 'col-lg-4');
            return trim(implode(' ', array_unique($extra_classes)));
        }

        if($full_field){
            $grid_classes = ['col-12'];
        }elseif($wide_field){
            $grid_classes = ['col-12', 'col-md-6', 'col-lg-8'];
        }elseif($small_field){
            $grid_classes = ['col-12', 'col-md-6', 'col-lg-4'];
        }else{
            $grid_classes = ['col-12', 'col-md-6', 'col-lg-4'];
        }

        return trim(implode(' ', array_unique(array_merge($grid_classes, $extra_classes))));
    }

	
	public static function form_element($column,$wrapper=NULL,$name_prefix=NULL,$name_suffix=NULL,$id_suffix=NULL,$set_value=NULL){
		global $app;
		$name=str_replace($app->intent->table.'.','',$column['alias']);
                $column_name=$name;

                // Aggiunge HELP sul campo
               /* if(isset($column['help_label']) && isset($column['id_help_label']) ){
                        $help_label = ' <font id="help_label_'.$column['id_help_label'].'" show> <span type="button" class="glyphicon glyphicon-info-sign" data-toggle="tooltip" data-html="true" data-placement="right" title="'.$column['help_label'].'"></span> </font>';
                }else{
                        $help_label = '';
                }*/
		if(isset($app->rendering_additional_fields)&&$app->rendering_additional_fields):
			$name='additional['.$name.']';
		else:
			$name=$name_prefix==NULL? 'main['.$name.']' : $name_prefix.'['.$name.']';
			if($name_suffix!=NULL):
				$name.=$name_suffix;
			endif;
		endif;
		//$name=isset($app->rendering_additional_fields)? 'additional['.$name.']' : 'main['.$name.']';
                $id=str_replace('.','_',$column['alias']);
		if($id_suffix!=NULL):
			$id=$id.$id_suffix;
		endif;
                
		$required=isset($column['required'])? ' required="required"' : '';
		$required_label=isset($column['required'])? '<span class="fas fa-asterisk required-label" title="Campo richiesto"></span>' : '';
		$attributes=isset($column['attributes'])? join(" ",$column['attributes']) : '';
                $help_block=isset($column['help_block'])? '<span class="" id="'.$id.'_help_text" data-bs-toggle="tooltip" data-bs-placement="top" title="'.$column['help_block'].'"><i class="far fa-question-circle"></i></span>' : '';
                //if($help_block!=''){$attributes.=' aria-describedby="'.$id.'_help_text"'; }
		
		if($column['type']=='select' or $column['type']=='select_standard' or $column['type']=='multiple' or $column['type']=='multiple_checkbox'):
			if($column['options_type']=='sql'):
				$db=$app->get_db();
				$result=$db->query($column['options']);
				$options=[];                                
				while($row=mysqli_fetch_object($result)):
                                    $option_text_desc='';
                                    if(isset($row->option_text_desc) && $row->option_text_desc!=''){
                                        $option_text_desc.=' - ';                                        
                                        if(strtotime($row->option_text_desc)){ $option_text_desc.=$app->latin_date($row->option_text_desc); }
                                        else{$option_text_desc.=$row->option_text_desc;}
                                    }
                                    $options[$row->option_value]=$row->option_text.$option_text_desc;
                                endwhile;
			elseif($column['options_type']=='array'):
				$options=$column['options'];
			endif;
		endif;
		$extra_css_class=isset($column['extra_css_class'])? $column['extra_css_class'] : '';
                
                // add class hide for field api visibility
                if(isset($app->config['api']) && $app->config['api']==false){
                    if(isset($column['api_visibility'])){ 
                        if(isset($column['api_visibility']) && $column['api_visibility']==true){
                                $extra_css_class .= $extra_css_class.'d-none'; 
                        }
                    }
		}
                
                // rewrite textarea (plain_editor) + placeholder cols
                if($column['type']=='plain_editor' && (isset($column['placeholder'])&&$column['placeholder']==true)){ $wrapper = 'col-8'; }
                /*if(isset($column['wrapper_column']) && $column['wrapper_column']!=''){ $wrapper=$column['wrapper_column']; }
		$response=$wrapper==NULL?'<div class="col-xs-12 col-lg-3 col-md-4 col-sm-4">':'<div class="'.$wrapper.'">';*/
                      
				if(isset($column['wrapper_column']) && $column['wrapper_column']!=''){ $wrapper=$column['wrapper_column']; }
                $wrapper = self::normalize_form_wrapper($wrapper);
		$response='<div class="'.$wrapper.'">';
					  
			switch($column['type']):
                                case 'hidden':
					$form_value = form_value($column['value']);
                                        if($set_value != NULL){ $form_value = $set_value; }
                                        $response.='<input type="hidden" class="form-control '.$extra_css_class.'" id="'.$id.'" name="'.$name.'" value="'.$form_value.'" '.$attributes.'>';
				break;
				case 'text':
                                    $response.='				
                                        <label class="form-label" for="'.$id.'">'.$column['label'].' '.$required_label.' '.$help_block.'</label>
                                        <input type="text" class="form-control"'.$required.' id="'.$id.'" name="'.$name.'" value="'.form_value($column['value']).'" placeholder="'.$column['label'].'" '.$attributes.'>
                                        <div class="invalid-feedback">Campo obbligatorio</div>
                                    ';
				break;
                                case 'email':
                                    $response.='			
                                        <label class="form-label" for="'.$id.'">'.$column['label'].' '.$required_label.' '.$help_block.'</label>
                                        <input type="email" class="form-control"'.$required.' id="'.$id.'" name="'.$name.'" value="'.form_value($column['value']).'" placeholder="'.$column['label'].'" '.$attributes.'>
                                        <div class="invalid-feedback">Immettere un indirizzo email valido</div>
                                        <div class="additional-feedback"></div>
                                    ';
				break;
				case 'textarea':
                                    $response.='						
                                        <label class="form-label" for="'.$id.'">'.$column['label'].' '.$required_label.' '.$help_block.'</label>
                                        <textarea class="form-control"'.$required.' id="'.$id.'" name="'.$name.'" rows="1" placeholder="'.$column['label'].'" '.$attributes.'>'.$column['value'].'</textarea>
                                        <div class="invalid-feedback">Campo obbligatorio</div>
                                    ';
				break;
                                case 'limited_textarea':
                                    $max_length=isset($column['max_length'])?$column['max_length']:'140';
                                    $response.='                                    
                                        <label class="form-label" for="'.$id.'">'.$column['label'].' '.$required_label.' '.$help_block.'</label>
                                        <textarea class="form-control sms'.$required.'" id="'.$id.'" name="'.$name.
                                            '" data-chars="#count_'.$id.'" rows="'.$column['rows'].'" cols="'.$column['cols'].'" maxlength="'.
                                            $max_length.'" placeholder="'.$column['label'].'" '.$attributes.'>'.$column['value'].'</textarea>
                                        <div class="form-text">Caratteri: <span id="count_'.$id.'"></span> di '.$max_length.'</div>
                                        <div class="invalid-feedback">Campo obbligatorio</div>
                                   ';
                                    break;
				case 'user':
                                    $response.='                                        
                                        <label class="form-label" for="'.$id.'">'.$column['label'].' '.$required_label.' '.$help_block.'</label>
                                        <div class="input-group has-validation">
                                            <div class="input-group-text"><i class="far fa-user"></i></div>
                                            <input type="text" class="form-control '.$required.'" id="'.$id.'" name="'.$name.'" value="'.form_value($column['value']).'" placeholder="'.$column['label'].'" '.$attributes.'>
                                        </div>
                                        <div class="invalid-feedback">Campo obbligatorio</div>
                                    ';
				break;
				case 'password':
                                    $response.='                                       
                                        <label class="form-label" for="'.$id.'">'.$column['label'].' '.$required_label.' '.$help_block.'</label>
                                        <div class="input-group has-validation">
                                            <div class="input-group-text toggle_password"><span class="far fa-eye"></span></div>
                                            <input type="password" class="form-control '.$required.'" id="'.$id.'" name="'.$name.'" value="'.form_value($column['value']).'" placeholder="'.$column['label'].'" '.$attributes.'>
                                        </div>
                                        <div class="invalid-feedback">Campo obbligatorio</div>
                                     ';
				break;
				case 'amount':
                                    $response.='						
                                        <label class="form-label" for="'.$id.'">'.$column['label'].' '.$required_label.' '.$help_block.'</label>
                                        <div class="input-group has-validation">
                                            <div class="input-group-text"><span class="fas fa-euro-sign"></span></div>
                                            <input type="text" class="form-control numbers_only"'.$required.' id="'.$id.'" name="'.$name.'" value="'.form_value($column['value']).'" placeholder="'.$column['label'].'" '.$attributes.'>
                                        <div class="invalid-feedback">Campo obbligatorio</div>
                                    ';
				break;
				case 'date':
                                    $response.='                                   
                                        <label class="form-label" for="'.$id.'">'.$column['label'].' '.$required_label.' '.$help_block.'</label>
                                        <div class="input-group has-validation">
                                            <div class="input-group-text"><span class="fas fa-calendar-alt"></span></div>
                                            <input type="text" class="form-control form-date-control datetimepicker"'.$required.' id="'.$id.'" name="'.$name.'" value="'.form_value($column['value']).'" placeholder="dd/mm/yy" data-options=\'{"dateFormat":"d/m/Y","disableMobile":true,"allowInput":true}\' '.$attributes.'>
                                        </div>
                                        <div class="invalid-feedback">Campo obbligatorio</div>
                                    ';
				break;
				case 'time':
                                    $response.='						
                                        <label class="form-label" for="'.$id.'">'.$column['label'].' '.$required_label.' '.$help_block.'</label>
                                        <div class="input-group has-validation">
                                            <input type="text" class="form-control form-time-control datetimepicker"'.$required.' id="'.$id.'" name="'.$name.'" value="'.form_value($column['value']).'" placeholder="H:i" data-options=\'{"enableTime":true,"noCalendar":true,"dateFormat":"H:i","disableMobile":true,"allowInput":true}\' '.$attributes.'>
                                            <div class="input-group-text"><span class="far fa-clock"></span></div>
                                            <div class="invalid-feedback">Campo obbligatorio</div>
                                        </div>
                                    ';
				break;
				case 'datetime':
                                    $response.='						
                                        <label class="form-label" for="'.$id.'">'.$column['label'].' '.$required_label.' '.$help_block.'</label>
                                        <div class="input-group has-validation">
                                            <input type="text" class="form-control form-datetime-control datetimepicker"'.$required.' id="'.$id.'" name="'.$name.'" value="'.form_value($column['value']).'" data-options=\'{"enableTime":true,"dateFormat":"d/m/Y H:i","disableMobile":true,"allowInput":true}\' '.$attributes.'>
                                            <div class="input-group-text"><span class="far fa-clock"></span></div>
                                            <div class="invalid-feedback">Campo obbligatorio</div>
                                        </div>
                                    ';
				break;
				case 'checkbox':
                                    $checked=$column['value']==1? 'checked':'';
                                    $response.='
                                        <label class="form-label '.$extra_css_class.'" for="'.$id.'">'.$column['label'].' '.$required_label.' '.$help_block.'</label>
                                        <div class="form-check form-switch '.$extra_css_class.'">                                        
                                            <input type="checkbox" id="'.$id.'" class="form-check-input cbox"'.$required.' '.$checked.'>                                            
                                            <input type="hidden" name="'.$name.'" value="'.$column['value'].'">
                                            <div class="invalid-feedback">Campo obbligatorio</div>
                                        </div>
                                    ';
				break;
				case 'search':
                                    $intent=$app->data->grouped_intents[$column['intent']];
                                    $db=$app->get_db();
                                    $search_label="select ".$column['lookup_text']." from ".$intent->table;
                                    $where_column='id';
                                    if(isset($column['intent_joins'])):
                                            foreach($column['intent_joins'] as $table=>$on):
                                                    $search_label.=" left join ".$table." on (".$on.") ";
                                            endforeach;
                                            $where_column=$intent->table.'.id';
                                    endif;
                                    $search_label.=" where ".$where_column."=".$column['value'];

                                    $search_label=$column['value']!=''?(array)mysqli_fetch_object($db->query($search_label)) : [];
                                    $domain=isset($column['domain'])?$column['domain'] : '';
                                    $lookup=isset($column['lookup_text'])? $column['lookup_text'] :'';
                                    $lookup_filter=isset($column['lookup_filter'])? $column['lookup_filter'] :'';
                                    $response.='
                                        <label class="form-label" for="'.$id.'">'.$column['label'].' '.$required_label.' '.$help_block.'</label>
                                        <div class="input-group has-validation">
                                            <input type="text" class="form-control search"'.$required.' id="'.$id.'" value="'.form_value(join(' ',$search_label)).'">
                                            <div class="input-group-text clear"><span class="far fa-times-circle" title="Cancella"></span></div>
                                            <div class="input-group-text lookup" data-bs-toggle="modal" data-bs-target="#search_modal" 
                                                    data-intent="'.$column['intent'].'" data-domain="'.$domain.'" data-intent_name="'.$intent->singular.'" data-lookup="'.$lookup.'" data-lookup-filter="'.$lookup_filter.'">
                                                            <span class="fas fa-search" title="Cerca"></span>
                                            </div>
                                            <input type="hidden" name="'.$name.'" value="'.form_value($column['value']).'" id="'.$id.'_value_holder">
                                            <div class="invalid-feedback">Campo obbligatorio</div>
                                        </div>
                                    ';
				break;
                                case 'select_standard':
                                    $response.='
                                        <label class="form-label" for="'.$id.'">'.$column['label'].' '.$required_label.' '.$help_block.'</label>
                                        <select class="form-select js-choice"'.$required.' id="'.$id.'" name="'.$name.'" '.$attributes.'>
                                                <option value="">Seleziona</option>';
                                                foreach($options as $value=>$label):
                                                        $selected=$column['value']===(string)$value? 'selected':'';
                                                        $response.='<option value="'.$value.'" '.$selected.'>'.$label.'</option>';
                                                endforeach;
                                                $response.='
                                        </select>
                                        <div class="invalid-feedback">Campo obbligatorio</div>
                                    ';
				break;
				case 'select':
                                    $response.='  
                                        <label class="form-label" for="'.$id.'">'.$column['label'].' '.$required_label.' '.$help_block.'</label>
                                        <select class="form-select js-choice"'.$required.' id="'.$id.'" name="'.$name.'" '.$attributes.'>
                                            <option value="">Seleziona</option>'; 
                                            foreach($options as $value=>$label):
                                                    $selected=$column['value']===(string)$value? 'selected':'';
                                                    $response.='<option value="'.$value.'" '.$selected.'>'.$label.'</option>';
                                            endforeach;
                                            $response.='
                                        </select> 
                                        <div class="invalid-feedback">Campo obbligatorio</div>
                                    ';
				break;
				case 'editor': // EDITOR MODALE
                                    $response.='                                    
                                    <label class="form-label" for="'.$id.'">'.$column['label'].' '.$required_label.' '.$help_block.'</label>   
                                        <div class="d-grid"><button class="btn btn-dark" type="button" data-bs-toggle="modal" data-bs-target="#editor-modal-'.$id.'"><span class="far fa-edit me-1" data-fa-transform="shrink-3"></span>Visualizza/Modifica</button></div>
                                        <div class="modal fade" id="editor-modal-'.$id.'" tabindex="-1" role="dialog" aria-hidden="true">
                                          <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                                            <div class="modal-content position-relative">
                                              <div class="modal-body p-0">
                                                <textarea class="tinymce d-none" data-tinymce="data-tinymce"'.$required.'" name="'.$name.'" id="'.$id.'">'.$column['value'].'</textarea>
                                              </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                          </div>
                                        </div>
                                        <div class="invalid-feedback">Campo obbligatorio</div>
                                    ';
				break;
				case 'plain_editor':
                                    $response.='
                                    <div id="plain_editor'.$id.'">
                                        <label class="form-label" for="'.$id.'">'.$column['label'].' '.$required_label.' '.$help_block.'</label>
                                        <textarea class="tinymce d-none" data-tinymce="data-tinymce"'.$required.'" name="'.$name.'" id="'.$id.'" data-height="'.$column['editor_height'].'">'.$column['value'].'</textarea>
                                        <div class="invalid-feedback">Campo obbligatorio</div>
                                    </div>';
				break;
				case 'multiple':
                                    $response.='
                                        <label class="form-label" for="'.$id.'" style="display:block">'.$column['label'].' '.$required_label.' '.$help_block.'</label>
                                        <input type="hidden" name="'.$name.'[]" value="">
                                        <select class="form-select js-choice"'.$required.' id="'.$id.'" name="'.$name.'[]"  multiple="multiple" data-options=\'{"removeItemButton":true,"placeholder":true}\'>';
                                            $column['value']=unserialize($column['value']);
                                            foreach($options as $value=>$label):
                                                    if(is_array($column['value'])):
                                                            $selected=in_array($value,$column['value'])? 'selected':'';
                                                    else:
                                                            $selected='';
                                                    endif;
                                                    $response.='<option value="'.$value.'" '.$selected.'>'.$label.'</option>';
                                            endforeach;
                                            $response.='
                                            <option value="">&nbsp;</option>
                                        </select>
                                    ';
				break;
				case 'multiple_checkbox':						
                                    $column['value']=unserialize($column['value']);
                                    $response.='
                                        <label class="form-label" for="'.$id.'">'.$column['label'].' '.$required_label.' '.$help_block.'</label>
                                        <div class="row" data-masonry=\'{"percentPosition": true }\'>    ';                                      
                                                                                        
                                            if($options['type'] === 'grouped') {         
                                                foreach($options['data'] as $group => $items):
                                                $response.='<div class="col-3 mb-4">';
                                                    $response.='
                                                    <div class="card overflow-hidden">
                                                        <div class="card-body">
                                                            <h5 class="card-title">'.$group.'</h5><div class="card-text">';
                                                            foreach($items as $value => $label):
                                                                $for_label = str_replace(['[', ']'], '', $value);
                                                            $checked=in_array($value,$column['value'])? 'checked':'';
                                                            $response.='<div class="form-check form-switch">
                                                                <input type="hidden" name="'.$name.'[]" value="">
                                                                <input class="form-check-input" name="'.$name.'[]" id="checkbox_'.$for_label.'" type="checkbox" value="'.$value.'" '.$checked.'/>
                                                                
                                                                <label class="form-check-label" for="checkbox_'.$for_label.'">'.$label.'</label>
                                                            </div>';
                                                            endforeach;
                                                        $response.='</div></div></div>';
                                                $response.='</div>';
                                                endforeach;
                                            }
                                            
                                    $response.='</div>';
				break;
                                case 'tags':

    /*
    |--------------------------------------------------------------------------
    | ARCHY MEDIA - CAMPO TAGS CONTROLLATI V9 / BADGE REALI + SUGGERIMENTI
    |--------------------------------------------------------------------------
    |
    | Questa versione separa il campo visibile dal valore salvato:
    | - input visibile: serve solo per scrivere/cercare;
    | - input hidden: contiene la lista dei tag salvata in main[campo];
    | - badge grigi: rappresentano i tag realmente inseriti;
    | - Invio crea subito il badge e svuota il campo visibile.
    |
    | In questo modo non dipendiamo da Choices.js e non usiamo più il trucco
    | della virgola visibile. AppActions.php riceve comunque una stringa
    | separata da virgole, quindi il salvataggio già funzionante resta invariato.
    |
    */

    $tag_category = isset($column['tag_category']) && trim((string)$column['tag_category']) !== ''
        ? $column['tag_category']
        : 'Tag';

    $selected_tag_ids = App::parse_tags_value($column['value']);
    $tag_options = App::get_tag_options($tag_category);

    $selected_labels = [];

    foreach($selected_tag_ids as $selected_tag_id):
        $selected_tag_id = (string)$selected_tag_id;

        if(isset($tag_options[$selected_tag_id])):
            $selected_labels[] = $tag_options[$selected_tag_id];
        elseif($selected_tag_id !== ''):
            $selected_labels[] = $selected_tag_id;
        endif;
    endforeach;

    $selected_labels = array_values(array_unique(array_filter(array_map('trim', $selected_labels))));

    $suggestions = [];
    foreach($tag_options as $tag_id => $tag_label):
        $tag_label = trim((string)$tag_label);
        if($tag_label !== ''):
            $suggestions[] = $tag_label;
        endif;
    endforeach;
    $suggestions = array_values(array_unique($suggestions));

    $suggestions_json = htmlspecialchars(json_encode($suggestions), ENT_QUOTES, 'UTF-8');
    $hidden_value = htmlspecialchars(implode(', ', $selected_labels), ENT_QUOTES, 'UTF-8');
    $safe_category = htmlspecialchars($tag_category, ENT_QUOTES, 'UTF-8');
    $control_id = $id.'_tags_control';
    $input_id = $id.'_tags_editor';
    $hidden_id = $id;
    $chips_id = $id.'_tags_chips';
    $suggestions_id = $id.'_tags_suggestions';

    $response.='
        <label class="form-label" for="'.$input_id.'">'.$column['label'].' '.$required_label.' '.$help_block.'</label>

        <div
            class="archy-tags-control"
            id="'.$control_id.'"
            data-archy-tags-suggestions="'.$suggestions_json.'"
            style="position:relative;"
        >
            <div
                class="archy-tags-box form-control"
                style="min-height:38px; height:auto; display:flex; flex-wrap:wrap; align-items:center; gap:6px; padding:6px;"
                onclick="this.querySelector(\'.archy-tags-editor\').focus();"
            >
                <div id="'.$chips_id.'" class="archy-tags-chips" style="display:flex; flex-wrap:wrap; gap:6px;"></div>
                <input
                    type="text"
                    class="archy-tags-editor"
                    id="'.$input_id.'"
                    placeholder="Scrivi o seleziona un tag"
                    autocomplete="off"
                    style="border:0; outline:0; flex:1; min-width:160px; padding:2px 4px;"
                    '.$attributes.'
                >
            </div>

            <input
                type="hidden"
                class="archy-tags-hidden"
                id="'.$hidden_id.'"
                name="'.$name.'"
                value="'.$hidden_value.'"
            >

            <div
                id="'.$suggestions_id.'"
                class="archy-tags-suggestions"
                style="display:none; position:absolute; z-index:9999; left:0; right:0; top:100%; max-height:220px; overflow:auto; background:#fff; border:1px solid #d8e2ef; border-radius:0 0 .375rem .375rem; box-shadow:0 8px 18px rgba(0,0,0,.12);"
            ></div>
        </div>

        <div class="form-text">Scrivi un nuovo tag e premi Invio, oppure seleziona un suggerimento. I nuovi valori saranno salvati automaticamente nella categoria '.$safe_category.'.</div>

        <script>
        (function(){
            if(window.ArchyTagsBadgesReady !== true){
                window.ArchyTagsBadgesReady = true;

                window.ArchyInitTagsBadges = function(){
                    document.querySelectorAll(".archy-tags-control").forEach(function(control){
                        if(control.dataset.archyReady === "1"){
                            return;
                        }
                        control.dataset.archyReady = "1";

                        var editor = control.querySelector(".archy-tags-editor");
                        var hidden = control.querySelector(".archy-tags-hidden");
                        var chips = control.querySelector(".archy-tags-chips");
                        var suggestionsBox = control.querySelector(".archy-tags-suggestions");

                        if(!editor || !hidden || !chips || !suggestionsBox){
                            return;
                        }

                        var suggestions = [];
                        try {
                            suggestions = JSON.parse(control.getAttribute("data-archy-tags-suggestions") || "[]");
                        } catch(e) {
                            suggestions = [];
                        }

                        function values(){
                            return String(hidden.value || "")
                                .split(",")
                                .map(function(v){ return v.trim(); })
                                .filter(function(v){ return v !== ""; });
                        }

                        function normalize(list){
                            var clean = [];
                            list.forEach(function(v){
                                v = String(v || "").trim();
                                if(v !== "" && clean.map(function(x){ return x.toLowerCase(); }).indexOf(v.toLowerCase()) === -1){
                                    clean.push(v);
                                }
                            });
                            return clean;
                        }

                        function setValues(list){
                            list = normalize(list);
                            hidden.value = list.join(", ");
                            hidden.dispatchEvent(new Event("input", { bubbles:true }));
                            hidden.dispatchEvent(new Event("change", { bubbles:true }));
                            renderChips();
                        }

                        function removeValue(value){
                            var low = String(value || "").toLowerCase();
                            setValues(values().filter(function(v){ return v.toLowerCase() !== low; }));
                            editor.focus();
                        }

                        function addValue(value){
                            var parts = String(value || "")
                                .split(",")
                                .map(function(v){ return v.trim(); })
                                .filter(function(v){ return v !== ""; });

                            if(parts.length === 0){
                                return;
                            }

                            setValues(values().concat(parts));
                            editor.value = "";
                            hideSuggestions();
                            editor.focus();
                        }

                        function renderChips(){
                            chips.innerHTML = "";
                            values().forEach(function(label){
                                var chip = document.createElement("span");
                                chip.className = "archy-tag-chip badge rounded-pill bg-secondary";
                                chip.style.display = "inline-flex";
                                chip.style.alignItems = "center";
                                chip.style.gap = "6px";
                                chip.style.padding = "6px 8px";
                                chip.textContent = label;

                                var close = document.createElement("button");
                                close.type = "button";
                                close.innerHTML = "&times;";
                                close.setAttribute("aria-label", "Rimuovi tag");
                                close.style.border = "0";
                                close.style.background = "transparent";
                                close.style.color = "#fff";
                                close.style.fontWeight = "700";
                                close.style.lineHeight = "1";
                                close.style.padding = "0";
                                close.style.cursor = "pointer";
                                close.addEventListener("click", function(e){
                                    e.preventDefault();
                                    e.stopPropagation();
                                    removeValue(label);
                                });

                                chip.appendChild(close);
                                chips.appendChild(chip);
                            });
                        }

                        function hideSuggestions(){
                            suggestionsBox.style.display = "none";
                            suggestionsBox.innerHTML = "";
                        }

                        function renderSuggestions(){
                            var term = editor.value.trim().toLowerCase();
                            var selected = values().map(function(v){ return v.toLowerCase(); });

                            var filtered = suggestions.filter(function(label){
                                var low = String(label).toLowerCase();
                                if(selected.indexOf(low) !== -1){
                                    return false;
                                }
                                return term === "" || low.indexOf(term) !== -1;
                            }).slice(0, 50);

                            suggestionsBox.innerHTML = "";

                            if(filtered.length === 0){
                                hideSuggestions();
                                return;
                            }

                            filtered.forEach(function(label){
                                var item = document.createElement("button");
                                item.type = "button";
                                item.textContent = label;
                                item.style.display = "block";
                                item.style.width = "100%";
                                item.style.textAlign = "left";
                                item.style.padding = ".45rem .75rem";
                                item.style.border = "0";
                                item.style.background = "#fff";
                                item.style.cursor = "pointer";
                                item.addEventListener("mouseenter", function(){ item.style.background = "#edf2f9"; });
                                item.addEventListener("mouseleave", function(){ item.style.background = "#fff"; });
                                item.addEventListener("mousedown", function(e){
                                    e.preventDefault();
                                    addValue(label);
                                });
                                suggestionsBox.appendChild(item);
                            });

                            suggestionsBox.style.display = "block";
                        }

                        editor.addEventListener("focus", renderSuggestions);
                        editor.addEventListener("input", function(){
                            if(editor.value.indexOf(",") !== -1){
                                addValue(editor.value);
                                return;
                            }
                            renderSuggestions();
                        });

                        editor.addEventListener("keydown", function(e){
                            if(e.key === "Enter"){
                                e.preventDefault();
                                e.stopPropagation();
                                addValue(editor.value);
                                return false;
                            }

                            if(e.key === "Backspace" && editor.value === ""){
                                var list = values();
                                list.pop();
                                setValues(list);
                            }

                            if(e.key === "Escape"){
                                hideSuggestions();
                            }
                        });

                        document.addEventListener("mousedown", function(e){
                            if(!control.contains(e.target)){
                                hideSuggestions();
                            }
                        });

                        renderChips();
                    });
                };

                if(document.readyState === "loading"){
                    document.addEventListener("DOMContentLoaded", window.ArchyInitTagsBadges);
                } else {
                    window.ArchyInitTagsBadges();
                }
            } else if(typeof window.ArchyInitTagsBadges === "function"){
                window.ArchyInitTagsBadges();
            }
        })();
        </script>
    ';

break;

				
				case 'file':
                                        $file_target=isset($column['file_target'])? $column['file_target']:'';
                                        
				case 'bulk_upload':
                                        //$whitelist_type = array('image/jpeg', 'image/png','image/gif','image');
                                        $StoreCloud = new StoreCloud();
                                        $StoreR2Adapter = new StoreR2Adapter();
                                        $file_control=isset($column['file_control'])? $column['file_control']:'';
                                        $file_target=isset($column['file_target'])? $column['file_target']:'storage';
					$multiple=$column['type']=='bulk_upload'? 'multiple':'';
					$name_suffix=$column['type']=='bulk_upload'? '[]':'';
                                        //$intents_table_cloud = ['archy_art_catalog','archy_art_publications','archy_art_exhibitions','archy_art_condition_report'];
					if($column['value']!=''):
						if($app->is_serialized($column['value'])):
                                                    $files_object=unserialize($column['value']);
						elseif($StoreCloud->isJson($column['value'])):
                                                    $files_object= json_decode($column['value']);
                                                else:
                                                    $files_object=array($column['value']);
						endif;
						//$images=$attachments=$videos=$title=[];
						//$finfo=finfo_open(FILEINFO_MIME_TYPE);
						foreach($files_object as $file_object):
                                                        if(strpos($file_object, 'assets') === false){ // se sono caricate su cloud
                                                            //$type=$app->get_file_type($path);
                                                           /* if ($file_target == 'cloudImages'):
                                                                $target_path=$StoreCloud->getImageUrl($file_object,'thumbnail');   
                                                                $original_filename= $StoreCloud->getImageDetailFromDb($file_object,'file_name');
                                                                $file_location= 'cloudImages';
                                                                $images[]=$target_path;
                                                            else:  // se sono caricate su storage
                                                                $file_location = 'storage';
                                                                $attachments[]=$file_object;
                                                            endif;*/
                                                            $file_location = $file_target;
                                                            //$title[]=$file_object;
                                                        }else{ // compatibilità con file vecchi caricati su server
                                                            /*list($type,$extension)=explode("/",finfo_file($finfo, $app->subdomain_root.$file_object));
                                                            $original_filename=explode("/",$file_object);
                                                            $target_path=$file_object;
                                                            $title[]=end($original_filename);*/
                                                            $file_location = 'local';
                                                        }
							/*if ($file_target == 'cloudImages'):
								
                                                        else:
								
							endif;*/
						endforeach;
						$total=count($files_object);
						/*if($total>5):
							$title=array_chunk($title, 5);
							$title=$title[0];
							$title[]='altri '.($total-5).' elementi...';
						endif;*/
						$addon=empty($files_object)? false : '<i class="far fa-file"></i><sup class="file_counter">'.$total.'</sup>';

					else:
						$addon=false;
					endif;
					if($addon):
						$required='';
						$data_field=isset($app->rendering_additional_fields) && $app->rendering_additional_fields ? 'value' : $column_name;
						$data_table=isset($app->rendering_additional_fields) && $app->rendering_additional_fields ? 'additional_fields_values' : $app->intent->table;
						if(isset($app->rendering_additional_fields) && $app->rendering_additional_fields):
							$domain_id=str_replace('additional_','',$column_name);
							$record_id=mysqli_fetch_object($app->db->query("select id from additional_fields_values where domain_id='".addslashes($domain_id)."' and record_id='".addslashes(@$_GET['id'])."'"));
							$data_id=@$record_id->id;
						else:
							$data_id=@$_GET['id'];
						endif;
						$response.='
                                                        <label class="form-label" for="'.$id.'">'.$column['label'].' '.$required_label.' '.$help_block.'</label>
                                                        <div class="input-group">
                                                            <input type="file" accept="'.$file_control.'" class="form-control"'.$required.' id="'.$id.'" name="'.$name.$name_suffix.'" '.$multiple.'>
                                                            <button class="btn btn-outline-secondary upload-btn" type="button" title="" data-html="true"
                                                                    data-bs-toggle="modal" data-bs-target="#uploads_modal"
                                                                    data-table="'.$data_table.'" data-location="'.$file_location.'" data-id="'.$data_id.'" data-field="'.$data_field.'">
                                                                    '.$addon.'
                                                            </button>
                                                            <input type="hidden" name="file_target_'.$column_name.'" value="'.$file_target.'">
                                                        </div>
                                                    ';
					else:
						$response.='
                                                        <label class="form-label" for="'.$id.'">'.$column['label'].' '.$required_label.' '.$help_block.'</label>
                                                        <input type="file" accept="'.$file_control.'" class="form-control"'.$required.' id="'.$id.'" name="'.$name.$name_suffix.'" '.$multiple.'>
                                                        <input type="hidden" name="file_target_'.$column_name.'" value="'.$file_target.'">
                                                ';
					endif;
				break;
			endswitch;
		$response.='</div>';
                
                // rewrite textarea + placeholder cols
                if($column['type']=='plain_editor' && (isset($column['placeholder'])&&$column['placeholder']==true)){
                    $Placeholder = new Placeholder();
                    $response .='<div class="col-4">
                            <div class="placeholder_container" data-rel="'.$id.'">
                            <p><strong>Placeholder</strong></p>
                            '.$Placeholder->getAvailablePlaceholders($column['placeholder_origin']).'
                            </div></div>';
                }
                
		return $response;
	}
        
        public function get_file_type($path){
            $result=mysqli_fetch_object($this->db->query("select file_media_type from archy_files where file_cloud_id='".$path."'"));
            return $result->file_media_type;
        }
	
	public function get_lookup_text(){
    global $app;

    // FIX MIRATO MODELLI PDF
    if(isset($_POST['table']) && $_POST['table'] == 'archy_models'){
        $id = (int)$_POST['id'];
        $lookup = $_POST['lookup'];

        $q = "select ".$lookup." from archy_models where id=".$id." limit 1";
        $search_label = (array) mysqli_fetch_object($this->db->query($q));

        echo trim(join(' ', $search_label));
        return;
    }

    include $this->main_root.'/private/_common/intents/'.$_POST['table'].'.php';

    $app->init();

    $q="select ".$_POST['lookup']." from ".$_POST['table'];

    if(count($app->intent->joins)):
        foreach($app->intent->joins as $join_table=>$on):
            $q.=" left join ".$join_table." on (".$on.")";
        endforeach;
    endif;

    $q.=" where ".$app->actions_column."=".$_POST['id'];

    $search_label=(array)mysqli_fetch_object($this->db->query($q));

    echo join(' ',$search_label);
}
	
		/*
|--------------------------------------------------------------------------
| STORICO - Vecchia gestione additional_fields()
|--------------------------------------------------------------------------
| Questa funzione originale gestiva direttamente dentro App.php:
|
| - rendering campi aggiuntivi
| - recupero valori
| - view_value
| - serializzazione
| - gestione upload
|
| Problemi principali riscontrati:
|
| - explode() su valori NULL
| - unserialize() su NULL/stringhe vuote
| - foreach() su bool/string
| - gestione date italiane incompatibile con DateTime()
| - App.php diventato troppo corposo
| - logica troppo accoppiata al core
|
| Dal 2026:
| La gestione è stata spostata nella classe:
|
| /private/_common/classes/AdditionalFields.php
|
| App.php ora usa solo un wrapper:
|
| public function additional_fields($where)
| {
|     if (!class_exists('AdditionalFields')) {
|         require_once __DIR__ . '/AdditionalFields.php';
|     }
|
|     $AdditionalFields = new AdditionalFields($this);
|
|     return $AdditionalFields->handle($where);
| }
|--------------------------------------------------------------------------
*/

/*
public function additional_fields($where){

	global $app;

	$db=$this->get_db();

	$fields=$db->query("
		select id, field_id
		from additional_fields_domain
		where table_name='".$this->intent->table."'
	");

	if($fields->num_rows>0):

		if($where=='form'):
			// echo '<h3>Campi aggiuntivi</h3><hr>';
			// echo '<div class="row">';
		endif;

		if($where=='get'):
			$additional_fields=[];
		endif;

		$app->rendering_additional_fields=true;

		while($domain=mysqli_fetch_object($fields)):

			$field=mysqli_fetch_object(
				$db->query("
					select *
					from additional_fields
					where id=".$domain->field_id
				)
			);

			$column=[];

			$column['alias']='additional_'.$domain->id;
			$column['label']=$field->name;
			$column['type']=$field->type;

			if($field->type=='select' or $field->type=='multiple'):
				$column['options_type']='array';
				$column['options']=unserialize($field->options);
			endif;

			if($field->required):
				$column['required']=true;
			endif;

			if(@$_GET['id']!=''):

				$field_value=mysqli_fetch_object(
					$db->query("
						select value
						from additional_fields_values
						where domain_id=".$domain->id."
						and record_id=".$_GET['id']
					)
				);

				$column['value']=@$field_value->value;

				if($where!='form'):

					switch($column['type']):

						case 'date':
							$column['view_value']=$this->latin_date($column['value']);
						break;

						case 'checkbox':
							$column['view_value']=$column['value']==0? 'No':'Sì';
						break;

						case 'select':
							$column['view_value']=@$column['options'][$column['value']];
						break;

						case 'multiple':

							$values=[];

							foreach($column['value'] as $key):
								$values[]=$column['options'][$key];
							endforeach;

							$column['view_value']=join(', ',$values);

						break;

						case 'file':

							$filename=explode('/',$column['value']);

							$column['view_value']='<a href="'.$column['value'].'" download>'.end($filename).'</a>';

						break;

						case 'bulk_upload':

							$paths=unserialize($column['value']);

							$values=[];

							foreach($paths as $path):

								$filename=explode('/',$path);

								$values[]='<a href="'.$path.'" download>'.end($filename).'</a>';

							endforeach;

							$column['view_value']=join(', ',$values);

						break;

						default:
							$column['view_value']=$column['value'];

					endswitch;

				endif;

			else:

				$column['value']=NULL;

			endif;

			switch($where):

				case 'form':
					echo $app::form_element($column);
				break;

				case 'list':
					echo $app::view_element($column,'list');
				break;

				case 'grid':
					echo $app::view_element($column,'grid');
				break;

				case 'get':
					$additional_fields[]=$column;
				break;

			endswitch;

		endwhile;

		if($where=='form'):
			// echo '</div>';
		endif;

		$app->rendering_additional_fields=false;

		if($where=='get'):
			return $additional_fields;
		endif;

	endif;
}
*/

public function additional_fields($where)
{
    if (!class_exists('AdditionalFields')) {
        require_once __DIR__ . '/AdditionalFields.php';
    }

    $AdditionalFields = new AdditionalFields($this);

    return $AdditionalFields->handle($where);
}
	

    /* COPIA FUNZIONE CAMPI PERSONALIZZATI */

	/*public function campi_personalizzati ($where){
		global $app;
		$db=$this->get_db();
		$fields=$db->query("select id, field_id from additional_fields_domain where table_name='".$this->intent->table."'");
		if($fields->num_rows>0):
			if($where=='form'):
				echo '<p>Campi Personalizzati</p><hr><div class="campi_personalizzati">';
			endif;
			if($where=='get'):
				$additional_fields=[];
			endif;
			$app->rendering_additional_fields=true;
			while($domain=mysqli_fetch_object($fields)):
				$field=mysqli_fetch_object($db->query("select * from additional_fields where id=".$domain->field_id));
				$column=[];
				$column['alias']='additional_'.$domain->id;
				$column['label']=$field->name;
				$column['type']=$field->type;
				if($field->type=='select' or $field->type=='multiple'):
					$column['options_type']='array';
					$column['options']=unserialize($field->options);
				endif;
				if($field->required):
					$column['required']=true;
				endif;
				if(@$_GET['id']!=''):
					$field_value=mysqli_fetch_object($db->query("select value from additional_fields_values where domain_id=".$domain->id." and record_id=".$_GET['id']));
					$column['value']=@$field_value->value;
					if($where!='form'):
						switch($column['type']):
							case 'date':
								$column['view_value']=$this->latin_date($column['value']);
							break;
							case 'checkbox':
								$column['view_value']=$column['value']==0? 'No':'Sì';
							break;
							case 'select':
								$column['view_value']=@$column['options'][$column['value']];
							break;
							case 'multiple':
								$values=[];
								foreach($column['value'] as $key):
									$values[]=$column['options'][$key];
								endforeach;
								$column['view_value']=join(', ',$values);
							break;
							case 'file':
								$filename=explode('/',$column['value']);
								$column['view_value']='<a href="'.$column['value'].'" download>'.end($filename).'</a>';
							break;
							case 'bulk_upload':
								$paths=unserialize($column['value']);
								$values=[];
								foreach($paths as $path):
									$filename=explode('/',$path);
									$values[]='<a href="'.$path.'" download>'.end($filename).'</a>';
								endforeach;
								$column['view_value']=join(', ',$values);
							break;
							default:
								$column['view_value']=$column['value'];
						endswitch;
					endif;
				else:
					$column['value']=NULL;
				endif;
				switch($where):
					case 'form':	echo $app::form_element($column);			break;
					case 'list':	echo $app::view_element($column,'list');	break;
					case 'grid':	echo $app::view_element($column,'grid');	break;
					case 'get':		$additional_fields[]=$column;					break;
				endswitch;
			endwhile;
			if($where=='form'):
				echo '</div>';
			endif;
			$app->rendering_additional_fields=false;
			if($where=='get'):
				return $additional_fields;
			endif;
		endif;
	}*/

	/* FINE FUNZIONE CAMPI PERSONALIZZATI */


	public static function view_element($column,$mode){
		if($mode=='list'):
			return '<tr><th>'.$column['label'].'</th><td>'.$column['view_value'].'</td></tr>';
		elseif($mode=='grid'):
			return '<div class="col-xs-12 col-sm-6 col-md-3 col-lg-2 fixed-height"><strong>'.$column['label'].'</strong><br>'.$column['view_value'].'</div>';
		endif;
	}
	
	public function load_view($id){
		$db=$this->get_db();
		/* ANDREA - PARTE DI FUNZIONE COMMENTATA PER GESTIRE LA VISUALIZZAZIONE DEI NUOVI CAMPI AGGIUNTIVI */
		/*$fields=array_keys($this->cols);
		foreach($fields as $index=>$field):
			$fields[$index]=$field.' as '.str_replace(".","___",$field);
		endforeach;
		$q="select ".join(',',$fields)." from ".$this->intent->table;*/
		/* ANDREA - INIZIO BLOCCO MODIFICA NUOVA FUNZIONE*/
		$fields = array_keys($this->cols);
		$additional_joins = [];

		// Qualifica la colonna ID una sola volta.
		// In alcuni intent $this->actions_column è "id", in altri è già "nome_tabella.id".
		$qualified_actions_column = (strpos($this->actions_column, '.') !== false)
			? $this->actions_column
			: $this->intent->table.'.'.$this->actions_column;

		foreach($fields as $index => $field):

			/*
			|--------------------------------------------------------------------------
			| CAMPI AGGIUNTIVI IN LOAD_VIEW - CONTROLLO SICURO
			|--------------------------------------------------------------------------
			|
			| Un campo aggiuntivo dinamico deve avere forma:
			|
			|     additional_12
			|
			| quindi "additional_" seguito da un ID numerico.
			|
			| Non devono invece essere intercettati i campi nativi
			| dell'intent additional_fields, ad esempio:
			|
			|     additional_fields.name
			|     additional_fields.type
			|
			| Senza questo controllo, il sistema genera alias SQL non validi
			| come "AS additional_fields.name".
			|
			*/
			if (preg_match('/^additional_[0-9]+$/', $field)):
				$domain_id = (int) str_replace('additional_', '', $field);
				$alias = 'afv_'.$domain_id;

				$fields[$index] = $alias.'.value as '.$field;

				$additional_joins[$alias] = "
					left join additional_fields_values as ".$alias."
					on (
						".$alias.".record_id = ".$qualified_actions_column."
						and ".$alias.".domain_id = ".$domain_id."
					)
				";
			else:
				$fields[$index] = $field.' as '.str_replace(".","___",$field);
			endif;

		endforeach;

		$q="select ".join(',',$fields)." from ".$this->intent->table;
		/* FINE BLOCCO MODIFICA NUOVA FUNZIONE*/
		if(count($this->intent->joins)):
			foreach($this->intent->joins as $join_table=>$on):
				$q.=" left join ".$join_table." on (".$on.") ";
			endforeach;
		endif;
		// ANDREA - AGGIUNTA BLOCCO PER NUOVI CAMPI AGGIUNTIVI
		if (!empty($additional_joins)):
			foreach($additional_joins as $additional_join):
				$q .= $additional_join;
			endforeach;
		endif;
		// ANDREA - FINE AGGIUNTA BLOCCO PER NUOVI CAMPI AGGIUNTIVI
		$q.=" where ".$qualified_actions_column."=".(int)$id;
		//echo '<pre style="display:none">'.$q.'</pre>';
		$recordset=(array)mysqli_fetch_object($db->query($q));
		$data=array();
		$title=$this->intent->view_title;

		foreach($recordset as $alias=>$value):
                    
                    $alias=str_replace('___','.',$alias);
                    $column=$this->cols[$alias];
                    $label=isset($column['label'])?$column['label']:'';
                    if(isset($column['formatter'])):
                            $value=$column['formatter']($value,'');
                    endif;
                    $data[$alias]=array('label'=>$label,'value'=>$value);
                    if($value === null){
                        $value = '';
                    }
                    if(!is_array($value)):
                            $title=str_replace($alias,$value,$title);
                    endif;
		endforeach;
		$return=new stdClass();
		$return->title=$title;
		$return->data=$data;
		$return->additional_fields=$this->additional_fields('get');
		return $return;
	}
	
	public function get_view_title(){
		global $app;
		if(@$_GET['id']!=''):
			$view=$app->load_view($_GET['id']);
			$title=$view->title;
		else:
			$title=$app->intent->add;
		endif;
		return $title;
	}
        
        public function get_intent_filter(){
		global $app;
		if(@$_GET['intent_filter']!=''):
			$filter=$_GET['intent_filter'];
		else:
			$filter=null;
		endif;
		return $filter;
	}
	
	public function get_form($new_load=false){
                
                //if($id_to_load!=NULL){$id_call=$id_to_load;}
		$db=$this->get_db();
		$data=$hidden_columns=array();
		if(isset($_GET['id']) && $_GET['id']!='' && !$new_load)://edit
                        $id_call = $_GET['id'];
			$fields=array_keys($this->main_cols);
			foreach($fields as $index=>$field):
				$fields[$index]=$field.' as '.str_replace(".","___",$field);
			endforeach;
			
			$q="select ".join(',',$fields)." from ".$this->intent->table;
			$q.=" where ".$this->actions_column."=".$id_call;
                        
			$recordset=(array)mysqli_fetch_object($db->query($q));
			foreach($recordset as $alias=>$value):
				$alias=str_replace('___','.',$alias);
				$column=$this->cols[$alias];
				if(isset($column['formatter']) and !isset($column['formatter_hide_in_form'])):
					$value=$column['formatter']($value,'');
				endif;
				$column['value']=$value;
				$column['alias']=$alias;
				if(!isset($column['hidden'])):
					$data[$alias]=$column;
				else:
					$hidden_columns[$alias]=$column;
				endif;
			endforeach;
		else://add
			$columns=$this->main_cols;
			foreach($columns as $alias=>$column):
				$column['value']=isset($column['default_value'])? $column['default_value'] : '';
				$column['alias']=$alias;
				if(!isset($column['hidden'])):
					$data[$alias]=$column;
				else:
					$hidden_columns[$alias]=$column;
				endif;
			endforeach;
		endif;
		return array('columns'=>$data,'hidden_columns'=>$hidden_columns);
	}
	
	public function get_custom_form($intent,$record_id=NULL){
		global $app;
		$app=new App();
		require $this->main_root.'/private/_common/intents/'.$intent.'.php';
		$app->init();
		if(isset($_GET['id'])):
			$id=$_GET['id'];
			$_GET['id']=$record_id;
		endif;
		$response=$app->get_form();
		if(isset($id)):
			$_GET['id']=$id;
		endif;
		return $response;
	}
		
	//extra actions
	/*public function auth_certificates_extra_action(){
		$auth_certificate=new AuthCertificate();
		$auth_certificate->store_attachments($this->insert_id);
		$auth_certificate->store_pdf($this->insert_id);
	}*/
	
	public static function is_serialized($string){
		return (@unserialize($string) !== false);
	}
        
        public function get_cloud_attachments(){
            global $app;
            $cloud = new StoreCloud();
            $r2 = new StoreR2Adapter();
            $repo = new StoreFileRepository();
            
            $clouds_id = '';
            $images = false;
            $storage = false;
            $local = false;
            $editing= false;
            
            if(isset($_POST['source_table']) && $_POST['source_table']!=''){ // chiamo la modale dall'editing
                $files = $repo->getFilesFromDb($_POST['source_id'],$_POST['source_field'],$_POST['source_table']);
                if($_POST['source_location']== 'cloudImages'){$images = true;}
				elseif($_POST['source_location']== 'storage'){$storage = true;}
				else{$local=true;}
                $editing = true;
            }else{
                if($_POST['data_images']!=''){ $clouds_id = $_POST['data_images']; $images = true; }
                elseif($_POST['data_files']!=''){ $clouds_id = $_POST['data_files']; $storage = true; }
                $files = explode(',',$clouds_id);
            }
            
            $result = '<table class="table table-striped"><tbody>';
            foreach ($files as $file){
                $addButtons = '';
                $file_dest_url = '';
                $file_name = $cloud->getImageDetailFromDb($file,'file_name');
                if($images){
                    $file_url = $cloud->getImageUrl($file,'thumbnail');
                    $file_dest_url = $cloud->getImageUrl($file,'public');
                }elseif($storage){
                    $file_url = $r2->getFileUrl($file);
                    $file_dest_url = $file_url;
                }else{
					$file_dest_url = $file;
				}
                if($images){ $addButtons .= '<a href="/file_cloud_serve.php?imageId='.$file.'" class="btn btn-primary btn-lg btn-block">Scarica</a>'; }
                if($editing){ $addButtons.='<a class="btn btn-danger btn-lg btn-block delete_attachment" href="#" data-file="'.$file.'">Elimina</a>'; }
                
                $result .= '<tr>
                    <td>
                        '.$repo->getFilePreview($file_name,$file_url).'
                    </td>    
                    <td>'.$file_name.'</td>
                    <td>
                        <a href="'.$file_dest_url.'" target="_blank" class="btn btn-primary btn-lg btn-block">Visualizza</a>
                        '.$addButtons.'
                    </td>
                    </tr>
                ';
                
            }
            $result .= '</tbody></div>';
            echo $result;
        }
	
	/*
	|--------------------------------------------------------------------------
	| ARCHY MEDIA - CANCELLAZIONE ALLEGATO CONTESTUALIZZATA
	|--------------------------------------------------------------------------
	|
	| La versione precedente chiamava StoreManager->delete() usando solo il
	| file_cloud_id/filename. In presenza di schede duplicate, più record
	| archy_files possono condividere lo stesso file_cloud_id: la cancellazione
	| globale prendeva il primo record trovato e poteva aggiornare/cancellare
	| l'allegato della scheda originale invece di quello della scheda duplicata.
	|
	| Questa versione cancella solo il riferimento della scheda/campo corrente:
	|
	| - table  = tabella sorgente, es. archy_art_catalog
	| - id     = ID record corrente
	| - field  = campo corrente
	| - filename = file_cloud_id / percorso file
	|
	| Il file fisico/cloud viene eliminato solo se non restano altri riferimenti
	| allo stesso file_cloud_id in archy_files.
	|
	*/
	public function delete_attachment(){
		global $app;

		header('Content-Type: application/json');

		$db = $this->get_db();

		$table = isset($_POST['table']) ? preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['table']) : '';
		$field = isset($_POST['field']) ? preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['field']) : '';
		$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
		$filename = isset($_POST['filename']) ? trim((string) $_POST['filename']) : '';
		$location = isset($_POST['location']) ? trim((string) $_POST['location']) : '';

		if($table == '' || $field == '' || $id <= 0 || $filename == ''){
			echo json_encode([
				'success' => false,
				'message' => 'Parametri allegato non validi.'
			]);
			exit;
		}

		$encode_value = function($values, $original){
			$values = array_values(array_filter($values, function($v){
				return trim((string)$v) !== '';
			}));

			if(empty($values)){
				return '';
			}

			if($this->is_serialized($original)){
				return serialize($values);
			}

			$json = json_decode($original, true);
			if(is_array($json)){
				return json_encode($values);
			}

			if(count($values) === 1){
				return (string) $values[0];
			}

			return json_encode($values);
		};

		$get_values = function($value){
			if($value === null || $value === ''){
				return [];
			}

			if($this->is_serialized($value)){
				$unserialized = @unserialize($value);
				return is_array($unserialized) ? $unserialized : [$value];
			}

			$json = json_decode($value, true);
			if(is_array($json)){
				return $json;
			}

			return [$value];
		};

		/*
		|--------------------------------------------------------------------------
		| Aggiorna il campo del record corrente
		|--------------------------------------------------------------------------
		*/
		$current_value = '';
		$res = $db->query("SELECT `".$db->real_escape_string($field)."` AS field_value FROM `".$db->real_escape_string($table)."` WHERE id = ".$id." LIMIT 1");

		if($res && $row = mysqli_fetch_assoc($res)){
			$current_value = (string) $row['field_value'];
			$values = $get_values($current_value);

			$values = array_values(array_filter($values, function($v) use ($filename){
				return (string)$v !== (string)$filename;
			}));

			$new_value = $encode_value($values, $current_value);

			$db->query("UPDATE `".$db->real_escape_string($table)."` SET `".$db->real_escape_string($field)."` = '".$db->real_escape_string($new_value)."' WHERE id = ".$id." LIMIT 1");
		}

		/*
		|--------------------------------------------------------------------------
		| Cancella solo il record archy_files collegato al record/campo corrente
		|--------------------------------------------------------------------------
		*/
		$db->query("
			DELETE FROM archy_files
			WHERE file_reference_id = ".$id."
			AND file_reference_table = '".$db->real_escape_string($table)."'
			AND file_reference_field = '".$db->real_escape_string($field)."'
			AND file_cloud_id = '".$db->real_escape_string($filename)."'
			LIMIT 1
		");

		/*
		|--------------------------------------------------------------------------
		| Elimina fisicamente il file solo se non è più usato da nessun record
		|--------------------------------------------------------------------------
		*/
		$remaining = mysqli_fetch_object($db->query("
			SELECT COUNT(*) AS cnt
			FROM archy_files
			WHERE file_cloud_id = '".$db->real_escape_string($filename)."'
		"));

		$physical_deleted = false;

		if($remaining && (int)$remaining->cnt === 0){
			try{
				if($location == 'cloudImages'){
					$StoreCloud = new StoreCloud($app);
					$StoreCloud->deleteImage($filename);
					$physical_deleted = true;
				}elseif($location == 'storage'){
					$StoreR2Adapter = new StoreR2Adapter($app);
					$StoreR2Adapter->deleteFile($filename);
					$physical_deleted = true;
				}elseif($location == 'local'){
					$path = $this->subdomain_root.$filename;
					if(is_file($path)){
						unlink($path);
						$physical_deleted = true;
					}
				}
			}catch(Throwable $e){
				error_log('delete_attachment physical delete error: '.$e->getMessage());
			}
		}

		echo json_encode([
			'success' => true,
			'physical_deleted' => $physical_deleted
		]);
		exit;
	}

    // Note: non gestisce campi serializzati 
    public function delete_attachment_settings(){
		$db=$this->get_db();
        $ret=$db->query("delete from archy_settings where domain='".addslashes($_POST['domain'])."' and name='".addslashes($_POST['name'])."'");
        if(is_file($this->subdomain_root.$_POST['filename'])):
            unlink($this->subdomain_root.$_POST['filename']);
        endif;
    }

	public function store_additional_field_domains(){
		$db=$this->get_db();
		if($_POST['action']=='update'):
			$db->query("delete from additional_fields_domain where field_id=".$this->insert_id);
		endif;
		foreach($_POST['main']['tables'] as $table_name):
			$db->query("insert into additional_fields_domain set field_id='".$this->insert_id."', table_name='".addslashes($table_name)."'");
		endforeach;
	}
	

	/*
	|--------------------------------------------------------------------------
	| EXPORT DATATABLE - VERSIONE ORIGINALE
	|--------------------------------------------------------------------------
	|
	| Questa era la funzione originale sviluppata per esportare le DataTable
	| in Excel/PDF leggendo solo i campi nativi della tabella principale
	| e le eventuali join gia definite nell'intent.
	|
	| La lasciamo commentata come storico tecnico, per confronto e rollback.
	| Non viene eseguita.
	|
	*/

	// public function export(){
	// 		//error_reporting(E_ALL); ini_set('display_errors', TRUE); ini_set('display_startup_errors', TRUE);
	// 		global $app;
	// 		
	// 		require_once $this->main_root.'/private/_common/intents/'.$_GET['intent'].'.php';
	// 		$app->init();
	// 		
	// 		$columns=explode(',',$_GET['columns']);
	// 		$columns_labels=$columns_data=$query_columns=[];
	// 		foreach($columns as $column):
	// 			$column_label=$app->cols[$column]['label'];
	// 			$columns_data[]=$app->cols[$column];
	// 			if(empty($app->intent->joins)):
	// 				$temp_array=explode('.',$column);
	// 				$column=end($temp_array);
	// 				$columns_labels[]=$column_label;
	// 			else:
	// 				if(in_array($column_label,$columns_labels)):
	// 					list($table_name,$column_name)=explode('.',$column);
	// 					$column_label=$column_label.' '.@$app->data->grouped_intents[$table_name]->singular;
	// 				endif;
	// 				$columns_labels[]=$column_label;
	// 				$column=$column.' as '.preg_replace('/[^A-Za-z0-9]/','',$column_label);
	// 			endif;
	// 			$query_columns[]=$column;
	// 		endforeach;
	// 		
	// 		$q='select '.implode(',',$query_columns).' from '.$_GET['intent'];
	// 		foreach($app->intent->joins as $join_table=>$on):
	// 			$q.=" left join ".$join_table." on (".$on.") ";
	// 		endforeach;
	// 		
	// 		$where='';
	// 		if(@$_GET['search']!=''):
	// 			$globalSearch=[];
	// 			foreach($columns as $column):
	// 				$globalSearch[]=" ".$column." like '%".addslashes($_GET['search'])."%' ";
	// 			endforeach;
	// 			$where = '('.implode(' OR ', $globalSearch).')';
	// 		endif;
	// 		if(isset($_GET['filter'])):
	// 			$columnSearch=[];
	// 			foreach($_GET['filter'] as $column_name=>$value):
	// 				$columnSearch[]=" ".$column_name." like '%".addslashes($value)."%' ";
	// 			endforeach;
	// 			$where = $where === '' ?
	// 				implode(' AND ', $columnSearch) :
	// 				$where .' AND '. implode(' AND ', $columnSearch);
	// 		endif;		
	// 		if($where!==''):
	// 			$q.=' WHERE '.$where;
	// 		endif;
	// 		
	// 		$q.=' order by '.$_GET['order_by'].' '.$_GET['order'];
	// 		$db=$this->get_db();
	// 		//error_log('QUERY: '.$q);
	// 		$data=$db->query($q);
	// 		$format=$_GET['format'];
	// 		$app->$format($data,$filename=$app->intent->title,$columns_labels,$columns_data);		
	// 	}

	/*
	|--------------------------------------------------------------------------
	| EXPORT DATATABLE - VERSIONE ESTESA CON CAMPI AGGIUNTIVI
	|--------------------------------------------------------------------------
	|
	| Questa versione mantiene il comportamento originale dell'export, ma aggiunge
	| il supporto ai campi aggiuntivi dinamici selezionati nella DataTable.
	|
	| I campi additional_XX non esistono fisicamente nella tabella principale:
	| vengono quindi letti con LEFT JOIN dinamiche su additional_fields_values.
	|
	| La modifica serve ai pulsanti export Excel/PDF in alto nella griglia e non
	| altera la logica dei tre puntini riga-record, che resta gestita dall'intent.
	|
	| Funzionalita introdotte:
	| - riconoscimento automatico colonne additional_XX;
	| - JOIN dinamiche su additional_fields_values;
	| - compatibilita con colonne native e intent gia esistenti;
	| - protezione da doppi prefissi tabella.tabella.campo.
	|
	*/

	/*
	|--------------------------------------------------------------------------
	| RIFERIMENTO FUNZIONE ORIGINALE EXPORT - NON ELIMINARE
	|--------------------------------------------------------------------------
	|
	| La funzione export originale del gestionale gestiva le esportazioni
	| partendo dalle sole colonne native presenti in $app->cols.
	|
	| Schema logico originale:
	|
	| public function export(){
	|     - inizializza intent corrente;
	|     - legge colonne richieste;
	|     - costruisce SELECT sulla tabella principale;
	|     - applica filtri / ricerca / ordinamento;
	|     - richiama $app->excel(...) oppure $app->pdf(...).
	| }
	|
	| Limite della versione originale:
	| - non riconosceva colonne additional_XX;
	| - non creava JOIN su additional_fields_values;
	| - non esportava i valori dei campi aggiuntivi;
	| - non trasformava upload/bulk_upload in link scaricabili.
	|
	| Questo blocco è documentale: non contiene codice eseguibile e serve
	| come riferimento per eventuali confronti futuri.
	|
	*/

	/*
	|--------------------------------------------------------------------------
	| EXPORT DATATABLE - VERSIONE ESTESA
	|--------------------------------------------------------------------------
	|
	| Questa funzione gestisce i pulsanti di esportazione della DataTable.
	|
	| Rispetto alla funzione originale, mantiene il comportamento standard
	| dell'export ma aggiunge il supporto ai campi aggiuntivi dinamici.
	|
	| Funzionalità gestite:
	| - colonne native del modulo;
	| - colonne additional_XX;
	| - normalizzazione colonne tipo tabella.additional_XX;
	| - LEFT JOIN su additional_fields_values;
	| - export Excel;
	| - export PDF;
	| - link completi per campi file / bulk_upload.
	|
	| NOTA DI COMPATIBILITÀ
	| -----------------------------------------------------------------------
	| La funzione originale non è stata eliminata concettualmente: la logica
	| standard è stata mantenuta e ampliata per evitare di rompere i processi
	| esistenti del gestionale.
	|
	*/



	public function export(){
		//error_reporting(E_ALL); ini_set('display_errors', TRUE); ini_set('display_startup_errors', TRUE);
		global $app;
		
		require_once $this->main_root.'/private/_common/intents/'.$_GET['intent'].'.php';
		$app->init();

		/*
		|--------------------------------------------------------------------------
		| EXPORT DATATABLE ESTESO - CAMPI AGGIUNTIVI
		|--------------------------------------------------------------------------
		|
		| Questa versione mantiene la logica originale di export Excel/PDF ma
		| aggiunge il supporto alle colonne dinamiche additional_XX.
		|
		| Nota importante:
		| i campi aggiuntivi potrebbero non essere presenti in $app->cols quando
		| l'export viene lanciato da pulsante, quindi vengono recuperati anche
		| direttamente da additional_fields_domain + additional_fields.
		|
		*/

		$export_qualify = function($table, $column) {
			return (strpos($column, '.') !== false) ? $column : $table.'.'.$column;
		};

		$export_parse_multiple = function($value) {
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
				if ($v !== '') {
					$clean[] = $v;
				}
			}

			return array_values(array_unique($clean));
		};

		$export_safe_unserialize = function($value, $fallback = []) {
			if ($value === null || $value === '') {
				return $fallback;
			}

			$unserialized = @unserialize($value);
			return is_array($unserialized) ? $unserialized : $fallback;
		};

		/*
		|--------------------------------------------------------------------------
		| URL DOWNLOAD CAMPI AGGIUNTIVI FILE / BULK UPLOAD
		|--------------------------------------------------------------------------
		|
		| I campi aggiuntivi di tipo file e bulk_upload salvano normalmente il
		| nome file o un percorso parziale. In export Excel il valore viene
		| trasformato in URL scaricabile dal sottodominio corrente.
		|
		| Cartella pubblica di riferimento:
		| /assets/documents/upload_extrafield/
		|
		*/
		$export_upload_url = function($value) {
			$value = trim((string)$value);

			if ($value === '') {
				return '';
			}

			$decoded = json_decode($value, true);

			if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
				$value = reset($decoded);
				$value = trim((string)$value);
			}

			$value = trim($value, '[]"');

			if ($value === '') {
				return '';
			}

			if (preg_match('#^https?://#i', $value)) {
				return $value;
			}

			$filename = basename($value);

			if ($filename === '') {
				return '';
			}

			return 'https://demo-storage.archyart.it/uploads/' . rawurlencode($filename);
		};

		// Normalizza i nomi delle colonne additional.
		// La griglia può passare sia "additional_12" sia "archy_art_catalog.additional_12"
		// tramite l'attributo data-column dell'header. Per l'export devono essere
		// trattati allo stesso modo.
		$export_normalize_column = function($column) {
			$column = trim((string)$column);
			if (preg_match('/(?:^|\.)(additional_\d+)$/', $column, $m)) {
				return $m[1];
			}
			return $column;
		};

		$export_build_additional_column = function($column) use ($app, $export_parse_multiple, $export_safe_unserialize, $export_normalize_column, $export_upload_url) {
			$column = $export_normalize_column($column);
			$domain_id = (int) str_replace('additional_', '', $column);
			$field = null;

			if (isset($app->cols[$column])) {
				$field = (object) [
					'domain_id' => $domain_id,
					'name' => isset($app->cols[$column]['label']) ? $app->cols[$column]['label'] : $column,
					'type' => isset($app->cols[$column]['type']) ? $app->cols[$column]['type'] : 'text',
					'options' => isset($app->cols[$column]['options']) ? $app->cols[$column]['options'] : []
				];
			} else {
				$res = $this->db->query("
					SELECT
						afd.id AS domain_id,
						af.name,
						af.type,
						af.options
					FROM additional_fields_domain afd
					INNER JOIN additional_fields af ON af.id = afd.field_id
					WHERE afd.id = '".$domain_id."'
					LIMIT 1
				");

				if ($res && mysqli_num_rows($res)) {
					$field = mysqli_fetch_object($res);
					$field->options = $export_safe_unserialize($field->options, []);
				}
			}

			if (!$field) {
				return null;
			}

			$options = isset($field->options) && is_array($field->options) ? $field->options : [];
			$type = isset($field->type) ? $field->type : 'text';

			$formatter = function($value, $row = '') use ($type, $options, $export_parse_multiple, $export_upload_url) {
				if ($value === null || $value === '') {
					return '';
				}

				switch ($type) {
					case 'data':
					case 'date':
						$ts = strtotime($value);
						return $ts ? date('d/m/Y', $ts) : $value;

					case 'checkbox':
						return ((int)$value === 1) ? 'Sì' : 'No';

					case 'select':
						return isset($options[$value]) ? $options[$value] : $value;

					case 'multiple':
						$values = $export_parse_multiple($value);
						$labels = [];
						foreach ($values as $v) {
							$label = isset($options[$v]) ? $options[$v] : $v;
							$label = trim((string)$label);
							if ($label !== '') {
								$labels[] = $label;
							}
						}
						return implode('<br>', $labels);

					case 'bulk_upload':
						$files = @unserialize($value);
						if (!is_array($files)) {
							$files = $export_parse_multiple($value);
						}
						$urls = [];
						foreach ($files as $path) {
							if (is_array($path)) {
								continue;
							}
							$url = $export_upload_url($path);
							if ($url !== '') {
								$urls[] = $url;
							}
						}
						return implode('<br>', $urls);

					case 'file':
						return $export_upload_url($value);

					default:
						return $value;
				}
			};

			$excel_formatter = function($value, $row = '') use ($formatter) {
				return str_replace(['<br />', '<br/>', '<br>'], "\n", $formatter($value, $row));
			};

			return [
				'label' => $field->name,
				'type' => $type,
				'additional_field' => true,
				'domain_id' => $domain_id,
				'options' => $options,
				'formatter' => $formatter,
				'excel_formatter' => $excel_formatter,
				'pdf_formatter' => $formatter
			];
		};

		$export_sql_column = function($column, $useAliasValue = true) use ($app, $export_normalize_column) {
			$column = $export_normalize_column($column);
			if (strpos($column, 'additional_') === 0) {
				$domain_id = (int) str_replace('additional_', '', $column);
				$alias = 'afv_export_'.$domain_id;
				return $useAliasValue ? "COALESCE(".$alias.".value,'')" : $alias.'.value';
			}

			if (empty($app->intent->joins)) {
				$temp_array = explode('.', $column);
				return end($temp_array);
			}

			return $column;
		};
		
		$columns = isset($_GET['columns']) ? explode(',', $_GET['columns']) : [];
		$columns = array_values(array_filter($columns));
		$columns = array_map($export_normalize_column, $columns);
		$columns_labels = $columns_data = $query_columns = [];
		$additionalJoins = [];
		$table = $_GET['intent'];
		$actionColumn = $export_qualify($table, $app->actions_column);

		foreach($columns as $column):
			if(strpos($column, 'additional_') === 0):
				$additionalColumn = $export_build_additional_column($column);
				if(!$additionalColumn):
					continue;
				endif;

				$domain_id = (int) str_replace('additional_', '', $column);
				$alias = 'afv_export_'.$domain_id;
				$additionalJoins[$alias] = " left join additional_fields_values as ".$alias." on (".$alias.".record_id = ".$actionColumn." and ".$alias.".domain_id = ".$domain_id.") ";

				$columns_labels[] = $additionalColumn['label'];
				$columns_data[] = $additionalColumn;
				$query_columns[] = "COALESCE(".$alias.".value,'') as ".$column;
				continue;
			endif;

			if(!isset($app->cols[$column])):
				continue;
			endif;

			$column_label = $app->cols[$column]['label'];
			$columns_data[] = $app->cols[$column];

			if(empty($app->intent->joins)):
				$temp_array = explode('.',$column);
				$column = end($temp_array);
				$columns_labels[] = $column_label;
			else:
				if(in_array($column_label,$columns_labels)):
					list($table_name,$column_name) = explode('.',$column);
					$column_label = $column_label.' '.@$app->data->grouped_intents[$table_name]->singular;
				endif;
				$columns_labels[] = $column_label;
				$column = $column.' as '.preg_replace('/[^A-Za-z0-9]/','',$column_label);
			endif;

			$query_columns[] = $column;
		endforeach;
		
		if(empty($query_columns)):
			die('Nessuna colonna esportabile selezionata');
		endif;

		$q = 'select '.implode(',',$query_columns).' from '.$_GET['intent'];

		foreach($app->intent->joins as $join_table=>$on):
			$q .= " left join ".$join_table." on (".$on.") ";
		endforeach;

		foreach($additionalJoins as $additionalJoin):
			$q .= $additionalJoin;
		endforeach;
		
		$where = '';

		if(@$_GET['search'] != ''):
			$globalSearch = [];
			foreach($columns as $column):
				if(strpos($column, 'additional_') === 0 || isset($app->cols[$column])):
					$sqlColumn = $export_sql_column($column, false);
					$globalSearch[] = " ".$sqlColumn." like '%".addslashes($_GET['search'])."%' ";
				endif;
			endforeach;
			if(!empty($globalSearch)):
				$where = '('.implode(' OR ', $globalSearch).')';
			endif;
		endif;

		if(isset($_GET['filter'])):
			$columnSearch = [];
			foreach($_GET['filter'] as $column_name=>$value):
				$column_name = $export_normalize_column($column_name);
				if($value === ''):
					continue;
				endif;
				$sqlColumn = $export_sql_column($column_name, false);
				$columnSearch[] = " ".$sqlColumn." like '%".addslashes($value)."%' ";
			endforeach;
			if(!empty($columnSearch)):
				$where = $where === '' ? implode(' AND ', $columnSearch) : $where .' AND '. implode(' AND ', $columnSearch);
			endif;
		endif;


		/*
		|--------------------------------------------------------------------------
		| LIMITAZIONE EXPORT ASINCRONO PER ID
		|--------------------------------------------------------------------------
		| Il worker PDF DataTable passa solo gli ID del batch corrente. In questo
		| modo ogni richiesta genera poche righe e libera rapidamente PHP-FPM.
		*/
		if (!empty($_GET['_export_ids'])):
			$exportIds = array_values(array_unique(array_filter(array_map('intval', explode(',', (string)$_GET['_export_ids'])))));
			if (empty($exportIds)):
				$where = $where === '' ? '1=0' : '(' . $where . ') AND 1=0';
			else:
				$idCondition = $actionColumn . ' IN (' . implode(',', $exportIds) . ')';
				$where = $where === '' ? $idCondition : '(' . $where . ') AND ' . $idCondition;
			endif;
		endif;

		if($where !== ''):
			$q .= ' WHERE '.$where;
		endif;

		if(isset($_GET['order_by']) && $_GET['order_by'] != ''):
			$q .= ' order by '.$export_sql_column($_GET['order_by'], false).' '.$_GET['order'];
		endif;

		$db = $this->get_db();
		// error_log('EXPORT QUERY: '.$q);
		$data = $db->query($q);

		if(!$data):
			die('Errore export: '.$db->error);
		endif;

		$format = $_GET['format'];
		$app->$format($data,$filename=$app->intent->title,$columns_labels,$columns_data);
	}

public function excel(&$data, $filename, &$columns_labels, &$columns_data) {

    $job_id = uniqid();

    $basePath = $this->subdomain_root . "/tmp/";
    $filePath = $basePath . "export_$job_id.xlsx";
    $jsonPath = $basePath . "$job_id.json";

    file_put_contents($jsonPath, json_encode([
        'progress' => 0,
        'ready' => false,
        'file' => $filePath
    ]));

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->freezePane('A2');

    $excel_column = 'A';
    foreach ($columns_labels as $column_title) {
        $sheet->setCellValue($excel_column.'1', $column_title);
        $sheet->getColumnDimension($excel_column)->setAutoSize(true);
        $excel_column++;
    }

    $rowCount = 2;
    $total = mysqli_num_rows($data);
    $i = 0;

    while ($row = mysqli_fetch_row($data)) {

        $excel_column = 'A';

        foreach ($row as $index => $cell) {

            $cellAddress = $excel_column . $rowCount;

            $is_upload_column = (
                isset($columns_data[$index]['type']) &&
                in_array($columns_data[$index]['type'], ['file', 'bulk_upload'])
            );

            if ($is_upload_column) {

                $raw = trim((string)$cell);

                if ($raw === '' || $raw === '[]' || $raw === '{}' || strtolower($raw) === 'null') {
                    $sheet->setCellValue($cellAddress, '');
                    $excel_column++;
                    continue;
                }

                $decoded = json_decode($raw, true);

                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $items = $decoded;
                } elseif ($this->is_serialized($raw)) {
                    $items = unserialize($raw);
                } else {
                    $raw = str_replace(['<br />', '<br/>', '<br>'], "\n", $raw);
                    $raw = trim($raw, '[]"');
                    $items = preg_split('/\r\n|\r|\n|","|",\s*"/', $raw);
                }

                if (!is_array($items)) {
                    $items = [$items];
                }

                $links = [];

                foreach ($items as $item) {
                    $item = trim((string)$item);
                    $item = trim($item, '[]"');

                    if ($item === '' || $item === '[]' || $item === '{}' || strtolower($item) === 'null') {
                        continue;
                    }
					
					if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $item)) {

					$StoreCloud = new StoreCloud();
					$file_location = $StoreCloud->getImageDetailFromDb($item, 'file_location');

					if ($file_location == 'cloudImages') {
						$links[] = $StoreCloud->getImageUrl($item, 'public');
						continue;
					}

					$StoreR2Adapter = new StoreR2Adapter();
					$links[] = $StoreR2Adapter->getFileUrl($item);
					continue;
				}



                    if (preg_match('#^https?://#i', $item)) {
                        $links[] = $item;
                    } else {
                        $basename = basename($item);

                        if ($basename !== '' && $basename !== '.' && $basename !== '/') {
                            $links[] = 'https://demo-storage.archyart.it/uploads/' . $basename;
                        }
                    }
                }

                $sheet->setCellValue($cellAddress, implode("\n", array_unique($links)));
                $sheet->getStyle($cellAddress)->getAlignment()->setWrapText(true);

                $excel_column++;
                continue;
            }

            if (isset($columns_data[$index]['excel_formatter'])) {
                $cell = $columns_data[$index]['excel_formatter']($cell, '');
            } elseif (isset($columns_data[$index]['formatter']) && !isset($columns_data[$index]['formatter_hide_in_excel'])) {
                $cell = $columns_data[$index]['formatter']($cell, '');
            }

            if (
                isset($columns_data[$index]['type']) &&
                ($columns_data[$index]['type'] == 'amount' || $columns_data[$index]['type'] == 'text')
            ) {

                if (strpos((string)$cell, "€") === false) {
                    $sheet->setCellValue($cellAddress, $cell);
                } else {
                    $cell = substr($cell, 4);
                    $cell = str_replace(".", "", $cell);
                    $cell = str_replace(",", ".", $cell);

                    $sheet->setCellValue($cellAddress, (float)$cell);

                    $sheet->getStyle($cellAddress)
                        ->getNumberFormat()
                        ->setFormatCode('€ #,##0.00;[Red]-€ #,##0.00');
                }

            } else {

    if (is_string($cell)) {

        $cell = preg_replace('/<\/p>\s*<p>/i', "\n", $cell);
        $cell = preg_replace('/<br\s*\/?>/i', "\n", $cell);
        $cell = strip_tags($cell);
        $cell = html_entity_decode($cell, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    $sheet->setCellValue($cellAddress, $cell);
}

            $excel_column++;
        }

        $rowCount++;
        $i++;

        if ($i % 10 == 0 || $i == $total) {
            file_put_contents($jsonPath, json_encode([
                'progress' => $total > 0 ? intval(($i / $total) * 100) : 100,
                'ready' => false,
                'file' => $filePath
            ]));
        }
    }

    $writer = new Xlsx($spreadsheet);
    $writer->save($filePath);

    file_put_contents($jsonPath, json_encode([
        'progress' => 100,
        'ready' => true,
        'file' => $filePath
    ]));

    header('Content-Type: application/json');
    echo json_encode(['job_id' => $job_id]);
    exit;
}

        public function export_status() {

            $job_id = $_GET['job_id'];

            $jsonPath = $this->subdomain_root . "/tmp/$job_id.json";

            if (!file_exists($jsonPath)) {
                echo json_encode(['progress' => 0]);
                return;
            }

            echo file_get_contents($jsonPath);
        }

        public function export_download() {

            $job_id = isset($_GET['job_id']) ? $_GET['job_id'] : '';

            $jsonPath = $this->subdomain_root . "/tmp/$job_id.json";

            if ($job_id == '' || !file_exists($jsonPath)) {
                die("File non trovato");
            }

            $meta = json_decode(file_get_contents($jsonPath), true);

            if (!$meta || empty($meta['ready'])) {
                die("File non pronto");
            }

            $file = isset($meta['file']) ? $meta['file'] : '';

            if ($file == '' || !file_exists($file)) {
                die("File export non trovato");
            }

            $mime = isset($meta['mime']) && $meta['mime'] != ''
                ? $meta['mime']
                : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

            $downloadName = isset($meta['download_name']) && $meta['download_name'] != ''
                ? $meta['download_name']
                : basename($file);

            // Pulisco eventuali output precedenti prima del download.
            while (ob_get_level() > 0) {
                @ob_end_clean();
            }

            header('Content-Type: '.$mime);
            header('Content-Disposition: attachment; filename="'.basename($downloadName).'"');
            header('Content-Length: '.filesize($file));
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');

            readfile($file);

            @unlink($file);
            @unlink($jsonPath);

            exit;
        }
	
	/*public function excel(&$data,$filename,&$columns_labels,&$columns_data){
		
		header('Content-Type: application/vnd.ms-excel');
		header('Set-Cookie: fileDownload=true; path=/');
		header('Cache-Control: max-age=60, must-revalidate');
		header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
		require_once $this->main_root.'/private/_common/classes/PHPExcel.php';
		
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getActiveSheet()->freezePane('A2');
		$styleArray=array('font'=>array('bold'=>true,'color'=>array('rgb'=>'333333')));
		$excel_column='A';
		foreach($columns_labels as $index=>$column_title):
			$objPHPExcel->getActiveSheet()->SetCellValue($excel_column.'1', $column_title)->getStyle($excel_column.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($excel_column)->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getStyle($excel_column)->getAlignment()
				->setWrapText(true)
				->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$excel_column++;
		endforeach;
		$excel_column='A';
                for($i=0; $i<count($columns_labels)-1; $i++)$excel_column++;
		$objPHPExcel->getActiveSheet()->setAutoFilter('A1:'.$excel_column.'1');
		//error_log(print_r($data,true));
		$rowCount=2;
		while($row=mysqli_fetch_row($data)):
			$formatter_row=[];
			
			$excel_column='A';
			foreach($row as $index=>$cell):
				if(isset($columns_data[$index]['excel_formatter'])):
					$cell=$columns_data[$index]['excel_formatter']($cell,'');
				elseif(isset($columns_data[$index]['formatter']) and !isset($columns_data[$index]['formatter_hide_in_excel'])):
					$cell=$columns_data[$index]['formatter']($cell,'');
				endif;

                if($columns_data[$index]['type']=='amount' || $columns_data[$index]['type']=='text') {
                    if(!strstr($cell,"\xe2\x82\xac")) {
                        $objPHPExcel->getActiveSheet()->SetCellValue($excel_column.$rowCount,$cell);
                    } else {
                        $cell=substr($cell,4);
                        $cell=str_replace(".","",$cell); // le cifre sopra le migliaia non vengono formattate correttamente 
                        $cell=str_replace(",",".",$cell);
                        $objPHPExcel->getActiveSheet()->SetCellValue($excel_column.$rowCount,$cell)->getStyle($excel_column.$rowCount)->getNumberFormat()->setFormatCode("€ #,##0.00;[red]-€ #,##0.00");
                    }
                } else {
                    $objPHPExcel->getActiveSheet()->SetCellValue($excel_column.$rowCount,$cell);
                }

				$excel_column++;
			endforeach;
			$rowCount++;
		endwhile;
		
		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); 
		$objWriter->save('TEMP');
		echo file_get_contents('TEMP');
		unlink('TEMP');
	}*/
	
	public function export_petty_cash(){
		$petty_cash=new PettyCash();
		$petty_cash->export();
	}
    // Esportazione rendiconto finanziario
	public function export_refa(){
		$petty_cash=new PettyCash();
		$petty_cash->export_refa();
	}
	public function export_cash_budget(){
		$cash_budget=new CashBudget();
		$cash_budget->export();
	}
	
    // Esportazione PDF da vista griglia (grid)

	/*
	|--------------------------------------------------------------------------
	| EXPORT PDF DATATABLE - TCPDF
	|--------------------------------------------------------------------------
	|
	| Nuova gestione dell'export PDF della DataTable.
	|
	| Il PDF viene generato tramite TCPDF e salvato come file temporaneo,
	| così da essere scaricato con lo stesso flusso asincrono già usato
	| dall'export Excel.
	|
	| Motivo della modifica:
	| - il precedente metodo tentava il download diretto del PDF;
	| - il download diretto non era compatibile con il flusso AJAX;
	| - il risultato era un click apparentemente avviato ma senza file scaricato.
	|
	| Questa funzione NON modifica la generazione delle schede catalogo PDF
	| dei singoli record: riguarda solo l'export tabellare.
	|
	*/

	/*
	|--------------------------------------------------------------------------
	| FUNZIONE ORIGINALE PDF DATATABLE - NON ELIMINARE
	|--------------------------------------------------------------------------
	|
	| Questa è la funzione PDF precedente, mantenuta commentata come
	| riferimento tecnico.
	|
	| Motivo della sostituzione:
	| - il PDF veniva generato correttamente, ma la tabella risultava
	|   poco leggibile quando erano presenti molte colonne;
	| - intestazioni, link e testi lunghi venivano spezzati in verticale;
	| - mancava una formattazione tabellare di default adatta all'export.
	|
	| ATTENZIONE
	| -------------------------------------------------------------------------
	| Non riattivare questa funzione senza verificare:
	| - flusso AJAX dell'export;
	| - compatibilità con export_download();
	| - campi aggiuntivi additional_XX;
	| - link upload / bulk_upload.
	|
	*/

	//
	//
	// 	public function pdf(&$data,$filename,&$columns_labels,&$columns_data){
	//
	//         /*
	//         |--------------------------------------------------------------------------
	//         | EXPORT PDF DATATABLE - FIX ASINCRONO REALE
	//         |--------------------------------------------------------------------------
	//         |
	//         | Excel usa un flusso asincrono: crea il file in /tmp, restituisce job_id,
	//         | poi il browser scarica tramite export_download(). Il PDF deve fare la
	//         | stessa cosa.
	//         |
	//         | Nota importante:
	//         | NON usiamo ManagePdf::generatePdf('download') perché quel metodo invia
	//         | il PDF direttamente al browser con TCPDF Output(..., 'D') e quindi rompe
	//         | il flusso AJAX/JSON dei pulsanti export.
	//         |
	//         | Qui generiamo invece il PDF direttamente con TCPDF e lo salviamo su disco
	//         | con Output(..., 'F'), poi restituiamo il job_id come fa Excel.
	//         |
	//         */
	//
	//         $job_id = uniqid();
	//
	//         $basePath = $this->subdomain_root . "/tmp/";
	//         if(!is_dir($basePath)):
	//             @mkdir($basePath, 0775, true);
	//         endif;
	//
	//         $safe_filename = preg_replace('/[^A-Za-z0-9_\-]/', '_', (string)$filename);
	//         if($safe_filename == ''):
	//             $safe_filename = 'export';
	//         endif;
	//
	//         $filePath = $basePath . "export_".$job_id.".pdf";
	//         $jsonPath = $basePath . $job_id.".json";
	//         $logPath = $basePath . "export_pdf_".$job_id.".log";
	//
	//         $write_status = function($data) use ($jsonPath, $filePath, $safe_filename) {
	//             $base = [
	//                 'progress' => 0,
	//                 'ready' => false,
	//                 'file' => $filePath,
	//                 'mime' => 'application/pdf',
	//                 'download_name' => $safe_filename.'.pdf'
	//             ];
	//             file_put_contents($jsonPath, json_encode(array_merge($base, $data)));
	//         };
	//
	//         $write_status(['progress' => 0, 'ready' => false]);
	//
	//         try{
	//
	//             if(!class_exists('TCPDF')):
	//                 $tcpdfPath = $this->main_root.'/private/_common/classes/tcpdf/tcpdf.php';
	//                 if(file_exists($tcpdfPath)):
	//                     require_once $tcpdfPath;
	//                 endif;
	//             endif;
	//
	//             if(!class_exists('TCPDF')):
	//                 throw new Exception('Classe TCPDF non trovata. Verificare inclusione tcpdf.php.');
	//             endif;
	//
	//             $sheet = @$_GET['sheet']!=''? $_GET['sheet'] : 'A4';
	//             $orientation = @$_GET['orientation']!=''? $_GET['orientation'] : 'L';
	//             $size = @$_GET['size']!=''? (int)$_GET['size'] : 8;
	//             $top = @$_GET['up']!=''? (int)$_GET['up'] : 10;
	//             $right = @$_GET['right']!=''? (int)$_GET['right'] : 8;
	//             $left = @$_GET['left']!=''? (int)$_GET['left'] : 8;
	//
	//             $escape_pdf_cell = function($value){
	//                 $value = (string)$value;
	//                 $value = str_replace(['<br />','<br/>','<br>'], "\n", $value);
	//                 $value = strip_tags($value);
	//                 return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
	//             };
	//
	//             $pdf_styles='\n
	//                 <style type="text/css">\n
	//                     body{font-family:helvetica,dejavusans,sans-serif;font-size:'.$size.'pt;line-height:1.35;}\n
	//                     h2{font-size:14pt;margin:0 0 8px 0;}\n
	//                     table{width:100%;border-collapse:collapse;}\n
	//                     th{background-color:#f1f1f1;font-weight:bold;}\n
	//                     th,td{border:1px solid #999;padding:4px;vertical-align:top;}\n
	//                 </style>';
	//
	//             $pdf='<h2>'.htmlspecialchars($filename, ENT_QUOTES, 'UTF-8').'</h2>';
	//             $pdf.='<table width="100%" border="1" cellspacing="0" cellpadding="4">';
	//             $pdf.='<thead><tr>';
	//
	//             foreach($columns_labels as $column_title):
	//                 $pdf.='<th>'.htmlspecialchars($column_title, ENT_QUOTES, 'UTF-8').'</th>';
	//             endforeach;
	//
	//             $pdf.='</tr></thead><tbody>';
	//
	//             $total = mysqli_num_rows($data);
	//             $i = 0;
	//
	//             while($row=mysqli_fetch_row($data)):
	//                 $pdf.='<tr>';
	//
	//                 foreach($row as $index=>$cell):
	//
	//                     if(isset($columns_data[$index]['pdf_formatter'])):
	//                         $cell=$columns_data[$index]['pdf_formatter']($cell,'');
	//                     elseif(isset($columns_data[$index]['formatter']) and !isset($columns_data[$index]['formatter_hide_in_pdf'])):
	//                         $cell=$columns_data[$index]['formatter']($cell,'');
	//                     endif;
	//
	//                     $pdf.='<td>'.$escape_pdf_cell($cell).'</td>';
	//
	//                 endforeach;
	//
	//                 $pdf.='</tr>';
	//                 $i++;
	//
	//                 if($i % 10 == 0 || $i == $total):
	//                     $write_status([
	//                         'progress' => $total > 0 ? intval(($i / max($total,1)) * 80) : 80,
	//                         'ready' => false
	//                     ]);
	//                 endif;
	//
	//             endwhile;
	//
	//             $pdf.='</tbody></table>';
	//             $pdf_file = '<html><head>'.$pdf_styles.'</head><body>'.$pdf.'</body></html>';
	//
	//             $tcpdf = new TCPDF($orientation, 'mm', $sheet, true, 'UTF-8', false);
	//             $tcpdf->SetAuthor('Eteria Cloud');
	//             $tcpdf->setPrintHeader(false);
	//             $tcpdf->setPrintFooter(false);
	//             $tcpdf->SetMargins($left, $top, $right);
	//             $tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
	//             $tcpdf->setJPEGQuality(75);
	//             $tcpdf->SetAutoPageBreak(true, 10);
	//             $tcpdf->AddPage();
	//             $tcpdf->writeHTML($pdf_file, true, false, true, false, '');
	//
	//             // Salvataggio su disco per flusso asincrono.
	//             $tcpdf->Output($filePath, 'F');
	//
	//             if(!file_exists($filePath) || filesize($filePath) <= 0):
	//                 throw new Exception('PDF non creato o file vuoto: '.$filePath);
	//             endif;
	//
	//             $write_status([
	//                 'progress' => 100,
	//                 'ready' => true
	//             ]);
	//
	//             header('Content-Type: application/json');
	//             echo json_encode(['job_id' => $job_id]);
	//             exit;
	//
	//         }catch(Throwable $e){
	//
	//             @file_put_contents($logPath, date('Y-m-d H:i:s').' - '.$e->getMessage()."\n".$e->getTraceAsString());
	//
	//             $write_status([
	//                 'progress' => 100,
	//                 'ready' => false,
	//                 'error' => $e->getMessage(),
	//                 'log' => $logPath
	//             ]);
	//
	//             header('Content-Type: application/json');
	//             echo json_encode([
	//                 'job_id' => $job_id,
	//                 'error' => $e->getMessage()
	//             ]);
	//             exit;
	//         }
	// 	}

	/*
	|--------------------------------------------------------------------------
	| EXPORT PDF DATATABLE - VERSIONE IMPAGINATA
	|--------------------------------------------------------------------------
	|
	| Nuova funzione PDF per esportazione tabellare della DataTable.
	|
	| Obiettivi:
	| - mantenere il flusso asincrono già funzionante;
	| - usare A4 orizzontale come default;
	| - rendere la tabella più leggibile;
	| - alternare lo sfondo delle righe;
	| - migliorare la resa di testi lunghi e link;
	| - mantenere compatibilità con i campi aggiuntivi e con l'export Excel.
	|
	| NOTA
	| -------------------------------------------------------------------------
	| Questa funzione riguarda solo l'export PDF della griglia.
	| Non modifica la generazione dei PDF delle singole schede catalogo.
	|
	| Step futuri previsti:
	| - sostituire orientamento/formato con base e altezza in cm;
	| - aggiungere intestazione personalizzabile;
	| - aggiungere footer personalizzabile;
	| - salvare impostazioni PDF per singolo intent/modulo.
	|
	*/

    /*
    |--------------------------------------------------------------------------
    | PDF DATATABLE - SINGOLO BATCH PER WORKER
    |--------------------------------------------------------------------------
    | Genera esclusivamente il file temporaneo indicato dal worker. Non crea
    | ulteriori job, non invia download e non mantiene aperta una richiesta
    | lunga per l'intero archivio.
    */
    public function pdf_batch(&$data, $filename, &$columns_labels, &$columns_data){

        $outputFile = isset($_GET['_batch_output']) ? (string)$_GET['_batch_output'] : '';

        if ($outputFile === '') {
            throw new Exception('Percorso output batch PDF mancante.');
        }

        $outputDir = dirname($outputFile);
        if (!is_dir($outputDir) && !@mkdir($outputDir, 0775, true)) {
            throw new Exception('Impossibile creare la cartella batch PDF: '.$outputDir);
        }

        if(!class_exists('TCPDF')){
            $tcpdfPath = $this->main_root.'/private/_common/classes/tcpdf/tcpdf.php';
            if(file_exists($tcpdfPath)){
                require_once $tcpdfPath;
            }
        }

        if(!class_exists('TCPDF')){
            throw new Exception('Classe TCPDF non trovata.');
        }

        $sheet = !empty($_GET['sheet']) ? preg_replace('/[^A-Za-z0-9]/', '', $_GET['sheet']) : 'A4';
        $orientation = !empty($_GET['orientation']) && strtoupper(substr($_GET['orientation'],0,1)) === 'P' ? 'P' : 'L';
        $size = !empty($_GET['size']) ? max(5, min(14, (int)$_GET['size'])) : 7;
        $top = isset($_GET['up']) ? max(4, min(50, (int)$_GET['up'])) : 8;
        $right = isset($_GET['right']) ? max(4, min(50, (int)$_GET['right'])) : 6;
        $bottom = isset($_GET['down']) ? max(4, min(50, (int)$_GET['down'])) : 8;
        $left = isset($_GET['left']) ? max(4, min(50, (int)$_GET['left'])) : 6;

        /*
        |--------------------------------------------------------------------------
        | NORMALIZZAZIONE VALORI CELLA
        |--------------------------------------------------------------------------
        | Alcuni formatter restituiscono stdClass o array. Nel vecchio batch
        | il cast diretto a stringa generava:
        | "Object of class stdClass could not be converted to string".
        */
        $normaliseCellValue = function($value) use (&$normaliseCellValue){

            if ($value === null) {
                return '';
            }

            if (is_bool($value)) {
                return $value ? '1' : '0';
            }

            if (is_scalar($value)) {
                return (string)$value;
            }

            if (is_object($value)) {

                if (method_exists($value, '__toString')) {
                    return (string)$value;
                }

                $value = get_object_vars($value);
            }

            if (is_array($value)) {

                $parts = [];

                array_walk_recursive($value, function($item) use (&$parts, &$normaliseCellValue){
                    $normalised = trim($normaliseCellValue($item));

                    if ($normalised !== '') {
                        $parts[] = $normalised;
                    }
                });

                return implode(', ', array_values(array_unique($parts)));
            }

            return '';
        };

        $cleanCell = function($value) use ($normaliseCellValue){

            $value = $normaliseCellValue($value);
            $value = preg_replace('/<br\s*\/?>/i', "\n", $value);
            $value = preg_replace('/<\/p>\s*<p[^>]*>/i', "\n", $value);
            $value = strip_tags($value);
            $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');

            return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
        };

        /*
        |--------------------------------------------------------------------------
        | MINIATURA FOTO UFFICIALE NEL PDF BATCH
        |--------------------------------------------------------------------------
        | Ripristina il comportamento del precedente PDF DataTable: la colonna
        | Foto ufficiale viene renderizzata come immagine 50x50.
        */
        $isPhotoColumn = function($index) use ($columns_labels, $columns_data, $normaliseCellValue){

            $label = isset($columns_labels[$index])
                ? mb_strtolower(trim($normaliseCellValue($columns_labels[$index])), 'UTF-8')
                : '';

            if (in_array($label, ['foto ufficiale','foto','immagine','immagine principale','foto principale'], true)) {
                return true;
            }

            $column = isset($columns_data[$index]) && is_array($columns_data[$index])
                ? $columns_data[$index]
                : [];

            foreach (['name','label','alias'] as $key) {
                if (!empty($column[$key])) {
                    $name = mb_strtolower(trim($normaliseCellValue($column[$key])), 'UTF-8');

                    if (
                        strpos($name, 'foto_ufficiale') !== false ||
                        strpos($name, 'foto ufficiale') !== false ||
                        in_array($name, ['foto','immagine','immagine_principale','image','photo'], true)
                    ) {
                        return true;
                    }
                }
            }

            return false;
        };

        $renderPhotoThumbnail = function($value) use ($normaliseCellValue){

            static $cache = [];
            static $storeCloudInitialised = false;
            static $storeCloud = null;

            $raw = trim($normaliseCellValue($value));

            if ($raw === '') {
                return '';
            }

            $cacheKey = md5($raw);

            if (isset($cache[$cacheKey])) {
                return $cache[$cacheKey];
            }

            $imageHtml = function($src) {
                $src = trim((string)$src);

                if ($src === '') {
                    return '';
                }

                return '<img src="' .
                    htmlspecialchars($src, ENT_QUOTES, 'UTF-8') .
                    '" width="50" height="50" />';
            };

            // Formatter già trasformato in tag IMG.
            if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $raw, $match)) {
                return $cache[$cacheKey] = $imageHtml(
                    html_entity_decode($match[1], ENT_QUOTES, 'UTF-8')
                );
            }

            // URL assoluto.
            if (preg_match('/^https?:\/\//i', $raw)) {
                return $cache[$cacheKey] = $imageHtml($raw);
            }

            // Il campo può contenere più valori: usa il primo.
            $parts = preg_split('/[\r\n,]+/', str_replace(['<br />','<br/>','<br>'], "\n", strip_tags($raw)));
            $fileId = trim((string)reset($parts));

            if ($fileId === '') {
                return $cache[$cacheKey] = '';
            }

            // UUID/ID gestito da StoreCloud.
            if (!$storeCloudInitialised) {
                $storeCloudInitialised = true;

                if (class_exists('StoreCloud')) {
                    try {
                        $storeCloud = new StoreCloud();
                    } catch (Throwable $e) {
                        $storeCloud = null;
                    }
                }
            }

            if ($storeCloud) {
                try {
                    $location = $storeCloud->getImageDetailFromDb($fileId, 'file_location');

                    if ($location === 'cloudImages') {
                        return $cache[$cacheKey] = $imageHtml(
                            $storeCloud->getImageUrl($fileId, 'thumbnail')
                        );
                    }

                    if ($location === 'local') {
                        return $cache[$cacheKey] = $imageHtml($fileId);
                    }
                } catch (Throwable $e) {
                    // Continua con i fallback locali.
                }
            }

            // Path locale/relativo.
            $documentRoot = isset($_SERVER['DOCUMENT_ROOT'])
                ? rtrim($_SERVER['DOCUMENT_ROOT'], '/')
                : '';

            $candidates = [
                $fileId,
                $documentRoot . '/' . ltrim($fileId, '/'),
                $documentRoot . '/assets/documents/upload_extrafield/' . basename($fileId),
                $documentRoot . '/assets/documents/scheda_catalogo/' . basename($fileId),
                $documentRoot . '/assets/img/' . basename($fileId),
                $documentRoot . '/assets/images/' . basename($fileId)
            ];

            foreach ($candidates as $candidate) {
                if ($candidate !== '' && is_file($candidate)) {
                    return $cache[$cacheKey] = $imageHtml($candidate);
                }
            }

            // Ultimo fallback: non stampa UUID/nome tecnico.
            return $cache[$cacheKey] = '';
        };


        /*
        |--------------------------------------------------------------------------
        | COLONNE ALLEGATI NEL PDF BATCH
        |--------------------------------------------------------------------------
        | Le colonne di documentazione non devono mostrare URL, UUID o il generico
        | "Vedi allegati". Mostriamo:
        | - Nessun allegato
        | - Allegato 1
        | - Allegato 2
        | - Allegato 3
        */
        $isAttachmentColumn = function($index) use ($columns_labels, $columns_data, $normaliseCellValue, $isPhotoColumn){

            if ($isPhotoColumn($index)) {
                return false;
            }

            $label = isset($columns_labels[$index])
                ? mb_strtolower(trim($normaliseCellValue($columns_labels[$index])), 'UTF-8')
                : '';

            if (
                strpos($label, 'documentazione') !== false ||
                strpos($label, 'allegat') !== false ||
                strpos($label, 'documenti') !== false
            ) {
                return true;
            }

            $column = isset($columns_data[$index]) && is_array($columns_data[$index])
                ? $columns_data[$index]
                : [];

            foreach (['name','label','alias'] as $key) {
                if (empty($column[$key])) {
                    continue;
                }

                $name = mb_strtolower(trim($normaliseCellValue($column[$key])), 'UTF-8');

                if (
                    strpos($name, 'documentazione') !== false ||
                    strpos($name, 'allegat') !== false ||
                    strpos($name, 'attachment') !== false ||
                    strpos($name, 'documenti') !== false
                ) {
                    return true;
                }
            }

            $type = isset($column['type'])
                ? mb_strtolower(trim($normaliseCellValue($column['type'])), 'UTF-8')
                : '';

            return in_array($type, ['bulk_upload','multiple_file','attachments'], true);
        };

        $renderAttachmentSummary = function($rawValue, $formattedValue) use ($normaliseCellValue, $cleanCell){

            $formatted = trim($normaliseCellValue($formattedValue));
            $raw = trim($normaliseCellValue($rawValue));

            $plainFormatted = trim(
                html_entity_decode(
                    strip_tags(
                        str_ireplace(
                            ['<br />','<br/>','<br>'],
                            "\n",
                            $formatted
                        )
                    ),
                    ENT_QUOTES | ENT_HTML5,
                    'UTF-8'
                )
            );

            if (
                $raw === '' &&
                (
                    $plainFormatted === '' ||
                    stripos($plainFormatted, 'nessun allegato') !== false
                )
            ) {
                return 'Nessun allegato';
            }

            /*
            | Recupera gli identificativi dagli attributi generati dal formatter
            | storico: data-images e data-files.
            */
            $items = [];

            if ($formatted !== '') {
                foreach (['data-images', 'data-files'] as $attribute) {
                    if (preg_match('/'.$attribute.'=["\']([^"\']*)["\']/i', $formatted, $match)) {
                        $attributeItems = preg_split('/[\s,;|]+/', trim($match[1]));

                        foreach ($attributeItems as $item) {
                            $item = trim($item);

                            if ($item !== '') {
                                $items[] = $item;
                            }
                        }
                    }
                }

                /*
                | Compatibilità con eventuali link HTML già prodotti.
                */
                if (preg_match_all('/<a[^>]+href=["\']([^"\']+)["\']/i', $formatted, $links)) {
                    foreach ($links[1] as $link) {
                        $link = trim(html_entity_decode($link, ENT_QUOTES, 'UTF-8'));

                        if ($link !== '') {
                            $items[] = $link;
                        }
                    }
                }
            }

            /*
            | Se il formatter non espone gli ID, usa il valore grezzo. Gestisce
            | serializzazioni semplici, JSON e liste separate da virgola/a capo.
            */
            if (empty($items) && $raw !== '') {

                $decodedJson = json_decode($raw, true);

                if (is_array($decodedJson)) {
                    array_walk_recursive($decodedJson, function($item) use (&$items){
                        if (is_scalar($item) && trim((string)$item) !== '') {
                            $items[] = trim((string)$item);
                        }
                    });
                } else {
                    $unserialised = @unserialize($raw);

                    if (is_array($unserialised)) {
                        array_walk_recursive($unserialised, function($item) use (&$items){
                            if (is_scalar($item) && trim((string)$item) !== '') {
                                $items[] = trim((string)$item);
                            }
                        });
                    } else {
                        $rawParts = preg_split(
                            '/[\r\n,;|]+/',
                            str_ireplace(
                                ['<br />','<br/>','<br>'],
                                "\n",
                                strip_tags($raw)
                            )
                        );

                        foreach ($rawParts as $item) {
                            $item = trim($item);

                            if ($item !== '') {
                                $items[] = $item;
                            }
                        }
                    }
                }
            }

            $items = array_values(array_unique(array_filter($items, function($item){
                $item = trim((string)$item);

                return (
                    $item !== '' &&
                    strcasecmp($item, 'Vedi allegati') !== 0 &&
                    stripos($item, 'Nessun allegato') === false
                );
            })));

            /*
            | Se il formatter dice che ci sono allegati ma non espone gli ID,
            | almeno mostriamo un elemento invece del generico "Vedi allegati".
            */
            if (empty($items) && stripos($plainFormatted, 'vedi allegati') !== false) {
                $items = ['allegato'];
            }

            if (empty($items)) {
                return 'Nessun allegato';
            }

            /*
            |--------------------------------------------------------------------------
            | RISOLUZIONE LINK ALLEGATI
            |--------------------------------------------------------------------------
            | Gli elementi possono essere URL/path oppure UUID StoreCloud/R2.
            | Il testo resta compatto (Allegato 1, Allegato 2...), ma diventa
            | cliccabile nel PDF.
            */
            static $storeCloudChecked = false;
            static $storeCloud = null;
            static $storeR2 = null;
            static $storeRepo = null;

            if (!$storeCloudChecked) {
                $storeCloudChecked = true;

                if (class_exists('StoreCloud')) {
                    try {
                        $storeCloud = new StoreCloud();
                    } catch (Throwable $e) {
                        $storeCloud = null;
                    }
                }

                if (class_exists('StoreR2Adapter')) {
                    try {
                        $storeR2 = new StoreR2Adapter();
                    } catch (Throwable $e) {
                        $storeR2 = null;
                    }
                }

                if (class_exists('StoreFileRepository')) {
                    try {
                        $storeRepo = new StoreFileRepository();
                    } catch (Throwable $e) {
                        $storeRepo = null;
                    }
                }
            }

            $isAbsoluteUrl = function($value){
                return preg_match('/^https?:\/\//i', trim((string)$value));
            };

            $isUuid = function($value){
                return preg_match(
                    '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i',
                    trim((string)$value)
                );
            };

            $resolveHref = function($item) use (
                $storeCloud,
                $storeR2,
                $storeRepo,
                $isAbsoluteUrl,
                $isUuid
            ){
                $item = trim((string)$item);

                if ($item === '') {
                    return '';
                }

                if ($isAbsoluteUrl($item)) {
                    return $item;
                }

                if (strpos($item, '/') === 0) {
                    $host = isset($_SERVER['HTTP_HOST']) ? trim((string)$_SERVER['HTTP_HOST']) : '';

                    if ($host !== '') {
                        return 'https://' . $host . $item;
                    }

                    return $item;
                }

                if ($isUuid($item)) {
                    try {
                        $fileLocation = '';

                        if ($storeRepo) {
                            $fileInfo = $storeRepo->findAll($item);

                            if (is_array($fileInfo) && !empty($fileInfo['file_location'])) {
                                $fileLocation = (string)$fileInfo['file_location'];
                            }
                        }

                        if ($fileLocation === '' && $storeCloud) {
                            $fileLocation = (string)$storeCloud->getImageDetailFromDb(
                                $item,
                                'file_location'
                            );
                        }

                        if ($fileLocation === 'cloudImages' && $storeCloud) {
                            return (string)$storeCloud->getImageUrl($item, 'public');
                        }

                        if ($storeR2) {
                            return (string)$storeR2->getFileUrl($item);
                        }
                    } catch (Throwable $e) {
                        return '';
                    }

                    return '';
                }

                /*
                | Compatibilità con i file storage storici salvati solo come nome.
                */
                return 'https://demo-storage.archyart.it/uploads/' .
                    rawurlencode(basename($item));
            };

            $links = [];

            foreach ($items as $position => $item) {

                $label = 'Allegato ' . ($position + 1);
                $href = trim($resolveHref($item));

                if ($href !== '') {
                    $links[] =
                        '<a href="' .
                        htmlspecialchars($href, ENT_QUOTES, 'UTF-8') .
                        '" target="_blank">' .
                        $cleanCell($label) .
                        '</a>';
                } else {
                    /*
                    | Se un vecchio UUID non è più risolvibile, non blocchiamo il PDF.
                    */
                    $links[] = $cleanCell($label);
                }
            }

            return implode('<br>', $links);
        };

        /*
        |--------------------------------------------------------------------------
        | RENDERING PDF CON MULTICELL
        |--------------------------------------------------------------------------
        | La tabella HTML di TCPDF può spezzare le righe tra due pagine. Qui ogni
        | record viene misurato prima della stampa e, se non entra nello spazio
        | residuo, viene spostato integralmente alla pagina seguente.
        */
        $pdf = new TCPDF($orientation, 'mm', $sheet, true, 'UTF-8', false);
        $pdf->SetCreator('Archy Art');
        $pdf->SetAuthor('Archy Art');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins($left, $top, $right);
        $pdf->SetAutoPageBreak(false, $bottom);
        $pdf->setImageScale(1.0);
        $pdf->setJPEGQuality(70);
        $pdf->SetFont('dejavusans', '', $size);
        $pdf->AddPage();

        $pdfTitle = $normaliseCellValue($filename);
        if ($pdfTitle === '') {
            $pdfTitle = 'Esportazione DataTable';
        }

        $pageWidth  = $pdf->getPageWidth();
        $pageHeight = $pdf->getPageHeight();
        $usableWidth = $pageWidth - $left - $right;
        $pageBottom = $pageHeight - $bottom;
        $columnCount = max(1, count($columns_labels));

        /* Colonna foto leggermente più larga; le altre si dividono lo spazio. */
        $photoIndexes = [];
        foreach ($columns_labels as $i => $unusedLabel) {
            if ($isPhotoColumn($i)) {
                $photoIndexes[] = $i;
            }
        }
        $photoWidth = ($columnCount > 8) ? 13.0 : 18.0;
        $remainingWidth = $usableWidth - (count($photoIndexes) * $photoWidth);
        $normalColumns = max(1, $columnCount - count($photoIndexes));
        $normalWidth = $remainingWidth / $normalColumns;
        $columnWidths = [];
        foreach ($columns_labels as $i => $unusedLabel) {
            $columnWidths[$i] = in_array($i, $photoIndexes, true) ? $photoWidth : $normalWidth;
        }

        $borderColor = [143, 164, 149];
        $headerFill  = [223, 242, 225]; // verde salvia chiaro
        $headerText  = [53, 91, 56];
        $rowOddFill  = [248, 251, 248];
        $rowEvenFill = [255, 255, 255];
        $cellPadding = 1.0;
        $lineHeight  = max(3.2, $size * 0.48);

        $drawTitle = function() use ($pdf, $pdfTitle, $left, $size) {
            $pdf->SetXY($left, $pdf->GetY());
            $pdf->SetFont('dejavusans', 'B', max(11, $size + 4));
            $pdf->SetTextColor(32, 45, 38);
            $pdf->Cell(0, 7, $pdfTitle, 0, 1, 'L', false);
            $pdf->Ln(1.5);
            $pdf->SetFont('dejavusans', '', $size);
        };

        $drawHeader = function() use (
            $pdf, $columns_labels, $columnWidths, $left, $pageBottom,
            $normaliseCellValue, $borderColor, $headerFill, $headerText,
            $cellPadding, $lineHeight, $size
        ) {
            $pdf->SetFont('dejavusans', 'B', $size);
            $headerHeight = $lineHeight + (2 * $cellPadding);
            foreach ($columns_labels as $i => $label) {
                $txt = trim(strip_tags($normaliseCellValue($label)));
                $h = $pdf->getStringHeight(max(1, $columnWidths[$i] - 2 * $cellPadding), $txt, false, true, '', 1);
                $headerHeight = max($headerHeight, $h + 2 * $cellPadding);
            }
            if ($pdf->GetY() + $headerHeight > $pageBottom) {
                $pdf->AddPage();
            }
            $x = $left;
            $y = $pdf->GetY();
            $pdf->SetDrawColor($borderColor[0], $borderColor[1], $borderColor[2]);
            $pdf->SetFillColor($headerFill[0], $headerFill[1], $headerFill[2]);
            $pdf->SetTextColor($headerText[0], $headerText[1], $headerText[2]);
            foreach ($columns_labels as $i => $label) {
                $w = $columnWidths[$i];
                $pdf->Rect($x, $y, $w, $headerHeight, 'DF');
                $pdf->SetXY($x + $cellPadding, $y + $cellPadding);
                $pdf->MultiCell($w - 2 * $cellPadding, $lineHeight, trim(strip_tags($normaliseCellValue($label))), 0, 'L', false, 0, '', '', true, 0, false, true, $headerHeight - 2 * $cellPadding, 'M');
                $x += $w;
            }
            $pdf->SetXY($left, $y + $headerHeight);
            $pdf->SetFont('dejavusans', '', $size);
            $pdf->SetTextColor(0, 0, 0);
            return $headerHeight;
        };

        $drawTitle();
        $drawHeader();

        $rowNumber = 0;
        while ($row = mysqli_fetch_row($data)) {
            $prepared = [];
            $rowHeight = $lineHeight + 2 * $cellPadding;

            foreach ($row as $index => $cell) {
                $rawCell = $cell;
                try {
                    if (isset($columns_data[$index]['pdf_formatter'])) {
                        $formatterInput = (is_object($cell) || is_array($cell)) ? $normaliseCellValue($cell) : $cell;
                        $cell = $columns_data[$index]['pdf_formatter']($formatterInput, '');
                    } elseif (isset($columns_data[$index]['formatter']) && !isset($columns_data[$index]['formatter_hide_in_pdf'])) {
                        if (isset($columns_data[$index]['type']) && $columns_data[$index]['type'] === 'file') {
                            $cell = basename(trim($normaliseCellValue($cell)));
                        } else {
                            $formatterInput = (is_object($cell) || is_array($cell)) ? $normaliseCellValue($cell) : $cell;
                            $cell = $columns_data[$index]['formatter']($formatterInput, '');
                        }
                    }
                } catch (Throwable $formatterError) {
                    $cell = $normaliseCellValue($rawCell);
                }

                $w = isset($columnWidths[$index]) ? $columnWidths[$index] : $normalWidth;

                if ($isPhotoColumn($index)) {
                    $photoHtml = $renderPhotoThumbnail($rawCell);
                    $src = '';
                    if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $photoHtml, $m)) {
                        $src = html_entity_decode($m[1], ENT_QUOTES, 'UTF-8');
                    }
                    $prepared[$index] = ['type' => 'photo', 'src' => $src];
                    $rowHeight = max($rowHeight, 15.0);
                } elseif ($isAttachmentColumn($index)) {
                    $attachmentHtml = $renderAttachmentSummary($rawCell, $cell);
                    $links = [];
                    if (preg_match_all('/<a[^>]+href=["\']([^"\']+)["\'][^>]*>(.*?)<\/a>/is', $attachmentHtml, $matches, PREG_SET_ORDER)) {
                        foreach ($matches as $m) {
                            $links[] = [
                                'href' => html_entity_decode($m[1], ENT_QUOTES, 'UTF-8'),
                                'label' => trim(strip_tags(html_entity_decode($m[2], ENT_QUOTES, 'UTF-8')))
                            ];
                        }
                    }
                    if (!$links) {
                        $plain = trim(strip_tags(str_ireplace(['<br>','<br/>','<br />'], "\n", $attachmentHtml)));
                        $prepared[$index] = ['type' => 'text', 'text' => $plain !== '' ? $plain : 'Nessun allegato'];
                        $h = $pdf->getStringHeight(max(1, $w - 2 * $cellPadding), $prepared[$index]['text'], false, true, '', 1);
                        $rowHeight = max($rowHeight, $h + 2 * $cellPadding);
                    } else {
                        /*
                        | MultiCell non dispone di un parametro URL. Calcoliamo
                        | quindi l'altezza reale di ogni etichetta e, in fase di
                        | stampa, aggiungiamo una vera annotazione PDF con Link().
                        */
                        $linksHeight = 0.0;
                        foreach ($links as &$linkItem) {
                            $linkItem['height'] = max(
                                $lineHeight,
                                $pdf->getStringHeight(
                                    max(1, $w - 2 * $cellPadding),
                                    $linkItem['label'],
                                    false,
                                    true,
                                    '',
                                    1
                                )
                            );
                            $linksHeight += $linkItem['height'];
                        }
                        unset($linkItem);

                        $prepared[$index] = ['type' => 'links', 'links' => $links];
                        $rowHeight = max($rowHeight, $linksHeight + 2 * $cellPadding);
                    }
                } else {
                    $text = trim(strip_tags(str_ireplace(['<br>','<br/>','<br />'], "\n", $normaliseCellValue($cell))));
                    $prepared[$index] = ['type' => 'text', 'text' => html_entity_decode($text, ENT_QUOTES, 'UTF-8')];
                    $h = $pdf->getStringHeight(max(1, $w - 2 * $cellPadding), $prepared[$index]['text'], false, true, '', 1);
                    $rowHeight = max($rowHeight, $h + 2 * $cellPadding);
                }
            }

            /* La riga viene spostata integralmente alla pagina successiva. */
            if ($pdf->GetY() + $rowHeight > $pageBottom) {
                $pdf->AddPage();
                $drawHeader();
            }

            $x = $left;
            $y = $pdf->GetY();
            $fill = ($rowNumber % 2 === 0) ? $rowEvenFill : $rowOddFill;
            $pdf->SetDrawColor($borderColor[0], $borderColor[1], $borderColor[2]);
            $pdf->SetFillColor($fill[0], $fill[1], $fill[2]);
            $pdf->SetTextColor(0, 0, 0);

            foreach ($columns_labels as $index => $unusedLabel) {
                $w = $columnWidths[$index];
                $content = isset($prepared[$index]) ? $prepared[$index] : ['type' => 'text', 'text' => ''];
                $pdf->Rect($x, $y, $w, $rowHeight, 'DF');

                if ($content['type'] === 'photo' && $content['src'] !== '') {
                    try {
                        $maxW = max(1, $w - 2 * $cellPadding);
                        $maxH = max(1, $rowHeight - 2 * $cellPadding);
                        $pdf->Image($content['src'], $x + $cellPadding, $y + $cellPadding, $maxW, $maxH, '', '', '', true, 150, '', false, false, 0, true, false, false);
                    } catch (Throwable $imageError) {
                        // La riga resta valida anche se una miniatura remota non è disponibile.
                    }
                } elseif ($content['type'] === 'links') {
                    $cy = $y + $cellPadding;
                    $linkWidth = max(1, $w - 2 * $cellPadding);

                    $pdf->SetTextColor(0, 0, 190);
                    $pdf->SetFont('dejavusans', 'U', $size);

                    foreach ($content['links'] as $linkData) {
                        $linkHeight = isset($linkData['height'])
                            ? (float)$linkData['height']
                            : $lineHeight;

                        $pdf->SetXY($x + $cellPadding, $cy);
                        $pdf->MultiCell(
                            $linkWidth,
                            $lineHeight,
                            $linkData['label'],
                            0,
                            'L',
                            false,
                            0,
                            '',
                            '',
                            true,
                            0,
                            false,
                            true,
                            $linkHeight,
                            'T'
                        );

                        /*
                        | Link() crea l'annotazione cliccabile nel PDF. Il testo
                        | resta gestito da MultiCell, quindi può andare a capo.
                        */
                        if (!empty($linkData['href'])) {
                            $pdf->Link(
                                $x + $cellPadding,
                                $cy,
                                $linkWidth,
                                $linkHeight,
                                $linkData['href']
                            );
                        }

                        $cy += $linkHeight;
                    }

                    $pdf->SetFont('dejavusans', '', $size);
                    $pdf->SetTextColor(0, 0, 0);
                } else {
                    $pdf->SetXY($x + $cellPadding, $y + $cellPadding);
                    $pdf->MultiCell($w - 2 * $cellPadding, $lineHeight, $content['text'], 0, 'L', false, 0, '', '', true, 0, false, true, $rowHeight - 2 * $cellPadding, 'T');
                }
                $x += $w;
            }

            $pdf->SetXY($left, $y + $rowHeight);
            $rowNumber++;
        }

        $pdf->Output($outputFile, 'F');
        unset($pdf);

        if(!is_file($outputFile) || filesize($outputFile) <= 0){
            throw new Exception('Batch PDF non creato o vuoto: '.basename($outputFile));
        }

        return true;
    }

	public function pdf(&$data,$filename,&$columns_labels,&$columns_data){

        $job_id = uniqid();

        $basePath = $this->subdomain_root . "/tmp/";
        if(!is_dir($basePath)):
            @mkdir($basePath, 0775, true);
        endif;

        $safe_filename = preg_replace('/[^A-Za-z0-9_\-]/', '_', (string)$filename);
        if($safe_filename == ''):
            $safe_filename = 'export';
        endif;

        $filePath = $basePath . "export_".$job_id.".pdf";
        $jsonPath = $basePath . $job_id.".json";
        $logPath = $basePath . "export_pdf_".$job_id.".log";

        $write_status = function($data) use ($jsonPath, $filePath, $safe_filename) {
            $base = [
                'progress' => 0,
                'ready' => false,
                'file' => $filePath,
                'mime' => 'application/pdf',
                'download_name' => $safe_filename.'.pdf'
            ];
            file_put_contents($jsonPath, json_encode(array_merge($base, $data)));
        };

        $write_status(['progress' => 0, 'ready' => false]);

        try{

            if(!class_exists('TCPDF')):
                $tcpdfPath = $this->main_root.'/private/_common/classes/tcpdf/tcpdf.php';
                if(file_exists($tcpdfPath)):
                    require_once $tcpdfPath;
                endif;
            endif;

            if(!class_exists('TCPDF')):
                throw new Exception('Classe TCPDF non trovata. Verificare inclusione tcpdf.php.');
            endif;

            /*
            |--------------------------------------------------------------------------
            | IMPOSTAZIONI PDF DI DEFAULT
            |--------------------------------------------------------------------------
            |
            | Per ora manteniamo compatibilità con la modale esistente:
            | - sheet
            | - orientation
            | - size
            | - margini
            |
            | Default consigliato:
            | - A4 orizzontale
            | - font 7 pt
            | - margini contenuti
            |
            */

            /*
            |--------------------------------------------------------------------------
            | LETTURA E NORMALIZZAZIONE IMPOSTAZIONI MODALE PDF
            |--------------------------------------------------------------------------
            |
            | La modale può inviare valori descrittivi come:
            | - Orizzontale / orizzontale / landscape
            | - Verticale / verticale / portrait
            |
            | TCPDF invece accetta:
            | - L = landscape
            | - P = portrait
            |
            | Normalizziamo quindi i valori per evitare che TCPDF ricada
            | automaticamente sul formato verticale.
            |
            */

            $sheet = @$_GET['sheet']!=''? trim($_GET['sheet']) : 'A4';
            $sheet = strtoupper($sheet);

            $orientation_raw = @$_GET['orientation']!=''? trim($_GET['orientation']) : 'L';
            $orientation_normalized = strtolower($orientation_raw);

            if(in_array($orientation_normalized, ['orizzontale','horizontal','landscape','l'])):
                $orientation = 'L';
            elseif(in_array($orientation_normalized, ['verticale','vertical','portrait','p'])):
                $orientation = 'P';
            else:
                // Default richiesto: A4 orizzontale
                $orientation = 'L';
            endif;

            $size = @$_GET['size']!=''? (int)$_GET['size'] : 7;
            if($size <= 0):
                $size = 7;
            endif;

            $top = @$_GET['up']!=''? (float)$_GET['up'] : 8;
            $bottom = @$_GET['down']!=''? (float)$_GET['down'] : 8;
            $right = @$_GET['right']!=''? (float)$_GET['right'] : 5;
            $left = @$_GET['left']!=''? (float)$_GET['left'] : 5;

            /*
            |--------------------------------------------------------------------------
            | HELPERS DI FORMATTAZIONE
            |--------------------------------------------------------------------------
            */

            $escape_pdf_cell = function($value){
				
				if(is_array($value)):
                    $flat = [];
                    array_walk_recursive($value, function($item) use (&$flat){
                        if(is_scalar($item) && trim((string)$item) != ''):
                            $flat[] = trim((string)$item);
                        endif;
                    });
                    $value = implode(', ', $flat);
                endif;

                $value = (string)$value;

                // Mantiene separazioni leggibili già presenti.
                $value = str_replace(['<br />','<br/>','<br>'], "\n", $value);
                $value = strip_tags($value);

                // Evita spazi e ritorni multipli.
                $value = preg_replace("/[ \t]+/", " ", $value);
                $value = preg_replace("/\n{3,}/", "\n\n", $value);

                return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
            };

            $escape_pdf_title = function($value){
                return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
            };

            /*
            |--------------------------------------------------------------------------
            | RENDER LINK E MINIATURE PDF
            |--------------------------------------------------------------------------
            |
            | Nel vecchio sistema la Foto ufficiale veniva stampata come miniatura
            | 50x50 px, mentre gli altri campi upload restavano link.
            |
            | Manteniamo quindi questa regola:
            | - colonna Foto ufficiale / foto_ufficiale / immagine principale:
            |   render immagine 50x50;
            | - campi file e bulk_upload:
            |   render link testuale compatto usando il nome file.
            |
            */

            $is_absolute_url = function($value){
                return preg_match('/^https?:\/\//i', trim((string)$value));
            };

            $is_image_file = function($value){
                return preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', trim((string)$value));
            };

            $filename_from_url = function($url){
                $path = parse_url((string)$url, PHP_URL_PATH);
                $name = basename($path ?: (string)$url);
                return $name ?: (string)$url;
            };

            $render_pdf_upload_links = function($value) use ($escape_pdf_cell, $filename_from_url, $is_absolute_url){
                /*
                |--------------------------------------------------------------------------
                | RENDER ALLEGATI NEL PDF DATATABLE
                |--------------------------------------------------------------------------
                |
                | Gestisce sia:
                | - campi aggiuntivi file/bulk_upload con path reali;
                | - campi upload nativi del gestionale con ID StoreCloud/R2;
                | - array PHP serializzati;
                | - JSON;
                | - HTML già formattato.
                |
                */

                $original = (string)$value;
                $items = [];

                $push_item = function($item) use (&$items){
                    if(is_array($item)):
                        foreach($item as $sub):
                            if(is_array($sub)):
                                foreach($sub as $sub2):
                                    if(is_scalar($sub2) && trim((string)$sub2) != ''):
                                        $items[] = trim((string)$sub2);
                                    endif;
                                endforeach;
                            elseif(is_scalar($sub) && trim((string)$sub) != ''):
                                $items[] = trim((string)$sub);
                            endif;
                        endforeach;
                    elseif(is_scalar($item) && trim((string)$item) != ''):
                        $items[] = trim((string)$item);
                    endif;
                };

                /*
                | 1. Se arriva HTML, prova a estrarre href e src.
                */
                if(preg_match_all('/href=["\']([^"\']+)["\']/i', $original, $matches)):
                    foreach($matches[1] as $href):
                        $push_item(html_entity_decode($href, ENT_QUOTES, 'UTF-8'));
                    endforeach;
                endif;

                if(preg_match_all('/src=["\']([^"\']+)["\']/i', $original, $srcMatches)):
                    foreach($srcMatches[1] as $src):
                        $push_item(html_entity_decode($src, ENT_QUOTES, 'UTF-8'));
                    endforeach;
                endif;

                /*
                | 2. PHP serialized array.
                */
                if(empty($items)):
                    $unserialized = @unserialize($original);
                    if($unserialized !== false || $original === 'b:0;'):
                        $push_item($unserialized);
                    endif;
                endif;

                /*
                | 3. JSON array.
                */
                if(empty($items)):
                    $json = json_decode($original, true);
                    if(json_last_error() === JSON_ERROR_NONE):
                        $push_item($json);
                    endif;
                endif;

                /*
                | 4. Serialized parziale / testo semplice.
                */
                if(empty($items)):
                    $clean = str_replace(['<br />','<br/>','<br>'], "\n", $original);
                    $clean = strip_tags($clean);

                    if(preg_match_all('/s:\d+:"([^"]+)"/', $clean, $serializedMatches)):
                        foreach($serializedMatches[1] as $match):
                            $push_item($match);
                        endforeach;
                    else:
                        $parts = preg_split('/[\n,]+/', $clean);
                        foreach($parts as $part):
                            $push_item($part);
                        endforeach;
                    endif;
                endif;

                $items = array_values(array_unique(array_filter(array_map('trim', $items))));

                if(empty($items)):
                    return '';
                endif;

                /*
                |--------------------------------------------------------------------------
                | HELPERS STORECLOUD / R2
                |--------------------------------------------------------------------------
                |
                | I campi upload nativi spesso salvano solo UUID, non path.
                | In quel caso proviamo a risolvere URL/nome tramite StoreCloud.
                |
                */

                $is_uuid = function($value){
                    return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', trim((string)$value));
                };

                $storeCloud = null;
                $storeR2 = null;
                $storeRepo = null;

                if(class_exists('StoreCloud')):
                    try{
                        $storeCloud = new StoreCloud();
                    }catch(Throwable $e){
                        $storeCloud = null;
                    }
                endif;

                if(class_exists('StoreR2Adapter')):
                    try{
                        $storeR2 = new StoreR2Adapter();
                    }catch(Throwable $e){
                        $storeR2 = null;
                    }
                endif;

                if(class_exists('StoreFileRepository')):
                    try{
                        $storeRepo = new StoreFileRepository();
                    }catch(Throwable $e){
                        $storeRepo = null;
                    }
                endif;

                $resolve_cloud_item = function($item) use ($storeCloud, $storeR2, $storeRepo, $is_uuid){
                    $item = trim((string)$item);

                    if($item == '' || !$is_uuid($item)):
                        return null;
                    endif;

                    /*
                    |--------------------------------------------------------------------------
                    | RISOLUZIONE ALLEGATI CLOUD/R2 COME NEL GESTIONALE
                    |--------------------------------------------------------------------------
                    |
                    | La funzione storica StoreCloud::renderCloudImg() usa:
                    |
                    | - StoreFileRepository::findAll($file) per leggere file_location
                    | - StoreCloud::getImageUrl($file, 'public') per Cloudflare Images
                    | - StoreR2Adapter::getFileUrl($file) per R2 pubblico
                    |
                    | Qui replichiamo quella logica per evitare link a varianti
                    | private/original non accessibili.
                    |
                    */

                    try{

                        $file_info = [];
                        $file_name = $item;
                        $file_location = '';

                        if($storeRepo):
                            $file_info = $storeRepo->findAll($item);

                            if(is_array($file_info)):
                                if(isset($file_info['file_location'])):
                                    $file_location = $file_info['file_location'];
                                endif;

                                if(isset($file_info['file_name']) && $file_info['file_name'] != ''):
                                    $file_name = $file_info['file_name'];
                                elseif(method_exists($storeRepo, 'getFileName')):
                                    $repoFileName = $storeRepo->getFileName($item);
                                    if($repoFileName):
                                        $file_name = $repoFileName;
                                    endif;
                                endif;
                            endif;
                        endif;

                        /*
                        | Fallback DB diretto tramite StoreCloud se repository non basta.
                        */
                        if($file_location == '' && $storeCloud):
                            $file_location = $storeCloud->getImageDetailFromDb($item, 'file_location');
                        endif;

                        if(($file_name == $item || $file_name == '') && $storeCloud):
                            $dbFileName = $storeCloud->getImageDetailFromDb($item, 'file_name');
                            if($dbFileName):
                                $file_name = $dbFileName;
                            endif;
                        endif;

                        /*
                        | Cloudflare Images: variante pubblica.
                        */
                        if($file_location == 'cloudImages' && $storeCloud):
                            return [
                                'href' => $storeCloud->getImageUrl($item, 'public'),
                                'label' => $file_name
                            ];
                        endif;

                        /*
                        | Cloudflare R2 / storage pubblico.
                        */
                        if($storeR2):
                            return [
                                'href' => $storeR2->getFileUrl($item),
                                'label' => $file_name
                            ];
                        endif;

                    }catch(Throwable $e){
                        return null;
                    }

                    return null;
                };

                $links = [];
					$attachment_counter = 1;

					foreach($items as $item):

                    $item = trim((string)$item);

                    if($item == ''):
                        continue;
                    endif;

                    /*
                    |--------------------------------------------------------------------------
                    | PULIZIA TOKEN TECNICI / VUOTI
                    |--------------------------------------------------------------------------
                    |
                    | Alcuni campi upload nativi possono contenere frammenti tecnici
                    | derivati da array vuoti o serializzazioni parziali, ad esempio:
                    |
                    |     0:{}
                    |     a:0:{}
                    |     []
                    |     {}
                    |
                    | Questi non sono allegati reali e non devono comparire nel PDF.
                    |
                    */
                    $lower_item = strtolower(trim($item));

                    if(in_array($lower_item, ['vedi allegati','nessun allegato','0:{}','a:0:{}','[]','{}','null'])):
                        continue;
                    endif;

                    if(preg_match('/^[0-9]+:\{\}$/', $item)):
                        continue;
                    endif;

                    if(preg_match('/^a:0:\{\}$/', $item)):
                        continue;
                    endif;

                    /*
                    | 1. UUID StoreCloud/R2.
                    */
                    $cloud = $resolve_cloud_item($item);

                    if($cloud && !empty($cloud['href'])):
						$links[] = '<a href="'.htmlspecialchars($cloud['href'], ENT_QUOTES, 'UTF-8').'" target="_blank">Apri allegato '.$attachment_counter++.'</a>';
						continue;
					endif;

                    /*
                    | 2. Path / URL reale.
                    */
                    $href = $item;

                    if(!$is_absolute_url($href) && strpos($href, '/') === 0):
                        $scheme = 'https://';
                        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
                        if($host != ''):
                            $href = $scheme.$host.$href;
                        endif;
                    endif;

                    $label = basename(parse_url($item, PHP_URL_PATH) ?: $item);
                    $label = $label != '' ? $label : $item;

                    if($is_absolute_url($href) || strpos($item, '/') === 0):

						$links[] = '<a href="'.htmlspecialchars($href, ENT_QUOTES, 'UTF-8').'" target="_blank">Apri allegato '.$attachment_counter++.'</a>';
						continue;

					endif;

					if($is_uuid($item)):

						$links[] = 'Allegato cloud';
						continue;

					endif;

					$storage_url = 'https://demo-storage.archyart.it/uploads/' . rawurlencode(basename($item));

					$links[] = '<a href="'.htmlspecialchars($storage_url, ENT_QUOTES, 'UTF-8').'" target="_blank">Apri allegato '.$attachment_counter++.'</a>';
					continue;

                endforeach;

                return implode('<br />', $links);
            };

            $render_pdf_thumbnail = function($value) use ($escape_pdf_cell, $is_absolute_url, $is_image_file){
                /*
                |--------------------------------------------------------------------------
                | EXPORT PDF DATATABLE - MINIATURE OTTIMIZZATE V1.2
                |--------------------------------------------------------------------------
                |
                | La vecchia versione risolveva la Foto ufficiale ripetendo per ogni cella:
                | - creazione StoreCloud;
                | - query getImageDetailFromDb();
                | - controlli file_exists() su vari fallback locali;
                | - parsing HTML.
                |
                | Su archivi grandi questo può diventare molto costoso, soprattutto se nel
                | PDF vengono esportate centinaia/migliaia di righe con immagini.
                |
                | Questa versione mantiene lo stesso comportamento, ma aggiunge:
                | - cache per valore cella;
                | - istanza StoreCloud riutilizzata;
                | - controlli locali ridotti;
                | - uscita rapida per URL assoluti e <img src="..."> già risolti.
                */

                static $thumbCache = [];
                static $storeCloudChecked = false;
                static $storeCloud = null;
                static $documentRoot = null;
                static $host = null;

                $original_value = (string)$value;
                $value = trim($original_value);

                if($value == ''):
                    return '';
                endif;

                $cacheKey = md5($original_value);
                if(isset($thumbCache[$cacheKey])):
                    return $thumbCache[$cacheKey];
                endif;

                $cache_return = function($html) use (&$thumbCache, $cacheKey){
                    $thumbCache[$cacheKey] = $html;
                    return $html;
                };

                if($documentRoot === null):
                    $documentRoot = isset($_SERVER['DOCUMENT_ROOT']) ? rtrim($_SERVER['DOCUMENT_ROOT'], '/') : '';
                endif;

                if($host === null):
                    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
                endif;

                $render_img = function($src){
                    $src = trim((string)$src);
                    if($src == ''):
                        return '';
                    endif;

                    $src = htmlspecialchars($src, ENT_QUOTES, 'UTF-8');
                    return '<img src="'.$src.'" width="50" height="50" />';
                };

                /*
                | 1. Se la griglia ha già prodotto un <img>, usiamo direttamente src.
                |    Questo evita risoluzioni StoreCloud e fallback inutili.
                */
                if(preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $original_value, $m)):
                    $src = html_entity_decode($m[1], ENT_QUOTES, 'UTF-8');

                    if($is_absolute_url($src)):
                        return $cache_return($render_img($src));
                    endif;

                    if($documentRoot != ''):
                        $local = $documentRoot.'/'.ltrim($src, '/');
                        if(is_file($local)):
                            return $cache_return($render_img($local));
                        endif;
                    endif;

                    return $cache_return($render_img($src));
                endif;

                $value = trim(strip_tags($original_value));

                /*
                | 2. URL assoluto già pronto.
                */
                if($is_absolute_url($value)):
                    return $cache_return($render_img($value));
                endif;

                /*
                | 3. StoreCloud: istanza unica + cache per cella.
                */
                if(!$storeCloudChecked):
                    $storeCloudChecked = true;
                    if(class_exists('StoreCloud')):
                        try{
                            $storeCloud = new StoreCloud();
                        }catch(Throwable $e){
                            $storeCloud = null;
                        }
                    endif;
                endif;

                if($storeCloud):
                    try{
                        $file_location = $storeCloud->getImageDetailFromDb($value, 'file_location');

                        if($file_location == 'cloudImages'):
                            return $cache_return($render_img($storeCloud->getImageUrl($value, 'thumbnail')));
                        elseif($file_location == 'local'):
                            return $cache_return($render_img($value));
                        endif;
                    }catch(Throwable $e){
                        // Non blocchiamo l'export PDF: proseguiamo con i fallback.
                    }
                endif;

                $candidates_from_filename = function($filename) use ($documentRoot, $host){
                    $filename = ltrim(trim((string)$filename), '/');
                    $basename = basename($filename);
                    $candidates = [];

                    if($documentRoot != ''):
                        $candidates[] = $documentRoot.'/'.$filename;
                        $candidates[] = $documentRoot.'/assets/documents/upload_extrafield/'.$basename;
                        $candidates[] = $documentRoot.'/assets/documents/scheda_catalogo/'.$basename;
                        $candidates[] = $documentRoot.'/assets/img/'.$basename;
                        $candidates[] = $documentRoot.'/assets/images/'.$basename;
                    endif;

                    if($host != ''):
                        $scheme = 'https://';
                        $candidates[] = $scheme.$host.'/'.$filename;
                        $candidates[] = $scheme.$host.'/assets/documents/upload_extrafield/'.$basename;
                        $candidates[] = $scheme.$host.'/assets/documents/scheda_catalogo/'.$basename;
                    endif;

                    return $candidates;
                };

                /*
                | 4. Più elementi nella cella: usa il primo file immagine utile.
                */
                $parts = preg_split('/[\n,]+/', str_replace(['<br />','<br/>','<br>'], "\n", $value));

                foreach($parts as $part):
                    $part = trim($part);

                    if($part == ''):
                        continue;
                    endif;

                    if($is_absolute_url($part)):
                        return $cache_return($render_img($part));
                    endif;

                    if($is_image_file($part)):
                        foreach($candidates_from_filename($part) as $candidate):
                            if(strpos($candidate, 'http') === 0):
                                return $cache_return($render_img($candidate));
                            endif;

                            if(is_file($candidate)):
                                return $cache_return($render_img($candidate));
                            endif;
                        endforeach;
                    endif;

                endforeach;

                return $cache_return($escape_pdf_cell($value));
            };

            $is_pdf_photo_column = function($label, $column_data){
                $label = mb_strtolower(trim((string)$label), 'UTF-8');

                if(in_array($label, ['foto ufficiale','foto','immagine','immagine principale','foto principale'])):
                    return true;
                endif;

                if(isset($column_data['name'])):
                    $name = mb_strtolower((string)$column_data['name'], 'UTF-8');
                    if(in_array($name, ['foto_ufficiale','foto','immagine','immagine_principale','image','photo'])):
                        return true;
                    endif;
                endif;

                if(isset($column_data['label'])):
                    $cd_label = mb_strtolower((string)$column_data['label'], 'UTF-8');
                    if(in_array($cd_label, ['foto ufficiale','foto','immagine','immagine principale','foto principale'])):
                        return true;
                    endif;
                endif;

                return false;
            };

            /*
            |--------------------------------------------------------------------------
            | COSTRUZIONE HTML PDF
            |--------------------------------------------------------------------------
            */

            $pdf_styles='
                <style type="text/css">
                    body{
                        font-family:helvetica,dejavusans,sans-serif;
                        font-size:'.$size.'pt;
                        line-height:1.25;
                        color:#222222;
                    }

                    h2{
                        font-size:13pt;
                        font-weight:bold;
                        margin:0 0 7px 0;
                        color:#1f2d3d;
                    }

                    table{
                        width:100%;
                        border-collapse:collapse;
                    }

                    thead tr{
                        background-color:#e9eef5;
                    }

                    th{
                        font-weight:bold;
                        color:#1f2d3d;
                        border:0.3px solid #cfd6df;
                        padding:3px 3px;
                        vertical-align:middle;
                    }

                    td{
                        border:0.3px solid #d9dee6;
                        padding:3px 3px;
                        vertical-align:top;
                    }

                    tr.row-even td{
                        background-color:#ffffff;
                    }

                    tr.row-odd td{
                        background-color:#f7f8fa;
                    }

                    a{
                        color:#1a66cc;
                        text-decoration:none;
                    }

                    img{
                        border:0.3px solid #d9dee6;
                    }

                    .small-note{
                        font-size:6pt;
                        color:#777777;
                    }
                </style>';

            $pdf='<h2>'.$escape_pdf_title($filename).'</h2>';
            $pdf.='<table width="100%" border="0" cellspacing="0" cellpadding="3">';
            $pdf.='<thead><tr>';

            foreach($columns_labels as $column_title):
                $pdf.='<th>'.$escape_pdf_title($column_title).'</th>';
            endforeach;

            $pdf.='</tr></thead><tbody>';

            $total = mysqli_num_rows($data);
            $i = 0;

            while($row=mysqli_fetch_row($data)):

                $row_class = ($i % 2 == 0) ? 'row-even' : 'row-odd';
                $pdf.='<tr class="'.$row_class.'">';

                foreach($row as $index=>$cell):

                    $column_label = isset($columns_labels[$index]) ? $columns_labels[$index] : '';
                    $column_data = isset($columns_data[$index]) ? $columns_data[$index] : [];

                    /*
                    |--------------------------------------------------------------------------
                    | EXPORT PDF - ALLEGATI / UPLOAD
                    |--------------------------------------------------------------------------
                    |
                    | Per i campi file e bulk_upload NON dobbiamo applicare il formatter
                    | della DataTable, perché in griglia quel formatter produce pulsanti
                    | HTML tipo "Vedi allegati". Nel PDF tabellare invece servono i dati
                    | reali: link, nomi file o miniatura della foto ufficiale.
                    |
                    | Quindi:
                    | - Foto ufficiale: miniatura 50x50;
                    | - altri file/bulk_upload: link o nome file leggibile;
                    | - altri campi: formatter standard se previsto.
                    |
                    */
						$is_documentation_column = (
						stripos((string)$column_label, 'documentazione') !== false ||
						(
							isset($column_data['label']) &&
							stripos((string)$column_data['label'], 'documentazione') !== false
						)
					);

					if($is_documentation_column):
						$pdf.='<td>'.$render_pdf_upload_links($cell).'</td>';
					elseif($is_pdf_photo_column($column_label, $column_data)):
						$pdf.='<td align="center">'.$render_pdf_thumbnail($cell).'</td>';
					elseif(
						isset($column_data['type']) &&
						in_array($column_data['type'], ['file','bulk_upload'])
					):
						$pdf.='<td>'.$render_pdf_upload_links($cell).'</td>';
					else:
                        if(isset($columns_data[$index]['pdf_formatter'])):
                            $cell=$columns_data[$index]['pdf_formatter']($cell,'');
                        elseif(isset($columns_data[$index]['formatter']) and !isset($columns_data[$index]['formatter_hide_in_pdf'])):
                            $cell=$columns_data[$index]['formatter']($cell,'');
                        endif;

                        $pdf.='<td>'.$escape_pdf_cell($cell).'</td>';
                    endif;

                endforeach;

                $pdf.='</tr>';

                $i++;

                if($i % 10 == 0 || $i == $total):
                    $write_status([
                        'progress' => $total > 0 ? intval(($i / max($total,1)) * 80) : 80,
                        'ready' => false
                    ]);
                endif;

            endwhile;

            $pdf.='</tbody></table>';

            /*
            |--------------------------------------------------------------------------
            | GENERAZIONE TCPDF
            |--------------------------------------------------------------------------
            */

            $pdf_file = '<html><head>'.$pdf_styles.'</head><body>'.$pdf.'</body></html>';

            $tcpdf = new TCPDF($orientation, 'mm', $sheet, true, 'UTF-8', false);
            $tcpdf->SetAuthor('Eteria Cloud');
            $tcpdf->setPrintHeader(false);
            $tcpdf->setPrintFooter(false);
            $tcpdf->SetMargins($left, $top, $right);
            $tcpdf->SetAutoPageBreak(true, $bottom);
            $tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
            $tcpdf->setJPEGQuality(75);
            $tcpdf->SetFont('dejavusans', '', $size);
            $tcpdf->AddPage();
            $tcpdf->writeHTML($pdf_file, true, false, true, false, '');

            // Salvataggio su disco per flusso asincrono.
            $tcpdf->Output($filePath, 'F');

            if(!file_exists($filePath) || filesize($filePath) <= 0):
                throw new Exception('PDF non creato o file vuoto: '.$filePath);
            endif;

            $write_status([
                'progress' => 100,
                'ready' => true
            ]);

            header('Content-Type: application/json');
            echo json_encode(['job_id' => $job_id]);
            exit;

        }catch(Throwable $e){

            @file_put_contents($logPath, date('Y-m-d H:i:s').' - '.$e->getMessage()."\n".$e->getTraceAsString());

            $write_status([
                'progress' => 100,
                'ready' => false,
                'error' => $e->getMessage(),
                'log' => $logPath
            ]);

            header('Content-Type: application/json');
            echo json_encode([
                'job_id' => $job_id,
                'error' => $e->getMessage()
            ]);
            exit;
        }
	}



	
        public function store_cloudflare(){
            
            $cloud = new Cloudflare();
            $cloud->ApiRequest();
            
        }
	
	public function store_settings(){
		$db=$this->get_db();
		foreach($_POST['settings'] as $domain=>$data):
			foreach($data as $name=>$value):
				if(is_array($value)):
					$value=serialize($value);
				endif;
				$db->query("delete from archy_settings where `domain`='".addslashes($domain)."' and `name`='".addslashes($name)."'");
				$db->query("insert into archy_settings (`domain`,`name`,`value`) values ('".addslashes($domain)."','".addslashes($name)."','".addslashes($value)."')");
			endforeach;
		endforeach;
                
		if(isset($_FILES['settings']['name'])):
			foreach($_FILES['settings']['name'] as $domain=>$data):
				foreach($data as $name=>$value):
                    if($value!=''):
                        // se e'è, rimuovo file dal fs
                        $ret=mysqli_fetch_object($db->query("select value from archy_settings where `domain`='".addslashes($domain)."' and `name`='".addslashes($name)."'"));
                        if(is_file($this->subdomain_root.$ret->value)):
                            unlink($this->subdomain_root.$ret->value);
                        endif;
                        // rimuovo anche dal db
                        $db->query("delete from archy_settings where `domain`='".addslashes($domain)."' and `name`='".addslashes($name)."'");
                        // mi assicuro che esista la cartella
                        $path='/assets/documents/settings/';
                        $physical_path=$this->subdomain_root.$path;
                        if(!is_dir($physical_path)):
                            mkdir($physical_path, 0775,true);
                        endif;
                        $prefix='';
                        // conflitto nomi uguali
                        $counter=1;
                        while(is_file($physical_path.$prefix.$value)):
                            $prefix=$counter.'-';
                            $counter++;
                        endwhile;
                        // store
                        move_uploaded_file($_FILES['settings']['tmp_name'][$domain][$name], $physical_path.$prefix.$value);
                        $db->query("insert into archy_settings (`domain`,`name`,`value`) values ('".addslashes($domain)."','".addslashes($name)."','".addslashes($path.$prefix.$value)."')");
                    endif;
				endforeach;
			endforeach;
		endif;
		set_message("success","Impostazioni salvate");
		header('location: '.$_SERVER['HTTP_REFERER']);
    }
    
	public function get_settings($domain=''){
		if($domain==''):
			$response=[];
			$domains=$this->db->query("select distinc `domain` from archy_settings");
			while($domain=mysqli_fetch_object($domains)):
				$response[$domain]=$this->get_domain_settings($domain);
			endwhile;
		else:
			$response=$this->get_domain_settings($domain);
		endif;
		return $response;
	}
	public function get_domain_settings($domain){
		$response=new stdClass();
		$settings=$this->db->query("select `name`,`value` from archy_settings where `domain`='".addslashes($domain)."'");
		while($setting=mysqli_fetch_object($settings)):
			$name=$setting->name;
			$value=$this->is_serialized($setting->value)? unserialize($setting->value) : $setting->value;
            $response->$name=$value;
            
            // error_log("@@@@@@@@@@@@@@@@@ get_domain_settings >".$name."=".$value."<");
		endwhile;
		return $response;
	}
	
	public function cities_autocomplete(){
		$db=$this->get_db();
		$cities=$db->query("select nome_comune from archy_it_cities where nome_comune like '".$_GET['term']."%' order by nome_comune limit 8");
		$response=[];
		while($city=mysqli_fetch_object($cities)):
			$tmp=new stdClass();
			$tmp->value=$tmp->label=$city->nome_comune;
			$response[]=$tmp;
		endwhile;
		echo json_encode($response);
	}
	
	//formatting functions
        public static function format_grid_multiple_files($img_name){
            $StoreCloud = new StoreCloud();
            $response = 'Nessun allegato';
            if($img_name!=null){
                
                $files_target = [];
                $images_target = [];
                if(App::is_serialized($img_name)){
                    $files = unserialize($img_name);
                }else{
                    $files = json_decode($img_name);                    
                }
                $storage=false;
                foreach ($files as $file) :
                 
                    if($file!=null){
                        $file_location = $StoreCloud->getImageDetailFromDb($file, 'file_location');
                        
                        
                        
                        if($file_location=='storage'){                    
                            $storage=true;
                        }else{$storage=false;}

                        if($storage){
                            $files_target[]=$file;
                        }else{
                            $images_target[]=$file;
                        }
                    }
                endforeach;
                
                if(count($files_target)>0 || count($images_target)>0){                    
                    $response ='<button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#view_cloud_modal" data-images="'.join(',',$images_target).'" data-files="'.join(',',$files_target).'">Vedi allegati</button>';
                }
            }
            return $response;
        }
        
        
        public static function format_grid_img($img_name){
            $StoreCloud = new StoreCloud();
            $url_img = '';
            $url_thumb_img = '';
            $back_img = '
                <div class="avatar avatar-3xl position-relative">
                  <div class="avatar-name d-flex flex-center position-absolute all-0"><span class="far fa-file-image"></span></div>
                </div>
            ';
            
            if($img_name!=null){
                $file_location = $StoreCloud->getImageDetailFromDb($img_name, 'file_location');
                $cloud=false;
                $storage=false;
                if($file_location=='local'){ 
                    $url_img = $img_name; 
                    $url_thumb_img = $img_name;                     
                }
                elseif($file_location == 'cloudImages'){ 
                    $url_img = $StoreCloud->getImageUrl($img_name); 
                    $url_thumb_img = $StoreCloud->getImageUrl($img_name,'thumbnail'); 
                    $cloud=true;                     
                }
                else{
                    
                    $storage=true;
                }
                if($file_location=='local' || $file_location=='cloudImages'){
                    $back_img = '
                    <div class="card card-grid overflow-hidden" style="width:5rem;max-width:5rem;">
                        <div class="card-img-top"><img style="width:100%;" src="' . $url_thumb_img . '" alt=""/></div>
                        <div class="card-body">
                            <a class="card-link" href="' . $url_img . '" target="_blank" data-bs-toggle="tooltip" data-bs-placement="top" title="Visualizza"><span class="far fa-eye"></span></a>';
                            if($cloud){ $back_img.='<a class="card-link" href="/file_cloud_serve.php?imageId='.$img_name.'" target="_blank" data-bs-toggle="tooltip" data-bs-placement="top" title="Download"><span class="fas fa-cloud-download-alt"></span></a>'; }
                        $back_img.='</div>
                    </div>';
                }else{
                    $back_img = '<a class="mb-3 icon-link" href="#!">
                    <span class="bi fas fa-link"></span>
                    Icon link
                    </a>';
                }
            }
            return $back_img;
        }
        
	public static function latin_date_time($timestamp){
		if($timestamp!='' && $timestamp!='0000-00-00 00:00:00' && $timestamp!='0000-00-00'):
			$date=new DateTime($timestamp);
			return $date->format('d/m/Y H:i:s');
        endif;
	}
	public static function email($email){
		return '<a href="mailto:'.$email.'">'.$email.'</a>';
	}
	public static function website($website){
		return '<a href="'.$website.'" target="_blank">'.$website.'</a>';
	}
	public static function checkbox($checkbox){
		return $checkbox=='0'? 'No':'Sì';
	}
	public static function latin_date($timestamp){
		if($timestamp!='' and $timestamp!='0000-00-00'):
			$date=new DateTime($timestamp);
			return $date->format('d/m/Y');
		endif;
	}
    /*
    |--------------------------------------------------------------------------
    | ARCHY MEDIA - HELPERS TAGS CONTROLLATI
    |--------------------------------------------------------------------------
    */
    public static function tag_slug($value){
        $value = trim((string)$value);
        if(function_exists('iconv')){
            $converted = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
            if($converted !== false){
                $value = $converted;
            }
        }
        $value = strtolower($value);
        $value = preg_replace('/[^a-z0-9]+/', '-', $value);
        $value = trim($value, '-');
        return $value !== '' ? $value : 'tag';
    }

    public static function parse_tags_value($value){
        if($value === null || $value === ''){
            return [];
        }

        if(is_array($value)){
            $values = $value;
        } elseif(App::is_serialized($value)) {
            $values = @unserialize($value);
        } else {
            $json = json_decode($value, true);
            if(is_array($json)){
                $values = $json;
            } else {
                $values = preg_split('/[,;|]+/', (string)$value);
            }
        }

        if(!is_array($values)){
            $values = [];
        }

        $clean = [];
        foreach($values as $v){
            if(is_array($v)){
                continue;
            }
            $v = trim((string)$v);
            if($v !== ''){
                $clean[] = $v;
            }
        }

        return array_values(array_unique($clean));
    }

    public static function get_tag_options($category = 'Tag'){
        global $app;
        $db = $app->get_db();

        $category = trim((string)$category);
        if($category === ''){
            $category = 'Tag';
        }

        $options = [];
        $res = $db->query("
            SELECT id, tag
            FROM archy_tag
            WHERE status = 1
            AND categoria = '".$db->real_escape_string($category)."'
            ORDER BY tag ASC
        ");

        if($res){
            while($row = mysqli_fetch_object($res)){
                $options[(string)$row->id] = $row->tag;
            }
        }

        return $options;
    }

    public static function format_tags($value, $row = ''){
        global $app;

        $ids = App::parse_tags_value($value);
        if(empty($ids)){
            return '';
        }

        $numeric_ids = [];
        $labels = [];

        foreach($ids as $id){
            if(ctype_digit((string)$id)){
                $numeric_ids[] = (int)$id;
            } else {
                $labels[] = $id;
            }
        }

        if(!empty($numeric_ids)){
            $db = $app->get_db();
            $res = $db->query("
                SELECT tag
                FROM archy_tag
                WHERE id IN (".implode(',', array_map('intval', $numeric_ids)).")
                ORDER BY tag ASC
            ");

            if($res){
                while($row = mysqli_fetch_object($res)){
                    $labels[] = $row->tag;
                }
            }
        }

        return implode(', ', array_values(array_unique($labels)));
    }

    public function is_tags_field($field){
        $key = $this->intent->table.'.'.$field;
        return (
            (isset($this->cols[$key]) && isset($this->cols[$key]['type']) && $this->cols[$key]['type'] === 'tags') ||
            (isset($this->cols[$field]) && isset($this->cols[$field]['type']) && $this->cols[$field]['type'] === 'tags')
        );
    }

    public function get_tags_field_config($field){
        $key = $this->intent->table.'.'.$field;
        if(isset($this->cols[$key])){
            return $this->cols[$key];
        }
        if(isset($this->cols[$field])){
            return $this->cols[$field];
        }
        return [];
    }

    public function normalize_tags_post_value($field, $value){
        $db = $this->get_db();
        $config = $this->get_tags_field_config($field);
        $category = isset($config['tag_category']) && trim((string)$config['tag_category']) !== '' ? $config['tag_category'] : 'Tag';

        $values = App::parse_tags_value($value);
        $tag_ids = [];

        foreach($values as $item){
            $item = trim((string)$item);
            if($item === ''){
                continue;
            }

            if(ctype_digit($item)){
                $tag_ids[] = (int)$item;
                continue;
            }

            $label = preg_replace('/\s+/', ' ', $item);
            $slug = App::tag_slug($label);

            $res = $db->query("
                SELECT id
                FROM archy_tag
                WHERE categoria = '".$db->real_escape_string($category)."'
                AND slug = '".$db->real_escape_string($slug)."'
                LIMIT 1
            ");

            if($res && mysqli_num_rows($res)){
                $tag_ids[] = (int) mysqli_fetch_object($res)->id;
                continue;
            }

            $db->query("
                INSERT INTO archy_tag
                SET
                    tag = '".$db->real_escape_string($label)."',
                    slug = '".$db->real_escape_string($slug)."',
                    categoria = '".$db->real_escape_string($category)."',
                    status = 1,
                    created_at = NOW(),
                    updated_at = NOW()
            ");

            $tag_ids[] = (int) $db->insert_id;
        }

        $tag_ids = array_values(array_unique(array_filter($tag_ids)));

        return empty($tag_ids) ? '' : serialize(array_map('strval', $tag_ids));
    }

        public static function format_date($date){
            if (empty($date)) {
                return '';
            }

            try {
                $d = new DateTime($date);

                // blocca date tipo 0000-00-00
                if ($d->format('Y') <= 0) {
                    return '';
                }

                return $d->format('d/m/Y');

            } catch (Exception $e) {
                return '';
            }
        }
	public static function db_date($latin_date){
		return substr($latin_date,6).'-'.substr($latin_date,3,2).'-'.substr($latin_date,0,2);
	}
	public static function db_datetime($latin_datetime){
		return substr($latin_datetime,6,4).'-'.substr($latin_datetime,3,2).'-'.substr($latin_datetime,0,2).' '.substr($latin_datetime,11);
	}
	public static function currency($amount){
		return '€ '.number_format((float)$amount,2,',','.');
	}
	public static function get_registry_full_name($registry_id){
		global $app;
		$db=$app->get_db();
		$name=mysqli_fetch_object($db->query("select name,surname from archy_registry where id=".$registry_id));
		return '['.$registry_id.'] '.$name->name.' '.$name->surname;
	}
	public static function format_additional_fields_tables($tables){
		global $app;
		$db=$app->get_db();
		$response=[];
		foreach($tables as $table):
			$alias=mysqli_fetch_object($db->query("select alias from admin_additional_fields where table_name='".addslashes($table)."'"));
			$response[]=$alias->alias;
		endforeach;
		return join(', ',$response);
    }
    public static function format_eps($data){
		global $app;
		$db=$app->get_db();
        $response=[];
        if(!empty($data)){
            $ser=trim(implode(',',unserialize($data)));
            if($ser!=""){
                $query = "SELECT title FROM archy_eps where id IN (".$ser.")";
                $result = $db->query($query);
                while($row = $result->fetch_array()) {
                    $response[] = $row[0];
                }
            }
        }
		return join(',',$response);
	}
    public static function format_speakers($data){
		global $app;
		$db=$app->get_db();
        $response=[];
        if(!empty($data)){
            $ser=trim(implode(',',unserialize($data)));
            if($ser!=""){
                $query = "SELECT name FROM archy_events_speakers where id IN (".$ser.")";
                $result = $db->query($query);
                while($row = $result->fetch_array()) {
                    $response[] = $row[0];
                }
            }
        }
		return join(',',$response);
	}
	
	//external methods
	public function instant_dispatch(){
		$message=new Message();
		$data=$_POST['main'];
                $message->instant_dispatch([

                    'media' => $data['media'],

                    // PASSA TUTTO L'ARRAY ORIGINALE
                    'recipients' => $data['recipients'],

                    'subject' => $data['subject'],
                    'message' => $_POST['main_message'],
                    'attachments' => @$data['attachments'],
                    'page' => $_POST['page'],
                    'sender_name' => $data['sender_name'],
                    'sender_mail' => $data['sender_mail']

                ]);
	}
	
	public function select2_datasource(){
		list($table,$column)=explode(".",$_GET['intent']);
		$db=$this->get_db();

        /*
        |--------------------------------------------------------------------------
        | ARCHY MEDIA - SELECT2 DATASOURCE PER CAMPI TAGS
        |--------------------------------------------------------------------------
        |
        | I campi type="tags" salvano nel record un serialize degli ID tag.
        | Il filtro sotto colonna, invece, deve mostrare le etichette leggibili
        | prese da archy_tag e filtrare il valore serializzato usando l'ID.
        |
        | Esempio valore salvato:
        |   a:1:{i:0;s:2:"12";}
        |
        | Per questo l'id restituito a Select2 è "12" con le virgolette:
        | così il LIKE sulla colonna serializzata è più preciso.
        |
        */
        $archy_tags_categories = [
            'tags'      => 'Tag',
            'tecnica'   => 'Tecnica',
            'materiali' => 'Materiali',
            'supporto'  => 'Supporto'
        ];

        if($table == 'archy_art_catalog' && isset($archy_tags_categories[$column])):

            $category = $archy_tags_categories[$column];
            $term = '';

            if(isset($_GET['search'])):
                $term = $_GET['search'];
            elseif(isset($_GET['term'])):
                $term = $_GET['term'];
            elseif(isset($_GET['q'])):
                $term = $_GET['q'];
            endif;

            $q = "
                SELECT id, tag
                FROM archy_tag
                WHERE categoria = '".$db->real_escape_string($category)."'
                AND status = 1
            ";

            if(trim((string)$term) != ''):
                $q .= " AND tag LIKE '%".$db->real_escape_string($term)."%' ";
            endif;

            $q .= " ORDER BY tag ASC LIMIT 50";

            $result = $db->query($q);
            $response = [];

            if($result):
                while($row = mysqli_fetch_array($result)):
                    $response[] = [
                        'id' => '"'.$row['id'].'"',
                        'text' => $row['tag']
                    ];
                endwhile;
            endif;

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($response);
            return;

        endif;

		$q="select ".$column." from ".$table;
		if(@$_GET['search']!=''):
			$q.=" where ".$column." like '%".addslashes($_GET['search'])."%' ";
		endif;
		if(isset($_GET['intent_filter'])){
			list($model_intent,$table_intent)=explode("|",$_GET['intent_filter']);
			if(isset($_GET['search'])){$q.=' and ';}else{$q.=' where ';}
			$q.=$model_intent." = '".$table_intent."'";
		}
		$q.=" order by ".$column;
		$result=$db->query($q);
		$response=[];
		while($row=mysqli_fetch_array($result)):
			$response[]=['id'=>$row[$column],'text'=>$row[$column]];
		endwhile;
		echo json_encode($response);
	}
	
	public function members_select2(){
		$result=$this->db->query("
			select m.id, r.label, r.tax_code
			from archy_memberships as m
			left join archy_registry as r on (m.registry_id=r.id)
			where r.label like '%".@$_GET['term']."%' limit 3
		");
		$response=[];
		while($row=mysqli_fetch_array($result)):
			$value=$row['tax_code']!=''? $row['label'].' ('.$row['tax_code'].')' : $row['label'];
			$response[]=['id'=>$row['id'],'value'=>$value];
		endwhile;
		echo json_encode($response);
	}
	
	
	public function fetch_notices(){
		$notice=new Notice();
		echo json_encode($notice->html());
	}
	public function fetch_dashboard_notices(){
		$notice=new Notice();
		echo json_encode($notice->dashboard_html(6));
	}
	public function mark_notices_as_read(){
		$notice=new Notice();
		$notice->mark_notices_as_read();
	}
	public function mark_notice_as_read(){
		$notice=new Notice();
		$notice->mark_notice_as_read(isset($_POST['id'])? $_POST['id'] : 0);
	}
	public function toggle_notice_seen(){
		$notice=new Notice();
		$notice->toggle_notice_seen(isset($_POST['id'])? $_POST['id'] : 0);
		echo json_encode($notice->dashboard_html(6));
	}
	public function get_notice_detail(){
		$notice=new Notice();
		echo json_encode($notice->detail_html(isset($_POST['id'])? $_POST['id'] : 0));
	}
	
	public function association(){
		
		$settings=$this->get_settings($domain='association');
		
		$association=new stdClass();
		$association->social_year_end=$settings->company_year;
		
	}
		
	//helpers
	public function difference_in_days($db_date1,$db_date2){
		$date1=new DateTime($db_date1);
		$date2=new DateTime($db_date2);
		return $date2->diff($date1)->format("%a");		
	}
	
	public static function file_system_string($name){
		$chars = array(
			// Decompositions for Latin-1 Supplement
			chr(194).chr(170) => 'a', chr(194).chr(186) => 'o',
			chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
			chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
			chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
			chr(195).chr(134) => 'AE',chr(195).chr(135) => 'C',
			chr(195).chr(136) => 'E', chr(195).chr(137) => 'E',
			chr(195).chr(138) => 'E', chr(195).chr(139) => 'E',
			chr(195).chr(140) => 'I', chr(195).chr(141) => 'I',
			chr(195).chr(142) => 'I', chr(195).chr(143) => 'I',
			chr(195).chr(144) => 'D', chr(195).chr(145) => 'N',
			chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
			chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
			chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
			chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
			chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
			chr(195).chr(158) => 'TH',chr(195).chr(159) => 's',
			chr(195).chr(160) => 'a', chr(195).chr(161) => 'a',
			chr(195).chr(162) => 'a', chr(195).chr(163) => 'a',
			chr(195).chr(164) => 'a', chr(195).chr(165) => 'a',
			chr(195).chr(166) => 'ae',chr(195).chr(167) => 'c',
			chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
			chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
			chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
			chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
			chr(195).chr(176) => 'd', chr(195).chr(177) => 'n',
			chr(195).chr(178) => 'o', chr(195).chr(179) => 'o',
			chr(195).chr(180) => 'o', chr(195).chr(181) => 'o',
			chr(195).chr(182) => 'o', chr(195).chr(184) => 'o',
			chr(195).chr(185) => 'u', chr(195).chr(186) => 'u',
			chr(195).chr(187) => 'u', chr(195).chr(188) => 'u',
			chr(195).chr(189) => 'y', chr(195).chr(190) => 'th',
			chr(195).chr(191) => 'y', chr(195).chr(152) => 'O',
			// Decompositions for Latin Extended-A
			chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
			chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
			chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
			chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
			chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
			chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
			chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
			chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
			chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
			chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
			chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
			chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
			chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
			chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
			chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
			chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
			chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
			chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
			chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
			chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
			chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
			chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
			chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
			chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
			chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
			chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
			chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
			chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
			chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
			chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
			chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
			chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
			chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
			chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
			chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
			chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
			chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
			chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
			chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
			chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
			chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
			chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
			chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
			chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
			chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
			chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
			chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
			chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
			chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
			chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
			chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
			chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
			chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
			chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
			chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
			chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
			chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
			chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
			chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
			chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
			chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
			chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
			chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
			chr(197).chr(190) => 'z', chr(197).chr(191) => 's',
			// Decompositions for Latin Extended-B
			chr(200).chr(152) => 'S', chr(200).chr(153) => 's',
			chr(200).chr(154) => 'T', chr(200).chr(155) => 't',
			// Euro Sign
			chr(226).chr(130).chr(172) => 'E',
			// GBP (Pound) Sign
			chr(194).chr(163) => '',
			// Vowels with diacritic (Vietnamese)
			// unmarked
			chr(198).chr(160) => 'O', chr(198).chr(161) => 'o',
			chr(198).chr(175) => 'U', chr(198).chr(176) => 'u',
			// grave accent
			chr(225).chr(186).chr(166) => 'A', chr(225).chr(186).chr(167) => 'a',
			chr(225).chr(186).chr(176) => 'A', chr(225).chr(186).chr(177) => 'a',
			chr(225).chr(187).chr(128) => 'E', chr(225).chr(187).chr(129) => 'e',
			chr(225).chr(187).chr(146) => 'O', chr(225).chr(187).chr(147) => 'o',
			chr(225).chr(187).chr(156) => 'O', chr(225).chr(187).chr(157) => 'o',
			chr(225).chr(187).chr(170) => 'U', chr(225).chr(187).chr(171) => 'u',
			chr(225).chr(187).chr(178) => 'Y', chr(225).chr(187).chr(179) => 'y',
			// hook
			chr(225).chr(186).chr(162) => 'A', chr(225).chr(186).chr(163) => 'a',
			chr(225).chr(186).chr(168) => 'A', chr(225).chr(186).chr(169) => 'a',
			chr(225).chr(186).chr(178) => 'A', chr(225).chr(186).chr(179) => 'a',
			chr(225).chr(186).chr(186) => 'E', chr(225).chr(186).chr(187) => 'e',
			chr(225).chr(187).chr(130) => 'E', chr(225).chr(187).chr(131) => 'e',
			chr(225).chr(187).chr(136) => 'I', chr(225).chr(187).chr(137) => 'i',
			chr(225).chr(187).chr(142) => 'O', chr(225).chr(187).chr(143) => 'o',
			chr(225).chr(187).chr(148) => 'O', chr(225).chr(187).chr(149) => 'o',
			chr(225).chr(187).chr(158) => 'O', chr(225).chr(187).chr(159) => 'o',
			chr(225).chr(187).chr(166) => 'U', chr(225).chr(187).chr(167) => 'u',
			chr(225).chr(187).chr(172) => 'U', chr(225).chr(187).chr(173) => 'u',
			chr(225).chr(187).chr(182) => 'Y', chr(225).chr(187).chr(183) => 'y',
			// tilde
			chr(225).chr(186).chr(170) => 'A', chr(225).chr(186).chr(171) => 'a',
			chr(225).chr(186).chr(180) => 'A', chr(225).chr(186).chr(181) => 'a',
			chr(225).chr(186).chr(188) => 'E', chr(225).chr(186).chr(189) => 'e',
			chr(225).chr(187).chr(132) => 'E', chr(225).chr(187).chr(133) => 'e',
			chr(225).chr(187).chr(150) => 'O', chr(225).chr(187).chr(151) => 'o',
			chr(225).chr(187).chr(160) => 'O', chr(225).chr(187).chr(161) => 'o',
			chr(225).chr(187).chr(174) => 'U', chr(225).chr(187).chr(175) => 'u',
			chr(225).chr(187).chr(184) => 'Y', chr(225).chr(187).chr(185) => 'y',
			// acute accent
			chr(225).chr(186).chr(164) => 'A', chr(225).chr(186).chr(165) => 'a',
			chr(225).chr(186).chr(174) => 'A', chr(225).chr(186).chr(175) => 'a',
			chr(225).chr(186).chr(190) => 'E', chr(225).chr(186).chr(191) => 'e',
			chr(225).chr(187).chr(144) => 'O', chr(225).chr(187).chr(145) => 'o',
			chr(225).chr(187).chr(154) => 'O', chr(225).chr(187).chr(155) => 'o',
			chr(225).chr(187).chr(168) => 'U', chr(225).chr(187).chr(169) => 'u',
			// dot below
			chr(225).chr(186).chr(160) => 'A', chr(225).chr(186).chr(161) => 'a',
			chr(225).chr(186).chr(172) => 'A', chr(225).chr(186).chr(173) => 'a',
			chr(225).chr(186).chr(182) => 'A', chr(225).chr(186).chr(183) => 'a',
			chr(225).chr(186).chr(184) => 'E', chr(225).chr(186).chr(185) => 'e',
			chr(225).chr(187).chr(134) => 'E', chr(225).chr(187).chr(135) => 'e',
			chr(225).chr(187).chr(138) => 'I', chr(225).chr(187).chr(139) => 'i',
			chr(225).chr(187).chr(140) => 'O', chr(225).chr(187).chr(141) => 'o',
			chr(225).chr(187).chr(152) => 'O', chr(225).chr(187).chr(153) => 'o',
			chr(225).chr(187).chr(162) => 'O', chr(225).chr(187).chr(163) => 'o',
			chr(225).chr(187).chr(164) => 'U', chr(225).chr(187).chr(165) => 'u',
			chr(225).chr(187).chr(176) => 'U', chr(225).chr(187).chr(177) => 'u',
			chr(225).chr(187).chr(180) => 'Y', chr(225).chr(187).chr(181) => 'y',
			// Vowels with diacritic (Chinese, Hanyu Pinyin)
			chr(201).chr(145) => 'a',
			// macron
			chr(199).chr(149) => 'U', chr(199).chr(150) => 'u',
			// acute accent
			chr(199).chr(151) => 'U', chr(199).chr(152) => 'u',
			// caron
			chr(199).chr(141) => 'A', chr(199).chr(142) => 'a',
			chr(199).chr(143) => 'I', chr(199).chr(144) => 'i',
			chr(199).chr(145) => 'O', chr(199).chr(146) => 'o',
			chr(199).chr(147) => 'U', chr(199).chr(148) => 'u',
			chr(199).chr(153) => 'U', chr(199).chr(154) => 'u',
			// grave accent
			chr(199).chr(155) => 'U', chr(199).chr(156) => 'u'
		);
		
		$string=str_replace("'"," ",$name);
		$string=strtr($string, $chars);//remove accents
		$string=preg_replace('/[^A-Za-z0-9_\- ]/','',$string);//remove special chars
		$string=preg_replace('/\s{2,}/',' ',$string);//remove double spaces
		return str_replace(' ','-',trim($string));
	}

    // ----- Helpers HTML
    public function htmlCheckbox($k,$v) {
        if ($v == 1) {
            $ret = '<input type="checkbox" id="'.$k.'" name="'.$k.'" checked="checked">';
        } else {
            $ret = '<input type="checkbox" id="'.$k.'" name="'.$k.'">';
        }
        return $ret;
    }

    public function protocol() {
        return isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 'https' : 'http';
    }

    // Rendering immagini in html per shortcode
    public function htmlImg($v,$n,$s) {
        $class="";
        $style="";
        if($n!=""){$class="class=\"$n\"";}
        if($s!=""){$style="style=\"$s\"";}
        $v_unserialize = unserialize($v);
        $ret="";
        if(is_array($v_unserialize)) {
            foreach ($v_unserialize as $i) {
                if(!$i!="") {
                    $ret .= "<img src=\"".$this->subdomain_root.$i."\" $class $style>";
                }
            }
        } else {
            if($v!="") {
                $ret = "<img src=\"".$this->subdomain_root.$v."\" $class $style>";
            }
        }
        return $ret;
    }
    // Rendering anchor in html per shortcode
    public function htmlA($v) {
        $v_unserialize = unserialize($v);
        if(is_array($v_unserialize)) {
            $ret = '';
            foreach ($v_unserialize as $i) {
                $ret .= "<p><a href=\"$i\" target=\"_blank\">$i</a></p>";
            }
        } else {
            $ret = "<p><a href=\"$v\" target=\"_blank\">$v</a></p>";
        }
        return $ret;
    }


/* ################### NEWMOD ANDREA ##################*/
    public static function getArrayValues($field,$data='',$params=null){
        global $app;
        $data_get = '';
        include $app->main_root.'/private/_common/assets/settings/intents/config_values_form.php';
        if($params!=null){ $field=$params; }
        if($data!=''){
            if(isset($data_arr[$field][$data])){ $data_get = $data_arr[$field][$data]; }
            else{ $data_get = 'Errore valore'; }
            return $data_get;
        }else{
            return $data_arr[$field];
        }
    }
    
    public static function getArrayMultipleValues($field,$data=''){
        global $app;
        //$data_get = '';
        $response=[];
        include $app->main_root.'/private/_common/assets/settings/intents/config_multiple_values_form.php';
        
        if($data!=''){
            $data_ser = unserialize($data);
            foreach($data_ser as $data_in){
                $response[] = $data_arr[$field][$data_in];
            }
            
            /*if(isset($data_arr[$field][$data])){  }
            else{ $data_get = 'Errore valore'; }*/
            return join(', ',$response);
        }else{
            return $data_arr[$field];
        }
    }

    public static function strip_p_tags($text=''){
        if($text!=null){ $text = preg_replace('%<p(.*?)>|</p>%s','',$text); }
        return $text;
    }
    
    public static function has_content_key(array $arr): bool  {
        //rsort( $arr );
        return isset($arr['OptGroup']);
    }
    
    public function check_fields_visibility(){ 
            $table = $_POST['table'];
            $filter_table = $_POST['filter_table'];
            $filter_value = $_POST['filter_value'];
            $response = '';

            $query = "SELECT table_fields FROM archy_art_settings WHERE table_name='".$table."'";
            if($filter_table != '' && $filter_value != ''){ $query.= ' AND  table_filter="'.$filter_table.'" AND  table_filter_value="'.$filter_value.'"'; }  
            $result = $this->db->query($query);

            if($result->num_rows > 0){
                $row = $result->fetch_array();
                $row2 = unserialize($row[0]);
                if(count($row2)>0){ $response = $row2; }
            }

            echo json_encode($response);
        }
    
   /* public static function getColumnsName($table){
        global $app;
        $db=$app->get_db();
        //$labels=[];
        $exclude = ['id', 'created_at', 'updated_at'];
        $query = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'archy_art_catalog' AND TABLE_SCHEMA = 'archymedia_dev'";
        $result = $db->query($query);
        $columns = [];

while ($row = $result->fetch_assoc()) {
    if (!in_array($columns, $exclude)) { $columns[] = $row['COLUMN_NAME']; }
}
        return $columns;
    }*/
        
    public function get_table_name(){
            global $app;
            return $app->intent->table;
    }
    
    public static function get_explode_data($delimiter,$data){
            $response = null;
            if($data!=''){ $response = explode($delimiter, $data); }
            return $response;
    }
    
    public static function format_single_select($data,$field,$table){ 
        global $app;
        $db=$app->get_db();
        $response=[];
        if(!empty($data) && $data!=0){             
            $query = "SELECT $field FROM $table where id=".$data;
            $result = $db->query($query); 
            if($result->num_rows > 0){
                $row = $result->fetch_array();
                $response[] = $row[0];
            }
        } return join(', ',$response);
    }

    public static function format_multiple_select($data,$field,$table){ 
        global $app;
        $db=$app->get_db();
        $response=[];
        if(!empty($data) && $data!=null){ 
            $data_ser = unserialize($data);
            foreach($data_ser as $data_in){
                $query = "SELECT $field FROM $table where id=".$data_in;
                $result = $db->query($query); 
                if($result->num_rows > 0){
                    $row = $result->fetch_array();
                    $response[] = $row[0];
                }
            }
        } return join(', ',$response);
    }
    
    public static function format_serialize_data($data,$type='link'){
        global $app;
        $StoreCloud = new StoreCloud();
        $response = '';
        if(!empty($data) && $data!=null){
            $data_ser = unserialize($data);
            foreach ($data_ser as $i) {
                if($type=='img'){
                    if(strpos($i, 'assets') === false){
                        $response .= "<br><img src=\"".$StoreCloud->getImageUrl($i,'public')."\" width=\"200\">";
                    }else{
                        $response .= "<br><img src=\"".$app->subdomain_root.$i."\" width=\"200\">";
                    }
                }else{
                    $response .= "<br><a href=\"$i\" target=\"_blank\">$i</a>";
                }
            }
        }
        return $response;
    }
	
    public function htmlCheckboxDef($v) {
        $ret='';
        if ($v == 1) {
            $ret = 'SI';
        } else {
            $ret = 'NO';
        }
        return $ret;
    }
    
    public function generaUsername($nome, $cognome) {

        // unisce nome e cognome
        $username = $nome . $cognome;
        // converte in minuscolo
        $username = mb_strtolower($username, 'UTF-8');
        // rimuove accenti (à -> a, è -> e, ecc.)
        $username = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $username);
        // elimina tutto ciò che NON è lettera o numero
        $username = preg_replace('/[^a-z0-9]/', '', $username);

        return $username;
    }
    
    public function explode_all_fields($p_intent, $p_ids) {
        $data='';
        if($p_ids!=null){
            global $app;
            $db=$app->get_db();
            $app_save=clone $app;
            require_once $this->main_root.'/private/_common/intents/'.$p_intent.'.php';
            $app->init();
            $data='<div class="multiple_select">';

            $res=$db->query("SELECT * FROM ".$p_intent." WHERE id IN (".implode(',',unserialize($p_ids)).")");
            while($obj = mysqli_fetch_object($res)) {
                $data.='<div class="multiple_select_rows">';
                foreach($obj as $k=>$v) {
                    if($k=="created_at" || $k=="updated_at") {
                        continue;
                    }
                    if($k=="id") {
                        $view=$app->load_view($v);
                    } else {
                        $label=$view->data[$p_intent.'.'.$k]['label'];
                        $classname="allfields_".$k;
                        if($app->intent->columns[$p_intent.'.'.$k]['type']=='file') {
                            $v=$app->htmlImg($v,$classname,""); // Gestire le classi css con "allfields_NOMECAMPO"
                        } else {
                            $v=$view->data[$p_intent.'.'.$k]['value'];
                        }
                        if($v!="") {
                            $data.='<div class="'.$classname.'">'.$label.': '.$v."</div>";
                        }
                    }
                }
                $data.="</div>";
            }
            $data.="</div>";

            $app=clone $app_save;
        }
        return $data;
    }
    
    public function regenerates_attachments(){
        global $app;
        $db=$app->get_db();
        
        $intent = $_POST['intent'];        
        
        switch ($intent) {
            case 'archy_events_sessions':
                $response = 'OK';
                $event_id = $_POST['event_id'];
                $session_id = $_POST['session_id'];
                $manage_pdf=new ManagePdf();
                $query_get=$db->query("SELECT id,subscriber_id,sessions FROM archy_events_subscriptions WHERE accounted!='' AND event_id='".$event_id."'");
                while($obj = mysqli_fetch_object($query_get)) {
                    $accounted=$obj->sessions;
                    if($this->is_serialized($accounted)):
                        $accounted_arr=unserialize($accounted);  
                        if($accounted_arr[$session_id]){
                            $new_file = $manage_pdf->init_pdf($obj->id,'custom_event',array('data_intent'=>'archy_events_subscriptions','session_id'=>$session_id,'subscriber_id'=>$obj->subscriber_id));                    
                           
                        }
                    endif;                    
                }
                echo $response;
                break;
            
            case 'archy_memberships':
                $response = 'OK';
                //$filename = $this->subdomain_root.'/log_prova.txt';
                //$handler = fopen($filename, 'w+');
                $membership=new Membership();
                $query_get=$db->query("SELECT id FROM archy_memberships WHERE status!=4"); // tutti tranne i cessati
                while($obj = mysqli_fetch_object($query_get)) {                    
                    $new_member = $membership->create_card($obj->id);
                    //fwrite($handler, "Associato ".$obj->id." - ".$new_member."\n");
                }
               // fclose($handler);
                echo $response;
                break;
            
            case 'archy_art_catalog':    
                $response = 'OK';
                $manage_pdf=new ManagePdf();
                $query_get=$db->query("SELECT id FROM archy_art_catalog");
                while($obj = mysqli_fetch_object($query_get)) {
                    $new_file = $manage_pdf->init_pdf($obj->id,'standard',array('data_intent'=>'archy_art_catalog'));
                }
                echo $response;
                break;
                
            default:
                break;
        }
        
        
    }
    
    /* ################################### */
    /* ####### ARCHY ART FUNCTIONS ####### */
    /* ################################### */
    
    public function getArtCatalogData($id=null,$field=''){
        if($id==null){ $id=$_POST['id']; } 
        $field_get = '*';
        if($field!=''){$field_get = $field;}
        $result=$this->db->query("SELECT $field_get FROM archy_art_catalog WHERE id = $id");

        $response = [];
        if($field!=''){
            $row = mysqli_fetch_assoc($result);
            return $row[$field];
        }else{
            while($rows=  mysqli_fetch_assoc($result)):
                $response[] = $rows;
            endwhile;
            echo json_encode($response);
        }
        
    }

} 

//helpers
function form_value($str){
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}
function set_message($class,$message){
	//@$_SESSION['system-message'].='<div class="alert alert-'.$class.' border-0 d-flex align-items-center" role="alert">'.$message.' <span class="glyphicon glyphicon-remove"></span></div>';
        switch ($class) {
            case 'danger':
                $message_icon = 'fa-times-circle';
                break;
            case 'warning':
                $message_icon = 'fa-exclamation-circle';
                break;
            case 'info':
                $message_icon = 'fa-info-circle';
                break;
            default:
                $message_icon = 'fa-check-circle';
                break;
        }       
        $_SESSION['system-message'].='
        <div class="alert alert-'.$class.' border-0 d-flex align-items-center system-message" role="alert">
            <div class="bg-'.$class.' me-3 icon-item"><span class="fas '.$message_icon.' text-white fs-6"></span></div>
            <p class="mb-0 flex-1">'.$message.'</p>
            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
}
function display_system_message(){
	if(isset($_SESSION['system-message'])):
		echo '<div id="system-message-wrapper">'.$_SESSION['system-message'].'</div>';
		unset($_SESSION['system-message']);
	endif;
}

function show_query_error($ret,$db,$q='',$where='Missing') {
    if($ret === FALSE) {
        set_message("warning",$db->error);
        error_log('@@@@@ --------------------- #### '.date("Y-m-d h:i:s").' ####');
        error_log('@@@@@ ERROR #### >'.print_r($db->error_list,true).'< #####');
        error_log('@@@@@ QUERY #### >'.$q.'< #####');
        error_log('@@@@@ WHERE #### >'.$where.'< #####');
        echo("<h1>Database query error!</h1><p>".$db->error."</p><p>".$q."</p>");
        exit();
    }
}
