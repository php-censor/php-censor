<?php

namespace PHPCensor\Model;

use PHPCensor\Model;
use b8\Store\Factory;

class ProjectGroup extends Model
{
    /**
     * @var array
     */
    public static $sleepable = [];

    /**
     * @var string
     */
    protected $tableName = 'project_group';

    /**
     * @var string
     */
    protected $modelName = 'ProjectGroup';

    /**
     * @var array
     */
    protected $data = [
        'id'    => null,
        'title' => null,
    ];

    /**
     * @var array
     */
    protected $getters = [
        // Direct property getters:
        'id'    => 'getId',
        'title' => 'getTitle',
        // Foreign key getters:
    ];

    /**
     * @var array
     */
    protected $setters = [
        // Direct property setters:
        'id'    => 'setId',
        'title' => 'setTitle',
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
        'title' => [
            'type'    => 'varchar',
            'length'  => 100,
            'default' => null,
        ],
    ];

    /**
     * @var array
     */
    public $indexes = [
        'PRIMARY' => ['unique' => true, 'columns' => 'id'],
    ];

    /**
     * @var array
     */
    public $foreignKeys = [];

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
     * Get the value of Title / title.
     *
     * @return string
     */
    public function getTitle()
    {
        $rtn    = $this->data['title'];

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
     * Set the value of Title / title.
     *
     * Must not be null.
     * @param $value string
     */
    public function setTitle($value)
    {
        $this->validateNotNull('Title', $value);
        $this->validateString('Title', $value);

        if ($this->data['title'] === $value) {
            return;
        }

        $this->data['title'] = $value;

        $this->setModified('title');
    }

    /**
     * Get Project models by GroupId for this ProjectGroup.
     *
     * @uses \PHPCensor\Store\ProjectStore::getByGroupId()
     * @uses \PHPCensor\Model\Project
     * @return \PHPCensor\Model\Project[]
     */
    public function getGroupProjects()
    {
        return Factory::getStore('Project', 'PHPCensor')->getByGroupId($this->getId(), false);
    }
}
