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
     * @var array
     */
    public static $sleepable = [];

    /**
     * @var string
     */
    protected $tableName = 'build_error';

    /**
     * @var string
     */
    protected $modelName = 'BuildError';

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

        // Foreign key setters:
        'Build' => 'setBuild',
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
     * @return int
     */
    public function getLineStart()
    {
        $rtn = $this->data['line_start'];

        return $rtn;
    }

    /**
     * @return int
     */
    public function getLineEnd()
    {
        $rtn = $this->data['line_end'];

        return $rtn;
    }

    /**
     * @return int
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
     * @param $value string
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
     * @param $value string
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
     * @param $value int
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
     * @param $value int
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
     * @param $value int
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
     * @param $value string
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
     * @param $value \DateTime
     */
    public function setCreateDate($value)
    {
        $this->validateNotNull('create_date', $value);
        $this->validateDate('create_date', $value);

        if ($this->data['create_date'] === $value) {
            return;
        }

        $this->data['create_date'] = $value;

        $this->setModified('create_date');
    }

    /**
     * @param $value string
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
     * @param $value int
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
     * @return \PHPCensor\Model\Build
     */
    public function getBuild()
    {
        $key = $this->getBuildId();

        if (empty($key)) {
            return null;
        }

        $cacheKey   = 'php-censor.build-' . $key;
        $rtn        = $this->cache->get($cacheKey);

        if (empty($rtn)) {
            $rtn    = Factory::getStore('Build', 'PHPCensor')->getById($key);
            $this->cache->set($cacheKey, $rtn);
        }

        return $rtn;
    }

    /**
     * Set Build - Accepts an ID, an array representing a Build or a Build model.
     *
     * @param $value mixed
     */
    public function setBuild($value)
    {
        // Is this an instance of Build?
        if ($value instanceof Build) {
            return $this->setBuildObject($value);
        }

        // Is this an array representing a Build item?
        if (is_array($value) && !empty($value['id'])) {
            return $this->setBuildId($value['id']);
        }

        // Is this a scalar value representing the ID of this foreign key?
        return $this->setBuildId($value);
    }

    /**
     * Set Build - Accepts a Build model.
     *
     * @param $value Build
     */
    public function setBuildObject(Build $value)
    {
        return $this->setBuildId($value->getId());
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
