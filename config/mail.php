<?php

return [
    'mailer' => getenv('MAIL_MAILER') ?: 'smtp',
    'host' => getenv('MAIL_HOST') ?: 'smtp.mailtrap.io',
    'port' => getenv('MAIL_PORT') ?: 2525,
    'username' => getenv('MAIL_USERNAME') ?: '',
    'password' => getenv('MAIL_PASSWORD') ?: '',
    'encryption' => getenv('MAIL_ENCRYPTION') ?: null,
    'from' => [
        'address' => getenv('MAIL_FROM_ADDRESS') ?: 'info@houseconnect.ng',
        'name' => getenv('MAIL_FROM_NAME') ?: 'Babura House Connect',
    ],
];
