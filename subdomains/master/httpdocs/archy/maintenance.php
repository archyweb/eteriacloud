<?php 
    $main_root = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
    $subdomain_root = dirname(dirname(__FILE__));
    $child_template_root = $subdomain_root . '/admin_template_child';
    
    require_once $main_root . '/private/_common/classes/load_classes.php';
    $app = new App($subdomain_root);
    require_once $child_template_root . '/html/maintenance.php'; 
?>