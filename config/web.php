<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'name'=>'deliveryservice',
    'language'=>'ru-RU',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'Leonid',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
                'multipart/form-data' => 'yii\web\MultipartFormDataParser',
            ]
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
            'enableSession' => false,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
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
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [

                'POST register' => 'user/create',
                'POST login' => 'user/login',
                'POST admin/courier_reg' => 'user/create_courier',
                'PATCH <id>/profile/edit' => 'user/edit',
                'DELETE admin/delete/<id>' => 'user/delete',
                'POST <id>/store/reg' => 'store/create',
                'PATCH <id>/store/edit' => 'store/edit',
                'DELETE <id>/store/delete' => 'store/delete',
                'POST <id>/orders/create' => 'order/create',
                'GET orders/show_all' => 'order/view_all',
                'PATCH <id>/orders/take'=>'order/take',
                'PATCH <id>/orders/edit_status' => 'order/edit',
                'GET <id>/orders/show_my' => 'order/view_my',
                'GET <id>/orders/show' => 'order/view',

                //  ['class' => 'yii\rest\UrlRule', 'controller' => 'user'],
                //   ['class' => 'yii\rest\UrlRule', 'controller' => 'store'],
                //   ['class' => 'yii\rest\UrlRule', 'controller' => 'order'],

            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '::1', '*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '::1', '*'],
    ];
}

return $config;
