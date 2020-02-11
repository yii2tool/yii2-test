<?php

use yii2rails\app\domain\helpers\EnvService;
use yii2lab\applicationTemplate\common\enums\ApplicationPermissionEnum;

return [
	'leftMenu' => [
		[
			'label' => ['guide/main', 'title'],
			'url' => 'guide',
			'module' => 'guide',
			'visible' => YII_ENV_DEV,
		],
		[
			'label' => 'Gii',
			'url' => 'gii',
			'module' => 'gii',
			'access' => ApplicationPermissionEnum::BACKEND_ALL,
			'visible' => YII_ENV_DEV,
		],
		[
			'label' => ['main', 'go_to_backend'],
			'url' => $_ENV['ADMIN_DOMAIN_URL'],
			'access' => ApplicationPermissionEnum::BACKEND_ALL,
			'visible' => APP != BACKEND,
		],
	],
];