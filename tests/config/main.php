<?php

use albertborsos\couchbase\Connection;

$config = [
    'components' => [
        'couchbase' => [
            // travis config
            'class' => Connection::class,
            'dsn' => '127.0.0.1?detailed_errcodes=true',
            'username' => 'Administrator',
            'password' => 'password',
            'defaultBucketName' => 'default',
            'defaultBucketPassword' => '',
        ],
    ],
];

$localConfigFile = dirname(__FILE__) . '/main.local.php';

$localConfig = [];
if (is_file($localConfigFile)) {
    $localConfig = require($localConfigFile);
}

return \yii\helpers\ArrayHelper::merge($config, $localConfig);
