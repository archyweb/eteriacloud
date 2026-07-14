<?php

class CoreUpdate
{
    protected $options = array();
    protected $mainRoot;
    protected $updateRoot;

    public function __construct($options = array())
    {
        $this->options = is_array($options) ? $options : array();
        $classesRoot = dirname(__FILE__);
        $this->mainRoot = isset($this->options['main_root'])
            ? $this->normalizePath($this->options['main_root'])
            : realpath($classesRoot . '/../../..');
        $this->updateRoot = $this->mainRoot . '/private/_common/update';
    }

    public function run($options = array())
    {
        $options = array_merge($this->options, is_array($options) ? $options : array());
        $dryRun = $this->toBool(isset($options['dry_run']) ? $options['dry_run'] : true);

        $manifestPath = isset($options['manifest']) ? $options['manifest'] : $this->updateRoot . '/manifest.json';
        $customersPath = isset($options['customers']) ? $options['customers'] : $this->defaultCustomersPath();

        $manifest = $this->loadManifest($manifestPath);
        $sql = $this->loadSqlFromManifest($manifest, $manifestPath);
        $customers = $this->loadCustomers($customersPath);

        $results = array();
        foreach ($customers as $customer) {
            if (!$this->toBool(isset($customer['enabled']) ? $customer['enabled'] : true)) {
                $results[] = array(
                    'customer' => $customer['customer'],
                    'status' => 'skipped',
                    'message' => 'Cliente disabilitato.',
                );
                continue;
            }

            $results[] = $this->processCustomer($customer, $manifest, $sql, $dryRun);
        }

        return array(
            'status' => $this->hasFailures($results) ? 'partial' : 'success',
            'dry_run' => $dryRun,
            'manifest_version' => isset($manifest['version']) ? $manifest['version'] : null,
            'customers_file' => $this->resolvePath($customersPath),
            'summary' => $this->summary($results),
            'results' => $results,
        );
    }

    protected function processCustomer($customer, $manifest, $sql, $dryRun)
    {
        $mysqli = null;

        try {
            $mysqli = $this->connectCustomer($customer);

            if ($dryRun) {
                $mysqli->close();
                return array(
                    'customer' => $customer['customer'],
                    'database' => $customer['db_name'],
                    'status' => 'success',
                    'message' => 'Connessione DB riuscita. Nessuna query eseguita per dry-run.',
                );
            }

            $this->executeSql($mysqli, $sql);
            $this->recordUpdate($mysqli, $manifest, 'success', false, 'Aggiornamento SQL eseguito correttamente.', array(
                'customer' => $customer['customer'],
                'database' => $customer['db_name'],
            ));

            $mysqli->close();
            return array(
                'customer' => $customer['customer'],
                'database' => $customer['db_name'],
                'status' => 'success',
                'message' => 'Aggiornamento SQL eseguito correttamente.',
            );
        } catch (Exception $e) {
            if ($mysqli instanceof mysqli) {
                try {
                    $this->recordUpdate($mysqli, $manifest, 'failed', $dryRun, $e->getMessage(), array(
                        'customer' => $customer['customer'],
                        'database' => isset($customer['db_name']) ? $customer['db_name'] : null,
                    ));
                    $mysqli->close();
                } catch (Exception $ignored) {
                }
            }

            return array(
                'customer' => isset($customer['customer']) ? $customer['customer'] : null,
                'database' => isset($customer['db_name']) ? $customer['db_name'] : null,
                'status' => 'failed',
                'message' => $e->getMessage(),
            );
        }
    }

    protected function connectCustomer($customer)
    {
        foreach (array('customer', 'db_host', 'db_name', 'db_user') as $key) {
            if (!isset($customer[$key]) || trim((string) $customer[$key]) === '') {
                throw new RuntimeException('Configurazione cliente incompleta: campo mancante ' . $key . '.');
            }
        }

        $port = isset($customer['db_port']) && $customer['db_port'] !== '' ? (int) $customer['db_port'] : 3306;
        $mysqli = @new mysqli(
            $customer['db_host'],
            $customer['db_user'],
            isset($customer['db_pass']) ? $customer['db_pass'] : '',
            $customer['db_name'],
            $port
        );

        if ($mysqli->connect_errno) {
            throw new RuntimeException('Connessione DB fallita: ' . $mysqli->connect_error);
        }

        $mysqli->set_charset('utf8mb4');
        return $mysqli;
    }

    protected function executeSql($mysqli, $sql)
    {
        if (trim($sql) === '') {
            throw new RuntimeException('File SQL vuoto.');
        }

        if (!$mysqli->multi_query($sql)) {
            throw new RuntimeException('Errore SQL: ' . $mysqli->error);
        }

        do {
            $result = $mysqli->store_result();
            if ($result) {
                $result->free();
            }
        } while ($mysqli->more_results() && $mysqli->next_result());

        if ($mysqli->errno) {
            throw new RuntimeException('Errore SQL: ' . $mysqli->error);
        }
    }

