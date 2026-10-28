<?php

namespace PHPAuth;

use ZxcvbnPhp\Zxcvbn;

class ArchyAuth extends Auth
{
    private $securityOptions = [];

    public function configureSecurity(array $options): void
    {
        $this->securityOptions = $options;
    }

    public function securityPreferences(int $uid): array
    {
        $query = $this->dbh->prepare('SELECT email_otp, totp_otp, phone FROM archy_user_security WHERE uid = ?');
        $query->execute([$uid]);
        $saved = $query->fetch(\PDO::FETCH_ASSOC);
        return $saved ?: ['email_otp' => !empty($this->securityOptions['login_email_otp']),
            'totp_otp' => !empty($this->securityOptions['login_google_authenticator_otp']), 'phone' => ''];
    }

    public function securityMethods(int $uid): array
    {
        $preferences = $this->securityPreferences($uid);
        $email = !empty($preferences['email_otp']);
        $google = !empty($preferences['totp_otp']);
        if (!$email && !$google && !empty($this->securityOptions['login_otp_required'])) {
            $email = true;
            $google = true;
        }
        return ['email' => $email, 'google' => $google];
    }

    private function verifySecurityOwner(string $password, string $code): int
    {
        $user = $this->getCurrentUser();
        if (!$user || empty($user['uid'])) {
            throw new \RuntimeException('Sessione non valida.');
        }
        if ($password !== '') {
            $check = $this->verifyLoginCredentials($user['email'], $password, 0, '', true);
            if (!empty($check['error'])) {
                throw new \RuntimeException($check['message']);
            }
        }
        $uid = (int) $user['uid'];
        $account = $this->getBaseUser($uid);
        if (!$account || empty($account['isactive'])) {
            throw new \RuntimeException('Account non attivo.');
        }
        $row = $this->getTotpRow($uid);
        if ($row && !empty($row['enabled'])) {
            $counter = null;
            if ((!empty($row['locked_until']) && strtotime($row['locked_until']) > time()) ||
                !$this->verifyTotpCode($row['secret'], $code, $counter) ||
                ($row['last_used_counter'] !== null && $counter <= (int) $row['last_used_counter'])) {
                $this->registerTotpFailure($uid, (int) $row['failed_attempts']);
                throw new \RuntimeException('Codice Authenticator non valido, gia usato o temporaneamente bloccato.');
            }
            $this->registerTotpSuccess($uid, $counter);
        }
        return $uid;
    }

    public function saveSecurityPreferences(string $password, string $code, bool $email, bool $google, string $phone): void
    {
        $uid = $this->verifySecurityOwner($password, $code);
        $phone = preg_replace('/[\s().-]+/', '', trim($phone));
        if ($phone !== '' && !preg_match('/^\+[1-9][0-9]{7,14}$/D', $phone)) {
            throw new \RuntimeException('Inserisci il telefono con prefisso internazionale, ad esempio +393331234567.');
        }
        if (!$email && !$google && !empty($this->securityOptions['login_otp_required'])) {
            throw new \RuntimeException('La doppia autenticazione e obbligatoria: attiva almeno un metodo.');
        }
        $row = $this->getTotpRow($uid);
        if ($google && (!$row || empty($row['enabled']))) {
            throw new \RuntimeException('Collega e conferma Google Authenticator prima di attivarlo.');
        }
        $this->dbh->prepare('INSERT INTO archy_user_security (uid, email_otp, totp_otp, phone, updated_at) VALUES (?, ?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE email_otp = VALUES(email_otp), totp_otp = VALUES(totp_otp), phone = VALUES(phone), updated_at = NOW()')
            ->execute([$uid, (int) $email, (int) $google, $phone]);
        unset($_SESSION['archy_login_otp_choices']);
    }

    public function accountTotpStatus(): array
    {
        $user = $this->getCurrentUser();
        if (!$user || empty($user['uid'])) {
            throw new \RuntimeException('Sessione non valida.');
        }
        return ['enabled' => !empty(($this->getTotpRow((int) $user['uid']) ?: [])['enabled'])];
    }

    public function beginAccountTotp(string $password, string $code): array
    {
        $uid = $this->verifySecurityOwner($password, $code);
        if (!empty(($this->getTotpRow($uid) ?: [])['enabled'])) {
            throw new \RuntimeException('Google Authenticator e gia collegato.');
        }
        $secret = $this->generateTotpSecret();
        $this->savePendingTotpSecret($uid, $secret);
        $_SESSION['archy_security_setup'] = ['uid' => $uid, 'expires' => time() + 600, 'secret' => $secret];
        return ['secret' => $secret, 'qr' => $this->createTotpQrDataUri($this->getCurrentUser()['email'], $secret)];
    }

