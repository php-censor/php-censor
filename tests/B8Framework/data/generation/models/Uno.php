<?php

namespace Test\Model\Base;

use b8\Model;

class Uno extends Model
{
    protected $_tableName = 'uno';

    public $columns = [
        'id'             => ['type' => 'int', 'primary_key' => true, 'auto_increment' => true],
        'field_varchar'  => ['type' => 'varchar', 'length' => '250'],
        'field_text'     => ['type' => 'text'],
        'field_ltext'    => ['type' => 'longtext'],
        'field_mtext'    => ['type' => 'mediumtext'],
        'field_date'     => ['type' => 'date'],
        'field_datetime' => ['type' => 'datetime'],
        'field_int'      => ['type' => 'int'],
        'field_tinyint'  => ['type' => 'tinyint', 'length' => '1'],
        'field_float'    => ['type' => 'float'],
        'field_double'   => ['type' => 'double', 'length' => '15,2'],
    ];

    public $indexes     = [];
    public $foreignKeys = [];
}