<?php

namespace yii2tool\test\traits;

use Throwable;
use yii\base\InvalidArgumentException;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii2tool\test\enums\TypeEnum;
use yii2rails\domain\BaseEntity;
use yii2rails\domain\data\EntityCollection;
use yii2rails\domain\exceptions\UnprocessableEntityHttpException;
use yii2rails\extension\common\enums\RegexpPatternEnum;
use yii2rails\extension\common\helpers\UrlHelper;
use yii2bundle\error\domain\helpers\UnProcessibleHelper;

trait UnitAssertTrait
{
	
	public function assertUrl(array $expected, $actual) {
		$parsedUrl = UrlHelper::parse($actual);
		$this->assertArraySubset($expected, $parsedUrl);
	}
	
	
	public function assertBase64String($actual) {
		$this->assertRegExp(RegexpPatternEnum::BASE_64_REQUIRED, $actual);
	}
	
	public function assertHexString($actual) {
		$this->assertRegExp(RegexpPatternEnum::HEX_REQUIRED, $actual);
	}
	
	public function assertStringLength($length, $actual) {
		$lengthRange = ArrayHelper::toArray($length);
		$lengthExp = implode(',', $lengthRange);
		$this->assertRegExp('#^[\s\S]{' . $lengthExp . '}$#', $actual);
	}
	
	public function assertStringFormat($charExp, $actual) {
		$this->assertRegExp('#^[' . $charExp . ']+$#', $actual);
	}
	
	public function assertNice() {
		$this->assertTrue(true);
	}
	
	public function assertBad($fakeParam = null) {
		$this->assertTrue(false);
	}
	
	public function assertPagination(array $expect, Pagination $pagination) {
		$p = [
			'offset' => $pagination->offset,
			'page' => $pagination->page,
			'limit' => $pagination->limit,
			'pageSize' => $pagination->pageSize,
		];
		
		$this->assertArraySubset($expect, $p);
	}

    public function assertUnprocessableEntityExceptionFields(array $expect, array $fields) {
        $resultFields = [];
	    foreach ($fields as $fieldArray) {
	        if(!empty($fieldArray['field'])) {
                $resultFields[] = $fieldArray['field'];
            }
        }

        $resultFields = array_unique($resultFields);
        $expect = array_unique($expect);

        sort($resultFields);
        sort($expect);

        $resultFields = array_values($resultFields);
        $expect = array_values($expect);

        $this->assertEquals($resultFields, $expect);
    }

	public function assertUnprocessableEntityExceptionMessage(array $expect, UnprocessableEntityHttpException $exception) {
		$array = UnProcessibleHelper::assoc2indexed($exception->getErrors());
		$this->assertEquals($expect, $array);
	}
	
	public function assertExceptionMessage(string $expect, Throwable $exception) {
		$this->assertEquals($expect, $exception->getMessage());
	}

    public function assertExceptionMessageRegexp(string $expect, Throwable $exception) {
        $this->assertRegExp($expect, $exception->getMessage());
    }

	public function assertExceptionCode(int $expect, Throwable $exception) {
		$this->assertEquals($expect, $exception->getCode());
	}
	
	// crutch for linux and windows
	private function normalizeArrayForNewLines(array $data) {
		foreach($data as $k => $item) {
			if(is_string($item)) {
				$data[$k] = str_replace(["\r\n", "\n\r", "\r"], "\n", $item);
			} elseif(is_array($item)) {
				$data[$k] = $this->normalizeArrayForNewLines($item);
			}
		}
		return $data;
	}
	
	public function assertEntity(array $expect, BaseEntity $entity, $isStrict = false) {
		$expect = $this->normalizeArrayForNewLines($expect);
		$entityData = $this->normalizeArrayForNewLines($entity->toArray());
		if($isStrict) {
			$this->assertEquals($expect, $entityData);
		} else {
			$this->assertArraySubset($expect, $entityData);
		}
	}
	
