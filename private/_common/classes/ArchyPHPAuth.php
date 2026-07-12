<?php

namespace PHPAuth;

use ZxcvbnPhp\Zxcvbn;

class ArchyAuth extends Auth
{
    public function __construct($dbh, Config $config) {
        parent::__construct($dbh, $config);
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

        $return['error'] = true;
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
