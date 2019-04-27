<?php

namespace yii2lab\test\Test;

use yii\helpers\ArrayHelper;
use yii2lab\test\helpers\TestHelper;
use yubundle\account\domain\v2\helpers\test\AuthTestHelper;
use yubundle\account\domain\v2\helpers\test\CurrentPhoneTestHelper;

// todo: autoreplace "use PHPUnit\Framework\TestResult;" to "use yii2lab\test\Test\Unit;"

/**
 * Class Unit
 *
 * @package yii2lab\test\Test
 *
 * @property \RestTester|\UnitTester $tester
 */
class Rest extends Base {
	
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

    protected function isSkipBug($message = null) {
        $isSkip = (boolean) TestHelper::getEnvLocalConfig('test.skipBug');
        if($isSkip) {
            $m = 'skip bug';
            if($message) {
                $m .= ' - ' . $message;
            }
            TestHelper::printMessage($m);
        }
        return $isSkip;
    }

    protected function authByNewUser() {
        $phone = CurrentPhoneTestHelper::get();
        AuthTestHelper::authByLogin('test' . $phone);
    }

    protected function getUrlFromEnv() {
        $envConfig = include(__DIR__ . '/../../../../../common/config/env-local.php');
        $url = ArrayHelper::getValue($envConfig, 'url.api');
        $url = trim($url, SL);
        return $url;
    }

	protected function url($uri = null) {
        $url = $this->getUrlFromEnv();
        $url .= SL . 'index-test.php';
		if(!empty($uri)) {
			$url .= SL . $uri;
		}
		return $url;
	}

}