<?php

namespace yii2tool\test\Test;

use yii\web\NotFoundHttpException;
use yii2tool\test\Test\Unit;
use yii\data\DataProviderInterface;
use yii\helpers\ArrayHelper;
use yii2tool\test\helpers\DataHelper;
use yii2rails\domain\BaseEntity;
use App;
use domain\mail\v1\interfaces\services\BoxInterface;
use yii2tool\test\Test\BaseDomainTest;
use yii2rails\domain\data\Query;
use yii2rails\domain\services\base\BaseService;

class BaseActiveDomainTest extends BaseDomainTest
{

    public function relations() {
        return [];
    }

    public function authBy() {
        return null;
    }

    //abstract public function service();

    public function existsEntityId() {
        return 1;
    }

    public function NotExistsEntityId() {
        return 999999999;
    }

    public function testAll() {
        $this->prepareAuth();
        $query = $this->prepareQuery();
        /** @var BoxInterface $serviceInstance */
        $serviceInstance = $this->serviceInstance();
        $dataProvider = $serviceInstance->getDataProvider($query);
        $this->assertDataProvider($dataProvider, $this->prepareMethod(__METHOD__));
    }

    public function _testAllPage2() {
        $this->prepareAuth();
        $query = $this->prepareQuery();
        $query->page(2);
        /** @var BoxInterface $serviceInstance */
        $serviceInstance = $this->serviceInstance();
        $dataProvider = $serviceInstance->getDataProvider($query);
        $this->assertDataProvider($dataProvider, $this->prepareMethod(__METHOD__));
    }

    public function testAllWithRelations() {
        $this->prepareAuth();
        $query = $this->prepareQuery(['relations']);
        /** @var BoxInterface $serviceInstance */
        $serviceInstance = $this->serviceInstance();
        $dataProvider = $serviceInstance->getDataProvider($query);
        $this->assertDataProvider($dataProvider, $this->prepareMethod(__METHOD__));
    }

    public function _testOne() {
        $this->prepareAuth();
        $query = $this->prepareQuery();
        /** @var BoxInterface $serviceInstance */
        $serviceInstance = $this->serviceInstance();
        $entity = $serviceInstance->oneById($this->existsEntityId(), $query);
        $this->assertArray($entity->toArray(), $this->prepareMethod(__METHOD__));
    }

    public function _testDelete() {
        $this->prepareAuth();
        /** @var BoxInterface $serviceInstance */
        $serviceInstance = $this->serviceInstance();
        $serviceInstance->deleteById($this->existsEntityId());

        $dataProvider = $serviceInstance->getDataProvider();
        $this->assertDataProvider($dataProvider, $this->prepareMethod(__METHOD__));
    }

    public function _testOneWithRelations() {
        $this->prepareAuth();
        $query = $this->prepareQuery(['relations']);
        /** @var BoxInterface $serviceInstance */
        $serviceInstance = $this->serviceInstance();
        $entity = $serviceInstance->oneById($this->existsEntityId(), $query);
        $this->assertArray($entity->toArray(), $this->prepareMethod(__METHOD__));
    }

    public function _testOneNotExists() {
        $this->prepareAuth();
        $query = $this->prepareQuery();
        /** @var BoxInterface $serviceInstance */
        $serviceInstance = $this->serviceInstance();
        try {
            $serviceInstance->oneById($this->testOneNotExists(), $query);
            $this->tester->assertBad();
        } catch (NotFoundHttpException $e) {
            $this->tester->assertNice();
        }
    }

    protected function prepareMethod($method) {
        list($n, $methodName) = explode('::', $method);
        $namespace = (get_class($this));
        return $namespace . SL . $methodName;
    }

    protected function serviceInstance() : BaseService {
        $serviceInstance = ArrayHelper::getValue(App::$domain, $this->service());
        return $serviceInstance;
    }

    protected function prepareAuth() {
        $auth = $this->authBy();
        if($auth) {
            DataHelper::auth($auth);
        }
    }

    protected function prepareQuery($options = []) : Query {
        $query = new Query;
        $relations = $this->relations();
        if($relations && in_array('relations', $options)) {
            $query->with($relations);
        }
        return $query;
    }

}
