<?php

namespace yii2tool\test\helpers;

use yii2lab\rest\domain\entities\ResponseEntity;

class RestContractTestHelper {

    public static function extractPaginationFromResponseEntity(ResponseEntity $responseEntity)
    {
        return [
            'page' => $responseEntity->headers['x-pagination-current-page'],
            'pageCount' => $responseEntity->headers['x-pagination-page-count'],
            'pageSize' => $responseEntity->headers['x-pagination-per-page'],
            'totalCount' => $responseEntity->headers['x-pagination-total-count'],
            //'limit' => $responseEntity->headers['x-pagination-per-page'],
            'offset' => $responseEntity->headers['x-pagination-offset'],
        ];
    }

}
