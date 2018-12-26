<?php

namespace PHPCensor\Model\Base;

use PHPCensor\Model;

class Environment extends Model
{
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
     * @return integer
     */
    public function getId()
    {
        return (integer)$this->data['id'];
    }

    /**
     * @param integer $value
     *
     * @return boolean
     */
    public function setId($value)
    {
        $this->validateNotNull('id', $value);
        $this->validateInt('id', $value);

        if ($this->data['id'] === $value) {
            return false;
        }

        $this->data['id'] = $value;

        return $this->setModified('id');
    }

    /**
     * @return integer
     */
    public function getProjectId()
    {
        return (integer)$this->data['project_id'];
    }

    /**
     * @param integer $value
     *
     * @return boolean
     */
    public function setProjectId($value)
    {
        $this->validateNotNull('project_id', $value);
        $this->validateInt('project_id', $value);

        if ($this->data['project_id'] === $value) {
            return false;
        }

        $this->data['project_id'] = $value;

        return $this->setModified('project_id');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->data['name'];
    }

    /**
     * @param string $value
     *
     * @return boolean
     */
    public function setName($value)
    {
        $this->validateNotNull('name', $value);
        $this->validateString('name', $value);

        if ($this->data['name'] === $value) {
            return false;
        }

        $this->data['name'] = $value;

        return $this->setModified('name');
    }

    /**
     * @return array
     */
    public function getBranches()
    {
        return array_filter(
            array_map(
                'trim',
                explode("\n", $this->data['branches'])
            )
        );
    }

    /**
     * @param array $value
     *
     * @return boolean
     */
    public function setBranches(array $value)
    {
        $this->validateNotNull('branches', $value);

        $branches = implode("\n", $value);
        if ($this->data['branches'] === $branches) {
            return false;
        }

        $this->data['branches'] = $branches;

        return $this->setModified('branches');
    }
}
