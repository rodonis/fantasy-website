<?php
declare(strict_types=1);

// Load .env file from project root (two levels up from src/app/)
(static function (): void {
    $envFile = dirname(__DIR__, 2) . '/.env';
    if (!file_exists($envFile)) return;
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#' || !str_contains($line, '=')) continue;
        [$k, $v] = explode('=', $line, 2);
        $k = trim($k);
        $v = trim($v, " \t\"'");
        if ($k !== '' && !isset($_ENV[$k])) {
            $_ENV[$k] = $v;
            putenv("$k=$v");
        }
    }
})();

// Autoload
spl_autoload_register(function (string $class): void {
    $paths = [
        __DIR__ . '/lib/' . $class . '.php',
        __DIR__ . '/' . $class . '.php',
        __DIR__ . '/controllers/' . $class . '.php',
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

require_once __DIR__ . '/lib/Parsedown.php';
require_once __DIR__ . '/lib/ParsedownExtra.php';
require_once __DIR__ . '/Markdown.php';
require_once __DIR__ . '/Db.php';
require_once __DIR__ . '/Auth.php';
require_once __DIR__ . '/Router.php';
require_once __DIR__ . '/controllers/PageController.php';
require_once __DIR__ . '/controllers/SearchController.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/UploadController.php';

// Session
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');
    session_start();
}

// Tweaks from cookie (theme, density, font, gmReveal)
function get_tweaks(): array {
    $defaults = ['theme' => 'parchment', 'density' => 'cozy', 'font' => 'serif', 'gmReveal' => false];
    if (!isset($_COOKIE['wiki_tweaks'])) return $defaults;
    $secret = $_ENV['WIKI_SECRET'] ?? getenv('WIKI_SECRET') ?: 'changeme';
    [$sig, $data] = explode('.', $_COOKIE['wiki_tweaks'], 2) + ['', ''];
    if (!hash_equals(hash_hmac('sha256', $data, $secret), $sig)) return $defaults;
    $t = json_decode(base64_decode($data), true);
    return is_array($t) ? array_merge($defaults, $t) : $defaults;
}
