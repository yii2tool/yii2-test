<?php

namespace yii2tool\test\Test;

use yii\helpers\ArrayHelper;
use yii2tool\test\helpers\DataHelper;

// todo: autoreplace "use PHPUnit\Framework\TestResult;" to "use yii2tool\test\Test\Unit;"

/**
 * Class Unit
 *
 * @package yii2tool\test\Test
 *
 * @property UnitTester $tester
 */
class Unit extends Base {
	
	protected function equalExpected($method, $data) {
		$data = ArrayHelper::toArray($data);
		$expect = DataHelper::loadForTest(self::PACKAGE, $method, $data);
		$this->tester->assertEquals($expect, $data, true);
	}

}