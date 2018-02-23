<?php

namespace PHPCensor\Model;

use PHPCensor\Model;

class Environment extends Model
{
    /**
     * @var string
     */
    protected $tableName = 'environment';

    /**
     * @var array
     */
    protected $data = [
        'id'         => null,
        'project_id' => null,
        'name'       => null,
        'branches'   => null,
    ];

    /**
     * @var array
     */
    protected $getters = [
        'id'         => 'getId',
        'project_id' => 'getProjectId',
        'name'       => 'getName',
        'branches'   => 'getBranches',
    ];

    /**
     * @var array
     */
    protected $setters = [
        'id'         => 'setId',
        'project_id' => 'setProjectId',
        'name'       => 'setName',
        'branches'   => 'setBranches',
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
     * @return int
     */
    public function getProjectId()
    {
        $rtn = $this->data['project_id'];

        return $rtn;
    }

    /**
     * @return string
     */
    public function getName()
    {
        $rtn = $this->data['name'];

        return $rtn;
    }

    /**
     * @return array
     */
    public function getBranches()
    {
        $rtn = array_filter(array_map('trim', explode("\n", $this->data['branches'])));

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
     * @param $value int
     */
    public function setProjectId($value)
    {
        $this->validateNotNull('project_id', $value);
        $this->validateInt('project_id', $value);

        if ($this->data['project_id'] === $value) {
            return;
        }

        $this->data['project_id'] = $value;

        $this->setModified('project_id');
    }

    /**
     * @param $value string
     */
    public function setName($value)
    {
        $this->validateNotNull('name', $value);
        $this->validateString('name', $value);

        if ($this->data['name'] === $value) {
            return;
        }

        $this->data['name'] = $value;

        $this->setModified('name');
    }

    /**
     * @param $value array
     */
    public function setBranches($value)
    {
        $this->validateNotNull('branches', $value);
        $value = implode("\n", $value);

        if ($this->data['branches'] === $value) {
            return;
        }

        $this->data['branches'] = $value;

        $this->setModified('branches');
    }
}
