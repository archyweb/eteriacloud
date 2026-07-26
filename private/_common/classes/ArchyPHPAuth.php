<?php

namespace PHPAuth;

use ZxcvbnPhp\Zxcvbn;

class ArchyAuth extends Auth
{
    public function __construct($dbh, Config $config) {
        parent::__construct($dbh, $config);
    }

    public function startEmailOtpLogin(string $email, string $password, int $remember = 0, string $captcha_response = ''): array
    {
        $email = trim(mb_strtolower($email, 'UTF-8'));
        $return = [
            'error' => true,
            'message' => 'Credenziali non valide.'
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

        if ($validateEmail['error'] == 1 || $validatePassword['error'] == 1 || ($remember != 0 && $remember != 1)) {
            $this->addAttempt();
            return $return;
        }

        $uid = $this->getUID($email);

        if (!$uid) {
            $this->addAttempt();
            return $return;
        }

        $user = $this->getBaseUser($uid);

        if (!$user || !$this->password_verify_with_rehash($password, $user['password'], $uid)) {
            $this->addAttempt();
            return $return;
        }

        if ($user['isactive'] != 1) {
            $this->addAttempt();
            $return['message'] = $this->__lang('account.not_activated');
            return $return;
        }

        $mailer = $this->config->mailerDriver;

        if (!is_object($mailer) || (method_exists($mailer, 'isConfigured') && !$mailer->isConfigured())) {
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
            $email,
            $selectorHash,
            password_hash($code, PASSWORD_DEFAULT),
            $remember,
            $expiresAt,
            self::getIp(),
            $agent
        ]);

        if (!$this->sendLoginOtpEmail($email, $code)) {
            $this->dbh->prepare("UPDATE archy_login_otps SET consumed_at = NOW() WHERE selector_hash = ?")->execute([$selectorHash]);
            $return['message'] = 'Impossibile inviare il codice di accesso. Riprova piu tardi.';
            return $return;
        }

        return [
            'error' => false,
            'message' => 'Ti abbiamo inviato un codice di accesso via email.',
            'otp_token' => $selector,
            'email' => $email
        ];
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
            'different' => [
                'label' => 'Diversa dalla password attuale',
                'passed' => $oldPassword === '' || !hash_equals($oldPassword, $password)
            ],
            'email' => [
                'label' => 'Non contiene la parte iniziale dell email',
                'passed' => $emailLocalPart === '' || mb_strlen($emailLocalPart, 'UTF-8') < 4 || strpos($passwordLower, $emailLocalPart) === false
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
                'passed' => true
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

        $response = $this->changePassword($uid, $oldPassword, $newPassword, $newPasswordRepeat);

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
