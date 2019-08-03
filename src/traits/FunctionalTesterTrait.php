<?php

namespace yii2tool\test\traits;

use Yii;
use yii2rails\extension\web\enums\HttpHeaderEnum;
use yii2mod\helpers\ArrayHelper;
use yii2tool\test\models\Login;
use Codeception\Util\HttpCode;

trait FunctionalTesterTrait
{
	
	public $format;
	public $password = 'Wwwqqq111';
	
	public function seeValidationError($message)
	{
		$this->see($message, '.help-block');
	}
	
	private function seeItemValue($item, $values) {
		foreach ($values as $name => $value) {
			$this->tester->assertTrue(isset($item[$name]));
			$this->assertEquals($item[$name], $value);
		}
	}
	
	public function seeContainValues($values) {
		$response = $this->getResponseBody();
		if(ArrayHelper::isIndexed($response)) {
			foreach($response as $item) {
				$this->seeItemValue($item, $values);
			}
		} else {
			$this->seeItemValue($response, $values);
		}
	}
	
	public function seeListHttpHeaders(array $values) {
		foreach($values as $name => $value) {
			$this->seeHttpHeader($name, $value);
		}
		$total_count = ArrayHelper::getValue($values, HttpHeaderEnum::TOTAL_COUNT);
		$per_page = ArrayHelper::getValue($values, HttpHeaderEnum::PER_PAGE, 20);
		if(empty($count)) {
			return;
		}
		if($total_count < $per_page) {
			$this->seeListCount($total_count);
		} else {
			$this->seeListCount($per_page);
		}
	}
	
	public function seeListCount($value) {
		$body = $this->getResponseBody();
		$count = count($body);
		$this->assertEquals($value, $count);
	}
	
	public function seeSort($first, $last, $key = null) {
		$this->seeItem($first, 'first', $key);
		$this->seeItem($last, 'last', $key);
	}
	
	public function seeItem($expect, $id, $key = null) {
		$body = $this->getResponseBody();
		if($id == 'first') {
			$item = ArrayHelper::first($body);
		} elseif($id == 'last') {
			$item = ArrayHelper::last($body);
		} else {
			$item = $body[$id];
		}
		$value = ArrayHelper::getValue($item, $key);
		$this->assertEquals($expect, $value);
	}
	
	public function getResponseBody() {
		$body = $this->grabResponse();
		$body = \GuzzleHttp\json_decode($body);
		return ArrayHelper::toArray($body);
	}
	
	public function seeBody($expected = null, $existsOnly = false) {
		$response = $this->getResponseBody();
		if(!$existsOnly) {
			$this->assertEquals($response, $expected);
		} else {
			if(is_array($expected)) {
				foreach($expected as $key => $value) {
					if(array_key_exists($key, $response)) {
						$this->assertEquals($response[$key], $expected[$key]);
					}
				}
			}
		}
	}
	
	public function seeUnprocessableEntity($body = null) {
		$this->seeResponseCodeIs(HttpCode::UNPROCESSABLE_ENTITY);
		if(!empty($body)) {
			$this->seeBody($body);
		} else {
			$this->seeResponseMatchesJsonType([
				'field' => 'string',
				'message' => 'string',
			]);
		}
	}

	protected function fieldsOnly($expected, $item) {
		$diff = array_diff(array_keys($item), $expected);
		$this->assertEquals($diff, []);
		$diff = array_diff($expected, array_keys($item));
		$this->assertEquals($diff, []);
	}

	public function seeResponseJsonFieldsOnly($fields) {
		$response = $this->getResponseBody();
		if(ArrayHelper::isIndexed($response)) {
			foreach($response as $item) {
				$this->fieldsOnly($fields, $item);
			}
		} else {
			$this->fieldsOnly($fields, $response);
		}
	}
	
	public function dontSeeResponseJsonFields($fields) {
		foreach($fields as $field) {
			$this->dontSeeResponseJsonMatchesJsonPath('$.' . $field);
		}
	}
	
	public function auth($login, $password = null) {
		$token = null;
		
		if(empty($login) || $login == 'guest') {
			$this->haveHttpHeader(HttpHeaderEnum::AUTHORIZATION, null);
			return false;
		}
		
		$user = $loginList = \App::$domain->account->test->oneByLogin($login);
		$password = !empty($password) ?  $password: $user->password;
		
		$this->sendPOST('auth', [
			'login' => $login,
			'password' => $password,
		]);
		$user = $this->getResponseBody();
		$token = $user['token'];
		
		$this->haveHttpHeader(HttpHeaderEnum::AUTHORIZATION, $token);
		return $token;
	}
	
	/*public function authAsRole($role = null) {
		$login = null;
		if($role) {
			$user = $loginList = \App::$domain->account->test->getOneByRole($role);
			$login = $user->login;
		}
		return self::auth($login);
	}*/
	
	public function dontSeeValidationError($message)
	{
		$this->dontSee($message, '.help-block');
	}
}