    public function unlinkAccountTotp(string $password, string $code): void
    {
        $uid = $this->verifySecurityOwner($password, $code);
        $preferences = $this->securityPreferences($uid);
        $email = !empty($preferences['email_otp']);
        if (!$email && !empty($this->securityOptions['login_otp_required'])) {
            throw new \RuntimeException('Attiva prima il codice email: la doppia autenticazione e obbligatoria.');
        }
        $this->dbh->beginTransaction();
        try {
            $this->dbh->prepare('INSERT INTO archy_user_security (uid, email_otp, totp_otp, phone, updated_at) VALUES (?, ?, 0, ?, NOW()) ON DUPLICATE KEY UPDATE totp_otp = 0, updated_at = NOW()')
                ->execute([$uid, (int) $preferences['email_otp'], $preferences['phone']]);
            $this->dbh->prepare('DELETE FROM archy_user_totp WHERE uid = ?')->execute([$uid]);
            $this->dbh->commit();
        } catch (\Throwable $error) {
            $this->dbh->rollBack();
            throw $error;
        }
        unset($_SESSION['archy_security_setup'], $_SESSION['archy_login_otp_choices']);
    }

    public function confirmAccountTotp(string $code): void
    {
        $user = $this->getCurrentUser();
        $setup = $_SESSION['archy_security_setup'] ?? [];
        if (!$user ||
            ($setup['uid'] ?? 0) !== (int) $user['uid'] || ($setup['expires'] ?? 0) < time()) {
            throw new \RuntimeException('Configurazione scaduta. Ripeti il collegamento.');
        }
        $row = $this->getTotpRow((int) $user['uid']);
        $counter = null;
        if (!$row || !empty($row['enabled']) || !hash_equals((string) $row['pending_secret'], $setup['secret']) ||
            (!empty($row['locked_until']) && strtotime($row['locked_until']) > time()) ||
            !$this->verifyTotpCode($setup['secret'], $code, $counter)) {
            if ($row) {
                $this->registerTotpFailure((int) $user['uid'], (int) $row['failed_attempts']);
            }
            throw new \RuntimeException('Codice non valido o temporaneamente bloccato.');
        }
        $this->confirmPendingTotpSecret((int) $user['uid'], $setup['secret'], $counter);
        $preferences = $this->securityPreferences((int) $user['uid']);
        if (isset($setup['preferences'])) {
            $preferences = $setup['preferences'];
        }
        $this->dbh->prepare('INSERT INTO archy_user_security (uid, email_otp, totp_otp, phone, updated_at) VALUES (?, ?, 1, ?, NOW()) ON DUPLICATE KEY UPDATE email_otp = VALUES(email_otp), totp_otp = 1, phone = VALUES(phone), updated_at = NOW()')
            ->execute([(int) $user['uid'], (int) $preferences['email_otp'], $preferences['phone']]);
        unset($_SESSION['archy_security_setup'], $_SESSION['archy_login_otp_choices']);
    }

    public function __construct($dbh, Config $config) {
        parent::__construct($dbh, $config);
    }

    public function login(string $email, string $password, int $remember = 0, string $captcha_response = ''): array
    {
        return $this->loginWithPasswordPolicy($email, $password, $remember, $captcha_response);
    }

    public function loginWithPasswordPolicy(string $email, string $password, int $remember = 0, string $captcha_response = ''): array
    {
        if ($this->securityOptions !== []) {
            return $this->prepareOtpLoginChoice($email, $password, $remember, $captcha_response);
        }
        $email = trim($email);
        $loginCheck = $this->verifyLoginCredentials($email, $password, $remember, $captcha_response);

        if (!empty($loginCheck['error'])) {
            return $loginCheck;
        }

        $accountEmail = $loginCheck['user']['email'] ?? $email;
        $passwordPolicy = $this->validateStrictPassword($password, $accountEmail);

        if (!$passwordPolicy['valid']) {
            return [
                'error' => true,
                'force_password_change_required' => true,
                'message' => 'La password attuale non rispetta i nuovi requisiti di sicurezza. Imposta una nuova password per accedere.',
                'email' => $accountEmail,
                'rules' => $passwordPolicy['rules']
            ];
        }

        $sessiondata = $this->addSession((int) $loginCheck['user']['uid'], (bool) $remember);

        if (!$sessiondata) {
            return [
                'error' => true,
                'hash' => '',
                'message' => $this->__lang('system.error') . ' #01'
            ];
        }

        return [
            'error' => false,
            'message' => $this->__lang('logged_in'),
            'hash' => $sessiondata['hash'],
            'expire' => $sessiondata['expire'],
            'cookie_name' => $this->config->cookie_name
        ];
    }

    public function startEmailOtpLogin(string $email, string $password, int $remember = 0, string $captcha_response = ''): array
    {
        $email = trim($email);
        $loginCheck = $this->verifyLoginCredentials($email, $password, $remember, $captcha_response, true);

        if (!empty($loginCheck['error'])) {
            return $loginCheck;
        }

        $accountEmail = $loginCheck['user']['email'] ?? $email;
        $passwordPolicy = $this->validateStrictPassword($password, $accountEmail);

        if (!$passwordPolicy['valid']) {
            return [
                'error' => true,
                'force_password_change_required' => true,
                'message' => 'La password attuale non rispetta i nuovi requisiti di sicurezza. Imposta una nuova password per accedere.',
                'email' => $accountEmail,
                'rules' => $passwordPolicy['rules']
            ];
        }

        return $this->createEmailOtpLogin((int) $loginCheck['user']['uid'], $accountEmail, $remember);
    }

