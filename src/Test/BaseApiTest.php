<?php

namespace yii2tool\test\Test;

use App;
use yii2lab\rest\domain\entities\RequestEntity;
use yii2lab\rest\domain\entities\ResponseEntity;
use yii2lab\rest\domain\helpers\RestHelper;
use yii2tool\test\helpers\RestTestHelper;
use yii2tool\test\Test\Rest;

class BaseApiTest extends Rest
{

    public $package;
    public $point;

    protected function sendRequest(RequestEntity $requestEntity) : ResponseEntity {
        $this->prepareUri($requestEntity);
        return RestTestHelper::sendRequest($requestEntity);
    }

    protected function prepareUri(RequestEntity $requestEntity) {
        $requestEntity->uri = trim($this->point . SL . $requestEntity->uri, SL);
    }

}
