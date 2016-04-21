<?php

namespace Test\Model\Base;

use b8\Model;

class Tres extends Model
{
    protected $_tableName = 'tres';

    public $columns = [
        'id'             => ['type' => 'int'],
        'field_varchar'  => ['type' => 'varchar', 'length' => '250'],
        'field_date'     => ['type' => 'date'],
        'field_datetime' => ['type' => 'datetime'],
        'field_int'      => ['type' => 'int'],
        'field_int_2'    => ['type' => 'int'],
    ];

    public $indexes = [
        'fk_tres_uno' => ['columns' => 'field_int'],
        'fk_tres_dos' => ['columns' => 'field_int_2'],
    ];

    public $foreignKeys = [
        'fk_tres_uno' => ['local_col' => 'field_int', 'table' => 'uno', 'col' => 'id'],
        'fk_tres_dos' => [
            'local_col' => 'field_int_2',
            'update'    => 'NO ACTION',
            'delete'    => 'CASCADE',
            'table'     => 'dos',
            'col'       => 'id'
        ],
    ];
}