    public function prepareOtpLoginChoice(string $email, string $password, int $remember = 0, string $captcha_response = ''): array
    {
        $email = trim($email);
        $loginCheck = $this->verifyLoginCredentials($email, $password, $remember, $captcha_response, true);

        if (!empty($loginCheck['error'])) {
            return $loginCheck;
        }

        $accountEmail = $loginCheck['user']['email'] ?? $email;
        $passwordPolicy = $this->validateStrictPassword($password, $accountEmail);

        if (!$passwordPolicy['valid']) {
            return [
                'error' => true,
                'force_password_change_required' => true,
                'message' => 'La password attuale non rispetta i nuovi requisiti di sicurezza. Imposta una nuova password per accedere.',
                'email' => $accountEmail,
                'rules' => $passwordPolicy['rules']
            ];
        }

        $methods = $this->securityMethods((int) $loginCheck['user']['uid']);
        if (!$methods['email'] && !$methods['google']) {
            $sessiondata = $this->addSession((int) $loginCheck['user']['uid'], (bool) $remember);
            return ['error' => !$sessiondata, 'authenticated' => (bool) $sessiondata,
                'message' => $sessiondata ? $this->__lang('logged_in') : $this->__lang('system.error')];
        }
        $selector = bin2hex(random_bytes(32));
        $selectorHash = hash('sha256', $selector);

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (empty($_SESSION['archy_login_otp_choices']) || !is_array($_SESSION['archy_login_otp_choices'])) {
            $_SESSION['archy_login_otp_choices'] = [];
        }

        foreach ($_SESSION['archy_login_otp_choices'] as $hash => $choice) {
            if (empty($choice['expires']) || (int) $choice['expires'] < time()) {
                unset($_SESSION['archy_login_otp_choices'][$hash]);
            }
        }

        $_SESSION['archy_login_otp_choices'][$selectorHash] = [
            'uid' => (int) $loginCheck['user']['uid'],
            'email' => $accountEmail,
            'remember' => (int) $remember,
            'expires' => time() + 600,
            'ip' => self::getIp()
        ];

        return [
            'error' => false,
            'message' => 'Scegli come ricevere il codice di accesso.',
            'otp_choice_token' => $selector,
            'methods' => $methods,
            'email' => $accountEmail
        ];
    }

    public function startEmailOtpLoginFromChoice(string $selector): array
    {
        $choice = $this->consumeOtpLoginChoice($selector, false);

        if (!$choice) {
            return [
                'error' => true,
                'message' => 'Sessione di verifica scaduta. Effettua nuovamente il login.'
            ];
        }

        if (!$this->securityMethods((int) $choice['uid'])['email']) {
            return ['error' => true, 'message' => 'Metodo email non attivo per questo utente.'];
        }

        $login = $this->createEmailOtpLogin((int) $choice['uid'], (string) $choice['email'], (int) $choice['remember']);

        if (empty($login['error'])) {
            $this->consumeOtpLoginChoice($selector);
        }

        return $login;
    }

    public function startTotpLoginFromChoice(string $selector): array
    {
        $choice = $this->consumeOtpLoginChoice($selector, false);

        if (!$choice) {
            return [
                'error' => true,
                'message' => 'Sessione di verifica scaduta. Effettua nuovamente il login.'
            ];
        }

        $uid = (int) $choice['uid'];
        $email = (string) $choice['email'];
        if (!$this->securityMethods($uid)['google']) {
            return ['error' => true, 'message' => 'Google Authenticator non attivo per questo utente.'];
        }
        $row = $this->getTotpRow($uid);

        if ($row && !empty($row['enabled']) && !empty($row['secret'])) {
            return [
                'error' => false,
                'message' => 'Inserisci il codice generato da Google Authenticator.',
                'email' => $email,
                'totp_token' => $selector,
                'totp_setup_required' => false
            ];
        }

        $secret = $row['pending_secret'] ?? '';

        if ($secret === '') {
            $secret = $this->generateTotpSecret();
            $this->savePendingTotpSecret($uid, $secret);
        }

        return [
            'error' => false,
            'message' => 'Scansiona il QR code con Google Authenticator e inserisci il codice generato.',
            'email' => $email,
            'totp_token' => $selector,
            'totp_setup_required' => true,
            'totp_secret' => $secret,
            'totp_qr_data_uri' => $this->createTotpQrDataUri($email, $secret)
        ];
    }

