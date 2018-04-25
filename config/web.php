<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'defaultRoute' => 'members/index',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'GyxaVmwLMtfO-nkyf4sLk9cL-LJjxwfN',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\Member',
            'enableAutoLogin' => true,
            'loginUrl' => '/site/login'
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '/tourney/new'=>'tourney/new',
                '/tourney/update'=>'tourney/update',
                '/tourney/<slug:[-a-zA-Z0-9]+>'=>'tourney/view',
                '/tourneys'=>'tourney/index',
                '/tactics/<slug:[-a-zA-Z0-9]+>'=>'tactics/level',
                '/tactics/<level:[-a-zA-Z0-9]+>/<test:[0-9]+>'=>'tactics/test',
                '/tactics/<level:[-a-zA-Z0-9]+>/<test:[0-9]+>/start'=>'tactics/start',
                '/tactics/<level:[-a-zA-Z0-9]+>/<test:[0-9]+>/answer'=>'tactics/answer',
                '/tactics/<level:[-a-zA-Z0-9]+>/<test:[0-9]+>/finish'=>'tactics/finish',
                '/tactics/<level:[-a-zA-Z0-9]+>/<test:[0-9]+>/result'=>'tactics/result',
                '/tactics/<level:[-a-zA-Z0-9]+>/<test:[0-9]+>/image<position:[0-9]+>'=>'tactics/image',
            ]
        ],

    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    /*$config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];*/

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
