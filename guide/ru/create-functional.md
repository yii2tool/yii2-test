Functional-тесты
===

## Основное

Описание класса:

```php
namespace tests\functional\v1\services;

use Codeception\Test\Unit;

class AuthTest extends Unit
{
	...
}
```

## Примеры тестов

### Проверка валидации (Негативный тест)

```php
public function testAuthenticationBadPassword()
{
	try {
		App::$domain->account->auth->authentication(LoginEnum::LOGIN_ADMIN, LoginEnum::PASSWORD_INCORRECT);
		$this->tester->assertBad();
	} catch(UnprocessableEntityHttpException $e) {
		$this->tester->assertUnprocessableEntityHttpException(['password' => 'Incorrect login or password'], $e);
	}
}
```

### Авторизация по токену (Негативный тест)

```php
public function testAuthenticationByBadToken()
{
	try {
		/** @var LoginEntity $entity */
		App::$domain->account->auth->authenticationByToken(LoginEnum::TOKEN_NOT_INCORRECT);
		$this->tester->assertBad();
	} catch(UnauthorizedHttpException $e) {
		$this->tester->assertTrue(true);
	}
}
```

### Проверка коллекции

```php
public function testAllWithRelations()
	{
		
		/** @var BaseEntity[] $collection */
		$query = Query::forge();
		$query->where('id', 2000);
		$query->limit(1);
		$collection = App::$domain->geo->city->all($query);
		
		$this->tester->assertCount(1, $collection);
		$this->tester->assertCollection([
			[
				'id' => 2000,
				'country_id' => 1894,
				'region_id' => 1994,
				'country' => null,
				'region' => null,
			]
		], $collection);
	}
```

### Проверка сущности

```php
public function testOneWithRelations()
	{
		
		/** @var BaseEntity $entity */
		$query = Query::forge();
		$query->where('id', 2000);
		$entity = App::$domain->geo->city->one($query);
		
		$this->tester->assertEntity([
			'id' => 2000,
			'country_id' => 1894,
			'region_id' => 1994,
			'country' => null,
			'region' => null,
		], $entity);
	}
```

