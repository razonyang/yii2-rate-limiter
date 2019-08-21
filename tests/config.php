<?php

return [
    'id' => 'test',
    'basePath' => __DIR__,
    'controllerMap' => [
        'migrate' => [
            'class' => \yii\console\controllers\MigrateController::class,
            'migrationNamespaces' => [
            ],
        ],
    ],
    'components' => [
        'redis' => [
            'class' => \yii\redis\Connection::class,
            'hostname' => 'localhost',
            'port' => 6379,
            // 'password' => 'foobar'
        ],
        'user' => [
            'identityClass' => \ RazonYang\Yii2\RateLimiter\Tests\TestUser::class,
        ],
        'request' => [
            'cookieValidationKey' => 'test',
        ],
    ],
];
