<?php
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $main_root = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
    $subdomain_root = dirname(dirname(__FILE__));
    $child_template_root = $subdomain_root . '/admin_template_child';    
    $login_error_message = '';
    $forgot_password_message = '';
    $forgot_password_success = false;
    $forgot_password_email = '';
    $reset_password_message = '';
    $reset_password_success = false;
    $reset_password_redirect = false;
    $reset_password_token = $_GET['token'] ?? '';
    $login_otp_message = '';
    $login_otp_success = false;
    $login_otp_email = '';
    $login_otp_token = '';
    $force_password_email = '';
    $force_password_message = '';
    $force_password_success = false;
    $mode = str_replace('-', '_', $_GET['mode'] ?? 'login');
    $options = require $main_root . '/private/_common/config.php';
    $login_config = require $subdomain_root . '/config-local.php';
    $login_email_otp_enabled = !empty($login_config['login_email_otp']);
    $allowed_modes = ['login', 'forgot_password', 'reset_password', 'force_password_change'];

    if ($login_email_otp_enabled) {
        $allowed_modes[] = 'login_otp';
    }

    $login_mode = in_array($mode, $allowed_modes, true) ? $mode : 'login';
    
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
                $reset_password_redirect = $reset_password_success;
                $reset_password_message = $reset_password_success
                    ? 'Password reimpostata correttamente. A breve verrai reindirizzato alla pagina di login.'
                    : $reset['message'];
            } elseif ($form_action === 'force_password_change') {
                $login_mode = 'force_password_change';
                $force_password_email = $_POST['force_email'] ?? '';
                $forceChange = $app->auth->changePasswordByEmailStrict(
                    $force_password_email,
                    $_POST['old_password'] ?? '',
                    $_POST['new_password'] ?? '',
                    $_POST['new_password_repeat'] ?? ''
                );
                $force_password_success = empty($forceChange['error']);
                $reset_password_redirect = $force_password_success;
                $force_password_message = $force_password_success
                    ? 'Password aggiornata correttamente. A breve verrai reindirizzato alla pagina di login.'
                    : $forceChange['message'];
            } elseif ($form_action === 'login_otp') {
                $login_mode = 'login_otp';
                $login_otp_token = $_POST['otp_token'] ?? '';
                $login_otp_email = $_POST['otp_email'] ?? '';

                if (!$login_email_otp_enabled) {
                    $login_error_message = 'Accesso con codice email non attivo.';
                    $login_mode = 'login';
                } else {
                    $login = $app->auth->completeEmailOtpLogin(
                        $login_otp_token,
                        $_POST['otp_code'] ?? ''
                    );

                    if (!empty($login['error'])) {
                        $login_otp_message = $login['message'];
                        $login_otp_success = false;
                    } else {
                        $app->dashboardPage();
                        exit;
                    }
                }
            } else {
                if ($login_email_otp_enabled) {
                    $login = $app->auth->startEmailOtpLogin(
                        $_POST['username'] ?? '',
                        $_POST['password'] ?? ''
                    );
                } else {
                    $login = $app->login($_POST['username'], $_POST['password']);
                }

                if (!empty($login['force_password_change_required'])) {
                    $login_mode = 'force_password_change';
                    $force_password_email = $login['email'] ?? ($_POST['username'] ?? '');
                    $force_password_message = $login['message'];
                } elseif ($login['error']!='') {
                    $login_error_message = $login['message'];
                } else {
                    if ($login_email_otp_enabled) {
                        $login_mode = 'login_otp';
                        $login_otp_message = $login['message'];
                        $login_otp_success = true;
                        $login_otp_token = $login['otp_token'] ?? '';
                        $login_otp_email = $login['email'] ?? ($_POST['username'] ?? '');
                    } else {
                        $app->dashboardPage();
                        exit;
                    }
                }
            }
        } catch (Throwable $e) {
            if ($login_mode === 'forgot_password') {
                $forgot_password_message = $e->getMessage();
            } elseif ($login_mode === 'reset_password') {
                $reset_password_message = $e->getMessage();
            } elseif ($login_mode === 'force_password_change') {
                $force_password_message = $e->getMessage();
            } else {
                $login_error_message = $e->getMessage();
            }
        }
    }

    require_once $child_template_root . '/html/login.php';
