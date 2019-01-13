<?php

namespace yii2lab\test\Test;

use PHPUnit\Framework\TestResult;
use yii\helpers\ArrayHelper;
use yii2lab\test\helpers\DataHelper;
use UnitTester;

// todo: autoreplace "use PHPUnit\Framework\TestResult;" to "use yii2lab\test\Test\Unit;"

/**
 * Class Unit
 *
 * @package yii2lab\test\Test
 *
 * @property UnitTester $tester
 */
class Unit extends \Codeception\Test\Unit {
	
	protected function equalExpected($method, $data) {
		$data = ArrayHelper::toArray($data);
		$expect = DataHelper::loadForTest(self::PACKAGE, $method, $data);
		$this->tester->assertEquals($expect, $data, true);
	}
	
	/**
	 * Count elements of an object
	 *
	 * @link http://php.net/manual/en/countable.count.php
	 * @return int The custom count as an integer.
	 * </p>
	 * <p>
	 * The return value is cast to an integer.
	 * @since 5.1.0
	 */
	public function count() {
		return parent::count();
	}
	
	/**
	 * Runs a test and collects its result in a TestResult instance.
	 *
	 * @param TestResult $result
	 *
	 * @return TestResult
	 */
	public function run(TestResult $result = null) {
		return parent::run($result);
	}
	
}