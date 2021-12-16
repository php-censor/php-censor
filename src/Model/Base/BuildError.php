<?php

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
     *
     * @throws InvalidArgumentException
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
     *
     * @throws InvalidArgumentException
     */
    public function setBuildId($value)
    {
        $this->validateNotNull('build_id', $value);
        $this->validateInt('build_id', $value);

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
     *
     * @throws InvalidArgumentException
     */
    public function setPlugin($value)
    {
        $this->validateNotNull('plugin', $value);
        $this->validateString('plugin', $value);

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
     * @param string $value
     *
     * @return bool
     *
     * @throws InvalidArgumentException
     */
    public function setFile($value)
    {
        $this->validateString('file', $value);

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
     * @param int $value
     *
     * @return bool
     *
     * @throws InvalidArgumentException
     */
    public function setLineStart($value)
    {
        $this->validateInt('line_start', $value);

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
     * @param int $value
     *
     * @return bool
     *
     * @throws InvalidArgumentException
     */
    public function setLineEnd($value)
    {
        $this->validateInt('line_end', $value);

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
     *
     * @throws InvalidArgumentException
     */
    public function setSeverity($value)
    {
        $this->validateNotNull('severity', $value);
        $this->validateInt('severity', $value);

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
     *
     * @throws InvalidArgumentException
     */
    public function setMessage($value)
    {
        $this->validateNotNull('message', $value);
        $this->validateString('message', $value);

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
     * @return bool
     *
     * @throws InvalidArgumentException
     */
    public function setCreateDate(DateTime $value)
    {
        $this->validateNotNull('create_date', $value);

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
     *
     * @throws InvalidArgumentException
     */
    public function setHash($value)
    {
        $this->validateNotNull('hash', $value);
        $this->validateString('hash', $value);

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
     *
     * @throws InvalidArgumentException
     */
    public function setIsNew($value)
    {
        $this->validateNotNull('is_new', $value);
        $this->validateBoolean('is_new', $value);

        if ($this->data['is_new'] === (int)$value) {
            return false;
        }

        $this->data['is_new'] = (int)$value;

        return $this->setModified('is_new');
    }
}
