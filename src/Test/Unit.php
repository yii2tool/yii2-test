<?php

namespace yii2lab\test\Test;

use yii\helpers\ArrayHelper;
use yii2lab\test\helpers\DataHelper;

// todo: autoreplace "use PHPUnit\Framework\TestResult;" to "use yii2lab\test\Test\Unit;"

/**
 * Class Unit
 *
 * @package yii2lab\test\Test
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