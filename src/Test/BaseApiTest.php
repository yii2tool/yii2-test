<?php

namespace yii2lab\test\Test;

use App;
use yii2lab\rest\domain\entities\RequestEntity;
use yii2lab\rest\domain\entities\ResponseEntity;
use yii2lab\rest\domain\helpers\RestHelper;
use yii2lab\test\Test\Rest;

class BaseApiTest extends Rest
{

    public $package;
    public $point;

    protected function sendRequest(RequestEntity $requestEntity) : ResponseEntity {
        $this->prepareRequest($requestEntity);
        return RestHelper::sendRequest($requestEntity);
    }

    protected function prepareRequest(RequestEntity $requestEntity) {
        $this->prepareLanguage($requestEntity);
        $this->prepareAuthorization($requestEntity);
        $this->prepareUri($requestEntity);
    }

    protected function prepareUri(RequestEntity $requestEntity) {
        $uri = $requestEntity->uri;
        $requestEntity->uri = $this->url($this->point);
        if($uri) {
            $requestEntity->uri = $requestEntity->uri . SL . $uri;
        }
    }

    protected function prepareLanguage(RequestEntity $requestEntity) {
        $headers = $requestEntity->headers;
        $headers['Language'] = 'xx';
        $requestEntity->headers = $headers;
    }

    protected function prepareAuthorization(RequestEntity $requestEntity) {
        $loginEntity = App::$domain->account->auth->identity;
        if($loginEntity == null) {
            return;
        }
        $headers = $requestEntity->headers;
        $headers['authorization'] = $loginEntity->token;
        $requestEntity->headers = $headers;
    }

}
