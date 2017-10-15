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
        'id'    => 'getId',
        'title' => 'getTitle',
    ];

    /**
     * @var array
     */
    protected $setters = [
        'id'    => 'setId',
        'title' => 'setTitle',
    ];

    /**
     * Get the value of Id / id.
     *
     * @return int
     */
    public function getId()
    {
        $rtn = $this->data['id'];

        return $rtn;
    }

    /**
     * Get the value of Title / title.
     *
     * @return string
     */
    public function getTitle()
    {
        $rtn = $this->data['title'];

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