    public function completeTotpLogin(string $selector, string $otp): array
    {
        $return = [
            'error' => true,
            'message' => 'Codice non valido o scaduto.'
        ];

        $choice = $this->consumeOtpLoginChoice($selector, false);
        $otp = preg_replace('/\D+/', '', $otp);

        if (!$choice || strlen($otp) !== 6) {
            $this->addAttempt();
            return $return;
        }

        $uid = (int) $choice['uid'];
        if (!$this->securityMethods($uid)['google']) {
            return $return;
        }
        $row = $this->getTotpRow($uid);

        if (!$row) {
            $this->addAttempt();
            $return['message'] = 'Google Authenticator non configurato per questo utente.';
            return $return;
        }

        if (!empty($row['locked_until']) && strtotime($row['locked_until']) > time()) {
            $this->addAttempt();
            $return['message'] = 'Troppi tentativi non validi. Riprova piu tardi.';
            return $return;
        }

        $setupRequired = empty($row['enabled']);
        $secret = $setupRequired ? ($row['pending_secret'] ?? '') : ($row['secret'] ?? '');

        if ($secret === '') {
            $this->addAttempt();
            $return['message'] = 'Google Authenticator non configurato per questo utente.';
            return $return;
        }

        $counter = null;
        $valid = $this->verifyTotpCode($secret, $otp, $counter);

        if (!$valid || (!$setupRequired && $counter !== null && $row['last_used_counter'] !== null && $counter <= (int) $row['last_used_counter'])) {
            $this->registerTotpFailure($uid, (int) ($row['failed_attempts'] ?? 0));
            $this->addAttempt();
            return $return;
        }

        if ($setupRequired) {
            $this->confirmPendingTotpSecret($uid, $secret, $counter);
        } else {
            $this->registerTotpSuccess($uid, $counter);
        }

        $sessiondata = $this->addSession($uid, (bool) $choice['remember']);

        if (!$sessiondata) {
            $return['message'] = $this->__lang('system.error') . ' #01';
            return $return;
        }

        $this->consumeOtpLoginChoice($selector);

        return [
            'error' => false,
            'message' => $this->__lang('logged_in'),
            'hash' => $sessiondata['hash'],
            'expire' => $sessiondata['expire'],
            'cookie_name' => $this->config->cookie_name
        ];
    }

    private function createEmailOtpLogin(int $uid, string $accountEmail, int $remember = 0): array
    {
        $mailer = $this->config->mailerDriver;

        if (!is_object($mailer) || (method_exists($mailer, 'isConfigured') && !$mailer->isConfigured())) {
            $return['error'] = true;
            $return['message'] = 'Configurazione SMTP incompleta. Impossibile inviare il codice di accesso.';
            return $return;
        }

        $code = (string) random_int(100000, 999999);
        $selector = bin2hex(random_bytes(32));
        $selectorHash = hash('sha256', $selector);
        $expiresAt = date('Y-m-d H:i:s', time() + 600);
        $agent = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255);

