<?php

namespace yii2lab\test;

use api\tests\FunctionalTester;
use Yii;
use yii\test\ActiveFixture;

class RestCest
{
	protected $authId = null;
	public $format = [];

	const EMPTY_STRING = '';
	
	public function _before(FunctionalTester $I) {
		$I->haveHttpHeader('language', 'xx');
		if(method_exists($this, '_fixtures')) {
			$fixtures = $this->_fixtures();
			$I->haveFixtures($fixtures);
		}
		$I->format = $this->format; // todo: kill
	}

	protected function loadFixtures($fixtures) {
		foreach($fixtures as $fixtureClass) {
			/** @var ActiveFixture $fixture */
			$fixture = Yii::createObject($fixtureClass);
			$fixture->unload();
			$fixture->load();
		}
	}

	protected function unloadFixtures($fixtures) {
		foreach($fixtures as $fixtureClass) {
			/** @var ActiveFixture $fixture */
			$fixture = new $fixtureClass;
			$fixture->unload();
		}
	}

}
