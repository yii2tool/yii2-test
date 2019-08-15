<?php

namespace yii2tool\test\Test;

use phpbundle\rest\domain\enums\HttpHeaderEnum;
use yii2tool\test\Test\BaseApiTest;

use common\enums\rbac\RoleEnum;
use yii\helpers\ArrayHelper;
use yii2bundle\account\domain\v3\helpers\test\PhoneTestHelper;
use yii2bundle\account\domain\v3\helpers\test\RegistrationTestHelper;
use yii2lab\rest\domain\entities\RequestEntity;
use yii2rails\extension\web\enums\HttpMethodEnum;
use yii2tool\test\helpers\TestHelper;
use yubundle\reference\tests\rest\v1\ReferenceSchema;
use api\tests\functional\v1\union\UnionSchema;
use yii2tool\test\helpers\CurrentIdTestHelper;
use yii2tool\test\Test\BaseActiveApiTest;
use yii2bundle\account\domain\v3\helpers\test\AuthTestHelper;
use yubundle\user\tests\rest\v1\UserSchema;

class BaseActiveApiAccessTest extends BaseActiveApiTest
{

    const ALLOW = 'allow';
    const DENY = 'deny';
    const ERROR = 'error';

    protected function entitySchema() : array {
        return [];
    }

    protected function newEntity() : array {
        return [];
    }
    
    protected function accessMap() : array {
        return [];
    }
    
    public function testCreateReal() {
        $newEntity = $this->newEntity();
        if(empty($newEntity)) {
            TestHelper::printMessage('skip - not defined new entity');
            return;
        }
        AuthTestHelper::authByLogin($newEntity['authBy']);
        $phone = PhoneTestHelper::nextPhone();
        $this->createEntity($this->resource, $newEntity['data'], true);
        //$schema = $this->schema;
        $id = CurrentIdTestHelper::get();
        $this->readEntity($this->resource, $id, $this->entitySchema());
    }
    
    public function testOne() {
        $this->runAccessTest('one');
    }

    public function testAll() {
        $this->runAccessTest('all');
    }

    public function testCreate() {
        $this->runAccessTest('create');
    }

    public function testUpdate() {
        $this->runAccessTest('update');
    }

    public function testDelete() {
        $this->runAccessTest('delete');
    }

    protected function accessMapForAction($name) {
        $map = $this->accessMap();
        return ArrayHelper::getValue($map, $name);
    }

    protected function runAccessTest($action, $method = null) {
        if($method == null) {
            $method = $this->getHttpMethodByAction($action);
        }
        $map = $this->accessMapForAction($action);
        if(empty($map)) {
            TestHelper::printMessage('skip - not defined');
            return;
        }
        foreach ($map as $item) {
            //TestHelper::printMessage('===');
            $this->runAccessItemTest($action, $method, $item);
        }
    }

    protected function runAccessItemTest($action, $method = null, $map) {
        $access = $map['access'];
        $actual = [];
        foreach ($access as $login => $expectedStatus) {
            $this->authByLogin($login);
            $uri = $this->forgeUri($map);
            $responseEntity = $this->send($uri, $method);

            if($responseEntity->status_code == 200 && $responseEntity->data) {
                if(in_array(HttpHeaderEnum::TOTAL_COUNT, $responseEntity->headers)) {
                    $this->tester->assertCollectionType($this->entitySchema(), $responseEntity->data);
                } else {
                    $this->tester->assertArrayType($this->entitySchema(), $responseEntity->data);
                }
            }
            $actual[$login] = $this->statusCodeToAccess($responseEntity->status_code);
            $isSuccessChar = $expectedStatus == $actual[$login] ? '+' : 'x';
            TestHelper::printMessagePure($isSuccessChar . SPC . $login);
        }
        $this->tester->assertEquals($access, $actual);
    }

    protected function statusCodeToAccess($actualStatusCode) {
        $access = $actualStatusCode;
        if($actualStatusCode == 422) {
            $access = self::ALLOW;
        }
        if($actualStatusCode == 404 || $actualStatusCode == 403 || $actualStatusCode == 401) {
            $access = self::DENY;
        }
        if($actualStatusCode >= 200 && $actualStatusCode < 300) {
            $access = self::ALLOW;
        }
        if($actualStatusCode >= 500 && $actualStatusCode < 600) {
            $access = self::ERROR;
        }
        return $access;
    }

    protected function getHttpMethodByAction($action) {
        $methods = [
            'one' => HttpMethodEnum::GET,
            'all' => HttpMethodEnum::GET,
            'create' => HttpMethodEnum::POST,
            'update' => HttpMethodEnum::PUT,
            'delete' => HttpMethodEnum::DELETE,
        ];
        return ArrayHelper::getValue($methods, $action);
    }

    protected function authByLogin($login) {
        if($login == 'guest') {
            AuthTestHelper::logout();
        } else {
            AuthTestHelper::authByLogin($login);
        }
    }

    protected function forgeUri($map = null) {
        if(!empty($map['uri'])) {
            return $this->resource . SL . $map['uri'];
        }
        return $this->resource;
    }
    
}

/*
class UnionIndustryAccessTest extends BaseActiveApiAccessTest
{

    public $package = 'api';
    public $point = 'v1';
    public $resource = 'industry-union';

    protected function newEntity() : array {
        $extId = Helper::microtimeId();
        return [
            'authBy' => 'manager_opo',
            'data' => [
                'title' => 'test category ' . $extId,
            ],
        ];
    }

    protected function accessMap() : array {
        $id = CurrentIdTestHelper::get();
        return [
            'one' => [
                [
                    'uri' => $id,
                    'access' => [
                        'guest' => self::DENY,
                        'vitaliy' => self::ALLOW,
                        'manager_opo' => self::ALLOW,
                    ],
                ],
            ],
            'all' => [
                [
                    'access' => [
                        'guest' => self::DENY,
                        'vitaliy' => self::ALLOW,
                        'manager_opo' => self::ALLOW,
                    ],
                ],
            ],
            'create' => [
                [
                    'access' => [
                        'guest' => self::DENY,
                        'vitaliy' => self::DENY,
                        'manager_opo' => self::ALLOW,
                    ],
                ],
            ],
            'update' => [
                [
                    'uri' => $id,
                    'access' => [
                        'guest' => self::DENY,
                        'vitaliy' => self::DENY,
                        'manager_opo' => self::ALLOW,
                    ],
                ],
            ],
            'delete' => [
                [
                    'uri' => $id,
                    'access' => [
                        'guest' => self::DENY,
                        'vitaliy' => self::DENY,
                        'manager_opo' => self::ALLOW,
                    ],
                ],
            ],
        ];
    }

}
*/
