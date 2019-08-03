<?php

namespace yii2tool\test\traits;

trait RestAssertTrait
{
	
	public function seeResponseArray($data) {
		$this->seeResponseContainsJson($data);
	}

}
