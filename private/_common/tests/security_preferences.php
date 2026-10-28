<?php
require_once __DIR__ . '/../libs/PHPAuth/vendor/autoload.php';
require_once __DIR__ . '/../classes/ArchyPHPAuth.php';

$db = new PDO('sqlite::memory:');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->exec('CREATE TABLE archy_user_security (uid INTEGER PRIMARY KEY, email_otp INTEGER, totp_otp INTEGER, phone TEXT)');
$reflection = new ReflectionClass(PHPAuth\ArchyAuth::class);
$auth = $reflection->newInstanceWithoutConstructor();
$dbh = new ReflectionProperty(PHPAuth\Auth::class, 'dbh');
$dbh->setAccessible(true);
$dbh->setValue($auth, $db);
$checks = 0;
$expect = static function ($actual, $expected, $label) use (&$checks) {
    if ($actual !== $expected) {
        throw new RuntimeException($label . ': ' . json_encode($actual));
    }
    $checks++;
};
$options = ['login_email_otp' => true, 'login_google_authenticator_otp' => true];
$auth->configureSecurity($options);
$expect($auth->securityMethods(1), ['email' => true, 'google' => true], 'Legacy defaults');
$db->exec("INSERT INTO archy_user_security VALUES (1, 1, 0, '+393331234567')");
$expect($auth->securityMethods(1), ['email' => true, 'google' => false], 'Email only');
$expect($auth->securityMethods(2), ['email' => true, 'google' => true], 'User isolation');
$expect($auth->securityPreferences(1)['phone'], '+393331234567', 'Phone saved');
$db->exec('UPDATE archy_user_security SET email_otp = 0, totp_otp = 1 WHERE uid = 1');
$expect($auth->securityMethods(1), ['email' => false, 'google' => true], 'Authenticator only');
$db->exec('UPDATE archy_user_security SET totp_otp = 0 WHERE uid = 1');
$expect($auth->securityMethods(1), ['email' => false, 'google' => false], 'Optional disabled');
$auth->configureSecurity($options + ['login_otp_required' => true]);
$expect($auth->securityMethods(1), ['email' => true, 'google' => true], 'Required overrides old opt-out');
$auth->configureSecurity(['login_email_otp' => true, 'login_otp_required' => true]);
$expect($auth->securityMethods(1), ['email' => true, 'google' => true], 'Required offers both user-configurable methods');
$auth->configureSecurity(['login_otp_required' => true]);
$expect($auth->securityMethods(1), ['email' => true, 'google' => true], 'Required without legacy flags');
$auth->configureSecurity(['login_email_otp' => false, 'login_google_authenticator_otp' => false]);
$db->exec('UPDATE archy_user_security SET email_otp = 1, totp_otp = 1 WHERE uid = 1');
$expect($auth->securityMethods(1), ['email' => true, 'google' => true], 'User activation overrides legacy defaults');
$expect($auth->securityMethods(2), ['email' => false, 'google' => false], 'Legacy defaults for unsaved users');
echo $checks . " security preference checks passed\n";
