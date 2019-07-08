<?php

use albertborsos\couchbase\Connection;

$config = [
    'components' => [
        'couchbase' => [
            // travis config
            'class' => Connection::class,
            'dsn' => '0.0.0.0?detailed_errcodes=true',
            'username' => 'admin',
            'password' => 'password',
            'defaultBucketName' => 'travel-sample',
            'defaultBucketPassword' => 'password',
        ],
    ],
];

$localConfigFile = dirname(__FILE__) . '/main.local.php';

$localConfig = [];
if (is_file($localConfigFile)) {
    $localConfig = require($localConfigFile);
}

return \yii\helpers\ArrayHelper::merge($config, $localConfig);
