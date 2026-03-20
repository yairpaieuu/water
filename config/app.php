<?php
declare(strict_types=1);

return [

    'app' => [
        'name'     => 'AquaCRM',
        'url'      => 'http://localhost',
        'timezone' => 'Asia/Colombo',
        'env'      => 'production',
        'debug'    => false,
    ],

    'db' => [
        'host'     => '127.0.0.1',
        'port'     => '3306',
        'dbname'   => 'u521037437_water',
        'username' => 'u521037437_water',
        'password' => '$Citytaxi02',
        'charset'  => 'utf8mb4',
        'options'  => [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ],
    ],

    'session' => [
        'name'     => 'aquacrm_session',
        'lifetime' => 7200,
        'secure'   => false,
        'httponly' => true,
        'samesite' => 'Lax',
    ],

    'upload' => [
        'max_size'      => 5242880, // 5 MB
        'allowed_types' => ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'],
    ],

];
