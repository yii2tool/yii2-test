<?php

namespace yii2tool\test\helpers;

use Yii;
use yii2lab\notify\domain\helpers\test\NotifyTestHelper;
use yii2lab\rest\domain\entities\RequestEntity;
use yii2lab\rest\domain\entities\ResponseEntity;
use yii2tool\test\helpers\RestTestHelper;
use yii2rails\app\domain\helpers\EnvService;
use yii2rails\extension\enum\enums\TimeEnum;
use yii2rails\extension\web\enums\HttpMethodEnum;

class BaseCacheTestHelper
{

    private static $classesData = [];

    static function get($default = null) {
        $key = self::key();
        return Yii::$app->cache->get($key);
    }

    static function set($value) {
        $key = self::key();
        Yii::$app->cache->set($key, $value);
    }

    static function remove() {
        $key = self::key();
        Yii::$app->cache->delete($key);
    }

    private static function key() {
        return [static::class, 'url' => RestTestHelper::getBaseUrl()];
    }

    protected function __construct() {}

}
