<?php

namespace yii2lab\test\Test;

use PHPUnit\Framework\TestResult;
use yii2lab\app\domain\helpers\EnvService;

// todo: autoreplace "use PHPUnit\Framework\TestResult;" to "use yii2lab\test\Test\Unit;"

/**
 * Class Unit
 *
 * @package yii2lab\test\Test
 *
 * @property \RestTester $tester
 */
class Rest extends \Codeception\Test\Unit {
	
	protected $url;
	protected $version = null;
	
	protected function _before() {
		parent::_before();
		$this->url = $this->url() . SL;
		if(!empty($this->version)) {
			$version = trim($this->version, 'v');
			$this->url .= 'v' . $version . SL;
		}
	}
	
	protected function url($uri = null) {
		$url = EnvService::getUrl('api', 'index-test.php');
		if(!empty($uri)) {
			$url .= SL . $uri;
		}
		return $url;
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