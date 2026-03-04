<?php
/** Cargador de variables .env */
class Environment {

    /** Carga .env a putenv y $_ENV */
    public static function load($path) {
        $envFile = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '.env';

        if (!file_exists($envFile)) {
            return;
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || strpos($line, '#') === 0) {
                continue;
            }

            if (strpos($line, '=') === false) {
                continue;
            }

            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value, " \t\n\r\0\x0B\"'");

            if (!empty($key)) {
                putenv("$key=$value");
                $_ENV[$key] = $value;
            }
        }
    }

    /** Obtiene variable con fallback */
    public static function get($key, $default = null) {
        $value = getenv($key);
        return ($value === false) ? $default : $value;
    }

    /** Entorno producción? */
    public static function isProduction() {
        return self::get('APP_ENV', 'development') === 'production';
    }

    /** Entorno desarrollo? */
    public static function isDevelopment() {
        return self::get('APP_ENV', 'development') === 'development';
    }
}
