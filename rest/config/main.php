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
                    //'except' => ['update'],
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST login' => 'login',
                        'POST logout' => 'logout',
                        'POST edit/<id:\d+>' => 'edit',
                        'OPTIONS edit/<id:\d+>' => 'options',
                        'OPTIONS logout' => 'options',
                    ],
                ],
                //'POST v1/<controller:\w+>/update/<id:\d+>' => 'v1/<controller>/update',
            ],
        ],
    ],
    'params' => $params,
];
