<?php

namespace Generation;
use Generation\Model\Uno;

class ArrayPropertyModel extends Uno
{
	public function __construct($initialData = [])
	{
		$this->_getters['array_property'] = 'getArrayProperty';
		self::$sleepable[] = 'array_property';
	}

	public function getArrayProperty()
	{
		return ['one' => 'two', 'three' => ['four' => 'five']];
	}
}