    protected function recordUpdate($mysqli, $manifest, $status, $dryRun, $message, $details)
    {
        $version = isset($manifest['version']) ? $manifest['version'] : '';
        if ($version === '') {
            return;
        }

        $detailsJson = json_encode($details);
        $values = array(
            'version' => $version,
            'requires_version' => isset($manifest['requires']) ? $manifest['requires'] : null,
            'status' => $status,
            'dry_run' => $dryRun ? 1 : 0,
            'package_file' => isset($manifest['package']) ? $manifest['package'] : null,
            'package_sha256' => isset($manifest['sha256']) ? $manifest['sha256'] : null,
            'sql_file' => isset($manifest['sql']) ? $manifest['sql'] : null,
            'sql_sha256' => isset($manifest['sql_sha256']) ? $manifest['sql_sha256'] : null,
            'message' => $message,
            'details' => $detailsJson,
        );

        $query = "
            INSERT INTO `archy_core_updates`
                (`version`, `requires_version`, `status`, `dry_run`, `package_file`, `package_sha256`, `sql_file`, `sql_sha256`, `started_at`, `finished_at`, `message`, `details`, `created_at`)
            VALUES
                (" . $this->sqlValue($mysqli, $values['version']) . ",
                 " . $this->sqlValue($mysqli, $values['requires_version']) . ",
                 " . $this->sqlValue($mysqli, $values['status']) . ",
                 " . (int) $values['dry_run'] . ",
                 " . $this->sqlValue($mysqli, $values['package_file']) . ",
                 " . $this->sqlValue($mysqli, $values['package_sha256']) . ",
                 " . $this->sqlValue($mysqli, $values['sql_file']) . ",
                 " . $this->sqlValue($mysqli, $values['sql_sha256']) . ",
                 NOW(),
                 NOW(),
                 " . $this->sqlValue($mysqli, $values['message']) . ",
                 " . $this->sqlValue($mysqli, $values['details']) . ",
                 NOW())
            ON DUPLICATE KEY UPDATE
                `requires_version` = VALUES(`requires_version`),
                `status` = VALUES(`status`),
                `dry_run` = VALUES(`dry_run`),
                `package_file` = VALUES(`package_file`),
                `package_sha256` = VALUES(`package_sha256`),
                `sql_file` = VALUES(`sql_file`),
                `sql_sha256` = VALUES(`sql_sha256`),
                `finished_at` = VALUES(`finished_at`),
                `message` = VALUES(`message`),
                `details` = VALUES(`details`)
        ";

        if (!$mysqli->query($query)) {
            throw new RuntimeException('Impossibile registrare aggiornamento: ' . $mysqli->error);
        }
    }

    protected function loadManifest($path)
    {
        $content = $this->readPathOrUrl($path);
        $manifest = json_decode($content, true);

        if (!is_array($manifest)) {
            throw new RuntimeException('Manifest non valido.');
        }

        foreach (array('version', 'sql') as $key) {
            if (!isset($manifest[$key]) || trim((string) $manifest[$key]) === '') {
                throw new RuntimeException('Manifest incompleto: campo mancante ' . $key . '.');
            }
        }

        return $manifest;
    }

    protected function loadSqlFromManifest($manifest, $manifestPath)
    {
        $sqlPath = $manifest['sql'];
        if (!$this->isUrl($sqlPath)) {
            if ($this->isUrl($manifestPath)) {
                $sqlPath = rtrim(dirname($manifestPath), '/\\') . '/' . ltrim($sqlPath, '/\\');
            } else {
                $manifestFile = $this->resolvePath($manifestPath);
                $sqlPath = dirname($manifestFile) . '/' . $sqlPath;
            }
        }

        $sql = $this->readPathOrUrl($sqlPath);

        if (isset($manifest['sql_sha256']) && trim((string) $manifest['sql_sha256']) !== '') {
            $hash = hash('sha256', $sql);
            if (!hash_equals(strtolower($manifest['sql_sha256']), strtolower($hash))) {
                throw new RuntimeException('Hash SHA256 del file SQL non valido.');
            }
        }

        return $sql;
    }

    protected function loadCustomers($path)
    {
        $path = $this->resolvePath($path);
        if (!is_file($path)) {
            throw new RuntimeException('File clienti non trovato: ' . $path);
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (in_array($extension, array('xlsx', 'xls'))) {
            return $this->loadCustomersFromExcel($path);
        }

        return $this->loadCustomersFromCsv($path);
    }

    protected function loadCustomersFromExcel($path)
    {
        if (!class_exists('\\PhpOffice\\PhpSpreadsheet\\IOFactory')) {
            throw new RuntimeException('PhpSpreadsheet non disponibile.');
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
        $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);

        return $this->normalizeCustomerRows($rows);
    }

    protected function loadCustomersFromCsv($path)
    {
        $handle = fopen($path, 'r');
        if (!$handle) {
            throw new RuntimeException('Impossibile leggere il file clienti.');
        }

        $rows = array();
        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            if (count($row) === 1 && strpos($row[0], ',') !== false) {
                $row = str_getcsv($row[0], ',');
            }
            $rows[] = $row;
        }
        fclose($handle);

        return $this->normalizeCustomerRows($rows);
    }

