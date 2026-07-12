<?php
$main_root = dirname(dirname(dirname(dirname(__FILE__))));
$subdomain_root = dirname(__FILE__);
$child_template_root = $subdomain_root . '/admin_template_child';
require_once $main_root . '/private/_common/classes/load_classes.php';
$app = new App($subdomain_root);
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/
if($app->options['maintenance']==true){$app->maintenancePage();}

if (!$app->isLogged()) {
   $app->loginPage();
   exit();
}

if (isset($_REQUEST['do_action'])):
    $method = $_REQUEST['do_action'];
    $app->$method();
else:
    ?>

    <?php include_once $child_template_root . '/html/header_index.php'; ?>

    <?php require_once $child_template_root . '/html/main.php'; ?>

    <?php include_once $child_template_root . '/html/footer_index.php'; ?>
      
    
<?php endif; ?>