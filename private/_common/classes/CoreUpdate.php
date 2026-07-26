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
        $mode = isset($options['mode']) ? strtolower(trim((string) $options['mode'])) : 'db';

        if (!in_array($mode, array('db', 'files', 'all'), true)) {
            throw new RuntimeException('Modalita aggiornamento non valida: ' . $mode . '.');
        }

        $manifestPath = isset($options['manifest']) ? $options['manifest'] : $this->updateRoot . '/manifest.json';
        $customersPath = isset($options['customers']) ? $options['customers'] : $this->defaultCustomersPath();

        $manifest = $this->loadManifest($manifestPath);
        $sql = in_array($mode, array('db', 'all'), true) ? $this->loadSqlFromManifest($manifest, $manifestPath) : null;
        $package = in_array($mode, array('files', 'all'), true) ? $this->loadPackageFromManifest($manifest, $manifestPath) : null;
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

            $results[] = $this->processCustomer($customer, $manifest, $sql, $package, $dryRun, $mode);
        }

        return array(
            'status' => $this->hasFailures($results) ? 'partial' : 'success',
            'dry_run' => $dryRun,
            'mode' => $mode,
            'manifest_version' => isset($manifest['version']) ? $manifest['version'] : null,
            'customers_file' => $this->resolvePath($customersPath),
            'summary' => $this->summary($results),
            'results' => $results,
        );
    }

    protected function processCustomer($customer, $manifest, $sql, $package, $dryRun, $mode)
    {
        $mysqli = null;

        try {
            $mysqli = $this->connectCustomer($customer);
            $this->assertUpdateRequirement($mysqli, $manifest);
            $fileTarget = in_array($mode, array('files', 'all'), true) ? $this->resolveCustomerRoot($customer) : null;

            if ($dryRun) {
                $mysqli->close();
                return array(
                    'customer' => $customer['customer'],
                    'database' => $customer['db_name'],
                    'file_target' => $fileTarget,
                    'status' => 'success',
                    'message' => 'Controlli aggiornamento riusciti. Nessuna modifica eseguita per dry-run.',
                );
            }

            $applied = array();

            if (in_array($mode, array('db', 'all'), true)) {
                $this->executeSql($mysqli, $sql);
                $applied[] = 'db';
            }

            if (in_array($mode, array('files', 'all'), true)) {
                $this->applyPackage($package, $fileTarget);
                $applied[] = 'files';
            }

            $this->recordUpdate($mysqli, $manifest, 'success', false, 'Aggiornamento eseguito correttamente.', array(
                'customer' => $customer['customer'],
                'database' => $customer['db_name'],
                'file_target' => $fileTarget,
                'mode' => $mode,
                'applied' => $applied,
            ));

            $mysqli->close();
            return array(
                'customer' => $customer['customer'],
                'database' => $customer['db_name'],
                'file_target' => $fileTarget,
                'status' => 'success',
                'message' => 'Aggiornamento eseguito correttamente.',
            );
        } catch (Throwable $e) {
            if ($mysqli instanceof mysqli) {
                try {
                    $this->recordUpdate($mysqli, $manifest, 'failed', $dryRun, $e->getMessage(), array(
                        'customer' => $customer['customer'],
                        'database' => isset($customer['db_name']) ? $customer['db_name'] : null,
                        'mode' => $mode,
                    ));
                    $mysqli->close();
                } catch (Throwable $ignored) {
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

        foreach (array('version') as $key) {
            if (!isset($manifest[$key]) || trim((string) $manifest[$key]) === '') {
                throw new RuntimeException('Manifest incompleto: campo mancante ' . $key . '.');
            }
        }

        return $manifest;
    }

    protected function assertUpdateRequirement($mysqli, $manifest)
    {
        $requires = isset($manifest['requires']) ? trim((string) $manifest['requires']) : '';

        if ($requires === '') {
            return;
        }

        if (!$this->tableExists($mysqli, 'archy_core_updates')) {
            throw new RuntimeException('Versione richiesta ' . $requires . ' non verificabile: tabella archy_core_updates assente.');
        }

        $requiresSql = $this->sqlValue($mysqli, $requires);
        $result = $mysqli->query("
            SELECT COUNT(*) AS total
            FROM `archy_core_updates`
            WHERE `version` = " . $requiresSql . "
              AND `status` = 'success'
              AND `dry_run` = 0
        ");

        if (!$result) {
            throw new RuntimeException('Impossibile verificare versione richiesta: ' . $mysqli->error);
        }

        $row = $result->fetch_assoc();
        $result->free();

        if ((int) $row['total'] === 0) {
            throw new RuntimeException('Versione richiesta non installata: ' . $requires . '.');
        }
    }

    protected function tableExists($mysqli, $table)
    {
        $tableSql = $this->sqlValue($mysqli, $table);
        $result = $mysqli->query("
            SELECT COUNT(*) AS total
            FROM INFORMATION_SCHEMA.TABLES
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = " . $tableSql . "
        ");

        if (!$result) {
            throw new RuntimeException('Impossibile verificare tabella ' . $table . ': ' . $mysqli->error);
        }

        $row = $result->fetch_assoc();
        $result->free();

        return (int) $row['total'] > 0;
    }

    protected function loadSqlFromManifest($manifest, $manifestPath)
    {
        if (!isset($manifest['sql']) || trim((string) $manifest['sql']) === '') {
            throw new RuntimeException('Manifest incompleto: campo mancante sql.');
        }

        $sqlPath = $manifest['sql'];
        $sqlPath = $this->resolveManifestAssetPath($manifestPath, $sqlPath);

        $sql = $this->readPathOrUrl($sqlPath);

        if (isset($manifest['sql_sha256']) && trim((string) $manifest['sql_sha256']) !== '') {
            $hash = hash('sha256', $sql);
            if (!hash_equals(strtolower($manifest['sql_sha256']), strtolower($hash))) {
                throw new RuntimeException('Hash SHA256 del file SQL non valido.');
            }
        }

        return $sql;
    }

    protected function loadPackageFromManifest($manifest, $manifestPath)
    {
        if (!class_exists('ZipArchive')) {
            throw new RuntimeException('Estensione PHP ZipArchive non disponibile.');
        }

        if (!isset($manifest['package']) || trim((string) $manifest['package']) === '') {
            throw new RuntimeException('Manifest incompleto: campo mancante package.');
        }

        $packagePath = $this->resolveManifestAssetPath($manifestPath, $manifest['package']);
        $runtimeDir = $this->updateRoot . '/runtime';
        $this->ensureDirectory($runtimeDir);

        $packageFile = $runtimeDir . '/' . basename(parse_url($packagePath, PHP_URL_PATH) ?: $manifest['package']);
        $this->copyPathOrUrl($packagePath, $packageFile);

        if (isset($manifest['sha256']) && trim((string) $manifest['sha256']) !== '') {
            $hash = hash_file('sha256', $packageFile);
            if (!hash_equals(strtolower($manifest['sha256']), strtolower($hash))) {
                throw new RuntimeException('Hash SHA256 del pacchetto non valido.');
            }
        }

        $zip = new ZipArchive();
        if ($zip->open($packageFile) !== true) {
            throw new RuntimeException('Pacchetto zip non valido: ' . $packageFile);
        }
        $zip->close();

        return array(
            'path' => $packageFile,
            'source' => $packagePath,
        );
    }

    protected function resolveManifestAssetPath($manifestPath, $assetPath)
    {
        if ($this->isUrl($assetPath)) {
            return $assetPath;
        }

        if ($this->isUrl($manifestPath)) {
            return rtrim(dirname($manifestPath), '/\\') . '/' . ltrim($assetPath, '/\\');
        }

        $manifestFile = $this->resolvePath($manifestPath);
        return dirname($manifestFile) . '/' . $assetPath;
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
                'main_root' => $this->valueByKey($row, $map, 'main_root'),
                'subdomain_root' => $this->valueByKey($row, $map, 'subdomain_root'),
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
            'main_root' => array('main_root', 'root', 'project_root', 'percorso_root', 'percorso'),
            'subdomain_root' => array('subdomain_root', 'httpdocs', 'document_root', 'percorso_httpdocs'),
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

    protected function resolveCustomerRoot($customer)
    {
        $root = '';

        if (isset($customer['main_root']) && trim((string) $customer['main_root']) !== '') {
            $root = trim((string) $customer['main_root']);
        } elseif (isset($customer['subdomain_root']) && trim((string) $customer['subdomain_root']) !== '') {
            $root = trim((string) $customer['subdomain_root']);
        }

        if ($root === '') {
            throw new RuntimeException('Percorso root cliente mancante per aggiornamento files.');
        }

        $root = str_replace('\\', '/', $root);
        $real = realpath($root);

        if (!$real || !is_dir($real)) {
            throw new RuntimeException('Percorso root cliente non valido: ' . $root);
        }

        return str_replace('\\', '/', $real);
    }

    protected function applyPackage($package, $targetRoot)
    {
        if (!is_array($package) || empty($package['path']) || !is_file($package['path'])) {
            throw new RuntimeException('Pacchetto aggiornamento files non disponibile.');
        }

        $zip = new ZipArchive();
        if ($zip->open($package['path']) !== true) {
            throw new RuntimeException('Impossibile aprire pacchetto aggiornamento files.');
        }

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entry = $zip->getNameIndex($i);
            if (!$this->isSafeZipEntry($entry)) {
                $zip->close();
                throw new RuntimeException('Percorso non consentito nel pacchetto: ' . $entry);
            }
        }

        if (!$zip->extractTo($targetRoot)) {
            $zip->close();
            throw new RuntimeException('Impossibile estrarre pacchetto in: ' . $targetRoot);
        }

        $zip->close();
    }

    protected function isSafeZipEntry($entry)
    {
        $entry = str_replace('\\', '/', (string) $entry);

        if ($entry === '' || strpos($entry, "\0") !== false) {
            return false;
        }

        if (strpos($entry, '../') !== false || preg_match('/(^|\/)\.\.(\/|$)/', $entry)) {
            return false;
        }

        if (preg_match('/^[a-zA-Z]:\//', $entry) || strpos($entry, '/') === 0) {
            return false;
        }

        return true;
    }

    protected function copyPathOrUrl($source, $destination)
    {
        $this->ensureDirectory(dirname($destination));

        if ($this->isUrl($source)) {
            $write = fopen($destination, 'wb');
            if (!$write) {
                throw new RuntimeException('Impossibile scrivere file: ' . $destination);
            }

            $ch = curl_init($source);
            curl_setopt($ch, CURLOPT_FILE, $write);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_TIMEOUT, 300);
            $ok = curl_exec($ch);
            $error = curl_error($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            fclose($write);

            if (!$ok || $code >= 400) {
                @unlink($destination);
                throw new RuntimeException('Impossibile scaricare URL ' . $source . ': ' . ($error ? $error : 'HTTP ' . $code));
            }

            return;
        }

        $source = $this->resolvePath($source);
        if (!is_file($source)) {
            throw new RuntimeException('File non trovato: ' . $source);
        }

        if (!copy($source, $destination)) {
            throw new RuntimeException('Impossibile copiare file: ' . $source);
        }
    }

    protected function ensureDirectory($dir)
    {
        if (is_dir($dir)) {
            return;
        }

        if (!mkdir($dir, 0755, true) && !is_dir($dir)) {
            throw new RuntimeException('Impossibile creare cartella: ' . $dir);
        }
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
