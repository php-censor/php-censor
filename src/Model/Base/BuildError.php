<?php

declare(strict_types=1);

namespace PHPCensor\Model\Base;

use DateTime;
use Exception;
use PHPCensor\Model;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class BuildError extends Model
{
    public const SEVERITY_CRITICAL = 0;
    public const SEVERITY_HIGH     = 1;
    public const SEVERITY_NORMAL   = 2;
    public const SEVERITY_LOW      = 3;

    protected array $data = [
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
