<?php

return [
    'name' => 'HackLib Panel',
    'debug' => true,
    'db_path' => __DIR__ . '/../storage/database.sqlite',
    'session_lifetime' => 3600, // 1 hour
    'master_admin' => [
        'username' => 'admin',
        'password' => 'admin123',
        'email' => 'admin@hacklib.dev',
    ],
];
