<?php

$settingsFile = dirname(__DIR__) . '/storage/settings.json';
$runtimeSettings = [];
if (file_exists($settingsFile)) {
    $runtimeSettings = json_decode(file_get_contents($settingsFile), true) ?: [];
}

return [
    'name' => $runtimeSettings['site_name'] ?? (getenv('APP_NAME') ?: 'Babura House Connect'),
    'env' => getenv('APP_ENV') ?: 'local',
    'debug' => filter_var(getenv('APP_DEBUG') ?: true, FILTER_VALIDATE_BOOLEAN),
    'url' => getenv('APP_URL') ?: 'http://localhost:8000',
    'maintenance' => isset($runtimeSettings['maintenance_mode']) ? (bool)$runtimeSettings['maintenance_mode'] : filter_var(getenv('APP_MAINTENANCE') ?: false, FILTER_VALIDATE_BOOLEAN),
    'session_lifetime' => (int) (getenv('SESSION_LIFETIME') ?: 120),
    'support_email' => $runtimeSettings['support_email'] ?? 'support@houseconnect.ng',
    'smtp_host' => $runtimeSettings['smtp_host'] ?? 'smtp.mailtrap.io',
    'smtp_port' => $runtimeSettings['smtp_port'] ?? '2525',
    'smtp_encryption' => $runtimeSettings['smtp_encryption'] ?? 'TLS',
];
