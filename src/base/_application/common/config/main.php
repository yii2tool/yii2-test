<?php

use yii2bundle\lang\domain\enums\LanguageEnum;

$commonDir = '@yii2lab/test/base/_application/common';

return [
	'name' => 'Test',
	'language' => LanguageEnum::SOURCE, // current Language
	'sourceLanguage' => LanguageEnum::SOURCE, // Language development
	'bootstrap' => ['log', 'queue'],
	'timeZone' => 'UTC',
	'components' => [
        'user' => [
			'class' => 'yii2bundle\account\domain\v3\web\User',
		],
		'log' => [
			'targets' => [
				[
					'class' => 'yii\log\FileTarget',
					'levels' => ['error', 'warning'],
					'except' => [
						'yii\web\HttpException:*',
						YII_ENV_TEST ? 'yii2bundle\lang\domain\i18n\PhpMessageSource::loadMessages' : null,
					],
				],
			],
			'traceLevel' => 0,
		],
		'authManager' => 'yii2bundle\rbac\domain\rbac\PhpManager',
		'cache' => [
			'class' => 'yii\caching\ArrayCache',
		],
		'i18n' => [
			'class' => 'yii2bundle\lang\domain\i18n\I18N',
			'aliases' => [
				'*' => '@yii2lab/test/base/_application/common/messages',
			],
		],
		'db' => 'yii2lab\db\domain\db\Connection',
		'mailer' => [
			'class' => 'yii\swiftmailer\Mailer',
			'viewPath' => $commonDir . '/mail',
            'htmlLayout' => '@yii2lab/notify/domain/mail/layouts/html',
            'textLayout' => '@yii2lab/notify/domain/mail/layouts/text',
			'useFileTransport' => true,
			'fileTransportPath' => $commonDir . '/runtime/mail',
		],
		'queue' => [
			'class' => 'yii\queue\file\Queue',
			'path' => $commonDir . '/runtime/queue',
		],
	],
];
