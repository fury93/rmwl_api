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
    'timeZone' => 'America/Los_Angeles',
    'components' => [
        'user' => [
            'identityClass' => 'rest\versions\v1\models\User',
            'enableSession' => false,
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'defaultRoles' => ['Guest', 'Admin', 'Patient', 'Entry', 'Management'],
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
                        'v1/user',
                    ],
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST login' => 'login',
                        'OPTIONS login' => 'login',
                        'POST logout' => 'logout',
                        'OPTIONS logout' => 'options',
                        'POST edit/<id:\d+>' => 'edit',
                        'OPTIONS edit/<id:\d+>' => 'options',
                        'POST check-authentication' => 'check-authentication',
                        'OPTIONS check-authentication' => 'options',
                        'POST reset-password' => 'reset-password',
                        'OPTIONS reset-password' => 'options',
                        'POST change-password' => 'change-password',
                        'OPTIONS change-password' => 'options',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => [
                        'v1/product'
                    ],
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST edit/<id:\d+>' => 'edit',
                        'OPTIONS edit/<id:\d+>' => 'options',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => [
                        'v1/patient'
                    ],
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST edit/<id:\d+>' => 'edit',
                        'OPTIONS edit/<id:\d+>' => 'options',
                    ],
                ],
                'GET v1/permission/roles-permission' => 'v1/permission/roles-permission',
                'GET v1/permission/user-permission/<id:\d+>' => 'v1/permission/user-permission',
                'POST v1/permission/roles-permission' => 'v1/permission/update-roles-permission',
                'POST v1/permission/user-permission/<id:\d+>' => 'v1/permission/update-user-permission',

                'OPTIONS v1/permission/roles-permission' => 'v1/permission/options',
                'OPTIONS v1/permission/user-permission/<id:\d+>' => 'v1/permission/options',
            ],
        ],
    ],
    'params' => $params,
];
