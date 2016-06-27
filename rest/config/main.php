<?php

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'rest-api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'modules' => [
        'v1' => [
            'class' => 'rest\versions\v1\RestModule'
        ],
    ],
    'components' => [
        'user' => [
            'identityClass' => 'rest\versions\v1\models\User',
            'enableSession' => false,
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'defaultRoles' => ['guest', 'employee', 'admin', 'patient'],
        ],
        'response' => [
            'format' => yii\web\Response::FORMAT_JSON,
            'charset' => 'UTF-8',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'request' => [
            'class' => '\yii\web\Request',
            'enableCookieValidation' => false,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
                'application/x-www-form-urlencoded' => 'yii\web\JsonParser'
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => [
                        'v1/user'
                    ],
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST login' => 'login',
                        //'OPTIONS v1/user/logout' => 'options',
                        'POST logout' => 'logout',
                        'OPTIONS logout' => 'options', //todo CORS problem with options requests
//                        'POST register' => 'register',
                        /*'POST v1/user/update/<id:\d+>'=> 'v1/user/update',
                        'POST v1/user/delete/<id:\d+>'=> 'v1/user/delete',
                        'GET v1/user/view/<id:\d+>'=> 'v1/user/view',*/
                    ],
                ],
                /*'<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>'*/

            ],
        ],
    ],
    'params' => $params,
];
