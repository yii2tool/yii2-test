<?php

namespace yii2lab\test\Test;

use yii2lab\test\Test\Unit;
use yii\data\DataProviderInterface;
use yii\helpers\ArrayHelper;
use yii2lab\test\helpers\DataHelper;
use yii2rails\domain\BaseEntity;

class BaseDomainTest extends Unit
{

    public $package;

    public function assertArray(array $actual, $method) {
        $expect = DataHelper::loadForTest2($this->package, $method, $actual);
        $this->tester->assertEquals($expect, $actual);
    }

    public function assertDataProvider(DataProviderInterface $dataProvider, $method) {
        $collection = $dataProvider->getModels();
        $pagination = $dataProvider->getPagination();
        $actual = [
            'pagination' => [
                'page' => $pagination->page,
                'pageCount' => $pagination->pageCount,
                'pageSize' => $pagination->pageSize,
                'totalCount' => $pagination->totalCount,
                'limit' => $pagination->limit,
                'offset' => $pagination->offset,
            ],
            'collection' => ArrayHelper::toArray($collection),
        ];
        $expect = DataHelper::loadForTest2($this->package, $method . 'DataProvider', $actual);
        $this->tester->assertEquals($expect, $actual);
    }

}
