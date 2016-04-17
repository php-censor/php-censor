<?php

namespace Test\Model\Base;
use b8\Model;

class BadModel extends Model
{
	protected $_tableName = 'bad_table';

	public $columns         = array(
		'id'            =>  array('type' => 'catfish'),
	);

	public $indexes         = array(
	);
	public $foreignKeys     = array(
	);
}