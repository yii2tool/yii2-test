<?php

namespace yii2lab\test\Test;

use api\tests\schemas\MailSchema;
use yii\helpers\ArrayHelper;
use yii2lab\rest\domain\entities\RequestEntity;
use yii2lab\test\helpers\CurrentIdTestHelper;
use yii2lab\rest\domain\entities\ResponseEntity;
use yii2lab\test\helpers\DataHelper;
use yii2lab\test\helpers\RestContractTestHelper;
use yii2lab\test\Test\BaseApiTest;
use yii2rails\extension\web\enums\HttpHeaderEnum;
use yii2rails\extension\web\enums\HttpMethodEnum;
use yii2module\account\domain\v3\helpers\test\AuthTestHelper;
use yii2module\account\domain\v3\helpers\test\CurrentPhoneTestHelper;

class BaseActiveApiTest extends BaseApiTest
{

    protected function checkAuth($login, $password) {
        AuthTestHelper::logout();
        $data = [
            'login' => $login,
            'password' => $password,
        ];
        $responseEntity = $this->send('auth', HttpMethodEnum::POST, $data);
        /*$requestEntity = new RequestEntity;
        $requestEntity->uri = 'auth';
        $requestEntity->method = HttpMethodEnum::POST;
        $requestEntity->data = [
            'login' => $login,
            'password' => $password,
        ];
        $responseEntity = $this->sendRequest($requestEntity);*/
        $this->tester->assertEquals(200, $responseEntity->status_code);
        AuthTestHelper::loadPrevAuth();
    }

    protected function readNotFoundEntity($endpoint, $id) {
        $responseEntity = $this->send($endpoint . SL . $id, HttpMethodEnum::GET);
       /* $requestEntity = new RequestEntity;
        $requestEntity->uri = $endpoint . SL . $id;
        $requestEntity->method = HttpMethodEnum::GET;
        $responseEntity = $this->sendRequest($requestEntity);*/
        $this->tester->assertEquals(404, $responseEntity->status_code);
    }

    protected function readEntity($endpoint, $id, $schema = [], $query = []) : array {
        $responseEntity = $this->send($endpoint . SL . $id, HttpMethodEnum::GET, $query);
        /*$requestEntity = new RequestEntity;
        $requestEntity->uri = trim($endpoint . SL . $id, SL);
        $requestEntity->data = $query;
        $requestEntity->method = HttpMethodEnum::GET;
        $responseEntity = $this->sendRequest($requestEntity);*/
        $this->tester->assertEquals(200, $responseEntity->status_code);
        $actual = $responseEntity->data;
        if(!empty($query['fields'])) {
            $fields = $this->extractFields($query);
            $schema = $this->filterSchemaByFields($schema, $fields);
            $this->assertFieldsContract($actual,$schema, $fields);
        }
        $this->tester->assertArrayType($schema, $actual);
        return $actual;
    }

    protected function readCollection($endpoint, $query, $schema, $pagination = null) : array {
        $responseEntity = $this->send($endpoint, HttpMethodEnum::GET, $query);
        /*$requestEntity = new RequestEntity;
        $requestEntity->uri = $endpoint;
        $requestEntity->data = $query;
        $requestEntity->method = HttpMethodEnum::GET;
        $responseEntity = $this->sendRequest($requestEntity);*/
        $this->tester->assertEquals(200, $responseEntity->status_code);
        $actual = $responseEntity->data;
        if(!empty($query['fields'])) {
            $fields = $this->extractFields($query);
            $schema = $this->filterSchemaByFields($schema, $fields);
            foreach ($actual as $entity) {
                $this->assertFieldsContract($entity, $schema, $fields);
            }
        }
        $this->tester->assertCollectionType($schema, $actual);
        if($pagination !== null) {
            $this->readCollectionPagination($responseEntity, $pagination);
        }
        return $actual;
    }

    private function filterSchemaByFields($schema, $fields) {
        $schema = ArrayHelper::filter($schema, $fields);
        return $schema;
    }

    private function extractFields($query) {
        $fields = explode(',', $query['fields']);
        if(!empty($query['expand'])) {
            $expand = explode(',', $query['expand']);
            $fields = ArrayHelper::merge($fields, $expand);
        }
        return $fields;
    }

    private function assertFieldsContract($actual, $schema, $fields) {
        foreach ($actual as $key => $value) {
            if(!in_array($key, $fields) && $value != null) {
                $this->tester->assertTrue(false, "Field \"$key\" not null (actual: \"$value\")");
            }
        }
    }

