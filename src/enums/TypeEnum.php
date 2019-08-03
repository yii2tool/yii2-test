<?php

namespace yii2tool\test\enums;

use yii2rails\extension\enum\base\BaseEnum;

class TypeEnum extends BaseEnum
{

	//const URL = 'string:url';
	//const DATE = 'string:date|null';
	const FLOAT = 'float';
    const NULL = 'null';
	const STRING = 'string';
    const TIME = 'string.time';
    const TIME_ISO8601 = 'string.time.iso8601';
	//const STRING_OR_NULL = 'string|null';
	const INTEGER = 'integer';
	//const INTEGER_OR_NULL = 'integer|null';
	const BOOLEAN = 'boolean';
	//const BOOLEAN_OR_NULL = 'boolean|null';
	const ARRAY = 'array';
	//const ARR_OR_NULL = 'array|null';
	
}
