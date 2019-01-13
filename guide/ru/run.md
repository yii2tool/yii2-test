Запуск тестов
===

Для пакетного запуска тестов есть несколько команд.

Тесты текущего проекта:

	php yii vendor/test/project

Тесты пакетов:

	php yii vendor/test/package

Пакеты + проект:

	php yii vendor/test/all

Командные файлы находятся здесь `cmd\test`.

Для задания путей тестов, пишем массив алиасов в конфиг домена `vendor`, в сегмент `services.test.aliases`:

```php
return [
	'vendor' => [
		...
		'services' => [
			...
			'test' => [
				'aliases' => [
					'@domain/v4/account',
				],
			],
			...
		],
	],
];
```
