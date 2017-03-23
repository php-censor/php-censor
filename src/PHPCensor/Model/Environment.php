<?php

namespace PHPCensor\Model;

use PHPCensor\Model;

class Environment extends Model
{
    /**
     * @var array
     */
    public static $sleepable = [];

    /**
     * @var string
     */
    protected $tableName = 'environment';

    /**
     * @var string
     */
    protected $modelName = 'Environment';

    /**
     * @var array
     */
    protected $data = [
        'id'    => null,
        'project_id' => null,
        'name' => null,
        'branches' => null,
    ];

    /**
     * @var array
     */
    protected $getters = [
        // Direct property getters:
        'id'    => 'getId',
        'project_id' => 'getProjectId',
        'name' => 'getName',
        'branches' => 'getBranches',
        // Foreign key getters:
    ];

    /**
     * @var array
     */
    protected $setters = [
        // Direct property setters:
        'id'    => 'setId',
        'project_id'    => 'setProjectId',
        'name' => 'setName',
        'branches' => 'setBranches',
        // Foreign key setters:
    ];

    /**
     * @var array
     */
    public $columns = [
        'id' => [
            'type'           => 'int',
            'length'         => 11,
            'primary_key'    => true,
            'auto_increment' => true,
            'default'        => null,
        ],
        'project_id' => [
            'type'           => 'int',
            'length'         => 11,
            'primary_key'    => true,
            'default'        => null,
        ],
        'name' => [
            'type'    => 'varchar',
            'length'  => 20,
            'default' => null,
        ],
        'branches' => [
            'type'    => 'text',
            'default' => '',
        ],
    ];

    /**
     * @var array
     */
    public $indexes = [
        'PRIMARY' => ['unique' => true, 'columns' => 'project_id, name'],
    ];

    /**
     * @var array
     */
    public $foreignKeys = [
        'environment_ibfk_1' => [
            'local_col' => 'project_id',
            'update'    => 'CASCADE',
            'delete'    => '',
            'table'     => 'project',
            'col'       => 'id'
        ],
    ];

    /**
     * Get the value of Id / id.
     *
     * @return int
     */
    public function getId()
    {
        $rtn    = $this->data['id'];

        return $rtn;
    }

    /**
     * Get the value of Id / id.
     *
     * @return int
     */
    public function getProjectId()
    {
        $rtn = $this->data['project_id'];

        return $rtn;
    }

    /**
     * Get the value of Title / title.
     *
     * @return string
     */
    public function getName()
    {
        $rtn    = $this->data['name'];

        return $rtn;
    }

    /**
     * Get the value of Title / title.
     *
     * @return string
     */
    public function getBranches()
    {
        $rtn = array_filter(array_map('trim', explode("\n", $this->data['branches'])));

        return $rtn;
    }

    /**
     * Set the value of Id / id.
     *
     * Must not be null.
     * @param $value int
     */
    public function setId($value)
    {
        $this->validateNotNull('Id', $value);
        $this->validateInt('Id', $value);

        if ($this->data['id'] === $value) {
            return;
        }

        $this->data['id'] = $value;

        $this->setModified('id');
    }

    /**
     * Set the value of Id / id.
     *
     * Must not be null.
     * @param $value int
     */
    public function setProjectId($value)
    {
        $this->validateNotNull('ProjectId', $value);
        $this->validateInt('ProjectId', $value);

        if ($this->data['project_id'] === $value) {
            return;
        }

        $this->data['project_id'] = $value;

        $this->setModified('project_id');
    }

    /**
     * Set the value of Name / name
     *
     * Must not be null.
     * @param $value string
     */
    public function setName($value)
    {
        $this->validateNotNull('Name', $value);
        $this->validateString('Name', $value);

        if ($this->data['name'] === $value) {
            return;
        }

        $this->data['name'] = $value;

        $this->setModified('name');
    }

    /**
     * Set the value of Branches / branches
     *
     * Must not be null.
     * @param $value array
     */
    public function setBranches($value)
    {
        $this->validateNotNull('Branches', $value);
        $value = implode("\n", $value);

        if ($this->data['branches'] === $value) {
            return;
        }

        $this->data['branches'] = $value;

        $this->setModified('branches');
    }
}
