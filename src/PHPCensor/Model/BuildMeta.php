<?php

namespace PHPCensor\Model;

use PHPCensor\Model;
use b8\Store\Factory;

class BuildMeta extends Model
{
    /**
     * @var array
     */
    public static $sleepable = [];

    /**
     * @var string
     */
    protected $tableName = 'build_meta';

    /**
     * @var string
     */
    protected $modelName = 'BuildMeta';

    /**
     * @var array
     */
    protected $data = [
        'id'         => null,
        'project_id' => null,
        'build_id'   => null,
        'meta_key'   => null,
        'meta_value' => null,
    ];

    /**
     * @var array
     */
    protected $getters = [
        // Direct property getters:
        'id'         => 'getId',
        'project_id' => 'getProjectId',
        'build_id'   => 'getBuildId',
        'meta_key'   => 'getMetaKey',
        'meta_value' => 'getMetaValue',
        // Foreign key getters:
        'Project'    => 'getProject',
        'Build'      => 'getBuild',
    ];

    /**
     * @var array
     */
    protected $setters = [
        // Direct property setters:
        'id'         => 'setId',
        'project_id' => 'setProjectId',
        'build_id'   => 'setBuildId',
        'meta_key'   => 'setMetaKey',
        'meta_value' => 'setMetaValue',
        // Foreign key setters:
        'Project'    => 'setProject',
        'Build'      => 'setBuild',
    ];

    /**
     * @var array
     */
    public $columns = [
        'id' => [
            'type'           => 'int',
            'length'         => 10,
            'primary_key'    => true,
            'auto_increment' => true,
            'default'        => null,
        ],
        'project_id' => [
            'type'    => 'int',
            'length'  => 11,
            'default' => null,
        ],
        'build_id' => [
            'type'    => 'int',
            'length'  => 11,
            'default' => null,
        ],
        'meta_key' => [
            'type'    => 'varchar',
            'length'  => 250,
            'default' => null,
        ],
        'meta_value' => [
            'type'    => 'mediumtext',
            'default' => null,
        ],
    ];

    /**
     * @var array
     */
    public $indexes = [
        'PRIMARY'     => ['unique' => true, 'columns' => 'id'],
        'idx_meta_id' => ['unique' => true, 'columns' => 'build_id, meta_key'],
        'project_id'  => ['columns' => 'project_id'],
    ];

    /**
     * @var array
     */
    public $foreignKeys = [
        'build_meta_ibfk_1' => [
            'local_col' => 'project_id',
            'update'    => 'CASCADE',
            'delete'    => 'CASCADE',
            'table'     => 'project',
            'col'       => 'id'
        ],
        'fk_meta_build_id' => [
            'local_col' => 'build_id',
            'update'    => 'CASCADE',
            'delete'    => 'CASCADE',
            'table'     => 'build',
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
     * Get the value of ProjectId / project_id.
     *
     * @return int
     */
    public function getProjectId()
    {
        $rtn    = $this->data['project_id'];

        return $rtn;
    }

    /**
     * Get the value of BuildId / build_id.
     *
     * @return int
     */
    public function getBuildId()
    {
        $rtn    = $this->data['build_id'];

        return $rtn;
    }

    /**
     * Get the value of MetaKey / meta_key.
     *
     * @return string
     */
    public function getMetaKey()
    {
        $rtn    = $this->data['meta_key'];

        return $rtn;
    }

    /**
     * Get the value of MetaValue / meta_value.
     *
     * @return string
     */
    public function getMetaValue()
    {
        $rtn    = $this->data['meta_value'];

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
     * Set the value of ProjectId / project_id.
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
     * Set the value of BuildId / build_id.
     *
     * Must not be null.
     * @param $value int
     */
    public function setBuildId($value)
    {
        $this->validateNotNull('BuildId', $value);
        $this->validateInt('BuildId', $value);

        if ($this->data['build_id'] === $value) {
            return;
        }

        $this->data['build_id'] = $value;

        $this->setModified('build_id');
    }

    /**
     * Set the value of MetaKey / meta_key.
     *
     * Must not be null.
     * @param $value string
     */
    public function setMetaKey($value)
    {
        $this->validateNotNull('MetaKey', $value);
        $this->validateString('MetaKey', $value);

        if ($this->data['meta_key'] === $value) {
            return;
        }

        $this->data['meta_key'] = $value;

        $this->setModified('meta_key');
    }

    /**
     * Set the value of MetaValue / meta_value.
     *
     * Must not be null.
     * @param $value string
     */
    public function setMetaValue($value)
    {
        $this->validateNotNull('MetaValue', $value);
        $this->validateString('MetaValue', $value);

        if ($this->data['meta_value'] === $value) {
            return;
        }

        $this->data['meta_value'] = $value;

        $this->setModified('meta_value');
    }

    /**
     * Get the Project model for this BuildMeta by Id.
     *
     * @uses \PHPCensor\Store\ProjectStore::getById()
     * @uses \PHPCensor\Model\Project
     * @return \PHPCensor\Model\Project
     */
    public function getProject()
    {
        $key = $this->getProjectId();

        if (empty($key)) {
            return null;
        }

        $cacheKey   = 'Cache.Project.' . $key;
        $rtn        = $this->cache->get($cacheKey, null);

        if (empty($rtn)) {
            $rtn    = Factory::getStore('Project', 'PHPCensor')->getById($key);
            $this->cache->set($cacheKey, $rtn);
        }

        return $rtn;
    }

    /**
     * Set Project - Accepts an ID, an array representing a Project or a Project model.
     *
     * @param $value mixed
     */
    public function setProject($value)
    {
        // Is this an instance of Project?
        if ($value instanceof Project) {
            return $this->setProjectObject($value);
        }

        // Is this an array representing a Project item?
        if (is_array($value) && !empty($value['id'])) {
            return $this->setProjectId($value['id']);
        }

        // Is this a scalar value representing the ID of this foreign key?
        return $this->setProjectId($value);
    }

    /**
     * Set Project - Accepts a Project model.
     *
     * @param $value Project
     */
    public function setProjectObject(Project $value)
    {
        return $this->setProjectId($value->getId());
    }

    /**
     * Get the Build model for this BuildMeta by Id.
     *
     * @uses \PHPCensor\Store\BuildStore::getById()
     * @uses \PHPCensor\Model\Build
     * @return \PHPCensor\Model\Build
     */
    public function getBuild()
    {
        $key = $this->getBuildId();

        if (empty($key)) {
            return null;
        }

        $cacheKey   = 'Cache.Build.' . $key;
        $rtn        = $this->cache->get($cacheKey, null);

        if (empty($rtn)) {
            $rtn    = Factory::getStore('Build', 'PHPCensor')->getById($key);
            $this->cache->set($cacheKey, $rtn);
        }

        return $rtn;
    }

    /**
     * Set Build - Accepts an ID, an array representing a Build or a Build model.
     *
     * @param $value mixed
     */
    public function setBuild($value)
    {
        // Is this an instance of Build?
        if ($value instanceof Build) {
            return $this->setBuildObject($value);
        }

        // Is this an array representing a Build item?
        if (is_array($value) && !empty($value['id'])) {
            return $this->setBuildId($value['id']);
        }

        // Is this a scalar value representing the ID of this foreign key?
        return $this->setBuildId($value);
    }

    /**
     * Set Build - Accepts a Build model.
     *
     * @param $value Build
     */
    public function setBuildObject(Build $value)
    {
        return $this->setBuildId($value->getId());
    }
}
