<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Questo comando puo essere eseguito solo da CLI.');
}

$subdomain_root = realpath(__DIR__ . '/../..');
$main_root = realpath($subdomain_root . '/../../..');

if (!$subdomain_root || !$main_root) {
    fwrite(STDERR, "Root gestionale non rilevata.\n");
    exit(1);
}

require_once $main_root . '/private/_common/classes/load_classes.php';

$options = array(
    'main_root' => $main_root,
    'subdomain_root' => $subdomain_root,
    'dry_run' => true,
);

foreach (array_slice($argv, 1) as $argument) {
    if (strpos($argument, '--') !== 0) {
        continue;
    }

    $argument = substr($argument, 2);
    $parts = explode('=', $argument, 2);
    $key = str_replace('-', '_', $parts[0]);
    $value = count($parts) === 2 ? $parts[1] : true;

    $options[$key] = $value;
}

try {
    $updater = new CoreUpdate($options);
    $result = $updater->run($options);

    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
    exit(0);
} catch (Throwable $e) {
    fwrite(STDERR, $e->getMessage() . PHP_EOL);
    exit(1);
}
