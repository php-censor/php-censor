<?php

namespace PHPCensor\Model;

use PHPCensor\Model;
use b8\Store\Factory;

class BuildError extends Model
{
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
        'created_date' => null,
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
        'created_date' => 'getCreatedDate',

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
        'created_date' => 'setCreatedDate',

        // Foreign key setters:
        'Build' => 'setBuild',
    ];

    /**
     * @var array
     */
    public $columns = [
        'id' => [
            'type'           => 'int',
            'length'         => 11,
            'primary_key'    => true,
            'auto_increment' => true,
            'default'        => null,
        ],
        'build_id' => [
            'type'    => 'int',
            'length'  => 11,
            'default' => null,
        ],
        'plugin' => [
            'type'    => 'varchar',
            'length'  => 100,
            'default' => null,
        ],
        'file' => [
            'type'     => 'varchar',
            'length'   => 250,
            'nullable' => true,
            'default'  => null,
        ],
        'line_start' => [
            'type'     => 'int',
            'length'   => 11,
            'nullable' => true,
            'default'  => null,
        ],
        'line_end' => [
            'type'     => 'int',
            'length'   => 11,
            'nullable' => true,
            'default'  => null,
        ],
        'severity' => [
            'type'    => 'tinyint',
            'length'  => 3,
            'default' => null,
        ],
        'message' => [
            'type'    => 'varchar',
            'length'  => 250,
            'default' => null,
        ],
        'created_date' => [
            'type'    => 'datetime',
            'default' => null,
        ],
    ];

    /**
     * @var array
     */
    public $indexes = [
        'PRIMARY'  => ['unique' => true, 'columns' => 'id'],
        'build_id' => ['columns' => 'build_id, created_date'],
    ];

    /**
     * @var array
     */
    public $foreignKeys = [
        'build_error_ibfk_1' => [
            'local_col' => 'build_id',
            'update'    => 'CASCADE',
            'delete'    => 'CASCADE',
            'table'     => 'build',
            'col'       => 'id'
        ],
    ];

    /**
     * Get the value of Id / id.
     *
     * @return int
     */
    public function getId()
    {
        $rtn = $this->data['id'];

        return $rtn;
    }

    /**
     * Get the value of BuildId / build_id.
     *
     * @return int
     */
    public function getBuildId()
    {
        $rtn = $this->data['build_id'];

        return $rtn;
    }

    /**
     * Get the value of Plugin / plugin.
     *
     * @return string
     */
    public function getPlugin()
    {
        $rtn = $this->data['plugin'];

        return $rtn;
    }

    /**
     * Get the value of File / file.
     *
     * @return string
     */
    public function getFile()
    {
        $rtn = $this->data['file'];

        return $rtn;
    }

    /**
     * Get the value of LineStart / line_start.
     *
     * @return int
     */
    public function getLineStart()
    {
        $rtn = $this->data['line_start'];

        return $rtn;
    }

    /**
     * Get the value of LineEnd / line_end.
     *
     * @return int
     */
    public function getLineEnd()
    {
        $rtn = $this->data['line_end'];

        return $rtn;
    }

    /**
     * Get the value of Severity / severity.
     *
     * @return int
     */
    public function getSeverity()
    {
        $rtn = $this->data['severity'];

        return $rtn;
    }

    /**
     * Get the value of Message / message.
     *
     * @return string
     */
    public function getMessage()
    {
        $rtn = $this->data['message'];

        return $rtn;
    }

    /**
     * Get the value of CreatedDate / created_date.
     *
     * @return \DateTime
     */
    public function getCreatedDate()
    {
        $rtn = $this->data['created_date'];

        if (!empty($rtn)) {
            $rtn = new \DateTime($rtn);
        }

        return $rtn;
    }

    /**
     * Set the value of Id / id.
     *
     * Must not be null.
     * @param $value int
     */
    public function setId($value)
    {
        $this->validateNotNull('Id', $value);
        $this->validateInt('Id', $value);

        if ($this->data['id'] === $value) {
            return;
        }

        $this->data['id'] = $value;

        $this->setModified('id');
    }

    /**
     * Set the value of BuildId / build_id.
     *
     * Must not be null.
     * @param $value int
     */
    public function setBuildId($value)
    {
        $this->validateNotNull('BuildId', $value);
        $this->validateInt('BuildId', $value);

        if ($this->data['build_id'] === $value) {
            return;
        }

        $this->data['build_id'] = $value;

        $this->setModified('build_id');
    }

    /**
     * Set the value of Plugin / plugin.
     *
     * Must not be null.
     * @param $value string
     */
    public function setPlugin($value)
    {
        $this->validateNotNull('Plugin', $value);
        $this->validateString('Plugin', $value);

        if ($this->data['plugin'] === $value) {
            return;
        }

        $this->data['plugin'] = $value;

        $this->setModified('plugin');
    }

    /**
     * Set the value of File / file.
     *
     * @param $value string
     */
    public function setFile($value)
    {
        $this->validateString('File', $value);

        if ($this->data['file'] === $value) {
            return;
        }

        $this->data['file'] = $value;

        $this->setModified('file');
    }

    /**
     * Set the value of LineStart / line_start.
     *
     * @param $value int
     */
    public function setLineStart($value)
    {
        $this->validateInt('LineStart', $value);

        if ($this->data['line_start'] === $value) {
            return;
        }

        $this->data['line_start'] = $value;

        $this->setModified('line_start');
    }

    /**
     * Set the value of LineEnd / line_end.
     *
     * @param $value int
     */
    public function setLineEnd($value)
    {
        $this->validateInt('LineEnd', $value);

        if ($this->data['line_end'] === $value) {
            return;
        }

        $this->data['line_end'] = $value;

        $this->setModified('line_end');
    }

    /**
     * Set the value of Severity / severity.
     *
     * Must not be null.
     * @param $value int
     */
    public function setSeverity($value)
    {
        $this->validateNotNull('Severity', $value);
        $this->validateInt('Severity', $value);

        if ($this->data['severity'] === $value) {
            return;
        }

        $this->data['severity'] = $value;

        $this->setModified('severity');
    }

    /**
     * Set the value of Message / message.
     *
     * Must not be null.
     * @param $value string
     */
    public function setMessage($value)
    {
        $this->validateNotNull('Message', $value);
        $this->validateString('Message', $value);

        if ($this->data['message'] === $value) {
            return;
        }

        $this->data['message'] = $value;

        $this->setModified('message');
    }

    /**
     * Set the value of CreatedDate / created_date.
     *
     * Must not be null.
     * @param $value \DateTime
     */
    public function setCreatedDate($value)
    {
        $this->validateNotNull('CreatedDate', $value);
        $this->validateDate('CreatedDate', $value);

        if ($this->data['created_date'] === $value) {
            return;
        }

        $this->data['created_date'] = $value;

        $this->setModified('created_date');
    }

    /**
     * Get the Build model for this BuildError by Id.
     *
     * @uses \PHPCensor\Store\BuildStore::getById()
     * @uses \PHPCensor\Model\Build
     * @return \PHPCensor\Model\Build
     */
    public function getBuild()
    {
        $key = $this->getBuildId();

        if (empty($key)) {
            return null;
        }

        $cacheKey   = 'Cache.Build.' . $key;
        $rtn        = $this->cache->get($cacheKey, null);

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

    const SEVERITY_CRITICAL = 0;
    const SEVERITY_HIGH = 1;
    const SEVERITY_NORMAL = 2;
    const SEVERITY_LOW = 3;

    /**
     * Get the language string key for this error's severity level.
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
     * Get the class to apply to HTML elements representing this error.
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
