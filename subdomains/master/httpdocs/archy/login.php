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
    $login_otp_choice_message = '';
    $login_otp_choice_email = '';
    $login_otp_choice_token = '';
    $login_totp_message = '';
    $login_totp_email = '';
    $login_totp_token = '';
    $login_totp_setup_required = false;
    $login_totp_secret = '';
    $login_totp_qr_data_uri = '';
    $force_password_email = '';
    $force_password_message = '';
    $force_password_success = false;
    $mode = str_replace('-', '_', $_GET['mode'] ?? 'login');
    $options = require $main_root . '/private/_common/config.php';
    $login_config = require $subdomain_root . '/config-local.php';
    $login_email_otp_enabled = true;
    $login_google_authenticator_otp_enabled = true;
    $login_otp_enabled = $login_email_otp_enabled || $login_google_authenticator_otp_enabled;
    $allowed_modes = ['login', 'forgot_password', 'reset_password', 'force_password_change'];

    if ($login_otp_enabled) {
        $allowed_modes[] = 'login_otp_choice';
    }

    if ($login_email_otp_enabled) {
        $allowed_modes[] = 'login_otp';
    }

    if ($login_google_authenticator_otp_enabled) {
        $allowed_modes[] = 'login_totp';
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
            // Validate before checking credentials or sending a login OTP.
            // Unknown actions also reach the password login branch below.
            if (!in_array($form_action, ['forgot_password', 'reset_password', 'force_password_change', 'login_otp'], true)) {
                if (!ArchyTurnstile::verify($_POST['cf-turnstile-response'] ?? '')) {
                    throw new RuntimeException('Verifica anti-bot non riuscita. Riprova completando il controllo di sicurezza.');
                }
            }

            $app = new App($subdomain_root);

            if (in_array($form_action, ['login_otp_choice', 'login_totp'], true)) {
                $choiceToken = $_POST['otp_choice_token'] ?? $_POST['totp_token'] ?? '';
                $choice = $_SESSION['archy_login_otp_choices'][hash('sha256', $choiceToken)] ?? null;
                if ($choice && ($choice['expires'] ?? 0) >= time()) {
                    $methods = $app->auth->securityMethods((int) $choice['uid']);
                    $login_email_otp_enabled = $methods['email'];
                    $login_google_authenticator_otp_enabled = $methods['google'];
                }
            }

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
            } elseif ($form_action === 'login_otp_choice') {
                $login_mode = 'login_otp_choice';
                $login_otp_choice_token = $_POST['otp_choice_token'] ?? '';
                $login_otp_choice_email = $_POST['otp_choice_email'] ?? '';
                $otp_method = $_POST['otp_method'] ?? '';

                if (!$login_otp_enabled) {
                    $login_error_message = 'Accesso con doppia autenticazione non attivo.';
                    $login_mode = 'login';
                } elseif ($otp_method === 'email') {
                    if (!$login_email_otp_enabled) {
                        $login_otp_choice_message = 'Accesso con codice email non attivo.';
                    } else {
                        $login = $app->auth->startEmailOtpLoginFromChoice($login_otp_choice_token);

                        if (!empty($login['error'])) {
                            $login_otp_choice_message = $login['message'];
                        } else {
                            $login_mode = 'login_otp';
                            $login_otp_message = $login['message'];
                            $login_otp_success = true;
                            $login_otp_token = $login['otp_token'] ?? '';
                            $login_otp_email = $login['email'] ?? $login_otp_choice_email;
                        }
                    }
                } elseif ($otp_method === 'google') {
                    if (!$login_google_authenticator_otp_enabled) {
                        $login_otp_choice_message = 'Accesso con Google Authenticator non attivo.';
                    } else {
                        $login = $app->auth->startTotpLoginFromChoice($login_otp_choice_token);
                        $login_mode = 'login_totp';
                        $login_totp_message = $login['message'] ?? '';
                        $login_totp_email = $login['email'] ?? $login_otp_choice_email;
                        $login_totp_token = $login['totp_token'] ?? $login_otp_choice_token;
                        $login_totp_setup_required = !empty($login['totp_setup_required']);
                        $login_totp_secret = $login['totp_secret'] ?? '';
                        $login_totp_qr_data_uri = $login['totp_qr_data_uri'] ?? '';
                    }
                } else {
                    $login_otp_choice_message = 'Scegli un metodo di verifica.';
                }
            } elseif ($form_action === 'login_totp') {
                $login_mode = 'login_totp';
                $login_totp_token = $_POST['totp_token'] ?? '';
                $login_totp_email = $_POST['totp_email'] ?? '';

                if (!$login_google_authenticator_otp_enabled) {
                    $login_error_message = 'Accesso con Google Authenticator non attivo.';
                    $login_mode = 'login';
                } else {
                    $login = $app->auth->completeTotpLogin(
                        $login_totp_token,
                        $_POST['totp_code'] ?? ''
                    );

                    if (!empty($login['error'])) {
                        $login_totp_message = $login['message'];
                        $prompt = $app->auth->startTotpLoginFromChoice($login_totp_token);

                        if (empty($prompt['error'])) {
                            $login_totp_email = $prompt['email'] ?? $login_totp_email;
                            $login_totp_token = $prompt['totp_token'] ?? $login_totp_token;
                            $login_totp_setup_required = !empty($prompt['totp_setup_required']);
                            $login_totp_secret = $prompt['totp_secret'] ?? '';
                            $login_totp_qr_data_uri = $prompt['totp_qr_data_uri'] ?? '';
                        }
                    } else {
                        $app->dashboardPage();
                        exit;
                    }
                }
            } else {
                if ($login_otp_enabled || !empty($login_config['login_otp_required'])) {
                    $login = $app->auth->prepareOtpLoginChoice(
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
                    if (!empty($login['authenticated'])) {
                        $app->dashboardPage();
                        exit;
                    } elseif ($login_otp_enabled) {
                        $login_email_otp_enabled = !empty($login['methods']['email']);
                        $login_google_authenticator_otp_enabled = !empty($login['methods']['google']);
                        $login_mode = 'login_otp_choice';
                        $login_otp_choice_message = $login['message'];
                        $login_otp_choice_token = $login['otp_choice_token'] ?? '';
                        $login_otp_choice_email = $login['email'] ?? ($_POST['username'] ?? '');
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
            } elseif ($login_mode === 'login_otp_choice') {
                $login_otp_choice_message = $e->getMessage();
            } elseif ($login_mode === 'login_totp') {
                $login_totp_message = $e->getMessage();
            } else {
                $login_error_message = $e->getMessage();
            }
        }
    }

    require_once $child_template_root . '/html/login.php';
