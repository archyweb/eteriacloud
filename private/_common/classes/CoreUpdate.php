<?php

class CoreUpdate
{
    protected $options = array();
    protected $mainRoot;
    protected $updateRoot;
    protected $appliedPackageDestinations = array();

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
        $mode = isset($options['mode']) ? strtolower(trim((string) $options['mode'])) : 'db';
        if (!in_array($mode, array('files', 'all'), true)) {
            return $this->runUnlocked($options);
        }
        $this->ensureDirectory($this->updateRoot . '/runtime');
        $lock = fopen($this->updateRoot . '/runtime/files.lock', 'c');
        if (!$lock) {
            throw new RuntimeException('Impossibile aprire il lock aggiornamenti file.');
        }
        try {
            if (!flock($lock, LOCK_EX | LOCK_NB)) {
                throw new RuntimeException('Un altro aggiornamento file e gia in corso.');
            }
            return $this->runUnlocked($options);
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    protected function runUnlocked($options = array())
    {
        $options = array_merge($this->options, is_array($options) ? $options : array());
        $dryRun = $this->toBool(isset($options['dry_run']) ? $options['dry_run'] : true);
        $mode = isset($options['mode']) ? strtolower(trim((string) $options['mode'])) : 'db';
        $this->appliedPackageDestinations = array();

        if (!in_array($mode, array('db', 'files', 'all'), true)) {
            throw new RuntimeException('Modalita aggiornamento non valida: ' . $mode . '.');
        }

        $manifestPath = isset($options['manifest']) ? $options['manifest'] : $this->updateRoot . '/manifest.json';
        $customersPath = isset($options['customers']) ? $options['customers'] : $this->defaultCustomersPath();

        if (!empty($options['sequence']) && $this->toBool($options['sequence'])) {
            if ($mode !== 'db') {
                throw new RuntimeException('La sequenza aggiornamenti supporta solo mode=db.');
            }
            return $this->runDbSequence($manifestPath, $customersPath, $dryRun);
        }

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

    protected function runDbSequence($catalogPath, $customersPath, $dryRun)
    {
        $catalog = json_decode($this->readPathOrUrl($catalogPath), true);
        if (!is_array($catalog) || empty($catalog['manifests']) || !is_array($catalog['manifests'])) {
            throw new RuntimeException('Catalogo SQL non valido: manca manifests.');
        }

        $updates = array();
        foreach ($catalog['manifests'] as $path) {
            $path = $this->resolveManifestAssetPath($catalogPath, $path);
            $manifest = $this->loadManifest($path);
            $version = $manifest['version'];
            if (isset($updates[$version])) {
                throw new RuntimeException('Versione duplicata nel catalogo: ' . $version);
            }
            if (empty($manifest['sql_sha256'])) {
                throw new RuntimeException('Hash SQL mancante per versione ' . $version);
            }
            $updates[$version] = array('manifest' => $manifest, 'sql' => $this->loadSqlFromManifest($manifest, $path));
        }

        $results = array();
        foreach ($this->loadCustomers($customersPath) as $customer) {
            if (!$this->toBool($customer['enabled'])) {
                $results[] = array('customer' => $customer['customer'], 'status' => 'skipped', 'message' => 'Cliente disabilitato.');
                continue;
            }
            $results[] = $this->processDbSequence($customer, $updates, $dryRun);
        }
        return array(
            'status' => $this->hasFailures($results) ? 'partial' : 'success',
            'dry_run' => $dryRun,
            'mode' => 'db',
            'sequence' => true,
            'versions' => array_keys($updates),
            'summary' => $this->summary($results),
            'results' => $results,
        );
    }

    protected function installedDbVersions($mysqli)
    {
        if (!$this->tableExists($mysqli, 'archy_core_updates')) {
            return array();
        }
        $result = $mysqli->query("SELECT version FROM archy_core_updates WHERE status = 'success' AND dry_run = 0 AND sql_file IS NOT NULL AND sql_file <> ''");
        if (!$result) {
            throw new RuntimeException('Impossibile leggere storico aggiornamenti: ' . $mysqli->error);
        }
        $versions = array();
        while ($row = $result->fetch_assoc()) {
            $versions[$row['version']] = true;
        }
        $result->free();
        return $versions;
    }

    protected function processDbSequence($customer, $updates, $dryRun)
    {
        $mysqli = null;
        $steps = array();
        $current = null;
        try {
            $mysqli = $this->connectCustomer($customer);
            $lock = $mysqli->query("SELECT GET_LOCK('archy_core_update_db', 0) AS acquired");
            if (!$lock || (int) $lock->fetch_assoc()['acquired'] !== 1) {
                throw new RuntimeException('Un altro aggiornamento DB e gia in corso.');
            }
            $installed = $this->installedDbVersions($mysqli);
            foreach ($updates as $version => $update) {
                $current = $update['manifest'];
                if (isset($installed[$version])) {
                    $steps[] = array('version' => $version, 'status' => 'skipped', 'message' => 'Gia installato.');
                    continue;
                }
                $requires = isset($current['requires']) ? trim($current['requires']) : '';
                if ($requires !== '' && !isset($installed[$requires])) {
                    throw new RuntimeException('Versione richiesta non installata o precedente nel catalogo: ' . $requires);
                }
                if (!$dryRun) {
                    $this->executeSql($mysqli, $update['sql']);
                    $this->recordUpdate($mysqli, $current, 'success', false, 'Aggiornamento DB eseguito.', array('mode' => 'db', 'applied' => array('db'), 'sequence' => true));
                }
                // Nel test le dipendenze successive includono gli aggiornamenti pianificati.
                $installed[$version] = true;
                $steps[] = array('version' => $version, 'status' => $dryRun ? 'pending' : 'success', 'message' => $dryRun ? 'Da eseguire.' : 'Eseguito.');
            }
            $status = 'success';
        } catch (Throwable $e) {
            $status = 'failed';
            $steps[] = array('version' => $current ? $current['version'] : null, 'status' => 'failed', 'message' => $e->getMessage());
        } finally {
            if ($mysqli) {
                $mysqli->close();
            }
        }
        return array('customer' => $customer['customer'], 'database' => $customer['db_name'], 'status' => $status, 'updates' => $steps);
    }

    protected function processCustomer($customer, $manifest, $sql, $package, $dryRun, $mode)
    {
        $mysqli = null;
        $canRecordFailure = false;

        try {
            $mysqli = $this->connectCustomer($customer);
            if (in_array($mode, array('files', 'all'), true) && !$this->tableExists($mysqli, 'archy_core_updates')) {
                throw new RuntimeException('Installare prima la tabella archy_core_updates con la procedura DB.');
            }
            if ($mode === 'files' && $this->isFileVersionInstalled($mysqli, $manifest)) {
                $targets = $this->resolveCustomerFileTargets($customer);
                $plan = $this->readPackageOperations($package, $targets, true);
                foreach ($plan['operations'] as $operation) {
                    if (!$operation['unchanged']) {
                        throw new RuntimeException('Versione file gia registrata ma file modificato o mancante: ' . $operation['destination']);
                    }
                }
                $mysqli->close();
                return array('customer' => $customer['customer'], 'status' => 'skipped', 'message' => 'Pacchetto gia installato e file verificati.');
            }
            $this->assertUpdateRequirement($mysqli, $manifest);
            $fileTargets = in_array($mode, array('files', 'all'), true) ? $this->resolveCustomerFileTargets($customer) : null;

            if ($dryRun) {
                $filePlan = $fileTargets ? $this->planPackage($package, $fileTargets) : null;
                $mysqli->close();
                $result = array(
                    'customer' => $customer['customer'],
                    'database' => $customer['db_name'],
                    'file_targets' => $fileTargets,
                    'status' => 'success',
                    'message' => 'Controlli aggiornamento riusciti. Nessuna modifica eseguita per dry-run.',
                );

                if ($filePlan) {
                    $result['file_plan'] = $filePlan;
                }

                return $result;
            }

            $applied = array();
            $canRecordFailure = true;
            $fileResult = null;
            $backupPath = null;

            if (in_array($mode, array('db', 'all'), true)) {
                $this->executeSql($mysqli, $sql);
                $applied[] = 'db';
            }

            if (in_array($mode, array('files', 'all'), true)) {
                $fileResult = $this->applyPackage($package, $fileTargets, $customer, $manifest);
                $backupPath = isset($fileResult['backup_path']) ? $fileResult['backup_path'] : null;
                $applied[] = 'files';
            }

            $this->recordUpdate($mysqli, $manifest, 'success', false, 'Aggiornamento eseguito correttamente.', array(
                'customer' => $customer['customer'],
                'database' => $customer['db_name'],
                'file_targets' => $fileTargets,
                'mode' => $mode,
                'applied' => $applied,
                'file_result' => $fileResult,
            ), $backupPath);

            $mysqli->close();
            return array(
                'customer' => $customer['customer'],
                'database' => $customer['db_name'],
                'file_targets' => $fileTargets,
                'file_result' => $fileResult,
                'status' => 'success',
                'message' => 'Aggiornamento eseguito correttamente.',
            );
        } catch (Throwable $e) {
            if ($mysqli instanceof mysqli && !$dryRun && $canRecordFailure) {
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
        } finally {
            if ($mysqli instanceof mysqli) {
                try { $mysqli->close(); } catch (Throwable $ignored) {}
            }
        }
    }

    protected function isFileVersionInstalled($mysqli, $manifest)
    {
        $result = $mysqli->query('SELECT status, dry_run, package_sha256 FROM archy_core_updates WHERE version = ' . $this->sqlValue($mysqli, $manifest['version']));
        if (!$result) {
            throw new RuntimeException('Impossibile leggere storico file: ' . $mysqli->error);
        }
        $row = $result->fetch_assoc();
        $result->free();
        if (!$row) { return false; }
        if (empty($row['package_sha256']) || !hash_equals(strtolower($row['package_sha256']), strtolower($manifest['sha256']))) {
            throw new RuntimeException('Versione gia utilizzata con contenuto diverso. Usare una nuova versione per il pacchetto file.');
        }
        return $row['status'] === 'success' && !(int) $row['dry_run'];
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

    protected function recordUpdate($mysqli, $manifest, $status, $dryRun, $message, $details, $backupPath = null)
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
            'backup_path' => $backupPath,
            'message' => $message,
            'details' => $detailsJson,
        );

        $query = "
            INSERT INTO `archy_core_updates`
                (`version`, `requires_version`, `status`, `dry_run`, `package_file`, `package_sha256`, `sql_file`, `sql_sha256`, `backup_path`, `started_at`, `finished_at`, `message`, `details`, `created_at`)
            VALUES
                (" . $this->sqlValue($mysqli, $values['version']) . ",
                 " . $this->sqlValue($mysqli, $values['requires_version']) . ",
                 " . $this->sqlValue($mysqli, $values['status']) . ",
                 " . (int) $values['dry_run'] . ",
                 " . $this->sqlValue($mysqli, $values['package_file']) . ",
                 " . $this->sqlValue($mysqli, $values['package_sha256']) . ",
                 " . $this->sqlValue($mysqli, $values['sql_file']) . ",
                 " . $this->sqlValue($mysqli, $values['sql_sha256']) . ",
                 " . $this->sqlValue($mysqli, $values['backup_path']) . ",
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
                `backup_path` = VALUES(`backup_path`),
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

        if (empty($manifest['sha256']) || !preg_match('/^[a-f0-9]{64}$/i', $manifest['sha256'])) {
            throw new RuntimeException('Hash SHA256 del pacchetto obbligatorio (64 caratteri esadecimali).');
        }
        $packageFile = $runtimeDir . '/' . strtolower($manifest['sha256']) . '.zip';
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
                'subdomain_key' => $this->valueByKey($row, $map, 'subdomain_key'),
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
            'subdomain_key' => array('subdomain_key', 'subdomain', 'sottodominio', 'codice_sottodominio', 'subdomain_name'),
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

    protected function resolveCustomerFileTargets($customer)
    {
        $mainRoot = isset($customer['main_root']) && trim((string) $customer['main_root']) !== ''
            ? trim((string) $customer['main_root'])
            : $this->mainRoot;

        $mainRoot = str_replace('\\', '/', $mainRoot);
        $mainRootReal = realpath($mainRoot);

        if (!$mainRootReal || !is_dir($mainRootReal)) {
            throw new RuntimeException('Root principale non valida per aggiornamento files: ' . $mainRoot);
        }

        $mainRootReal = str_replace('\\', '/', $mainRootReal);
        $subdomainKey = isset($customer['subdomain_key']) ? trim((string) $customer['subdomain_key']) : '';
        $subdomainRoot = isset($customer['subdomain_root']) ? trim((string) $customer['subdomain_root']) : '';

        if ($subdomainRoot === '' && $subdomainKey !== '') {
            if (!preg_match('/^[a-zA-Z0-9._-]+$/', $subdomainKey) || in_array($subdomainKey, array('.', '..'), true)) {
                throw new RuntimeException('Codice sottodominio non valido per ' . $customer['customer'] . ': ' . $subdomainKey);
            }
            $subdomainRoot = $mainRootReal . '/subdomains/' . $subdomainKey . '/httpdocs';
        }

        $subdomainRootReal = null;
        if ($subdomainRoot !== '') {
            $subdomainRoot = str_replace('\\', '/', $subdomainRoot);
            $subdomainRootReal = realpath($subdomainRoot);
            if (!$subdomainRootReal || !is_dir($subdomainRootReal)) {
                throw new RuntimeException('Root sottodominio non valida per aggiornamento files: ' . $subdomainRoot);
            }
            $subdomainRootReal = str_replace('\\', '/', $subdomainRootReal);
            $this->assertPathWithinRoot($subdomainRootReal, $mainRootReal);
        }

        return array(
            'main_root' => $mainRootReal,
            'httpdocs_root' => $mainRootReal . '/httpdocs',
            'subdomain_root' => $subdomainRootReal,
            'subdomain_key' => $subdomainKey,
        );
    }

    protected function planPackage($package, $targets)
    {
        $plan = $this->readPackageOperations($package, $targets, true);
        $plan['operations_limited'] = count($plan['operations']) > 80;
        $plan['operations'] = $this->limitOperations($plan['operations']);
        return $plan;
    }

    protected function applyPackage($package, $targets, $customer, $manifest)
    {
        $plan = $this->readPackageOperations($package, $targets, false);
        $version = preg_replace('/[^a-zA-Z0-9._-]+/', '-', $manifest['version']);
        $backupPath = $this->updateRoot . '/backups/' . $version . '-' . bin2hex(random_bytes(8)) . '.zip';
        $zip = new ZipArchive();
        if ($zip->open($package['path']) !== true) {
            throw new RuntimeException('Impossibile aprire pacchetto file.');
        }
        $staged = array();
        $changed = array();
        $originals = array();
        $skipped = 0;
        $backup = null;
        $backupReady = false;
        try {
            foreach ($plan['operations'] as $operation) {
                if ($operation['unchanged']) { $skipped++; continue; }
                $destination = $operation['destination'];
                $this->assertPathWithinRoot($destination, $targets['main_root']);
                $content = $zip->getFromName($operation['entry']);
                if ($content === false || !hash_equals($operation['sha256'], hash('sha256', $content))) {
                    throw new RuntimeException('Contenuto ZIP non valido: ' . $operation['entry']);
                }
                $old = is_file($destination) ? file_get_contents($destination) : null;
                if ($old === false) {
                    throw new RuntimeException('Impossibile leggere originale: ' . $destination);
                }
                $originals[$destination] = array(
                    'content' => $old,
                    'mode' => $old !== null ? (fileperms($destination) & 0777) : 0644,
                );
                $staged[$destination] = $this->stageFile($destination, $content, $originals[$destination]['mode']);
            }
            if (!$staged) {
                return array('copied' => 0, 'unchanged' => $skipped, 'backup_path' => null);
            }

            $this->ensureDirectory(dirname($backupPath));
            $backup = new ZipArchive();
            if ($backup->open($backupPath, ZipArchive::CREATE | ZipArchive::EXCL) !== true) {
                throw new RuntimeException('Impossibile creare backup: ' . $backupPath);
            }
            $restore = array();
            foreach ($originals as $destination => $original) {
                $relative = substr($destination, strlen(rtrim($targets['main_root'], '/')) + 1);
                $restore[] = array('path' => $relative, 'existed' => $original['content'] !== null, 'mode' => $original['mode']);
                if ($original['content'] !== null && !$backup->addFromString('files/' . $relative, $original['content'])) {
                    throw new RuntimeException('Impossibile salvare originale nel backup: ' . $relative);
                }
            }
            if (!$backup->addFromString('restore.json', json_encode(array('version' => $manifest['version'], 'files' => $restore), JSON_PRETTY_PRINT))) {
                throw new RuntimeException('Impossibile salvare indice backup.');
            }
            // Il backup deve essere persistito prima della prima sostituzione.
            if (!$backup->close()) {
                $backup = null;
                throw new RuntimeException('Impossibile completare backup: ' . $backupPath);
            }
            $backup = null;
            $backupReady = true;
            foreach ($staged as $destination => $temp) {
                $this->assertPathWithinRoot($destination, $targets['main_root']);
                $this->replaceStagedFile($temp, $destination);
                $changed[] = $destination;
                unset($staged[$destination]);
            }
            return array('copied' => count($changed), 'unchanged' => $skipped, 'backup_path' => $backupPath);
        } catch (Throwable $e) {
            $rollbackErrors = array();
            foreach (array_reverse($changed) as $destination) {
                try {
                    $original = $originals[$destination];
                    if ($original['content'] === null) {
                        if (!unlink($destination)) { throw new RuntimeException('rimozione fallita'); }
                    } else {
                        $temp = $this->stageFile($destination, $original['content'], $original['mode']);
                        try {
                            if (!rename($temp, $destination)) { throw new RuntimeException('ripristino fallito'); }
                        } finally {
                            if (is_file($temp)) { unlink($temp); }
                        }
                    }
                    if (function_exists('opcache_invalidate')) { @opcache_invalidate($destination, true); }
                } catch (Throwable $restoreError) {
                    $rollbackErrors[] = $destination . ': ' . $restoreError->getMessage();
                }
            }
            throw new RuntimeException($e->getMessage()
                . ($backupReady ? ' Backup: ' . $backupPath . '.' : '')
                . ($rollbackErrors ? ' Ripristino incompleto: ' . implode('; ', $rollbackErrors) : ' File sostituiti ripristinati.'), 0, $e);
        } finally {
            foreach ($staged as $temp) { if (is_file($temp)) { unlink($temp); } }
            if ($backup) { $backup->close(); }
            $zip->close();
        }
    }

    protected function stageFile($destination, $content, $mode)
    {
        $this->ensureDirectory(dirname($destination));
        $temp = tempnam(dirname($destination), '.archy-update-');
        if ($temp === false) { throw new RuntimeException('Impossibile creare file temporaneo: ' . $destination); }
        try {
            if (file_put_contents($temp, $content) !== strlen($content)
                || !hash_equals(hash('sha256', $content), hash_file('sha256', $temp))
                || !chmod($temp, $mode)) {
                throw new RuntimeException('Scrittura o verifica fallita: ' . $destination);
            }
            return $temp;
        } catch (Throwable $e) {
            unlink($temp);
            throw $e;
        }
    }

    protected function replaceStagedFile($temp, $destination)
    {
        if (!rename($temp, $destination)) {
            throw new RuntimeException('Sostituzione fallita: ' . $destination);
        }
        if (function_exists('opcache_invalidate')) { @opcache_invalidate($destination, true); }
    }

    protected function readPackageOperations($package, $targets, $dryRun)
    {
        if (!is_array($package) || empty($package['path']) || !is_file($package['path'])) {
            throw new RuntimeException('Pacchetto aggiornamento files non disponibile.');
        }

        $zip = new ZipArchive();
        if ($zip->open($package['path']) !== true) {
            throw new RuntimeException('Impossibile aprire pacchetto aggiornamento files.');
        }

        $operations = array();
        $total = 0;
        $destinations = array();
        $bytes = 0;
        try {
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entry = $zip->getNameIndex($i);
            if (!$this->isSafeZipEntry($entry)) {
                throw new RuntimeException('Percorso ZIP non consentito: ' . $entry);
            }
            $stat = $zip->statIndex($i);
            $zip->getExternalAttributesIndex($i, $opsys, $attributes);
            if ((($attributes >> 16) & 0170000) === 0120000) {
                throw new RuntimeException('Link simbolico ZIP non consentito: ' . $entry);
            }
            if (substr($entry, -1) === '/') {
                continue;
            }

            $destination = $this->resolvePackageEntryDestination($entry, $targets);
            $key = PHP_OS_FAMILY === 'Windows' ? strtolower($destination) : $destination;
            if (isset($destinations[$key])) {
                throw new RuntimeException('Destinazione duplicata nel pacchetto: ' . $entry);
            }
            $destinations[$key] = true;
            $bytes += $stat['size'];
            if ($bytes > 268435456 || $total >= 5000) {
                throw new RuntimeException('Pacchetto oltre i limiti: 256 MiB decompressi o 5000 file.');
            }
            if (file_exists($destination) && !is_file($destination)) {
                throw new RuntimeException('La destinazione non e un file: ' . $destination);
            }
            $parent = dirname($destination);
            while (!file_exists($parent)) { $parent = dirname($parent); }
            if (!is_dir($parent) || !is_writable($parent) || (is_file($destination) && !is_readable($destination))) {
                throw new RuntimeException('Permessi insufficienti: ' . $destination);
            }
            $content = $zip->getFromIndex($i);
            if ($content === false || strlen($content) !== $stat['size']
                || strtolower(hash('crc32b', $content)) !== sprintf('%08x', $stat['crc'])) {
                throw new RuntimeException('File ZIP danneggiato: ' . $entry);
            }
            $hash = hash('sha256', $content);
            $unchanged = is_file($destination) && hash_equals($hash, hash_file('sha256', $destination));
            $total++;
            $operations[] = array(
                'entry' => $entry,
                'destination' => $destination,
                'sha256' => $hash,
                'unchanged' => $unchanged,
                'exists' => is_file($destination),
                'action' => $unchanged ? 'unchanged' : ($dryRun ? (is_file($destination) ? 'would_replace' : 'would_create') : 'pending'),
            );
        }
        if (!$total) { throw new RuntimeException('Pacchetto senza file.'); }
        } finally {
        $zip->close();
        }

        return array(
            'package_source' => isset($package['source']) ? $package['source'] : null,
            'total_files' => $total,
            'operations' => $operations,
            'operations_limited' => false,
        );
    }

    protected function resolvePackageEntryDestination($entry, $targets)
    {
        $entry = str_replace('\\', '/', (string) $entry);

        if (!$this->isSafeZipEntry($entry)) {
            throw new RuntimeException('Percorso non consentito nel pacchetto: ' . $entry);
        }

        if ($this->isForbiddenPackageEntry($entry)) {
            throw new RuntimeException('File non consentito nel pacchetto: ' . $entry);
        }

        if (strpos($entry, 'subdomain/httpdocs/') === 0) {
            if (empty($targets['subdomain_root'])) {
                throw new RuntimeException('Il pacchetto contiene file per sottodominio ma nel CSV manca subdomain_key o subdomain_root.');
            }

            $relative = substr($entry, strlen('subdomain/httpdocs/'));
            $destination = rtrim($targets['subdomain_root'], '/') . '/' . $relative;
            $this->assertPathWithinRoot($destination, $targets['subdomain_root']);
            return $destination;
        }

        if (strpos($entry, 'private/') === 0) {
            $destination = rtrim($targets['main_root'], '/') . '/' . $entry;
            $this->assertPathWithinRoot($destination, $targets['main_root']);
            return $destination;
        }

        if (strpos($entry, 'httpdocs/') === 0) {
            $destination = rtrim($targets['main_root'], '/') . '/' . $entry;
            $this->assertPathWithinRoot($destination, $targets['main_root']);
            return $destination;
        }

        throw new RuntimeException('Percorso pacchetto non riconosciuto: ' . $entry . '. Usa private/, httpdocs/ oppure subdomain/httpdocs/.');
    }

    protected function isForbiddenPackageEntry($entry)
    {
        $entry = strtolower(str_replace('\\', '/', (string) $entry));
        $basename = basename($entry);

        if (strpos($basename, '.env') === 0 || in_array($basename, array('config-local.php', 'customers.csv', 'customers.xlsx', 'customers.xls'), true)
            || preg_match('~(^|/)(\.git|\.svn|uploads|storage|backups|logs)(/|$)~', $entry)) {
            return true;
        }

        $blockedPrefixes = array(
            'private/_common/config.php',
            'private/_common/update/logs/',
            'private/_common/update/backups/',
            'private/_common/update/runtime/',
            'assets/storage/',
            'assets/uploads/',
            'httpdocs/assets/storage/',
            'httpdocs/assets/uploads/',
            'subdomain/httpdocs/assets/storage/',
            'subdomain/httpdocs/assets/uploads/',
        );

        foreach ($blockedPrefixes as $prefix) {
            if (strpos($entry, $prefix) === 0) {
                return true;
            }
        }

        return false;
    }

    protected function assertPathWithinRoot($path, $root)
    {
        $probe = str_replace('\\', '/', $path);
        while ($probe !== dirname($probe)) {
            if (is_link($probe)) { throw new RuntimeException('Link simbolico nella destinazione: ' . $probe); }
            $probe = dirname($probe);
        }
        $parent = $path;
        while (!file_exists($parent)) { $parent = dirname($parent); }
        $resolved = str_replace('\\', '/', realpath($parent));
        $path = str_replace('\\', '/', $path);
        $root = rtrim(str_replace('\\', '/', $this->normalizePath($root)), '/');
        if (PHP_OS_FAMILY === 'Windows') {
            $path = strtolower($path); $root = strtolower($root); $resolved = strtolower($resolved);
        }

        if (($path !== $root && strpos($path, $root . '/') !== 0)
            || ($resolved !== $root && strpos($resolved, $root . '/') !== 0)) {
            throw new RuntimeException('Destinazione fuori dalla root consentita: ' . $path);
        }
    }

    protected function limitOperations($operations)
    {
        if (count($operations) <= 80) {
            return $operations;
        }

        return array_slice($operations, 0, 80);
    }

    protected function isSafeZipEntry($entry)
    {
        $entry = str_replace('\\', '/', (string) $entry);

        if ($entry === '' || strpos($entry, "\0") !== false) {
            return false;
        }

        foreach (explode('/', rtrim($entry, '/')) as $segment) {
            if ($segment === '' || preg_match('/[. ]$/', $segment)) { return false; }
        }

        if (preg_match('~(^|/)\.{1,2}(/|$)~', $entry) || strpos($entry, '//') !== false
            || preg_match('/[\x00-\x1f:]/', $entry)) {
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