        $this->dbh->prepare("
            UPDATE archy_login_otps
            SET consumed_at = NOW()
            WHERE uid = ?
            AND consumed_at IS NULL
        ")->execute([$uid]);

        $query = $this->dbh->prepare("
            INSERT INTO archy_login_otps
                (uid, email, selector_hash, otp_hash, remember, expires_at, ip, user_agent, created_at)
            VALUES
                (?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");

        $query->execute([
            $uid,
            $accountEmail,
            $selectorHash,
            password_hash($code, PASSWORD_DEFAULT),
            $remember,
            $expiresAt,
            self::getIp(),
            $agent
        ]);

        if (!$this->sendLoginOtpEmail($accountEmail, $code)) {
            $this->dbh->prepare("UPDATE archy_login_otps SET consumed_at = NOW() WHERE selector_hash = ?")->execute([$selectorHash]);
            $return['error'] = true;
            $return['message'] = 'Impossibile inviare il codice di accesso. Riprova piu tardi.';
            return $return;
        }

        return [
            'error' => false,
            'message' => 'Ti abbiamo inviato un codice di accesso via email.',
            'otp_token' => $selector,
            'email' => $accountEmail
        ];
    }

    private function consumeOtpLoginChoice(string $selector, bool $consume = true): ?array
    {
        $selector = trim($selector);

        if ($selector === '') {
            return null;
        }

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $selectorHash = hash('sha256', $selector);
        $choice = $_SESSION['archy_login_otp_choices'][$selectorHash] ?? null;

        if (!is_array($choice)) {
            return null;
        }

        if (empty($choice['expires']) || (int) $choice['expires'] < time()) {
            unset($_SESSION['archy_login_otp_choices'][$selectorHash]);
            return null;
        }

        if (!empty($choice['ip']) && $choice['ip'] !== self::getIp()) {
            unset($_SESSION['archy_login_otp_choices'][$selectorHash]);
            return null;
        }

        if ($consume) {
            unset($_SESSION['archy_login_otp_choices'][$selectorHash]);
        }

        return $choice;
    }

    private function getTotpRow(int $uid): ?array
    {
        $query = $this->dbh->prepare("
            SELECT uid, secret, pending_secret, enabled, last_used_counter, failed_attempts, locked_until
            FROM archy_user_totp
            WHERE uid = ?
            LIMIT 1
        ");
        $query->execute([$uid]);
        $row = $query->fetch(\PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    private function savePendingTotpSecret(int $uid, string $secret): void
    {
        $query = $this->dbh->prepare("
            INSERT INTO archy_user_totp
                (uid, pending_secret, enabled, failed_attempts, locked_until, created_at, updated_at)
            VALUES
                (?, ?, 0, 0, NULL, NOW(), NOW())
            ON DUPLICATE KEY UPDATE
                pending_secret = VALUES(pending_secret),
                failed_attempts = 0,
                locked_until = NULL,
                updated_at = NOW()
        ");
        $query->execute([$uid, $secret]);
    }

    private function confirmPendingTotpSecret(int $uid, string $secret, ?int $counter): void
    {
        $query = $this->dbh->prepare("
            UPDATE archy_user_totp
            SET secret = ?,
                pending_secret = NULL,
                enabled = 1,
                confirmed_at = NOW(),
                last_used_counter = ?,
                failed_attempts = 0,
                locked_until = NULL,
                updated_at = NOW()
            WHERE uid = ?
        ");
        $query->execute([$secret, $counter, $uid]);
    }

    private function registerTotpSuccess(int $uid, ?int $counter): void
    {
        $query = $this->dbh->prepare("
            UPDATE archy_user_totp
            SET last_used_counter = ?,
                failed_attempts = 0,
                locked_until = NULL,
                updated_at = NOW()
            WHERE uid = ?
        ");
        $query->execute([$counter, $uid]);
    }

    private function registerTotpFailure(int $uid, int $failedAttempts): void
    {
        $nextAttempts = min(5, $failedAttempts + 1);
        $lockedUntil = $nextAttempts >= 5 ? date('Y-m-d H:i:s', time() + 600) : null;

        $query = $this->dbh->prepare("
            UPDATE archy_user_totp
            SET failed_attempts = ?,
                locked_until = ?,
                updated_at = NOW()
            WHERE uid = ?
        ");
        $query->execute([$nextAttempts, $lockedUntil, $uid]);
    }

    private function generateTotpSecret(): string
    {
        return $this->base32Encode(random_bytes(20));
    }

    private function createTotpQrDataUri(string $email, string $secret): string
    {
        $qrLibrary = __DIR__ . '/../libs/phpqrcode/phpqrcode.php';
        $oldReporting = error_reporting();
        error_reporting($oldReporting & ~E_DEPRECATED & ~E_USER_DEPRECATED);

        try {
            if (!class_exists('\\QRcode', false) && is_readable($qrLibrary)) {
                require_once $qrLibrary;
            }

            if (!class_exists('\\QRcode', false)) {
                return '';
            }

            $uri = $this->buildTotpUri($email, $secret);
            $level = defined('QR_ECLEVEL_M') ? constant('QR_ECLEVEL_M') : 1;
            $tempFile = tempnam(sys_get_temp_dir(), 'archy_totp_qr_');

            if ($tempFile === false) {
                return '';
            }

            \QRcode::png($uri, $tempFile, $level, 5, 2);
            $png = file_get_contents($tempFile);
            @unlink($tempFile);

            return $png === false ? '' : 'data:image/png;base64,' . base64_encode($png);
        } finally {
            error_reporting($oldReporting);
        }
    }

    private function buildTotpUri(string $email, string $secret): string
    {
        $issuer = trim((string) ($this->config->site_name ?: 'Archy Art'));
        $label = rawurlencode($issuer . ':' . $email);
        $query = http_build_query([
            'secret' => $secret,
            'issuer' => $issuer,
            'algorithm' => 'SHA1',
            'digits' => 6,
            'period' => 30
        ], '', '&', PHP_QUERY_RFC3986);

        return 'otpauth://totp/' . $label . '?' . $query;
    }

    private function verifyTotpCode(string $secret, string $otp, ?int &$matchedCounter = null, int $window = 1): bool
    {
        $otp = preg_replace('/\D+/', '', $otp);

        if (strlen($otp) !== 6) {
            return false;
        }

        $timeCounter = (int) floor(time() / 30);

        for ($offset = -$window; $offset <= $window; $offset++) {
            $counter = $timeCounter + $offset;

            if ($counter < 0) {
                continue;
            }

            if (hash_equals($this->hotp($secret, $counter), $otp)) {
                $matchedCounter = $counter;
                return true;
            }
        }

        return false;
    }

    private function hotp(string $secret, int $counter): string
    {
        $key = $this->base32Decode($secret);
        $high = (int) floor($counter / 4294967296);
        $low = $counter & 0xFFFFFFFF;
        $binaryCounter = pack('N2', $high, $low);
        $hash = hash_hmac('sha1', $binaryCounter, $key, true);
        $offset = ord(substr($hash, -1)) & 0x0F;
        $part = substr($hash, $offset, 4);
        $value = unpack('N', $part)[1] & 0x7FFFFFFF;

        return str_pad((string) ($value % 1000000), 6, '0', STR_PAD_LEFT);
    }

    private function base32Encode(string $data): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $buffer = 0;
        $bitsLeft = 0;
        $encoded = '';

        for ($i = 0; $i < strlen($data); $i++) {
            $buffer = ($buffer << 8) | ord($data[$i]);
            $bitsLeft += 8;

            while ($bitsLeft >= 5) {
                $bitsLeft -= 5;
                $encoded .= $alphabet[($buffer >> $bitsLeft) & 31];
                $buffer &= (1 << $bitsLeft) - 1;
            }
        }

        if ($bitsLeft > 0) {
            $encoded .= $alphabet[($buffer << (5 - $bitsLeft)) & 31];
        }

        return $encoded;
    }

    private function base32Decode(string $secret): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = strtoupper(preg_replace('/[^A-Z2-7]/i', '', $secret));
        $buffer = 0;
        $bitsLeft = 0;
        $decoded = '';

        for ($i = 0; $i < strlen($secret); $i++) {
            $value = strpos($alphabet, $secret[$i]);

            if ($value === false) {
                continue;
            }

            $buffer = ($buffer << 5) | $value;
            $bitsLeft += 5;

            if ($bitsLeft >= 8) {
                $bitsLeft -= 8;
                $decoded .= chr(($buffer >> $bitsLeft) & 255);
                $buffer &= (1 << $bitsLeft) - 1;
            }
        }

        return $decoded;
    }

    public function completeEmailOtpLogin(string $selector, string $otp): array
    {
        $return = [
            'error' => true,
            'message' => 'Codice non valido o scaduto.'
        ];

        $selector = trim($selector);
        $otp = preg_replace('/\D+/', '', $otp);

        if ($selector === '' || strlen($otp) !== 6) {
            $this->addAttempt();
            return $return;
        }

        $query = $this->dbh->prepare("
            SELECT id, uid, otp_hash, remember, attempts, expires_at
            FROM archy_login_otps
            WHERE selector_hash = ?
            AND consumed_at IS NULL
            LIMIT 1
        ");
        $query->execute([hash('sha256', $selector)]);
        $otpRow = $query->fetch(\PDO::FETCH_ASSOC);

        if (!$otpRow) {
            $this->addAttempt();
            return $return;
        }

        if (strtotime($otpRow['expires_at']) < time() || (int) $otpRow['attempts'] >= 5) {
            $this->addAttempt();
            return $return;
        }

        if (!password_verify($otp, $otpRow['otp_hash'])) {
            $this->dbh->prepare("UPDATE archy_login_otps SET attempts = attempts + 1 WHERE id = ?")->execute([$otpRow['id']]);
            $this->addAttempt();
            return $return;
        }

        if (!$this->securityMethods((int) $otpRow['uid'])['email']) {
            return $return;
        }
        $sessiondata = $this->addSession((int) $otpRow['uid'], (bool) $otpRow['remember']);

        if (!$sessiondata) {
            $return['message'] = $this->__lang('system.error') . ' #01';
            return $return;
        }

        $this->dbh->prepare("UPDATE archy_login_otps SET consumed_at = NOW() WHERE id = ?")->execute([$otpRow['id']]);

        return [
            'error' => false,
            'message' => $this->__lang('logged_in'),
            'hash' => $sessiondata['hash'],
            'expire' => $sessiondata['expire'],
            'cookie_name' => $this->config->cookie_name
        ];
    }

    private function sendLoginOtpEmail(string $email, string $code): bool
    {
        $siteName = $this->config->site_name ?: 'Archy Art';
        $subject = $siteName . ' - Codice di accesso';
        $body = '<p>Il tuo codice di accesso e:</p>'
            . '<p style="font-size:24px;font-weight:bold;letter-spacing:3px;">' . htmlspecialchars($code, ENT_QUOTES, 'UTF-8') . '</p>'
            . '<p>Il codice scade tra 10 minuti. Se non hai richiesto tu l accesso, ignora questa email.</p>';
        $altBody = "Il tuo codice di accesso e: " . $code . "\nIl codice scade tra 10 minuti.";

        return $this->config->mailerDriver->send($email, $subject, $body, $altBody);
    }

    private function verifyLoginCredentials(string $email, string $password, int $remember = 0, string $captcha_response = '', bool $genericMessage = false): array
    {
        $return = [
            'error' => true,
            'hash' => '',
            'message' => $genericMessage ? 'Credenziali non valide.' : ''
        ];

        $block_status = $this->isBlocked();

        if ($block_status == 'verify' && !$this->checkCaptcha($captcha_response)) {
            $return['message'] = $this->__lang('captcha.verify_code_invalid');
            return $return;
        }

        if ($block_status == 'block') {
            $return['message'] = $this->__lang('user.temporary_banned');
            return $return;
        }

        $validateEmail = $this->validateEmail($email);
        $validatePassword = $this->validatePasswordLength($password);

        if ($validateEmail['error'] == 1) {
            $this->addAttempt();
            $return['message'] = $genericMessage ? 'Credenziali non valide.' : $validateEmail['message'];
            return $return;
        }

        if ($validatePassword['error'] == 1) {
            $this->addAttempt();
            $return['message'] = $genericMessage ? 'Credenziali non valide.' : $validatePassword['message'];
            return $return;
        }

        if ($remember != 0 && $remember != 1) {
            $this->addAttempt();
            $return['message'] = $genericMessage ? 'Credenziali non valide.' : $this->__lang('remember_me_invalid');
            return $return;
        }

        $uid = $this->getUID($email);

        if (!$uid) {
            $this->addAttempt();
            $return['message'] = $genericMessage ? 'Credenziali non valide.' : $this->__lang('account.not_found');
            return $return;
        }

        $user = $this->getBaseUser($uid);

        if (!$user || !$this->password_verify_with_rehash($password, $user['password'], $uid)) {
            $this->addAttempt();
            $return['message'] = $genericMessage ? 'Credenziali non valide.' : $this->__lang('account.no_pair_user_and_password');
            return $return;
        }

        if ($user['isactive'] != 1) {
            $this->addAttempt();
            $return['message'] = $this->__lang('account.not_activated');
            return $return;
        }

        return [
            'error' => false,
            'user' => $user
        ];
    }

    public function addOrUpdateUser($old_email, $email, $password) {
        $email_activation = false;
        $userUID = $this->getUID($old_email);
        if (!$userUID) {
            $res = $this->addUser($email, $password, [], $email_activation);
            if ($res['error'] != 0) {
                $return['error'] = true;
                $return['message'] = $res['message'];
                return $return;
            }
        } else {
            if ($old_email != $email) {
                $res = $this->archyUpdateEmail($userUID, $email);
                if ($res['error'] != 0) {
                    $return['error'] = true;
                    $return['message'] = $res['message'];
                    return $return;
                }
            }

            if ($password) {
                $res = $this->forceChangePassword($userUID, $password);
                if ($res['error'] != 0) {
                    $return['error'] = true;
                    $return['message'] = $res['message'];
                    return $return;
                }
            }
        }

        $return['error'] = false;
        $return['message'] = $this->__lang("password_changed");

        return $return;
    }

    public function archyUpdateEmail($uid, $email) {
        $return['error'] = true;

        if ($this->isEmailTaken($email)) {
            $this->addAttempt();
            $return['message'] = $this->__lang("email_taken");

            return $return;
        }

        $user = $this->getBaseUser($uid);

        if (!$user) {
            $this->addAttempt();
            $return['message'] = $this->__lang("system_error") . " #14";

            return $return;
        }

        if ($email == $user['email']) {
            $this->addAttempt();
            $return['message'] = $this->__lang("newemail_match");

            return $return;
        }

        $query = "UPDATE {$this->config->table_users} SET email = ? WHERE id = ?";
        $query_prepared = $this->dbh->prepare($query);
        $query_prepared->execute([$email, $uid]);

        if ($query_prepared->rowCount() == 0) {
            $return['message'] = $this->__lang("system_error") . " #15";

            error_log("error");

            return $return;
        }

        $return['error'] = false;
        $return['message'] = $this->__lang("email_changed");

        return $return;
    }

    private function forceChangePassword($uid, $newpass) {
        $block_status = $this->isBlocked();

        if ($block_status == "block") {
            $return['message'] = $this->__lang("user_blocked");

            return $return;
        }

        /*
        // FIXME: implementare anche in javascript
        $zxcvbn = new Zxcvbn();

        if ($zxcvbn->passwordStrength($newpass)['score'] < intval($this->config->password_min_score)) {
            $return['message'] = $this->__lang('password_weak');

            return $return;
        }
        */

        $user = $this->getBaseUser($uid);

        if (!$user) {
            $this->addAttempt();
            $return['message'] = $this->__lang("system_error") . " #13";

            return $return;
        }

        $newpass = $this->getHash($newpass);

        $query = "UPDATE {$this->config->table_users} SET password = ? WHERE id = ?";
        $query_prepared = $this->dbh->prepare($query);
        $query_prepared->execute([$newpass, $uid]);

        $return['error'] = false;
        $return['message'] = $this->__lang("password_changed");

        return $return;
    }


    public function logoutUser() {
        $hash = $this->getCurrentSessionHash();
        $cookieName = $this->config->cookie_name;

        if ($hash !== '') {
            $this->logout($hash);
        }

        if ($this->config->uses_session && session_status() === PHP_SESSION_ACTIVE) {
            unset($_SESSION[$cookieName]);
            unset($_SESSION[$cookieName . '_expire']);
        }

        $cookieOptions = [
            'expires' => time() - 3600,
            'path' => $this->config->cookie_path ?: '/',
            'domain' => $this->config->cookie_domain ?: '',
            'secure' => (bool) $this->config->cookie_secure,
            'httponly' => (bool) $this->config->cookie_http,
            'samesite' => $this->config->cookie_samesite ?? 'Lax'
        ];

        unset($_COOKIE[$cookieName]);
        setcookie($cookieName, '', $cookieOptions);
    }

    public function isLoggedAuth() {
        return $this->checkSession($this->getCurrentSessionHash());
    }

    public function validateStrictPassword(string $password, string $email = '', string $oldPassword = ''): array {
        $passwordLower = mb_strtolower($password, 'UTF-8');
        $emailLocalPart = '';
        $siteName = (string) ($this->config->site_name ?? '');
        $siteTokens = preg_split('/[^A-Za-z0-9]+/', $siteName, -1, PREG_SPLIT_NO_EMPTY);
        $siteTokens = array_filter(array_map(function ($term) {
            $term = mb_strtolower($term, 'UTF-8');
            return mb_strlen($term, 'UTF-8') >= 4 ? $term : '';
        }, is_array($siteTokens) ? $siteTokens : []));
        $containsSiteTerm = false;

        foreach ($siteTokens as $siteToken) {
            if (strpos($passwordLower, $siteToken) !== false) {
                $containsSiteTerm = true;
                break;
            }
        }

        if ($email !== '' && strpos($email, '@') !== false) {
            $emailLocalPart = mb_strtolower(substr($email, 0, strpos($email, '@')), 'UTF-8');
        }

        $rules = [
            'length' => [
                'label' => 'Almeno 12 caratteri',
                'passed' => mb_strlen($password, 'UTF-8') >= 12
            ],
            'lowercase' => [
                'label' => 'Almeno una lettera minuscola',
                'passed' => (bool) preg_match('/[a-z]/', $password)
            ],
            'uppercase' => [
                'label' => 'Almeno una lettera maiuscola',
                'passed' => (bool) preg_match('/[A-Z]/', $password)
            ],
            'number' => [
                'label' => 'Almeno un numero',
                'passed' => (bool) preg_match('/[0-9]/', $password)
            ],
            'special' => [
                'label' => 'Almeno un carattere speciale',
                'passed' => (bool) preg_match('/[^A-Za-z0-9\s]/', $password)
            ],
            'spaces' => [
                'label' => 'Nessuno spazio',
                'passed' => !preg_match('/\s/', $password)
            ],
            'pattern' => [
                'label' => 'Non usa parole con simbolo e numeri finali',
                'passed' => !preg_match('/^[A-Za-z]{4,}[^A-Za-z0-9\s][0-9]{2,4}$/', $password)
            ],
            'different' => [
                'label' => 'Diversa dalla password attuale',
                'passed' => $oldPassword === '' || !hash_equals($oldPassword, $password)
            ],
            'email' => [
                'label' => 'Non contiene la parte iniziale dell email',
                'passed' => $emailLocalPart === '' || mb_strlen($emailLocalPart, 'UTF-8') < 4 || strpos($passwordLower, $emailLocalPart) === false
            ],
            'site' => [
                'label' => 'Non contiene il nome del cliente',
                'passed' => !$containsSiteTerm
            ]
        ];

        try {
            $zxcvbn = new Zxcvbn();
            $minimumScore = max(3, (int) $this->config->password_min_score);
            $rules['strength'] = [
                'label' => 'Complessita adeguata',
                'passed' => $zxcvbn->passwordStrength($password)['score'] >= $minimumScore
            ];
        } catch (\Throwable $e) {
            $rules['strength'] = [
                'label' => 'Complessita adeguata',
                'passed' => false
            ];
        }

        $failedRules = array_filter($rules, function ($rule) {
            return !$rule['passed'];
        });

        return [
            'valid' => count($failedRules) === 0,
            'rules' => $rules,
            'message' => count($failedRules) === 0 ? 'Password valida.' : 'La nuova password non rispetta tutti i requisiti.'
        ];
    }

    public function changePasswordStrict(int $uid, string $oldPassword, string $newPassword, string $newPasswordRepeat, string $email = ''): array {
        $return = [
            'error' => true,
            'message' => $this->__lang('system_error')
        ];
        $block_status = $this->isBlocked();

        if ($block_status == 'block') {
            $return['message'] = $this->__lang('user_blocked');
            return $return;
        }

        if ($newPassword !== $newPasswordRepeat) {
            $return['message'] = 'Le nuove password non coincidono.';
            return $return;
        }

        $user = $this->getBaseUser($uid);

        if (!$user) {
            $this->addAttempt();
            $return['message'] = $this->__lang('system_error') . ' #13';
            return $return;
        }

        if ($email === '') {
            $email = $user['email'] ?? '';
        }

        $validation = $this->validateStrictPassword($newPassword, $email, $oldPassword);

        if (!$validation['valid']) {
            return [
                'error' => true,
                'message' => $validation['message'],
                'rules' => $validation['rules']
            ];
        }

        if (!password_verify($oldPassword, $user['password'])) {
            $this->addAttempt();
            $return['message'] = $this->__lang('password_incorrect');
            return $return;
        }

        $newPassword = self::getHash($newPassword, $this->config->bcrypt_cost);
        $query = "UPDATE {$this->config->table_users} SET password = ? WHERE id = ?";
        $query_prepared = $this->dbh->prepare($query);
        $query_prepared->execute([$newPassword, $uid]);

        $return['error'] = false;
        $return['message'] = $this->__lang('password_changed');

        return $return;
    }

    public function changePasswordByEmailStrict(string $email, string $oldPassword, string $newPassword, string $newPasswordRepeat): array {
        $email = trim(mb_strtolower($email, 'UTF-8'));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'error' => true,
                'message' => 'Inserisci un indirizzo email valido.'
            ];
        }

        $uid = $this->getUID($email);

        if ($uid === 0) {
            return [
                'error' => true,
                'message' => 'Account non trovato.'
            ];
        }

        if ($newPassword !== $newPasswordRepeat) {
            return [
                'error' => true,
                'message' => 'Le nuove password non coincidono.'
            ];
        }

        $validation = $this->validateStrictPassword($newPassword, $email, $oldPassword);

        if (!$validation['valid']) {
            return [
                'error' => true,
                'message' => $validation['message'],
                'rules' => $validation['rules']
            ];
        }

        $response = $this->changePasswordStrict($uid, $oldPassword, $newPassword, $newPasswordRepeat, $email);

        if (empty($response['error'])) {
            $this->logoutAll($uid);
        }

        return $response;
    }

    public function resetPassStrict(string $key, string $password, string $repeatPassword): array {
        if ($password !== $repeatPassword) {
            return [
                'error' => true,
                'message' => 'Le nuove password non coincidono.'
            ];
        }

        $request = $this->getRequest($key, 'reset');

        if (!empty($request['error'])) {
            return $request;
        }

        $user = $this->getUser((int) $request['uid']);
        $validation = $this->validateStrictPassword($password, $user['email'] ?? '');

        if (!$validation['valid']) {
            return [
                'error' => true,
                'message' => $validation['message'],
                'rules' => $validation['rules']
            ];
        }

        $response = $this->resetPass($key, $password, $repeatPassword);

        if (empty($response['error'])) {
            $this->logoutAll((int) $request['uid']);
        }

        return $response;
    }
}
