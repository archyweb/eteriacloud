<?php

session_start();

$main_root = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$subdomain_root = dirname(dirname(__FILE__));

require_once $main_root . '/private/_common/classes/load_classes.php';

$app = new App($subdomain_root);

if (!$app->isLogged()) {
    header('location: /archy/login.php');
    exit;
}

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

$google_calendar = new GoogleCalendarService($app);

if (!$google_calendar->isConfigured()) {
    set_message('warning', 'Google Calendar non e ancora configurato.');
    header('location: /archy/app.php?view=dashboard');
    exit;
}

$state = bin2hex(random_bytes(32));
$_SESSION['google_calendar_oauth_state'] = $state;

$signed_state = $google_calendar->buildState($state, ['popup' => !empty($_GET['popup'])]);
$auth_url = $google_calendar->buildAuthUrl($signed_state);
$auth_parts = parse_url($auth_url);
$auth_params = [];

if (!empty($auth_parts['query'])) {
    parse_str($auth_parts['query'], $auth_params);
}

header('Content-Type: text/html; charset=utf-8');
?>
<!doctype html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="robots" content="noindex,nofollow">
    <meta name="referrer" content="no-referrer">
    <title>Collega Google Calendar</title>
    <link rel="stylesheet" href="/admin_template/vendors/bootstrap/bootstrap.min.css">
    <style>
        body {
            min-height: 100vh;
            background: #f6f8fb;
            color: #1f2937;
        }

        .oauth-shell {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 16px;
        }

        .oauth-panel {
            width: 100%;
            max-width: 520px;
            background: #fff;
            border: 1px solid #dbe3ef;
            border-radius: 8px;
            box-shadow: 0 18px 44px rgba(31, 41, 55, 0.12);
            overflow: hidden;
        }

        .oauth-header {
            padding: 28px 28px 20px;
            border-bottom: 1px solid #edf2f7;
        }

        .oauth-brand {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 20px;
        }

        .oauth-brand-icon {
            width: 48px;
            height: 48px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #eef5ff;
        }

        .oauth-brand-icon img {
            width: 28px;
            height: 28px;
            object-fit: contain;
        }

        .oauth-body {
            padding: 24px 28px 28px;
        }

        .oauth-permission {
            display: flex;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid #edf2f7;
            font-size: 14px;
        }

        .oauth-permission:last-child {
            border-bottom: 0;
        }

        .oauth-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #2c7be5;
            flex: 0 0 auto;
            margin-top: 7px;
        }

        .google-mark {
            width: 18px;
            height: 18px;
            object-fit: contain;
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <main class="oauth-shell">
        <div class="oauth-panel">
            <section class="oauth-header">
                <div class="oauth-brand">
                    <div class="oauth-brand-icon">
                        <img src="/admin_template/assets/img/icons/calendar.png" alt="">
                    </div>
                    <div>
                        <div class="small text-muted">ArchyArt</div>
                        <h1 class="h4 mb-0">Collega Google Calendar</h1>
                    </div>
                </div>
                <p class="text-muted mb-0">Autorizza la sincronizzazione degli eventi creati nella dashboard con il tuo calendario Google.</p>
            </section>
            <section class="oauth-body">
                <div class="mb-4">
                    <div class="oauth-permission">
                        <span class="oauth-dot"></span>
                        <span>ArchyArt leggerà solo l'elenco dei calendari disponibili per scegliere quello da sincronizzare.</span>
                    </div>
                    <div class="oauth-permission">
                        <span class="oauth-dot"></span>
                        <span>Gli eventi creati nella dashboard verranno salvati anche nel calendario Google selezionato.</span>
                    </div>
                </div>
                <form class="d-grid gap-2" action="https://accounts.google.com/o/oauth2/v2/auth" method="get">
                    <?php foreach($auth_params as $auth_param_name => $auth_param_value){ ?>
                        <input type="hidden" name="<?php echo htmlspecialchars($auth_param_name, ENT_QUOTES, 'UTF-8'); ?>" value="<?php echo htmlspecialchars($auth_param_value, ENT_QUOTES, 'UTF-8'); ?>">
                    <?php } ?>
                    <button class="btn btn-primary" type="submit">
                        <img class="google-mark" src="/admin_template/assets/img/logos/google.png" alt="">Continua con Google
                    </button>
                    <a class="btn btn-outline-secondary" href="/archy/app.php?view=dashboard">Annulla</a>
                </form>
            </section>
        </div>
    </main>
</body>
</html>
