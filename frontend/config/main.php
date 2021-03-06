<?php
use kartik\mpdf\Pdf;
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
    'components' => [
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
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
    ],
    'modules' => [
  
        'markdown' => [
            'class' => 'common\widgets\markdown\Module',
            'previewAction' => '/site/markdown-preview',            
                // Smarty class configuration
             'smartyParams' => [],
             // provide Yii::$app to the Smarty template as variable
             'smartyYiiApp' => true,
             // provide Yii::$app->params to the Smarty template as config variables
             'smartyYiiParams' => true,
        ]
    ],
    'aliases' => [
        '@mathjax' => '@vendor/mathjax/mathjax',
    ],
    'params' => $params,
];
