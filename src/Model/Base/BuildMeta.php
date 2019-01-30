<?php

declare(strict_types=1);

namespace PHPCensor\Model\Base;

use PHPCensor\Exception\InvalidArgumentException;
use PHPCensor\Model;

class BuildMeta extends Model
{
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
     * @return int
     */
    public function getId()
    {
        return (int)$this->data['id'];
    }

    /**
     * @param int $value
     *
     * @return bool
     */
    public function setId(int $value)
    {
        if ($this->data['id'] === $value) {
            return false;
        }

        $this->data['id'] = $value;

        return $this->setModified('id');
    }

    /**
     * @return int
     */
    public function getBuildId()
    {
        return (int)$this->data['build_id'];
    }

    /**
     * @param int $value
     *
     * @return bool
     */
    public function setBuildId(int $value)
    {
        if ($this->data['build_id'] === $value) {
            return false ;
        }

        $this->data['build_id'] = $value;

        return $this->setModified('build_id');
    }

    /**
     * @return string
     */
    public function getMetaKey()
    {
        return $this->data['meta_key'];
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    public function setMetaKey(string $value)
    {
        if ($this->data['meta_key'] === $value) {
            return false;
        }

        $this->data['meta_key'] = $value;

        return $this->setModified('meta_key');
    }

    /**
     * @return string
     */
    public function getMetaValue()
    {
        return $this->data['meta_value'];
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    public function setMetaValue(string $value)
    {
        if ($this->data['meta_value'] === $value) {
            return false;
        }

        $this->data['meta_value'] = $value;

        return $this->setModified('meta_value');
    }
}
