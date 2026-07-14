<?php 
    $main_root = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
    $subdomain_root = dirname(dirname(__FILE__));
    $child_template_root = $subdomain_root . '/admin_template_child';    
    $login_error_message = '';
    $forgot_password_message = '';
    $forgot_password_success = false;
    $forgot_password_email = '';
    $reset_password_message = '';
    $reset_password_success = false;
    $reset_password_token = $_GET['token'] ?? '';
    $mode = str_replace('-', '_', $_GET['mode'] ?? 'login');
    $allowed_modes = ['login', 'forgot_password', 'reset_password'];
    $login_mode = in_array($mode, $allowed_modes, true) ? $mode : 'login';
    $options = require $main_root . '/private/_common/config.php';
    
    if($options['maintenance']==true){
        header('location: /archy/maintenance.php');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require_once $main_root . '/private/_common/classes/load_classes.php';
        $form_action = $_POST['form_action'] ?? 'login';
        $login_mode = in_array($form_action, $allowed_modes, true) ? $form_action : 'login';

        try {
            $app = new App($subdomain_root);

            if ($form_action === 'forgot_password') {
                $forgot_password_email = $_POST['forgot_email'] ?? '';
                $mailer = $app->auth->config->mailerDriver;

                if (is_object($mailer) && method_exists($mailer, 'isConfigured') && !$mailer->isConfigured()) {
                    $forgot_password_message = 'Configurazione SMTP incompleta. Compila le impostazioni e-mail prima di attivare il recupero password.';
                } else {
                    $reset = $app->auth->requestReset($forgot_password_email, true);
                    $forgot_password_success = empty($reset['error']);
                    $forgot_password_message = $forgot_password_success
                        ? 'Se l indirizzo indicato e registrato riceverai una email con le istruzioni per reimpostare la password.'
                        : $reset['message'];
                }
            } elseif ($form_action === 'reset_password') {
                $reset_password_token = $_POST['reset_token'] ?? '';
                $reset = $app->auth->resetPassStrict(
                    $reset_password_token,
                    $_POST['new_password'] ?? '',
                    $_POST['new_password_repeat'] ?? ''
                );
                $reset_password_success = empty($reset['error']);
                $reset_password_message = $reset['message'];
            } else {
                $login = $app->login($_POST['username'], $_POST['password']);

                if ($login['error']!='') {
                    $login_error_message = $login['message'];
                } else {
                    $app->dashboardPage();
                    exit;
                }
            }
        } catch (Throwable $e) {
            if ($login_mode === 'forgot_password') {
                $forgot_password_message = $e->getMessage();
            } elseif ($login_mode === 'reset_password') {
                $reset_password_message = $e->getMessage();
            } else {
                $login_error_message = $e->getMessage();
            }
        }
    }

    require_once $child_template_root . '/html/login.php';
