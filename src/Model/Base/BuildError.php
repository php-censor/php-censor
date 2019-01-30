<?php

declare(strict_types=1);

namespace PHPCensor\Model\Base;

use DateTime;
use Exception;
use PHPCensor\Exception\InvalidArgumentException;
use PHPCensor\Model;

class BuildError extends Model
{
    const SEVERITY_CRITICAL = 0;
    const SEVERITY_HIGH     = 1;
    const SEVERITY_NORMAL   = 2;
    const SEVERITY_LOW      = 3;

    /**
     * @var array
     */
    protected $data = [
        'id'          => null,
        'build_id'    => null,
        'plugin'      => null,
        'file'        => null,
        'line_start'  => null,
        'line_end'    => null,
        'severity'    => null,
        'message'     => null,
        'create_date' => null,
        'hash'        => '',
        'is_new'      => 0,
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
            return false;
        }

        $this->data['build_id'] = $value;

        return $this->setModified('build_id');
    }

    /**
     * @return string
     */
    public function getPlugin()
    {
        return $this->data['plugin'];
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    public function setPlugin(string $value)
    {
        if ($this->data['plugin'] === $value) {
            return false;
        }

        $this->data['plugin'] = $value;

        return $this->setModified('plugin');
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->data['file'];
    }

    /**
     * @param string|null $value
     *
     * @return bool
     */
    public function setFile(?string $value)
    {
        if ($this->data['file'] === $value) {
            return false;
        }

        $this->data['file'] = $value;

        return $this->setModified('file');
    }

    /**
     * @return int
     */
    public function getLineStart()
    {
        return (int)$this->data['line_start'];
    }

    /**
     * @param int|null $value
     *
     * @return bool
     */
    public function setLineStart(?int $value)
    {
        if ($this->data['line_start'] === $value) {
            return false;
        }

        $this->data['line_start'] = $value;

        return $this->setModified('line_start');
    }

    /**
     * @return int
     */
    public function getLineEnd()
    {
        return (int)$this->data['line_end'];
    }

    /**
     * @param int|null $value
     *
     * @return bool
     */
    public function setLineEnd(?int $value)
    {
        if ($this->data['line_end'] === $value) {
            return false;
        }

        $this->data['line_end'] = $value;

        return $this->setModified('line_end');
    }

    /**
     * @return int
     */
    public function getSeverity()
    {
        return (int)$this->data['severity'];
    }

    /**
     * @param int $value
     *
     * @return bool
     */
    public function setSeverity(int $value)
    {
        if ($this->data['severity'] === $value) {
            return false;
        }

        $this->data['severity'] = $value;

        return $this->setModified('severity');
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->data['message'];
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    public function setMessage(string $value)
    {
        if ($this->data['message'] === $value) {
            return false;
        }

        $this->data['message'] = $value;

        return $this->setModified('message');
    }

    /**
     * @return DateTime|null
     *
     * @throws Exception
     */
    public function getCreateDate()
    {
        if ($this->data['create_date']) {
            return new DateTime($this->data['create_date']);
        }

        return null;
    }

    /**
     * @param DateTime $value
     *
     * @return bool
     */
    public function setCreateDate(DateTime $value)
    {
        $stringValue = $value->format('Y-m-d H:i:s');

        if ($this->data['create_date'] === $stringValue) {
            return false;
        }

        $this->data['create_date'] = $stringValue;

        return $this->setModified('create_date');
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->data['hash'];
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    public function setHash(string $value)
    {
        if ($this->data['hash'] === $value) {
            return false;
        }

        $this->data['hash'] = $value;

        return $this->setModified('hash');
    }

    /**
     * @return bool
     */
    public function getIsNew()
    {
        return (bool)$this->data['is_new'];
    }

    /**
     * @param bool $value
     *
     * @return bool
     */
    public function setIsNew(bool $value)
    {
        if ($this->data['is_new'] === (int)$value) {
            return false;
        }

        $this->data['is_new'] = (int)$value;

        return $this->setModified('is_new');
    }
}