	public function assertCollectionByFieldEquals(array $expect, array $collection, $count = null) {
		if(is_integer($count)) {
			$this->assertCount($count, $collection);
		}
		if(empty($collection)) {
			return;
		}
		foreach($collection as $entity) {
			foreach($expect as $name => $value) {
				$isEqual = $entity->{$name} == $value;
				$this->assertTrue($isEqual);
			}
		}
	}
	
	public function assertCollection(array $expect, array $collection, $isStrict = false) {
		if(empty($collection) && empty($expect)) {
			return;
		}
		foreach($expect as $key => $expectItem) {
			$entity = $collection[$key];
			$expectItem = ArrayHelper::toArray($expectItem);
			$this->assertEntity($expectItem, $entity);
		}
		if($isStrict) {
			$this->assertCount(count($expect), $collection);
		}
	}
	
	public function assertIsCollection(EntityCollection $collection, $class = BaseEntity::class) {
		//$this->assertInternalType(isType::TYPE_ARRAY, $collection);
		foreach($collection as $entity) {
			$this->assertIsEntity($entity, $class);
		}
	}
	
	public function assertIsEntity($entity, $class = BaseEntity::class) {
		$this->assertTrue($entity instanceof $class);
	}
	
	public function assertUnprocessableEntityHttpException($messages, UnprocessableEntityHttpException $e) {
		$errors = $e->getErrors();
		foreach($errors as $error) {
			$field = $error['field'];
			if(isset($messages[$field])) {
				$this->assertEquals($messages[$field], $error['message']);
				return;
			}
		}
		$this->assertTrue(false);
	}

    public function assertCollectionType(array $expect, array $collection, $isStrict = true) {
        foreach ($collection as $entity) {
            $this->assertArrayType($expect, $entity, $isStrict);
	    }
    }

    public function assertArrayType(array $schema, $entity, $isStrict = true) {
        foreach($schema as $field => $type) {
            if($isStrict && !array_key_exists($field, $entity)) {
                $this->assertTrue(false, "Attribute \"{$field}\" not exists!");
            }
            if($isStrict || array_key_exists($field, $entity)) {
                $value = $entity[$field];
                $this->assertType($field, $type, $value);
            }
        }
    }

    public function assertValueType($type, $actual) {
        $result = $this->isValidtType($type, $actual);
        if(!$result) {
            throw new InvalidArgumentException("$type, $actual");
        }
    }

    public function assertType($field, $type, $value) {
        if(is_array($type)) {
            if(ArrayHelper::isIndexed($type)) {
                $rr = 0;
                foreach ($type as $k => $typeItem) {
                    $result = $this->isValidtType($typeItem, $value);
                    if($result) {
                        $rr++;
                    }
                }
                if($rr == 0) {
                    throw new InvalidArgumentException("$field, $value");
                }
            } else {
                $this->assertArrayType($type, $value);
            }
        } else {
            $result = $this->isValidtType($type, $value);
            if(!$result) {
                throw new InvalidArgumentException("$field, $type, $value");
            }
        }
    }

    protected function isValidtType($type, $value) {
        if($type == TypeEnum::TIME) {
            $dateTime = new \DateTime($value);
        } elseif($type == TypeEnum::NULL) {
            if($value !== null) {
                return false;
            }
        } else {
            try {
                $this->assertInternalType($type, $value);
                return true;
            } catch (\PHPUnit\Framework\Exception $e) {
                return false;
            }
        }
        return true;
    }

	public function assertEntityFormat(array $expect, BaseEntity $entity, $isStrict = true) {
		foreach($expect as $field => $type) {
			if($isStrict && !$entity->hasProperty($field)) {
				$this->assertTrue(false, 'Attribute not exists in entity!');
			}
			if($isStrict || property_exists($entity, $field)) {
				$value = $entity->{$field};
				$this->assertInternalType($type, $value);
			}
		}
	}
	
	public function assertCollectionFormat(array $expect, $collection, $isStrict = true) {
		$this->assertIsCollection($collection);
		foreach($collection as $entity) {
			$this->assertEntityFormat($expect, $entity, $isStrict);
		}
	}
	
}
