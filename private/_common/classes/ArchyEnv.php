<?php

class ArchyEnv
{
    protected static $env = null;

    public static function value($keys, $default = '')
    {
        $keys = is_array($keys) ? $keys : array($keys);
        $env = self::load();

        foreach ($keys as $key) {
            if (isset($env[$key]) && $env[$key] !== '') {
                return $env[$key];
            }

            if (isset($_ENV[$key]) && $_ENV[$key] !== '') {
                return $_ENV[$key];
            }

            if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') {
                return $_SERVER[$key];
            }

            $value = getenv($key);
            if ($value !== false && $value !== '') {
                return $value;
            }
        }

        return $default;
    }

    public static function load()
    {
        if (self::$env !== null) {
            return self::$env;
        }

        self::$env = self::readEnvFiles(self::paths());

        return self::$env;
    }

    public static function paths()
    {
        $commonRoot = dirname(__DIR__);
        $mainRoot = dirname(dirname(dirname(__DIR__)));
        $envPaths = array(
            $commonRoot . '/.env',
            $mainRoot . '/.env',
        );

        if (isset($_SERVER['DOCUMENT_ROOT']) && $_SERVER['DOCUMENT_ROOT'] !== '') {
            $envPaths[] = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\') . '/.env';
        }

        return array_values(array_unique($envPaths));
    }

    public static function diagnostics($key)
    {
        $env = self::load();
        $value = self::value($key, '');
        $paths = array();

        foreach (self::paths() as $path) {
            $paths[] = array(
                'path' => $path,
                'exists' => file_exists($path),
                'readable' => is_readable($path),
                'contains_key' => is_readable($path) ? self::fileContainsKey($path, $key) : false,
            );
        }

        return array(
            'key' => $key,
            'loaded_from_env_array' => isset($env[$key]) && $env[$key] !== '',
            'loaded' => $value !== '',
            'length' => strlen((string) $value),
            'fingerprint' => $value !== '' ? substr(hash('sha256', (string) $value), 0, 12) : '',
            'paths' => $paths,
        );
    }

    protected static function fileContainsKey($path, $key)
    {
        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if ($line === '' || strpos($line, '#') === 0 || strpos($line, '=') === false) {
                continue;
            }

            list($lineKey) = explode('=', $line, 2);
            $lineKey = preg_replace('/^\xEF\xBB\xBF/', '', trim($lineKey));
            $lineKey = preg_replace('/^export\s+/', '', $lineKey);

            if ($lineKey === $key) {
                return true;
            }
        }

        return false;
    }

    protected static function readEnvFiles($envPaths)
    {
        $env = array();

        foreach ($envPaths as $envPath) {
            if (!is_readable($envPath)) {
                continue;
            }

            foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                $line = trim($line);
                if ($line === '' || strpos($line, '#') === 0 || strpos($line, '=') === false) {
                    continue;
                }

                list($key, $value) = explode('=', $line, 2);
                $key = preg_replace('/^\xEF\xBB\xBF/', '', trim($key));
                $key = preg_replace('/^export\s+/', '', $key);
                $value = trim($value);

                if ($value !== '' && !in_array($value[0], array('"', "'"), true)) {
                    $commentPosition = strpos($value, ' #');
                    if ($commentPosition !== false) {
                        $value = substr($value, 0, $commentPosition);
                    }
                }

                $env[trim($key)] = trim($value, " \t\n\r\0\x0B\"'");
            }
        }

        return $env;
    }
}
