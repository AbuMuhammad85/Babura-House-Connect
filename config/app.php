<?php

return [
    'name' => getenv('APP_NAME') ?: 'Babura House Connect',
    'env' => getenv('APP_ENV') ?: 'local',
    'debug' => filter_var(getenv('APP_DEBUG') ?: true, FILTER_VALIDATE_BOOLEAN),
    'url' => getenv('APP_URL') ?: 'http://localhost:8000',
    'maintenance' => filter_var(getenv('APP_MAINTENANCE') ?: false, FILTER_VALIDATE_BOOLEAN),
    'session_lifetime' => (int) (getenv('SESSION_LIFETIME') ?: 120),
];
