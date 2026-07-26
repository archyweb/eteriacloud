<?php

class GoogleCalendarService
{
    protected $app;
    protected $db;
    protected $config;
    protected $user_id;

    const AUTH_URL = 'https://accounts.google.com/o/oauth2/v2/auth';
    const TOKEN_URL = 'https://oauth2.googleapis.com/token';
    const USERINFO_URL = 'https://www.googleapis.com/oauth2/v3/userinfo';
    const CALENDAR_API_BASE = 'https://www.googleapis.com/calendar/v3';

    public function __construct($app = null)
    {
        if ($app === null) {
            global $app;
        }

        $this->app = $app;
        $this->db = $this->app ? $this->app->get_db() : null;
        $this->user_id = $this->app && isset($this->app->user_id) ? (int)$this->app->user_id : 0;
        $this->config = $this->loadConfig();
    }

    public function isConfigured()
    {
        return !empty($this->config['enabled'])
            && $this->config['client_id'] !== ''
            && $this->config['client_secret'] !== '';
    }

    public function getRedirectUri()
    {
        if ($this->config['redirect_uri'] !== '') {
            return $this->config['redirect_uri'];
        }

        $host = $_SERVER['HTTP_HOST'] ?? '';
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';

        if ($this->app && isset($this->app->protocol)) {
            $protocol = $this->app->protocol;
        }

        return $protocol . '://' . $host . '/archy/google_calendar_callback.php';
    }

    public function getLocalCallbackUri()
    {
        if ($this->config['local_callback_uri'] !== '') {
            return $this->config['local_callback_uri'];
        }

        $host = $_SERVER['HTTP_HOST'] ?? '';
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';

        if ($this->app && isset($this->app->protocol)) {
            $protocol = $this->app->protocol;
        }

        return $protocol . '://' . $host . '/archy/google_calendar_callback.php';
    }

    public function buildState($nonce)
    {
        return self::encodeState([
            'nonce' => $nonce,
            'return_uri' => $this->getLocalCallbackUri(),
            'created_at' => time()
        ], $this->getStateSecret());
    }

    public function parseState($state)
    {
        return self::decodeState($state, $this->getStateSecret());
    }

