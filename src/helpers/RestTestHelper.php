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
use yii2mod\helpers\ArrayHelper;
use yii2rails\app\domain\helpers\Config;
use yii2rails\app\domain\helpers\Env;
use yii2rails\app\domain\helpers\EnvService;
use yii2rails\extension\web\enums\HttpMethodEnum;
use yii2rails\extension\yii\helpers\FileHelper;
use yubundle\account\domain\v2\entities\LoginEntity;
use yubundle\account\domain\v2\helpers\test\AuthTestHelper;

class RestTestHelper {

    public static function getBaseUrl() {
        $configFile = __DIR__ . '/../../../../../common/config/env-local.php';
        $config = \yii2rails\extension\common\helpers\Helper::includeConfig($configFile);
        return ArrayHelper::getValue($config, 'url.test-api');
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

        $host = self::getBaseUrl();
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