    protected function normalizeCustomerRows($rows)
    {
        if (!is_array($rows) || count($rows) < 2) {
            throw new RuntimeException('File clienti vuoto o senza righe dati.');
        }

        $headers = array_shift($rows);
        $map = $this->headerMap($headers);
        $customers = array();

        foreach ($rows as $row) {
            if ($this->isEmptyRow($row)) {
                continue;
            }

            $customer = array(
                'customer' => $this->valueByKey($row, $map, 'customer'),
                'db_host' => $this->valueByKey($row, $map, 'db_host'),
                'db_port' => $this->valueByKey($row, $map, 'db_port'),
                'db_name' => $this->valueByKey($row, $map, 'db_name'),
                'db_user' => $this->valueByKey($row, $map, 'db_user'),
                'db_pass' => $this->valueByKey($row, $map, 'db_pass'),
                'enabled' => $this->valueByKey($row, $map, 'enabled'),
            );

            if ($customer['enabled'] === '') {
                $customer['enabled'] = '1';
            }

            $customers[] = $customer;
        }

        if (!$customers) {
            throw new RuntimeException('Nessun cliente valido nel file clienti.');
        }

        return $customers;
    }

    protected function headerMap($headers)
    {
        $aliases = array(
            'customer' => array('customer', 'cliente', 'name', 'nome', 'site', 'sito', 'ragione_sociale'),
            'db_host' => array('db_host', 'host', 'database_host', 'mysql_host', 'server'),
            'db_port' => array('db_port', 'port', 'porta'),
            'db_name' => array('db_name', 'database', 'database_name', 'nome_database', 'db', 'dbname'),
            'db_user' => array('db_user', 'user', 'username', 'utente', 'db_username'),
            'db_pass' => array('db_pass', 'pass', 'password', 'db_password'),
            'enabled' => array('enabled', 'active', 'attivo', 'abilitato', 'abilita'),
        );

        $map = array();
        foreach ($headers as $index => $header) {
            $normalized = $this->normalizeHeader($header);
            foreach ($aliases as $target => $names) {
                if (in_array($normalized, $names, true)) {
                    $map[$target] = $index;
                }
            }
        }

        return $map;
    }

    protected function valueByKey($row, $map, $key)
    {
        if (!isset($map[$key]) || !array_key_exists($map[$key], $row)) {
            return '';
        }

        return trim((string) $row[$map[$key]]);
    }

    protected function isEmptyRow($row)
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    protected function normalizeHeader($value)
    {
        $value = strtolower(trim((string) $value));
        $value = preg_replace('/[^a-z0-9]+/', '_', $value);
        return trim($value, '_');
    }

    protected function readPathOrUrl($path)
    {
        if ($this->isUrl($path)) {
            $ch = curl_init($path);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            $content = curl_exec($ch);
            $error = curl_error($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($content === false || $code >= 400) {
                throw new RuntimeException('Impossibile leggere URL ' . $path . ': ' . ($error ? $error : 'HTTP ' . $code));
            }

            return $content;
        }

        $path = $this->resolvePath($path);
        if (!is_file($path)) {
            throw new RuntimeException('File non trovato: ' . $path);
        }

        $content = file_get_contents($path);
        if ($content === false) {
            throw new RuntimeException('Impossibile leggere file: ' . $path);
        }

        return $content;
    }

    protected function resolvePath($path)
    {
        if ($this->isUrl($path)) {
            return $path;
        }

        $path = str_replace('\\', '/', $path);
        if (preg_match('/^[a-zA-Z]:\//', $path) || strpos($path, '/') === 0) {
            return $path;
        }

        return $this->mainRoot . '/' . ltrim($path, '/');
    }

    protected function normalizePath($path)
    {
        $real = realpath($path);
        return $real ? str_replace('\\', '/', $real) : str_replace('\\', '/', $path);
    }

    protected function defaultCustomersPath()
    {
        $xlsx = $this->updateRoot . '/customers.xlsx';
        if (is_file($xlsx)) {
            return $xlsx;
        }

        return $this->updateRoot . '/customers.csv';
    }

    protected function isUrl($value)
    {
        return (bool) preg_match('/^https?:\/\//i', (string) $value);
    }

    protected function toBool($value)
    {
        if (is_bool($value)) {
            return $value;
        }

        $value = strtolower(trim((string) $value));
        return !in_array($value, array('', '0', 'false', 'no', 'off'), true);
    }

    protected function sqlValue($mysqli, $value)
    {
        if ($value === null || $value === '') {
            return 'NULL';
        }

        return "'" . $mysqli->real_escape_string((string) $value) . "'";
    }

    protected function hasFailures($results)
    {
        foreach ($results as $result) {
            if (isset($result['status']) && $result['status'] === 'failed') {
                return true;
            }
        }

        return false;
    }

    protected function summary($results)
    {
        $summary = array('success' => 0, 'failed' => 0, 'skipped' => 0);
        foreach ($results as $result) {
            $status = isset($result['status']) ? $result['status'] : 'failed';
            if (!isset($summary[$status])) {
                $summary[$status] = 0;
            }
            $summary[$status]++;
        }

        return $summary;
    }
}
