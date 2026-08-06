<?php

require_once __DIR__ . '/../src/Helpers/helpers.php';

// Initialize autoloader fallback
$composerAutoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
} else {
    spl_autoload_register(function ($class) {
        $prefix = 'App\\';
        $base_dir = __DIR__ . '/../src/';
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }
        $relative_class = substr($class, $len);
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    });
}

// 1. Load Environment Variables
$dotenv = new \App\Core\Dotenv(__DIR__ . '/../.env');
$dotenv->load();

// 2. Set Config path
\App\Core\Config::setPath(__DIR__ . '/../config');

// 3. Initialize Logger & Error Handler
$logger = new \App\Core\Logger(__DIR__ . '/../storage/logs');
$errorHandler = new \App\Core\ErrorHandler($logger);
$errorHandler->register();

// 4. Secure Session Configurations
$sessionLifetime = \App\Core\Config::get('app.session_lifetime', 120) * 60;
ini_set('session.cookie_lifetime', $sessionLifetime);
ini_set('session.gc_maxlifetime', $sessionLifetime);
ini_set('session.cookie_httponly', '1');
ini_set('session.use_only_cookies', '1');

$appUrl = \App\Core\Config::get('app.url', 'http://localhost:8000');
if (strpos($appUrl, 'https://') === 0) {
    ini_set('session.cookie_secure', '1');
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inactivity Session Timeout
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $sessionLifetime)) {
    session_unset();
    session_destroy();
    session_start();
}
$_SESSION['LAST_ACTIVITY'] = time();

// CSRF Token Setup
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// 5. Maintenance Mode Check
if (\App\Core\Config::get('app.maintenance', false)) {
    http_response_code(503);
    $controller = new \App\Controllers\BaseController();
    $controller->setLayout('main');
    echo $controller->render('public/maintenance', ['title' => 'Service Under Maintenance']);
    exit;
}

return [
    'logger' => $logger,
];
