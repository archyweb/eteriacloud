<!DOCTYPE html>
<html data-bs-theme="light" lang="it-IT" dir="ltr">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- ===============================================-->
        <!--    Document Title-->
        <!-- ===============================================-->
        <title>Archy Art | Dashboard</title>

        <!-- ===============================================-->
        <!--    Favicons-->
        <!-- ===============================================-->
        <link rel="icon" type="image/png" href="/admin_template_child/img/favicons/archyart/favicon-96x96.png" sizes="96x96" />
        <link rel="icon" type="image/svg+xml" href="/admin_template_child/img/favicons/archyart/favicon.svg" />
        <link rel="shortcut icon" href="/admin_template_child/img/favicons/archyart/favicon.ico" />
        <link rel="apple-touch-icon" sizes="180x180" href="/admin_template_child/img/favicons/archyart/apple-touch-icon.png" />
        <link rel="manifest" href="/admin_template_child/img/favicons/archyart/site.webmanifest" />
        
        <meta name="theme-color" content="#ffffff">        
        <script src="/admin_template_child/js/config.js"></script>
        <script src="/admin_template/vendors/simplebar/simplebar.min.js"></script>
        
        <!-- ===============================================-->
        <!--    Stylesheets-->
        <!-- ===============================================-->
        
        <link href="/admin_template/vendors/select2/select2.min.css" rel="stylesheet">
        <link href="/admin_template/vendors/select2-bootstrap-5-theme/select2-bootstrap-5-theme.min.css" rel="stylesheet">
        <!--<link href="/admin_template/vendors/datatables.net-bs5/dataTables.bootstrap5.min.css" rel="stylesheet">-->
        <link href="/admin_template/vendors/datatables2/datatables.min.css" rel="stylesheet">
        <link href="/admin_template/vendors/prism/prism-okaidia.css" rel="stylesheet">
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700%7cPoppins:300,400,500,600,700,800,900&amp;display=swap" rel="stylesheet">
        <link href="/admin_template/vendors/simplebar/simplebar.min.css" rel="stylesheet">
        <link href="/admin_template/vendors/fullcalendar/main.min.css" rel="stylesheet">
        <link href="/admin_template/vendors/flatpickr/flatpickr.min.css" rel="stylesheet">
        <link href="/admin_template/vendors/choices/choices.min.css" rel="stylesheet" />
        <link href="/admin_template_child/css/jquery-ui.min.css" rel="stylesheet">
                
        <link href="/admin_template/assets/css/theme-rtl.css" rel="stylesheet" id="style-rtl">
        <link href="/admin_template/assets/css/theme.css" rel="stylesheet" id="style-default">
        <link href="/admin_template/assets/css/user-rtl.css" rel="stylesheet" id="user-style-rtl">
        <link href="/admin_template/assets/css/user.css" rel="stylesheet" id="user-style-default">        
        <link href="/admin_template_child/css/app.css?v=<?php echo filemtime($child_template_root . '/css/app.css'); ?>" rel="stylesheet" id="user-style-default">
        <link href="/admin_template_child/css/custom.css?v=<?php echo filemtime($child_template_root . '/css/custom.css'); ?>" rel="stylesheet" id="user-style-default">
        
        <style>body{}html{overflow-x:hidden}</style>
        <script>
            var isRTL = JSON.parse(localStorage.getItem('isRTL'));
            if (isRTL) {
              var linkDefault = document.getElementById('style-default');
              var userLinkDefault = document.getElementById('user-style-default');
              linkDefault.setAttribute('disabled', true);
              userLinkDefault.setAttribute('disabled', true);
              document.querySelector('html').setAttribute('dir', 'rtl');
            } else {
              var linkRTL = document.getElementById('style-rtl');
              var userLinkRTL = document.getElementById('user-style-rtl');
              linkRTL.setAttribute('disabled', true);
              userLinkRTL.setAttribute('disabled', true);
            }
        </script>

    </head>
    <body class="data-grid<?php if(isset($_GET['view']) && $_GET['view']=='dashboard'){ echo ' data-dashboard'; } ?>">