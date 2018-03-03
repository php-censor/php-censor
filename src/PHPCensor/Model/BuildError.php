<?php

namespace PHPCensor\Model;

use PHPCensor\Model;
use b8\Store\Factory;

class BuildError extends Model
{
    const SEVERITY_CRITICAL = 0;
    const SEVERITY_HIGH     = 1;
    const SEVERITY_NORMAL   = 2;
    const SEVERITY_LOW      = 3;

    /**
     * @var string
     */
    protected $tableName = 'build_error';

    /**
     * @var array
     */
    protected $data = [
        'id'           => null,
        'build_id'     => null,
        'plugin'       => null,
        'file'         => null,
        'line_start'   => null,
        'line_end'     => null,
        'severity'     => null,
        'message'      => null,
        'create_date'  => null,
        'hash'         => null,
        'is_new'       => null,
    ];

    /**
     * @var array
     */
    protected $getters = [
        // Direct property getters:
        'id'           => 'getId',
        'build_id'     => 'getBuildId',
        'plugin'       => 'getPlugin',
        'file'         => 'getFile',
        'line_start'   => 'getLineStart',
        'line_end'     => 'getLineEnd',
        'severity'     => 'getSeverity',
        'message'      => 'getMessage',
        'create_date'  => 'getCreateDate',
        'hash'         => 'getHash',
        'is_new'       => 'getIsNew',

        // Foreign key getters:
        'Build' => 'getBuild',
    ];

    /**
     * @var array
     */
    protected $setters = [
        // Direct property setters:
        'id'           => 'setId',
        'build_id'     => 'setBuildId',
        'plugin'       => 'setPlugin',
        'file'         => 'setFile',
        'line_start'   => 'setLineStart',
        'line_end'     => 'setLineEnd',
        'severity'     => 'setSeverity',
        'message'      => 'setMessage',
        'create_date'  => 'setCreateDate',
        'hash'         => 'setHash',
        'is_new'       => 'setIsNew',
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
    public function getPlugin()
    {
        $rtn = $this->data['plugin'];

        return $rtn;
    }

    /**
     * @return string
     */
    public function getFile()
    {
        $rtn = $this->data['file'];

        return $rtn;
    }

    /**
     * @return integer
     */
    public function getLineStart()
    {
        $rtn = $this->data['line_start'];

        return $rtn;
    }

    /**
     * @return integer
     */
    public function getLineEnd()
    {
        $rtn = $this->data['line_end'];

        return $rtn;
    }

    /**
     * @return integer
     */
    public function getSeverity()
    {
        $rtn = $this->data['severity'];

        return $rtn;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        $rtn = $this->data['message'];

        return $rtn;
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
     * @return string
     */
    public function getHash()
    {
        $rtn = (string)$this->data['hash'];

        return $rtn;
    }

    /**
     * @return string
     */
    public function getIsNew()
    {
        $rtn = $this->data['is_new'];

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
    public function setPlugin($value)
    {
        $this->validateNotNull('plugin', $value);
        $this->validateString('plugin', $value);

        if ($this->data['plugin'] === $value) {
            return;
        }

        $this->data['plugin'] = $value;

        $this->setModified('plugin');
    }

    /**
     * @param string $value
     */
    public function setFile($value)
    {
        $this->validateString('file', $value);

        if ($this->data['file'] === $value) {
            return;
        }

        $this->data['file'] = $value;

        $this->setModified('file');
    }

    /**
     * @param integer $value
     */
    public function setLineStart($value)
    {
        $this->validateInt('line_start', $value);

        if ($this->data['line_start'] === $value) {
            return;
        }

        $this->data['line_start'] = $value;

        $this->setModified('line_start');
    }

    /**
     * @param integer $value
     */
    public function setLineEnd($value)
    {
        $this->validateInt('line_end', $value);

        if ($this->data['line_end'] === $value) {
            return;
        }

        $this->data['line_end'] = $value;

        $this->setModified('line_end');
    }

    /**
     * @param integer $value
     */
    public function setSeverity($value)
    {
        $this->validateNotNull('severity', $value);
        $this->validateInt('severity', $value);

        if ($this->data['severity'] === $value) {
            return;
        }

        $this->data['severity'] = $value;

        $this->setModified('severity');
    }

    /**
     * @param string $value
     */
    public function setMessage($value)
    {
        $this->validateNotNull('message', $value);
        $this->validateString('message', $value);

        if ($this->data['message'] === $value) {
            return;
        }

        $this->data['message'] = $value;

        $this->setModified('message');
    }

    /**
     * @param \DateTime $value
     */
    public function setCreateDate(\DateTime $value)
    {
        $this->validateNotNull('create_date', $value);
        $this->validateDate('create_date', $value);

        $stringValue = $value->format('Y-m-d H:i:s');

        if ($this->data['create_date'] === $stringValue) {
            return;
        }

        $this->data['create_date'] = $stringValue;

        $this->setModified('create_date');
    }

    /**
     * @param string $value
     */
    public function setHash($value)
    {
        $this->validateNotNull('hash', $value);
        $this->validateString('hash', $value);

        if ($this->data['hash'] === $value) {
            return;
        }

        $this->data['hash'] = $value;

        $this->setModified('hash');
    }

    /**
     * @param integer $value
     */
    public function setIsNew($value)
    {
        $this->validateNotNull('is_new', $value);
        $this->validateInt('is_new', $value);

        if ($this->data['is_new'] === $value) {
            return;
        }

        $this->data['is_new'] = $value;

        $this->setModified('is_new');
    }

    /**
     * Get the Build model for this BuildError by Id.
     *
     * @return \PHPCensor\Model\Build|null
     */
    public function getBuild()
    {
        $buildId = $this->getBuildId();
        if (empty($buildId)) {
            return null;
        }

        return Factory::getStore('Build', 'PHPCensor')->getById($buildId);
    }

    /**
     * Get the language string key for this error's severity level.
     *
     * @return string
     */
    public function getSeverityString()
    {
        switch ($this->getSeverity()) {
            case self::SEVERITY_CRITICAL:
                return 'critical';

            case self::SEVERITY_HIGH:
                return 'high';

            case self::SEVERITY_NORMAL:
                return 'normal';

            case self::SEVERITY_LOW:
                return 'low';
        }
    }

    /**
     * Get the language string key for this error's severity level.
     *
     * @param integer $severity
     *
     * @return string
     */
    public static function getSeverityName($severity)
    {
        switch ($severity) {
            case self::SEVERITY_CRITICAL:
                return 'critical';

            case self::SEVERITY_HIGH:
                return 'high';

            case self::SEVERITY_NORMAL:
                return 'normal';

            case self::SEVERITY_LOW:
                return 'low';
        }
    }

    /**
     * @param string  $plugin
     * @param string  $file
     * @param integer $lineStart
     * @param integer $lineEnd
     * @param integer $severity
     * @param string  $message
     *
     * @return string
     */
    public static function generateHash($plugin, $file, $lineStart, $lineEnd, $severity, $message)
    {
        return md5($plugin . $file . $lineStart . $lineEnd . $severity . $message);
    }

    /**
     * Get the class to apply to HTML elements representing this error.
     *
     * @return string
     */
    public function getSeverityClass()
    {
        switch ($this->getSeverity()) {
            case self::SEVERITY_CRITICAL:
                return 'danger';

            case self::SEVERITY_HIGH:
                return 'warning';

            case self::SEVERITY_NORMAL:
                return 'info';

            case self::SEVERITY_LOW:
                return 'default';
        }
    }
}
