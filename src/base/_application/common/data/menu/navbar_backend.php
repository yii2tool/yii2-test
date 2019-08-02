<?php

return [
	'mainMenu' => [
		[
			'label' => ['admin', 'main'],
		],
		//'yii2bundle\article\admin\helpers\Menu',
		[
			'label' => ['admin', 'system'],
		],
		'yii2lab\applicationTemplate\backend\helpers\Menu',
		'yii2rails\app\admin\helpers\Menu',
		'yii2lab\notify\admin\helpers\Menu',
		'yii2bundle\rbac\admin\helpers\Menu',
		[
			'label' => ['admin', 'develop'],
		],
		'yii2lab\applicationTemplate\backend\helpers\ToolsMenu',
		'yii2tool\vendor\admin\helpers\Menu',
	],
];