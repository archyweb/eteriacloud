<?php

require_once __DIR__ . '/ArchyEnv.php';

class ArchyTurnstile
{
    public static function verify($token)
    {
        if (!is_string($token) || trim($token) === '' || strlen($token) > 2048) {
            return false;
        }

        $secret = ArchyEnv::value('CLOUDFLARE_TURNSTILE_SECRETKEY');
        if ($secret === '' || !function_exists('curl_init')) {
            error_log('Turnstile: secret key or PHP cURL extension missing.');
            return false;
        }

        $handle = curl_init('https://challenges.cloudflare.com/turnstile/v0/siteverify');
        if ($handle === false) {
            return false;
        }

        curl_setopt_array($handle, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query(['secret' => $secret, 'response' => $token]),
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);

        $response = curl_exec($handle);
        $status = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        curl_close($handle);

        if ($response === false || $status !== 200) {
            error_log('Turnstile: Siteverify request failed.');
            return false;
        }

        $result = json_decode($response, true);
        return is_array($result)
            && ($result['success'] ?? false) === true
            && ($result['action'] ?? '') === 'login';
    }
}
