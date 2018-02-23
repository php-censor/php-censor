<?php

namespace PHPCensor\Model;

use PHPCensor\Model;
use b8\Store\Factory;

class ProjectGroup extends Model
{
    /**
     * @var string
     */
    protected $tableName = 'project_group';

    /**
     * @var array
     */
    protected $data = [
        'id'          => null,
        'title'       => null,
        'create_date' => null,
        'user_id'     => 0,
    ];

    /**
     * @var array
     */
    protected $getters = [
        'id'          => 'getId',
        'title'       => 'getTitle',
        'create_date' => 'getCreateDate',
        'user_id'     => 'getUserId',
    ];

    /**
     * @var array
     */
    protected $setters = [
        'id'          => 'setId',
        'title'       => 'setTitle',
        'create_date' => 'setCreateDate',
        'user_id'     => 'setUserId',
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
     * @return string
     */
    public function getTitle()
    {
        $rtn = $this->data['title'];

        return $rtn;
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
     * @return \DateTime
     */
    public function getCreateDate()
    {
        $rtn = $this->data['create_date'];

        if (!empty($rtn)) {
            $rtn = new \DateTime($rtn);
        }

        return $rtn;
    }

    /**
     * @param $value \DateTime
     */
    public function setCreateDate($value)
    {
        $this->validateDate('create_date', $value);

        if ($this->data['create_date'] === $value) {
            return;
        }

        $this->data['create_date'] = $value;

        $this->setModified('create_date');
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        $rtn = $this->data['user_id'];

        return (integer)$rtn;
    }

    /**
     * @param $value integer
     */
    public function setUserId($value)
    {
        $this->validateNotNull('user_id', $value);
        $this->validateInt('user_id', $value);

        if ($this->data['user_id'] === $value) {
            return;
        }

        $this->data['user_id'] = $value;

        $this->setModified('user_id');
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
