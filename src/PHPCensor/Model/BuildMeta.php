<?php

namespace PHPCensor\Model;

use PHPCensor\Model;
use b8\Store\Factory;

class BuildMeta extends Model
{
    /**
     * @var string
     */
    protected $tableName = 'build_meta';

    /**
     * @var array
     */
    protected $data = [
        'id'         => null,
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
        'build_id'   => 'getBuildId',
        'meta_key'   => 'getMetaKey',
        'meta_value' => 'getMetaValue',

        // Foreign key getters:
        'Build' => 'getBuild',
    ];

    /**
     * @var array
     */
    protected $setters = [
        // Direct property setters:
        'id'         => 'setId',
        'build_id'   => 'setBuildId',
        'meta_key'   => 'setMetaKey',
        'meta_value' => 'setMetaValue',
    ];

    /**
     * @return integer
     */
    public function getId()
    {
        $rtn = $this->data['id'];

        return $rtn;
    }

    /**
     * @return integer
     */
    public function getBuildId()
    {
        $rtn = $this->data['build_id'];

        return $rtn;
    }

    /**
     * @return string
     */
    public function getMetaKey()
    {
        $rtn = $this->data['meta_key'];

        return $rtn;
    }

    /**
     * @return string
     */
    public function getMetaValue()
    {
        $rtn = $this->data['meta_value'];

        return $rtn;
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
     * @param integer $value
     */
    public function setBuildId($value)
    {
        $this->validateNotNull('build_id', $value);
        $this->validateInt('build_id', $value);

        if ($this->data['build_id'] === $value) {
            return;
        }

        $this->data['build_id'] = $value;

        $this->setModified('build_id');
    }

    /**
     * @param string $value
     */
    public function setMetaKey($value)
    {
        $this->validateNotNull('meta_key', $value);
        $this->validateString('meta_key', $value);

        if ($this->data['meta_key'] === $value) {
            return;
        }

        $this->data['meta_key'] = $value;

        $this->setModified('meta_key');
    }

    /**
     * @param string $value
     */
    public function setMetaValue($value)
    {
        $this->validateNotNull('meta_value', $value);
        $this->validateString('meta_value', $value);

        if ($this->data['meta_value'] === $value) {
            return;
        }

        $this->data['meta_value'] = $value;

        $this->setModified('meta_value');
    }

    /**
     * Get the Build model for this BuildMeta by Id.
     *
     * @return \PHPCensor\Model\Build
     */
    public function getBuild()
    {
        $buildId = $this->getBuildId();
        if (empty($buildId)) {
            return null;
        }

        return Factory::getStore('Build', 'PHPCensor')->getById($buildId);
    }
}
