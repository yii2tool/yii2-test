<?php

use yii\helpers\ArrayHelper;
use yii2tool\test\helpers\TestHelper;

$config = [
	'url' => [
		'frontend' => 'http://example.com/',
		'backend' => 'http://admin.example.com/',
		'api' => 'http://api.example.com/',
	],
    /*'aliases' => [
        '@common/data' => '@yii2tool/test/base/_application/common/data',
    ],*/
	'cookieValidationKey' => [
		'frontend' => 'bBXEWnH5ERCG7SF3wxtbotYxq3W-Op7B',
		'backend' => 'zbfqVR5PhdO3E8Xi7DB4aoxmxSstJ6aI',
	],
	'servers' => [
		'db' => [
			'main' => [
				'driver' => 'sqlite',
				'dbname' => '@yii2tool/test/db/test.db',
			],
			'test' => [
				'driver' => 'sqlite',
				'dbname' => '@yii2tool/test/db/test.db',
			],
		],
		'static' => [
			'publicPath' => '@frontend/web/',
			'domain' => 'https://static.example.com/',
			'driver' => 'local',
			'connection' => [
				'path' => '@frontend/web',
			],
		],
        'filedb' => [
            'path' => '@yii2tool/test/base/_application/common/data',
        ],
	],
];

$forceConfig = [
	'project' => 'test',
	'mode' => [
		'debug' => true,
		'env' => 'dev',
	],
	'domain' => [
		'driver' => [
			'primary' => 'filedb',
			'slave' => 'ar',
		],
	],
];

//$appConfig = TestHelper::loadConfig('common/config/env-local.php', '');
return ArrayHelper::merge($config, $forceConfig);
