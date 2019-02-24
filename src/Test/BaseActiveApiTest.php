<?php

namespace yii2lab\test\Test;

use yii2lab\rest\domain\entities\RequestEntity;
use yii2lab\test\helpers\DataHelper;
use yii2lab\test\Test\BaseApiTest;
use yii2rails\extension\web\enums\HttpMethodEnum;

class BaseActiveApiTest extends BaseApiTest
{

    protected function assertData($actual, $method, $postfix = '') {
        $expect = DataHelper::loadForTest2($this->package, $method . $postfix, $actual);
        $this->tester->assertEquals($expect, $actual);
    }

    protected function viewRequest($id) {
        $requestEntity = new RequestEntity;
        $requestEntity->uri = $id;
        $requestEntity->method = HttpMethodEnum::GET;
        $responseEntity = $this->sendRequest($requestEntity);
        $this->tester->assertEquals(200, $responseEntity->status_code);
        return $responseEntity;
    }

    protected function deleteRequest($id) {
        $requestEntity = new RequestEntity;
        $requestEntity->uri = $id;
        $requestEntity->method = HttpMethodEnum::DELETE;
        $responseEntity = $this->sendRequest($requestEntity);
        $this->tester->assertEquals(204, $responseEntity->status_code);
    }

    protected function indexRequest($query = [])
    {
        $requestEntity = new RequestEntity;
        $requestEntity->data = $query;
        $requestEntity->method = HttpMethodEnum::GET;
        $responseEntity = $this->sendRequest($requestEntity);

        $this->tester->assertEquals(200, $responseEntity->status_code);

        $actual = [
            'pagination' => [
                'page' => $responseEntity->headers['x-pagination-current-page'],
                'pageCount' => $responseEntity->headers['x-pagination-page-count'],
                'pageSize' => $responseEntity->headers['x-pagination-per-page'],
                'totalCount' => $responseEntity->headers['x-pagination-total-count'],
                //'limit' => $responseEntity->headers['x-pagination-per-page'],
                'offset' => $responseEntity->headers['x-pagination-offset'],
            ],
            'collection' => $responseEntity->data,
        ];

        return $actual;
    }

}
