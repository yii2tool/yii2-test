<?php

namespace yii2tool\test\helpers;

use App;
use yii\data\BaseDataProvider;
use yii\helpers\ArrayHelper;
use yii2rails\domain\base\BaseDto;
use yii2rails\domain\BaseEntity;
use yii2rails\extension\store\StoreFile;
use yii2rails\extension\yii\helpers\FileHelper;
use yii2bundle\account\domain\v3\entities\LoginEntity;
use yii2bundle\account\domain\v3\forms\LoginForm;
use yii2bundle\account\domain\v3\interfaces\entities\LoginEntityInterface;

class DataHelper {

    public static function authByLogin($login) {
        $loginEntity = App::$domain->account->login->oneByLogin($login);
        $loginEntity->token = self::forgeTokenById($loginEntity->id);
        \App::$domain->account->auth->login($loginEntity);
    }

    protected static function forgeTokenById($id) {
        $tokenEntity = \App::$domain->jwt->token->forgeBySubject(['id'=>$id], 'auth');
        $token = 'jwt ' . $tokenEntity->token;
        return $token;
    }

    public static function authById($id) {
        //$loginEntity = App::$domain->account->login->oneById($id);
        $loginEntity = new LoginEntity;
        $loginEntity->id = $id;
        $loginEntity->token = self::forgeTokenById($id);
        \App::$domain->account->auth->login($loginEntity);
    }

    public static function auth($login, $password = 'Wwwqqq111') {
        App::$domain->account->auth->logout();
        $loginForm = new LoginForm;
        $loginForm->login = $login;
        $loginForm->password = $password;
        $loginEntity = App::$domain->account->auth->authentication($login, $password);
        App::$domain->account->auth->login($loginEntity);
        return $loginEntity;
    }

    public static function fakeCollectionValue($collection, $values) {
        foreach ($collection as &$entity) {
            foreach ($values as $key => $val) {
                if(is_object($entity)) {
                    $entity->{$key} = $val;
                } else {
                    ArrayHelper::setValue($entity, $key, $val);
                }
            }
        }
        return $collection;
    }

    public static function loadForTest2($package, $method, $defaultData = null, $format = 'json') {
        //$method = basename($method);
        $method = str_replace('tests\\', '', $method);
        $path = str_replace('::', SL, $method);
        $fileName = '_expect' . SL . $path . DOT . $format;
        $packageDir = $package . DS . 'tests';
        return self::load($packageDir, $fileName, $defaultData);
    }

	public static function loadForTest($packageName, $method, $defaultData = null, $format = 'json') {
		//$method = basename($method);
		$method = str_replace('tests\\', '', $method);
		$path = str_replace('::', SL, $method);
		$fileName = '_expect' . SL . $path . DOT . $format;
		$packageDir = VENDOR_DIR . DS . $packageName . DS . 'tests';
		return self::load($packageDir, $fileName, $defaultData);
	}
	
	public static function load($packageName, $filename, $defaultData = null) {
		$store = new StoreFile(self::getDataFilename($packageName, $filename));
		$configExpect = $store->load();
		if(empty($configExpect)) {
			if(is_array($defaultData) || $defaultData instanceof BaseEntity || $defaultData instanceof BaseDto) {
				$defaultData = ArrayHelper::toArray($defaultData);
			}
			self::save($packageName, $filename, $defaultData);
			return $defaultData;
		}
		return $configExpect;
	}
	
	private static function save($packageName, $filename, $data) {
		$store = new StoreFile(self::getDataFilename($packageName, $filename));
		$store->save($data);
	}
	
	private static function getDataFilename($packageName, $filename) {
		return $packageName . DS . $filename;
	}
	
}
