<?php
$securityUser = $app->currentUser();
if (!$securityUser || empty($securityUser['uid'])) {
    http_response_code(403);
    return;
}
$app->auth->configureSecurity($app->config);
if (empty($_SESSION['archy_security_csrf'])) {
    $_SESSION['archy_security_csrf'] = bin2hex(random_bytes(32));
}
$securityCsrf = $_SESSION['archy_security_csrf'];
$securityMessage = '';
$securitySuccess = false;
$securitySetup = null;
$securityOperation = 'operazione';
$escapeSecurity = static function ($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); };
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!hash_equals($securityCsrf, (string) ($_POST['security_csrf'] ?? ''))) {
            throw new RuntimeException('Richiesta scaduta. Riapri la pagina Sicurezza.');
        }
        $securityAction = $_POST['security_action'] ?? '';
        $securityOperation = 'salvataggio sicurezza';
        if ($securityAction === 'save') {
            $twoFactorEnabled = !empty($_POST['two_factor_enabled']) || !empty($app->config['login_otp_required']);
            if ($twoFactorEnabled && empty($_POST['email_otp']) && empty($_POST['totp_otp'])) {
                throw new RuntimeException('Seleziona almeno un metodo per attivare la doppia autenticazione.');
            }
            $app->auth->saveSecurityPreferences($_POST['security_password'] ?? '', $_POST['security_code'] ?? '',
                $twoFactorEnabled && !empty($_POST['email_otp']), $twoFactorEnabled && !empty($_POST['totp_otp']), $_POST['phone'] ?? '');
            $securityMessage = 'Impostazioni di sicurezza aggiornate.';
        } elseif ($securityAction === 'setup') {
            $setupPhone = preg_replace('/[\s().-]+/', '', trim($_POST['phone'] ?? ''));
            if ($setupPhone !== '' && !preg_match('/^\+[1-9][0-9]{7,14}$/D', $setupPhone)) {
                throw new RuntimeException('Inserisci il telefono con prefisso internazionale, ad esempio +393331234567.');
            }
            $securitySetup = $app->auth->beginAccountTotp($_POST['security_password'] ?? '', $_POST['security_code'] ?? '');
            $_SESSION['archy_security_setup']['preferences'] = ['email_otp' => !empty($_POST['email_otp']), 'phone' => $setupPhone];
            $securityMessage = 'Collegamento pronto per la conferma.';
        } elseif ($securityAction === 'confirm') {
            $app->auth->confirmAccountTotp($_POST['setup_code'] ?? '');
            $securityMessage = 'Authenticator app collegata e attivata.';
        } elseif ($securityAction === 'unlink') {
            $app->auth->unlinkAccountTotp($_POST['security_password'] ?? '', $_POST['security_code'] ?? '');
            $securityMessage = 'Authenticator app scollegata.';
        } elseif ($securityAction === 'password') {
            $result = $app->auth->changePasswordStrict((int) $securityUser['uid'], $_POST['old_password'] ?? '',
                $_POST['new_password'] ?? '', $_POST['new_password_repeat'] ?? '', $securityUser['email']);
            if (!empty($result['error'])) {
                throw new RuntimeException($result['message']);
            }
            $securityMessage = $result['message'];
            unset($_SESSION['archy_login_otp_choices'], $_SESSION['archy_security_setup']);
        } else {
            throw new RuntimeException('Operazione non valida.');
        }
        $securitySuccess = true;
    }
    $securityOperation = 'lettura preferenze';
    $securityPreferences = $app->auth->securityPreferences((int) $securityUser['uid']);
    $securityOperation = 'lettura Authenticator app';
    $securityTotp = $app->auth->accountTotpStatus();
    $securityMethods = $app->auth->securityMethods((int) $securityUser['uid']);
    $securityRequired = !empty($app->config['login_otp_required']);
    $securityEnabled = $securityRequired || $securityMethods['email'] || $securityMethods['google'];
} catch (Throwable $error) {
    $securitySuccess = false;
    if ($error instanceof PDOException) {
        $sqlState = (string) ($error->errorInfo[0] ?? $error->getCode());
        $driverCode = (int) ($error->errorInfo[1] ?? 0);
        $detail = (string) ($error->errorInfo[2] ?? $error->getMessage());
        $securityMessage = 'Errore database durante ' . $securityOperation . ' [' . $sqlState . '/' . $driverCode . '].';
        if ($driverCode === 1146 && preg_match("/Table '([^']+)'/i", $detail, $match)) {
            $tableParts = explode('.', $match[1]);
            $securityMessage .= ' Tabella mancante: ' . end($tableParts) . '.';
        } elseif ($driverCode === 1054 && preg_match("/Unknown column '([^']+)'/i", $detail, $match)) {
            $securityMessage .= ' Colonna mancante: ' . $match[1] . '.';
        } elseif (in_array($driverCode, [1044, 1045, 1142, 1143], true)) {
            $securityMessage .= ' Verifica i permessi dell utente database configurato nel sottodominio.';
        }
        error_log('[Sicurezza] ' . $securityOperation . ' [' . $sqlState . '/' . $driverCode . '] ' . $detail);
    } else {
        $securityMessage = $error->getMessage();
    }
}
?>
<style>
#security-page #change-password-form > .col-12.mb-3 { margin-bottom: 0 !important; }
#security-page #change-password-form h5 { padding: 0 !important; margin-bottom: 0; }
#security-page > .security-title { margin-top: 0; }
#security-page > .security-settings { margin-top: 4px; }
</style>
<div class="row g-3" id="security-page">
    <div class="col-12 mb-3">
        <div class="row">
            <div class="d-flex justify-content-end mt-1 mt-sm-0">
                <div class="btn-toolbar" role="toolbar" aria-label="Eteria toolbars">
                    <div class="btn-group me-2 mb-2" role="group">
                        <a class="btn btn-secondary" href="/archy/app.php?view=security" data-toggle="tooltip" data-placement="bottom" title="Aggiorna scheda"><span class="fas fa-sync"></span> Ricarica</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 pl-4 w-100 security-title">
        <h4 class="d-inline-block mb-0">Sicurezza</h4>
    </div>
    <?php if ($securityMessage !== ''): ?>
        <div class="col-12"><div role="status" class="alert alert-<?php echo $securitySuccess ? 'success' : 'danger'; ?> mb-0"><?php echo $escapeSecurity($securityMessage); ?></div></div>
    <?php endif; ?>
    <?php if (isset($securityPreferences, $securityTotp)): ?>
    <div class="col-12 border-bottom pt-2 pb-2 mb-2 security-settings">
        <form method="post" action="/archy/app.php?view=security" class="row g-3" id="security-settings-form">
            <input type="hidden" name="security_csrf" value="<?php echo $escapeSecurity($securityCsrf); ?>">
            <div class="col-12">
                <div class="d-flex flex-column align-items-start gap-2 mb-2">
                    <div class="form-check form-switch mb-0">
                        <?php if ($securityRequired): ?><input type="hidden" name="two_factor_enabled" value="1"><?php endif; ?>
                        <input class="form-check-input" type="checkbox" role="switch" id="security-enabled" name="two_factor_enabled" value="1" <?php echo $securityEnabled ? 'checked' : ''; ?> <?php echo $securityRequired ? 'disabled' : ''; ?>>
                        <label class="form-check-label fw-medium" for="security-enabled">Attiva doppia autenticazione</label>
                    </div>
                    <h5 class="mb-0">Doppia autenticazione</h5>
                </div>
                <p class="text-700 mb-0">La doppia autenticazione aggiunge una protezione importante al tuo account e aiuta a proteggere i dati da accessi non autorizzati e furti, anche se la password viene compromessa.</p>
                <?php if ($securityRequired): ?><p class="text-primary fs-10 mt-2 mb-0"><span class="fas fa-lock me-1"></span>Obbligatoria per questo gestionale: mantieni attivo almeno un metodo.</p><?php endif; ?>
            </div>
            <div class="col-12 col-lg-4 d-flex">
                <div class="card shadow-none border w-100" style="border-radius:8px;min-width:0">
                    <div class="card-body d-flex flex-column p-3">
                        <div class="d-flex align-items-center gap-2 mb-3"><span class="fab fa-whatsapp text-success fs-6"></span><h6 class="mb-0">WhatsApp</h6></div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="security-whatsapp" <?php echo $securityPreferences['phone'] !== '' ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="security-whatsapp">Configura numero WhatsApp</label>
                        </div>
                        <label class="form-label" for="security-phone">Numero di telefono</label>
                        <input class="form-control" type="tel" id="security-phone" name="phone" autocomplete="tel" maxlength="30" placeholder="+393331234567" value="<?php echo $escapeSecurity($securityPreferences['phone']); ?>">
                        <div class="form-text">Numero da verificare. L'invio OTP WhatsApp richiede l'attivazione del servizio.</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4 d-flex">
                <div class="card shadow-none border w-100" style="border-radius:8px;min-width:0">
                    <div class="card-body d-flex flex-column p-3">
                        <div class="d-flex align-items-center gap-2 mb-3"><span class="fas fa-envelope text-primary fs-6"></span><h6 class="mb-0">Email</h6></div>
                        <p class="text-700 fs-10">Ricevi un codice di accesso nella tua casella email.</p>
                        <div class="text-break mb-3"><?php echo $escapeSecurity($securityUser['email']); ?></div>
                        <div class="form-check form-switch mt-auto">
                            <input class="form-check-input" type="checkbox" role="switch" id="security-email" name="email_otp" value="1" data-security-method data-available="1" <?php echo $securityMethods['email'] ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="security-email">Attiva email</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4 d-flex">
                <div class="card shadow-none border w-100" style="border-radius:8px;min-width:0">
                    <div class="card-body d-flex flex-column p-3">
                        <div class="d-flex align-items-center gap-2 mb-3"><span class="fas fa-mobile-alt text-info fs-6"></span><h6 class="mb-0">Authenticator app</h6></div>
                        <p class="text-700 fs-10 mb-2">Genera i codici con un'app di autenticazione compatibile con lo standard TOTP.</p>
                        <p class="text-700 fs-10">Ad esempio: Google Authenticator, Microsoft Authenticator, Authy, 1Password o Bitwarden.</p>
                        <div class="text-700 fs-10 mb-3"><?php echo !empty($securityTotp['enabled']) ? 'App collegata' : 'App da collegare'; ?></div>
                        <div class="form-check form-switch mt-auto">
                            <input class="form-check-input" type="checkbox" role="switch" id="security-google" name="totp_otp" value="1" data-security-method data-available="1" <?php echo $securityMethods['google'] && !empty($securityTotp['enabled']) ? 'checked' : ''; ?> data-linked="<?php echo !empty($securityTotp['enabled']) ? '1' : '0'; ?>">
                            <label class="form-check-label" for="security-google">Attiva Authenticator app</label>
                        </div>
                        <?php if (empty($securityTotp['enabled'])): ?>
                        <button class="btn btn-outline-primary btn-sm mt-3" name="security_action" value="setup" type="submit" data-security-setup><span class="fas fa-link me-1"></span>Collega app</button>
                        <?php elseif (!empty($securityTotp['enabled'])): ?>
                        <button class="btn btn-outline-danger btn-sm mt-3" name="security_action" value="unlink" type="submit"><span class="fas fa-unlink me-1"></span>Scollega app</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php if (!empty($securityTotp['enabled'])): ?>
            <div class="col-12 col-lg-7">
                <label class="form-label" for="security-code">Codice Authenticator</label>
                <input class="form-control" id="security-code" name="security_code" type="text" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" required>
            </div>
            <?php endif; ?>
            <div class="col-12 d-flex flex-wrap gap-2">
                <button class="btn btn-primary" name="security_action" value="save" type="submit"><span class="fas fa-save me-1"></span>Salva impostazioni</button>
            </div>
        </form>
        <?php if ($securitySetup): ?>
        <form method="post" action="/archy/app.php?view=security" class="mt-2" style="max-width:420px">
            <input type="hidden" name="security_csrf" value="<?php echo $escapeSecurity($securityCsrf); ?>">
            <input type="hidden" name="security_action" value="confirm">
            <?php if ($securitySetup['qr'] !== ''): ?><img class="img-fluid d-block mb-3" width="240" height="240" src="<?php echo $escapeSecurity($securitySetup['qr']); ?>" alt="QR per Authenticator app"><?php endif; ?>
            <label class="form-label" for="security-secret">Chiave manuale</label>
            <input class="form-control mb-3" id="security-secret" value="<?php echo $escapeSecurity($securitySetup['secret']); ?>" readonly>
            <label class="form-label" for="security-setup-code">Codice Authenticator</label>
            <input class="form-control mb-3" id="security-setup-code" name="setup_code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" required>
            <button class="btn btn-primary" type="submit"><span class="fas fa-check me-1"></span>Conferma collegamento</button>
        </form>
        <?php elseif (!empty($_SESSION['archy_security_setup']) && ($_SESSION['archy_security_setup']['expires'] ?? 0) >= time()): ?>
        <form method="post" action="/archy/app.php?view=security" class="mt-3">
            <input type="hidden" name="security_csrf" value="<?php echo $escapeSecurity($securityCsrf); ?>">
            <input type="hidden" name="security_action" value="confirm">
            <label class="form-label" for="retry-setup-code">Conferma codice Authenticator</label>
            <input class="form-control mb-2" style="max-width:420px" id="retry-setup-code" name="setup_code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" required>
            <button class="btn btn-primary" type="submit">Conferma</button>
        </form>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    <div class="col-12 pt-2">
        <?php $passwordFormAction = '/archy/app.php?view=security'; require __DIR__ . '/password.php'; ?>
    </div>
</div>
<script>
(function () {
    var form = document.getElementById('security-settings-form');
    if (!form) return;
    var enabled = form.querySelector('#security-enabled');
    var whatsapp = form.querySelector('#security-whatsapp');
    var phone = form.querySelector('#security-phone');
    var google = form.querySelector('#security-google');
    function updateMethods() {
        form.querySelectorAll('[data-security-method]').forEach(function (control) {
            control.disabled = !enabled.checked || control.dataset.available !== '1';
        });
        form.querySelectorAll('[data-security-setup]').forEach(function (control) {
            control.disabled = !enabled.checked || !google.checked;
        });
        whatsapp.disabled = !enabled.checked;
        phone.readOnly = !enabled.checked || !whatsapp.checked;
    }
    form.addEventListener('submit', function (event) {
        if (event.submitter && event.submitter.value === 'save' && enabled.checked && google.checked && google.dataset.linked !== '1') {
            event.preventDefault();
            var setup = form.querySelector('[data-security-setup]');
            if (setup) form.requestSubmit(setup);
        }
    });
    google.addEventListener('change', updateMethods);
    whatsapp.addEventListener('change', updateMethods);
    enabled.addEventListener('change', updateMethods);
    updateMethods();
})();
</script>
