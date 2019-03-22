<?php

namespace yii2lab\test\helpers;

use App;
use yii\web\NotFoundHttpException;
use yii2lab\notify\domain\entities\SmsEntity;
use yii2lab\notify\domain\entities\TestEntity;
use yii2lab\notify\domain\enums\TypeEnum;
use yii2lab\rest\domain\entities\RequestEntity;
use yii2lab\rest\domain\entities\ResponseEntity;
use yii2lab\rest\domain\helpers\RestHelper;
use yii2rails\app\domain\helpers\Config;
use yii2rails\app\domain\helpers\Env;
use yii2rails\app\domain\helpers\EnvService;
use yii2rails\extension\web\enums\HttpMethodEnum;
use yii2rails\extension\yii\helpers\FileHelper;
use yubundle\account\domain\v2\entities\LoginEntity;
use yubundle\account\domain\v2\helpers\test\AuthTestHelper;

class RestTestHelper {
    
    //private static $tokenCollection = [];

    public static function cleanSms() {
        $requestEntity = new RequestEntity;
        $requestEntity->method = HttpMethodEnum::DELETE;
        $requestEntity->uri = 'v1/notify-test';
        $responseEntity = self::sendRequest($requestEntity);
    }

    public static function getActivationCodeByPhone($phone) {
        $smsEntity = self::oneSmsByPhone($phone);
        $code = '';
        if (preg_match('/([0-9]{6})/s', $smsEntity->message, $matches)) {
            $code = $matches[1];
        }
        return $code;
    }

    private static function oneSmsByPhone($phone) : TestEntity {

        $oldIdentity = AuthTestHelper::getIdentity();
        AuthTestHelper::authByLogin('admin');

        $requestEntity = new RequestEntity;
        $requestEntity->method = HttpMethodEnum::GET;
        $requestEntity->uri = 'v1/notify-test';
        $requestEntity->data = [
            'type' => TypeEnum::SMS,
            'phone' => $phone,
            'sort' => '-address',
        ];
        $responseEntity = self::sendRequest($requestEntity);
        $collection = $responseEntity->data;
        if(empty($collection)) {
            throw new NotFoundHttpException('Sms not found');
        }
        $smsEntity = new TestEntity($collection[0]);

        if($oldIdentity == null) {
            AuthTestHelper::logout();
        } else {
            AuthTestHelper::login($oldIdentity);
        }

        return $smsEntity;
    }

    public static function sendRequest(RequestEntity $requestEntity) : ResponseEntity {
        self::prepareRequest($requestEntity);
        return RestHelper::sendRequest($requestEntity);
    }

    protected static function prepareRequest(RequestEntity $requestEntity) {
        self::prepareLanguage($requestEntity);
        self::prepareAuthorization($requestEntity);
        self::prepareUri($requestEntity);
    }

    protected static function prepareUri(RequestEntity $requestEntity) {
        $uri = $requestEntity->uri;

        $host = EnvService::get('url.test-api');
        $host = trim($host, SL);

        $requestEntity->uri = $host;
        if($uri) {
            $requestEntity->uri = $requestEntity->uri . SL . $uri;
        }
    }

    protected static function prepareLanguage(RequestEntity $requestEntity) {
        $headers = $requestEntity->headers;
        $headers['Language'] = 'xx';
        $requestEntity->headers = $headers;
    }

    protected static function prepareAuthorization(RequestEntity $requestEntity) {
        $loginEntity = AuthTestHelper::getIdentity();
        if($loginEntity == null) {
            return;
        }
        $headers = $requestEntity->headers;
        $headers['authorization'] = $loginEntity->token;
        $requestEntity->headers = $headers;
    }
    
}
