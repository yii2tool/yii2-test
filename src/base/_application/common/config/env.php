<?php

use yii2lab\app\domain\commands\ApiVersion;
use yii2lab\app\domain\commands\RunBootstrap;
use yii2lab\app\domain\filters\config\LoadConfig;
use yii2lab\app\domain\filters\config\LoadModuleConfig;
use yii2lab\domain\filters\LoadDomainConfig;
use yii2lab\app\domain\enums\YiiEnvEnum;
use yii2lab\app\domain\filters\config\LoadRouteConfig;

$basePath = TEST_APPLICATION_DIR . DS;

return [
	'app' => [
		'commands' => [
			[
				'class' => RunBootstrap::class,
				'app' => $basePath . COMMON,
			],
			[
				'class' => RunBootstrap::class,
				'app' => $basePath . APP,
			],
			[
				'class' => ApiVersion::class,
				'isEnabled' => APP == API || APP == CONSOLE,
			],
            [
                'class' => 'yii2lab\domain\filters\DefineDomainLocator',
                'filters' => [
                    [
                        'class' => LoadDomainConfig::class,
                        'app' => $basePath . COMMON,
                        'name' => 'domains',
                        'withLocal' => true,
                    ],
                ],
            ],
		],
	],
	'config' => [
		'filters' => [
			[
				'class' => LoadConfig::class,
				'app' => $basePath . COMMON,
				'name' => 'main',
				'withLocal' => true,
			],
			[
				'class' => LoadConfig::class,
				'app' => $basePath . APP,
				'name' => 'main',
				'withLocal' => true,
			],
			
			[
				'class' => LoadModuleConfig::class,
				'app' => $basePath . COMMON,
				'name' => 'modules',
				'withLocal' => true,
			],
			[
				'class' => LoadModuleConfig::class,
				'app' => $basePath . APP,
				'name' => 'modules',
				'withLocal' => true,
			],

            [
                'class' => LoadRouteConfig::class,
                'app' => $basePath . COMMON,
                'name' => 'routes',
                'withLocal' => true,
            ],
            [
                'class' => LoadRouteConfig::class,
                'app' => $basePath . APP,
                'name' => 'routes',
                'withLocal' => true,
            ],
			
			[
				'class' => LoadConfig::class,
				'app' => $basePath . COMMON,
				'name' => 'params',
				'withLocal' => true,
				'assignTo' => 'params',
			],
			[
				'class' => LoadConfig::class,
				'app' => $basePath . APP,
				'name' => 'params',
				'withLocal' => true,
				'assignTo' => 'params',
			],
			
			[
				'class' => LoadConfig::class,
				'app' => $basePath . COMMON,
				'name' => 'install',
			],
			[
				'class' => LoadConfig::class,
				'app' => $basePath . APP,
				'name' => 'install',
			],
			
			[
				'class' => LoadConfig::class,
				'app' => $basePath . COMMON,
				'name' => 'test',
				'withLocal' => true,
				'isEnabled' => YII_ENV == YiiEnvEnum::TEST,
			],
			[
				'class' => LoadConfig::class,
				'app' => $basePath . APP,
				'name' => 'test',
				'withLocal' => true,
				'isEnabled' => YII_ENV == YiiEnvEnum::TEST,
			],
			
            'yii2lab\app\domain\filters\config\StandardConfigMutations',
		],
	],
];