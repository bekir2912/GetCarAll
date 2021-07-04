<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'homeUrl' => '/',
    'name' => 'GTA',
    'language' => 'ru-RU',
    'components' => [
        'request' => [
            'class' => 'frontend\components\LanguageRequest',
            'csrfParam' => '_csrf-frontend',
            'baseUrl' => '',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
            'timeout' => 3600*24*30,
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
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'assetManager' => [
            'bundles' => [
                'dosamigos\google\maps\MapAsset' => [
                    'options' => [
                        'key' => 'AIzaSyB_x1j3WR5rH6hMDlm2_wfcVK7EI-30fx8',
                        'language' => 'ru',
                        'version' => '3.1.18'
                    ]
                ]
            ]
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'class'=>'frontend\components\LanguageUrlManager',
            'rules'=>[
                '/' => 'site/index',
//                'callback' => 'site/callback',
                'page/<id:.+>' => 'site/page',
                'category/<id:.+>' => 'category/index',
                'product/<id:.+>' => 'product/index',
                'shop/<id:.+>/<cat:.+>' => 'shop/index',
//                'brand/<id:.+>' => 'brand/index',
//                'map/index/<id:.+>' => 'map/index',
//                'all-news' => 'news/list',
//                'all-categories' => 'category/list',
                'all-shops/<id:.+>' => 'shop/list',
//                'all-brands' => 'brand/list',
//                'news/<id:.+>' => 'news/index',
//                'user/purchase/<id:\d+>' => 'user/purchase',
//                'search' => 'search/index',
                '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
            ]
        ],

//        'urlManagerBackEnd' => [
//            'class' => 'yii\web\urlManager',
//            'baseUrl' => '/store',
//            'enablePrettyUrl' => true,
//            'showScriptName' => false,
//        ],

    ],
    'params' => $params,
];
