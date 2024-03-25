<?php

return [
    'app'                => 'api',
    'default_controller' => 'index',
    'default_method'     => 'index',
    'db'                 => [
        'host'   => '127.0.0.1',
        'port'   => 3306,
        'dbname' => '',
        'user'   => '',
        'password'   => '',
    ],
    'redis'=>[
        'host'   => '',
        'port'   => 6379,
        'password'   => '',
        'database'   => 1
    ]
];