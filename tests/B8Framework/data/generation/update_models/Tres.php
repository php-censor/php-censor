<?php

namespace Update\Model\Base;

use b8\Model;

class Tres extends Model
{
    protected $_tableName = 'tres';

    public $columns = [
        'key_col'         => ['type' => 'int', 'primary_key' => true, 'auto_increment' => true],
        'id'              => ['type' => 'int'],
        'field_varchar'   => ['type' => 'varchar', 'length' => '250', 'default' => 'Hello World'],
        'field_datetime'  => ['type' => 'datetime'],
        'field_int'       => ['type' => 'int'],
        'field_int_2'     => ['type' => 'int'],
        'field_dt'        => ['type' => 'date', 'rename' => 'field_date'],
        'field_float_1'   => ['type' => 'float', 'default' => '1'],
        'field_varchar_2' => ['type' => 'varchar', 'length' => '10', 'default' => 'Hello'],
        'dosid'           => ['type' => 'int'],
    ];

    public $indexes = [
        'PRIMARY'       => ['unique' => true, 'columns' => 'key_col'],
        'fk_tres_dos'   => ['columns' => 'field_int_2'],
        'fk_tres_dos_2' => ['columns' => 'dosid'],
    ];

    public $foreignKeys = [
        'fk_tres_dos' => [
            'local_col' => 'field_int_2',
            'update'    => 'CASCADE',
            'delete'    => 'CASCADE',
            'table'     => 'dos',
            'col'       => 'id'
        ],
        'fk_tres_dos_2' => [
            'local_col' => 'dosid',
            'update'    => 'CASCADE',
            'delete'    => 'CASCADE',
            'table'     => 'dos',
            'col'       => 'id'
        ],
    ];
}