    public function buildAuthUrl($state)
    {
        $params = [
            'client_id' => $this->config['client_id'],
            'redirect_uri' => $this->getRedirectUri(),
            'response_type' => 'code',
            'scope' => implode(' ', $this->getScopes()),
            'access_type' => 'offline',
            'prompt' => 'consent',
            'include_granted_scopes' => 'true',
            'state' => $state
        ];

        return self::AUTH_URL . '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    }

    public function handleCallback($code)
    {
        $token = $this->exchangeCode($code);
        $profile = $this->fetchProfile($token['access_token']);
        $calendars = $this->fetchCalendars($token['access_token']);

        return $this->saveAccount($token, $profile, $calendars);
    }

    public function getAccount($user_id = null)
    {
        if (!$this->db || !$this->tableExists('archy_google_accounts')) {
            return null;
        }

        $user_id = $user_id === null ? $this->user_id : (int)$user_id;

        if ($user_id <= 0) {
            return null;
        }

        $row = $this->db->query("
            SELECT *
            FROM archy_google_accounts
            WHERE user_id = " . (int)$user_id . "
            LIMIT 1
        ");

        return $row ? mysqli_fetch_assoc($row) : null;
    }

    public function disconnect($user_id = null)
    {
        if (!$this->db || !$this->tableExists('archy_google_accounts')) {
            return;
        }

        $user_id = $user_id === null ? $this->user_id : (int)$user_id;

        if ($user_id > 0) {
            $this->db->query("DELETE FROM archy_google_accounts WHERE user_id = " . (int)$user_id . " LIMIT 1");
        }
    }

    public function listCalendars()
    {
        $account = $this->getAccount();

        if (!$account) {
            return [];
        }

        $access_token = $this->getValidAccessToken($account);
        return $this->fetchCalendars($access_token);
    }

    public function createEventForCalendarRecord($calendar_id, array $event_details)
    {
        if (!$this->isConfigured() || !$this->tableExists('archy_google_accounts')) {
            return false;
        }

        $account = $this->getAccount();

        if (!$account || empty($account['sync_enabled']) || empty($account['selected_calendar_id'])) {
            return false;
        }

        try {
            $access_token = $this->getValidAccessToken($account);
            $payload = $this->buildGoogleEventPayload($event_details);
            $google_calendar_id = $account['selected_calendar_id'];
            $url = self::CALENDAR_API_BASE . '/calendars/' . rawurlencode($google_calendar_id) . '/events';
            $google_event = $this->apiRequest('POST', $url, $access_token, $payload);

            $this->updateCalendarSyncSuccess($calendar_id, $account, $google_event);
            return $google_event;
        } catch (Exception $e) {
            $this->updateCalendarSyncFailure($calendar_id, $e->getMessage());
            return false;
        }
    }

    protected function loadConfig()
    {
        $config = [];

        if ($this->app && isset($this->app->config['google_calendar_oauth']) && is_array($this->app->config['google_calendar_oauth'])) {
            $config = $this->app->config['google_calendar_oauth'];
        }

        return [
            'enabled' => !empty($config['enabled']),
            'client_id' => trim((string)($config['client_id'] ?? ArchyEnv::value('GOOGLE_CALENDAR_CLIENT_ID', ''))),
            'client_secret' => trim((string)($config['client_secret'] ?? ArchyEnv::value('GOOGLE_CALENDAR_CLIENT_SECRET', ''))),
            'redirect_uri' => trim((string)($config['redirect_uri'] ?? ArchyEnv::value('GOOGLE_CALENDAR_REDIRECT_URI', ''))),
            'local_callback_uri' => trim((string)($config['local_callback_uri'] ?? '')),
            'state_secret' => trim((string)($config['state_secret'] ?? ArchyEnv::value('GOOGLE_CALENDAR_STATE_SECRET', ''))),
            'timezone' => trim((string)($config['timezone'] ?? 'Europe/Rome'))
        ];
    }

    protected function getStateSecret()
    {
        $secret = $this->config['state_secret'] !== '' ? $this->config['state_secret'] : $this->config['client_secret'];

        if ($secret === '') {
            throw new Exception('Secret OAuth Google Calendar mancante.');
        }

        return $secret;
    }

    public static function encodeState(array $payload, $secret)
    {
        $json = json_encode($payload);
        $encoded = self::base64UrlEncode($json);
        $signature = hash_hmac('sha256', $encoded, $secret);

        return $encoded . '.' . $signature;
    }

    public static function decodeState($state, $secret = '')
    {
        if (strpos($state, '.') === false) {
            return [
                'nonce' => $state,
                'return_uri' => ''
            ];
        }

        list($encoded, $signature) = explode('.', $state, 2);
        $expected = hash_hmac('sha256', $encoded, $secret);

        if (!hash_equals($expected, $signature)) {
            throw new Exception('Firma state OAuth Google Calendar non valida.');
        }

        $decoded = json_decode(self::base64UrlDecode($encoded), true);

        if (!is_array($decoded)) {
            throw new Exception('State OAuth Google Calendar non valido.');
        }

        return $decoded;
    }

    public static function decodeStateForCentralCallback($state)
    {
        $secret = ArchyEnv::value('GOOGLE_CALENDAR_STATE_SECRET', '');

        if ($secret === '') {
            $secret = ArchyEnv::value('GOOGLE_CALENDAR_CLIENT_SECRET', '');
        }

        if ($secret === '') {
            throw new Exception('GOOGLE_CALENDAR_STATE_SECRET non configurato.');
        }

        return self::decodeState($state, $secret);
    }

    protected static function base64UrlEncode($value)
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    protected static function base64UrlDecode($value)
    {
        return base64_decode(strtr($value, '-_', '+/'));
    }

    protected function getScopes()
    {
        return [
            'openid',
            'email',
            'https://www.googleapis.com/auth/calendar.readonly',
            'https://www.googleapis.com/auth/calendar.events'
        ];
    }

    protected function exchangeCode($code)
    {
        return $this->postForm(self::TOKEN_URL, [
            'code' => $code,
            'client_id' => $this->config['client_id'],
            'client_secret' => $this->config['client_secret'],
            'redirect_uri' => $this->getRedirectUri(),
            'grant_type' => 'authorization_code'
        ]);
    }

    protected function refreshAccessToken(array $account)
    {
        if (empty($account['refresh_token'])) {
            throw new Exception('Refresh token Google mancante.');
        }

        $token = $this->postForm(self::TOKEN_URL, [
            'client_id' => $this->config['client_id'],
            'client_secret' => $this->config['client_secret'],
            'refresh_token' => $account['refresh_token'],
            'grant_type' => 'refresh_token'
        ]);

        $expires_at = $this->computeExpiresAt($token);
        $access_token = $this->sql($token['access_token'] ?? '');
        $token_type = $this->sql($token['token_type'] ?? 'Bearer');

        $this->db->query("
            UPDATE archy_google_accounts
            SET access_token = '" . $access_token . "',
                token_type = '" . $token_type . "',
                expires_at = " . $this->sqlDate($expires_at) . ",
                updated_at = NOW()
            WHERE id = " . (int)$account['id'] . "
            LIMIT 1
        ");

        return $token['access_token'];
    }

    protected function getValidAccessToken(array $account)
    {
        if (!empty($account['access_token']) && !empty($account['expires_at']) && strtotime($account['expires_at']) > time() + 90) {
            return $account['access_token'];
        }

        return $this->refreshAccessToken($account);
    }

    protected function fetchProfile($access_token)
    {
        return $this->apiRequest('GET', self::USERINFO_URL, $access_token);
    }

    protected function fetchCalendars($access_token)
    {
        $response = $this->apiRequest('GET', self::CALENDAR_API_BASE . '/users/me/calendarList', $access_token);
        return isset($response['items']) && is_array($response['items']) ? $response['items'] : [];
    }

    protected function saveAccount(array $token, array $profile, array $calendars)
    {
        $selected = $this->selectDefaultCalendar($calendars);
        $expires_at = $this->computeExpiresAt($token);

        $google_account_id = $this->sql($profile['sub'] ?? '');
        $google_email = $this->sql($profile['email'] ?? '');
        $access_token = $this->sql($token['access_token'] ?? '');
        $refresh_token = $this->sql($token['refresh_token'] ?? '');
        $token_type = $this->sql($token['token_type'] ?? 'Bearer');
        $scope = $this->sql($token['scope'] ?? implode(' ', $this->getScopes()));
        $selected_calendar_id = $this->sql($selected['id'] ?? '');
        $selected_calendar_name = $this->sql($selected['summary'] ?? 'Google Calendar');

        $this->db->query("
            INSERT INTO archy_google_accounts
                (user_id, google_account_id, google_email, access_token, refresh_token, token_type, expires_at, scope, selected_calendar_id, selected_calendar_name, sync_enabled, created_at, updated_at)
            VALUES
                (" . (int)$this->user_id . ", '" . $google_account_id . "', '" . $google_email . "', '" . $access_token . "', '" . $refresh_token . "', '" . $token_type . "', " . $this->sqlDate($expires_at) . ", '" . $scope . "', '" . $selected_calendar_id . "', '" . $selected_calendar_name . "', 1, NOW(), NOW())
            ON DUPLICATE KEY UPDATE
                google_account_id = VALUES(google_account_id),
                google_email = VALUES(google_email),
                access_token = VALUES(access_token),
                refresh_token = IF(VALUES(refresh_token) <> '', VALUES(refresh_token), refresh_token),
                token_type = VALUES(token_type),
                expires_at = VALUES(expires_at),
                scope = VALUES(scope),
                selected_calendar_id = IF(selected_calendar_id IS NULL OR selected_calendar_id = '', VALUES(selected_calendar_id), selected_calendar_id),
                selected_calendar_name = IF(selected_calendar_name IS NULL OR selected_calendar_name = '', VALUES(selected_calendar_name), selected_calendar_name),
                sync_enabled = 1,
                updated_at = NOW()
        ");

        return $this->getAccount();
    }

    protected function selectDefaultCalendar(array $calendars)
    {
        foreach ($calendars as $calendar) {
            if (!empty($calendar['primary'])) {
                return $calendar;
            }
        }

        return count($calendars) > 0 ? $calendars[0] : [];
    }

    protected function buildGoogleEventPayload(array $event_details)
    {
        $title = trim((string)($event_details['title'] ?? ''));
        $description = trim((string)($event_details['description'] ?? ''));
        $start = trim((string)($event_details['start'] ?? ''));
        $end = trim((string)($event_details['end'] ?? ''));
        $all_day = filter_var($event_details['allDay'] ?? false, FILTER_VALIDATE_BOOLEAN);

        if ($title === '') {
            $title = 'Evento';
        }

        if ($start === '') {
            throw new Exception('Data inizio evento mancante.');
        }

        $payload = [
            'summary' => $title,
            'description' => $description
        ];

        if ($all_day) {
            $start_date = substr($start, 0, 10);
            $end_date = $end !== '' ? substr($end, 0, 10) : date('Y-m-d', strtotime($start_date . ' +1 day'));

            if ($end_date <= $start_date) {
                $end_date = date('Y-m-d', strtotime($start_date . ' +1 day'));
            }

            $payload['start'] = ['date' => $start_date];
            $payload['end'] = ['date' => $end_date];
        } else {
            $payload['start'] = [
                'dateTime' => $this->normalizeDateTime($start),
                'timeZone' => $this->config['timezone']
            ];
            $payload['end'] = [
                'dateTime' => $this->normalizeDateTime($end !== '' ? $end : date('Y-m-d H:i:s', strtotime($start . ' +1 hour'))),
                'timeZone' => $this->config['timezone']
            ];
        }

        return $payload;
    }

    protected function normalizeDateTime($value)
    {
        $timestamp = strtotime($value);

        if ($timestamp === false) {
            throw new Exception('Formato data evento non valido.');
        }

        return date('Y-m-d\TH:i:s', $timestamp);
    }

    protected function updateCalendarSyncSuccess($calendar_id, array $account, array $google_event)
    {
        if (!$this->tableExists('archy_calendar')) {
            return;
        }

        $this->db->query("
            UPDATE archy_calendar
            SET calendar_source = 'local',
                google_account_id = " . (int)$account['id'] . ",
                google_calendar_id = '" . $this->sql($account['selected_calendar_id']) . "',
                google_event_id = '" . $this->sql($google_event['id'] ?? '') . "',
                google_etag = '" . $this->sql($google_event['etag'] ?? '') . "',
                google_html_link = '" . $this->sql($google_event['htmlLink'] ?? '') . "',
                google_sync_status = 'synced',
                google_synced_at = NOW(),
                google_last_error = NULL
            WHERE id = " . (int)$calendar_id . "
            LIMIT 1
        ");
    }

    protected function updateCalendarSyncFailure($calendar_id, $message)
    {
        if (!$this->tableExists('archy_calendar')) {
            return;
        }

        $this->db->query("
            UPDATE archy_calendar
            SET google_sync_status = 'failed',
                google_last_error = '" . $this->sql($message) . "'
            WHERE id = " . (int)$calendar_id . "
            LIMIT 1
        ");
    }

    protected function apiRequest($method, $url, $access_token, array $payload = null)
    {
        $headers = [
            'Authorization: Bearer ' . $access_token,
            'Accept: application/json'
        ];

        if ($payload !== null) {
            $headers[] = 'Content-Type: application/json';
        }

        return $this->curlRequest($method, $url, $headers, $payload !== null ? json_encode($payload) : null);
    }

    protected function postForm($url, array $data)
    {
        return $this->curlRequest('POST', $url, ['Content-Type: application/x-www-form-urlencoded'], http_build_query($data, '', '&', PHP_QUERY_RFC3986));
    }

    protected function curlRequest($method, $url, array $headers, $body = null)
    {
        if (!function_exists('curl_init')) {
            throw new Exception('Estensione PHP cURL non disponibile.');
        }

        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 30
        ]);

        if ($body !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false) {
            throw new Exception('Errore cURL Google: ' . $error);
        }

        $decoded = json_decode($response, true);

        if ($status < 200 || $status >= 300) {
            $message = isset($decoded['error_description']) ? $decoded['error_description'] : ($decoded['error']['message'] ?? $response);
            throw new Exception('Errore Google API (' . $status . '): ' . $message);
        }

        return is_array($decoded) ? $decoded : [];
    }

    protected function computeExpiresAt(array $token)
    {
        $expires_in = isset($token['expires_in']) ? (int)$token['expires_in'] : 3600;
        return date('Y-m-d H:i:s', time() + max(60, $expires_in - 60));
    }

    protected function tableExists($table)
    {
        if (!$this->db) {
            return false;
        }

        $table = $this->sql($table);
        $result = $this->db->query("SHOW TABLES LIKE '" . $table . "'");
        return $result && $result->num_rows > 0;
    }

    protected function sql($value)
    {
        return $this->db ? $this->db->real_escape_string((string)$value) : addslashes((string)$value);
    }

    protected function sqlDate($value)
    {
        return $value ? "'" . $this->sql($value) . "'" : 'NULL';
    }
}
