<?php

namespace yii2lab\test\traits;

trait RestAssertTrait
{
	
	public function seeResponseArray($data) {
		$this->seeResponseContainsJson($data);
	}

}
