<?php

namespace Test\Model\Base;

use b8\Model;

class Dos extends Model
{
    protected $_tableName = 'dos';

    public $columns = [
        'id'             => ['type' => 'int', 'primary_key' => true, 'auto_increment' => false],
        'field_varchar'  => ['type' => 'varchar', 'length' => '250', 'default' => 'Hello World'],
        'field_datetime' => ['type' => 'datetime'],
        'field_int'      => ['type' => 'int'],
    ];

    public $indexes = [
        'PRIMARY'    => ['unique' => true, 'columns' => 'id'],
        'idx_test_1' => ['unique' => true, 'columns' => 'field_int'],
        'idx_test_2' => ['columns' => 'field_datetime'],
    ];
    public $foreignKeys = [];
}