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
     * @return int
     */
    public function getId()
    {
        $rtn = $this->data['id'];

        return $rtn;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        $rtn = $this->data['title'];

        return $rtn;
    }

    /**
     * @param $value int
     */
    public function setId($value)
    {
        $this->validateNotNull('id', $value);
        $this->validateInt('id', $value);

        if ($this->data['id'] === $value) {
            return;
        }

        $this->data['id'] = $value;

        $this->setModified('id');
    }

    /**
     * @param $value string
     */
    public function setTitle($value)
    {
        $this->validateNotNull('title', $value);
        $this->validateString('title', $value);

        if ($this->data['title'] === $value) {
            return;
        }

        $this->data['title'] = $value;

        $this->setModified('title');
    }

    /**
     * Get Project models by GroupId for this ProjectGroup.
     *
     * @return \PHPCensor\Model\Project[]
     */
    public function getGroupProjects()
    {
        return Factory::getStore('Project', 'PHPCensor')->getByGroupId($this->getId(), false);
    }
}
