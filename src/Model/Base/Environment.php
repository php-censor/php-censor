<?php

namespace PHPCensor\Model\Base;

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
     * @return integer
     */
    public function getId()
    {
        return (integer)$this->data['id'];
    }

    /**
     * @param integer $value
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
     * @return integer
     */
    public function getProjectId()
    {
        return (integer)$this->data['project_id'];
    }

    /**
     * @param integer $value
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
     * @return string
     */
    public function getName()
    {
        return $this->data['name'];
    }

    /**
     * @param string $value
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
     */
    public function setBranches(array $value)
    {
        $this->validateNotNull('branches', $value);

        $branches = implode("\n", $value);
        if ($this->data['branches'] === $branches) {
            return;
        }

        $this->data['branches'] = $branches;

        $this->setModified('branches');
    }
}
