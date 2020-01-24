<?php

return [
   'default' => 'intrasm',
   'connections' => [
        'intrasm' => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST'),
            'port'      => env('DB_PORT'),
            'database'  => env('DB_DATABASE'),
            'username'  => env('DB_USERNAME'),
            'password'  => env('DB_PASSWORD'),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],
        'cam' => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST3'),
            'port'      => env('DB_PORT'),
            'database'  => env('DB_DATABASE3'),
            'username'  => env('DB_USERNAME3'),
            'password'  => env('DB_PASSWORD3'),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],
        'mba' => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST2'),
            'port'      => env('DB_PORT'),
            'database'  => env('DB_DATABASE2'),
            'username'  => env('DB_USERNAME2'),
            'password'  => env('DB_PASSWORD2'),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],
    ],
];