<?php

session_start();
$main_root = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$subdomain_root = dirname(dirname(__FILE__));
$child_template_root = $subdomain_root . '/admin_template_child';
require_once $main_root . '/private/_common/classes/load_classes.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$app = new App($subdomain_root);

if (!$app->isLogged()) {
    echo "<script>top.window.location = '/archy/login.php'</script>";
    die;
}

if (isset($_REQUEST['do_action'])):
    $method = $_REQUEST['do_action'];
    $app->$method();   
else:
    if (isset($_GET['intent'])):
        require_once $main_root . '/private/_common/intents/' . $_GET['intent'] . '.php';
        $app->init();
    endif;
    ?>

    <?php include_once $child_template_root . '/html/header_app.php'; ?>
        
    <?php
        display_system_message();
        require_once $main_root . '/private/_common/views/' . $_GET['view'] . '.php';
    ?>
    
    <?php include_once $child_template_root . '/html/modal.php'; ?>

    <?php include_once $child_template_root . '/html/footer_app.php'; ?>

   
<?php endif; ?>
