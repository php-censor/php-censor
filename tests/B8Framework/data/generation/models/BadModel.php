<?php

namespace Test\Model\Base;

use b8\Model;

class BadModel extends Model
{
    protected $_tableName = 'bad_table';

    public $columns = [
        'id' => ['type' => 'catfish'],
    ];

    public $indexes     = [];
    public $foreignKeys = [];
}