    protected function deleteEntity($endpoint, $id) {
        $responseEntity = $this->send($endpoint . SL . $id, HttpMethodEnum::DELETE);
       /* $requestEntity = new RequestEntity;
        $requestEntity->uri = $endpoint . SL . $id;
        $requestEntity->method = HttpMethodEnum::DELETE;
        $responseEntity = $this->sendRequest($requestEntity);*/
        $this->tester->assertEquals(204, $responseEntity->status_code);
    }

    protected function updateEntity($endpoint, $id, $data) {
        $responseEntity = $this->send($endpoint . SL . $id, HttpMethodEnum::PUT, $data);
        /*$requestEntity = new RequestEntity;
        $requestEntity->uri = $endpoint . SL . $id;
        $requestEntity->method = HttpMethodEnum::PUT;
        $requestEntity->data = $data;
        $responseEntity = $this->sendRequest($requestEntity);*/
        $this->tester->assertEquals(204, $responseEntity->status_code);
    }

    protected function assertRelationContract($endpoint, $id, $schema, $query = []) {
        $expand = [];
        foreach ($schema as $schemaName => $schemaData) {
            $expand[] = $schemaName;
        }
        $expandString = implode(',', $expand);
        $query['expand'] = $expandString;

        $actual = $this->readEntity($endpoint, $id, [], $query);
        $this->validateSchemas($actual, $schema);

        $queryForCollection = $query;
        $queryForCollection['id'] = $id;
        $actual = $this->readCollection($endpoint, $queryForCollection, []);
        $this->validateSchemas($actual[0], $schema);
    }

    private function validateSchemas($actual, $schema) {
        foreach ($schema as $schemaName => $schemaData) {
            $value = ArrayHelper::getValue($actual, $schemaName);
            $this->tester->assertArrayType($schemaData, $value);
        }
    }

    private function readCollectionPagination($responseEntity, $pagination) {
        if(is_integer($pagination)) {
            $pagination = [
                'totalCount' => $pagination,
            ];
        }
        $defaultPagination = [
            'page' => 1,
            'pageSize' => 20,
            'offset' => 0,
        ];
        $pagination = ArrayHelper::merge($defaultPagination, $pagination);
        if(empty($pagination['pageCount'])) {
            $pagination['pageCount'] = intval(ceil($pagination['totalCount'] / $pagination['pageSize']));
        }
        $actualPagination = RestContractTestHelper::extractPaginationFromResponseEntity($responseEntity);
        $this->tester->assertEquals($pagination, $actualPagination);
    }

    protected function createEntityUnProcessible($endpoint, $data, $errorFields = null) {
        $responseEntity = $this->send($endpoint, HttpMethodEnum::POST, $data);
        /*$requestEntity = new RequestEntity;
        $requestEntity->uri = $endpoint;
        $requestEntity->method = HttpMethodEnum::POST;
        $requestEntity->data = $data;
        $responseEntity = $this->sendRequest($requestEntity);*/
        $this->tester->assertEquals(422, $responseEntity->status_code);
        if($errorFields) {
            $this->tester->assertUnprocessableEntityExceptionFields($errorFields, $responseEntity->data);
        }
        return $responseEntity->data;
    }

    protected function createEntity($endpoint, $data, $isRememberLastId = false) {
        $responseEntity = $this->send($endpoint, HttpMethodEnum::POST, $data);
        /*$requestEntity = new RequestEntity;
        $requestEntity->uri = $endpoint;
        $requestEntity->method = HttpMethodEnum::POST;
        $requestEntity->data = $data;
        $responseEntity = $this->sendRequest($requestEntity);*/
        $this->tester->assertEquals(201, $responseEntity->status_code);
        if($isRememberLastId) {
            $lastId = $responseEntity->headers[HttpHeaderEnum::X_ENTITY_ID];
            $this->tester->assertNotEmpty($lastId);
            $lastId = intval($lastId);
            CurrentIdTestHelper::set($lastId);
        }
    }

    protected function send($endpoint, $method, $data = null, $expectSatausCode = null) {
        $requestEntity = new RequestEntity;
        $requestEntity->uri = $endpoint;
        $requestEntity->method = $method;
        if($data) {
            $requestEntity->data = $data;
        }
        $responseEntity = $this->sendRequest($requestEntity);
        return $responseEntity;
    }

    /*protected function assertPagination(array $expected, ResponseEntity $responseEntity) {
        $pagination = RestContractTestHelper::extractPaginationFromResponseEntity($responseEntity);
        $this->tester->assertEquals($expected, $pagination);
    }

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
    }*/

}
