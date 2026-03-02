<?php
/**
 * Application Configuration
 * Loads .env file and returns configuration array
 */

// Load .env file
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || $line[0] === '#') continue;
        if (strpos($line, '=') === false) continue;
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value, " \t\n\r\0\x0B\"'");
        if (!array_key_exists($key, $_ENV)) {
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}

/**
 * Get environment variable with optional default
 */
function env(string $key, $default = null) {
    $value = getenv($key);
    if ($value === false) {
        return $default;
    }
    // Handle boolean-like strings
    $lower = strtolower($value);
    if ($lower === 'true') return true;
    if ($lower === 'false') return false;
    if ($lower === 'null') return null;
    return $value;
}

return [
    'app' => [
        'name'  => env('APP_NAME', 'Super Disparador AZ'),
        'url'   => env('APP_URL', 'http://localhost:8000'),
        'env'   => env('APP_ENV', 'development'),
    ],
    'database' => [
        'host'  => env('DB_HOST', 'localhost'),
        'port'  => env('DB_PORT', '3306'),
        'name'  => env('DB_NAME', 'super_disparador'),
        'user'  => env('DB_USER', 'root'),
        'pass'  => env('DB_PASS', ''),
    ],
    'evolution' => [
        'url'   => env('EVOLUTION_API_URL', ''),
        'token' => env('EVOLUTION_API_TOKEN', ''),
    ],
    'openai' => [
        'key'   => env('OPENAI_API_KEY', ''),
        'model' => env('OPENAI_MODEL', 'gpt-4.1'),
    ],
    'supabase' => [
        'url'    => env('SUPABASE_URL', ''),
        'secret' => env('SUPABASE_SECRET_KEY', ''),
    ],
];
