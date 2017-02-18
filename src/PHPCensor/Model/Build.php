<?php

namespace PHPCensor\Model;

use PHPCensor\Builder;
use Symfony\Component\Yaml\Parser as YamlParser;
use PHPCensor\Model;
use b8\Store\Factory;

/**
 * @author Dan Cryer <dan@block8.co.uk>
 */
class Build extends Model
{
    /**
     * @var array
     */
    public static $sleepable = [];

    /**
     * @var string
     */
    protected $tableName = 'build';

    /**
     * @var string
     */
    protected $modelName = 'Build';

    /**
     * @var array
     */
    protected $data = [
        'id'              => null,
        'project_id'      => null,
        'commit_id'       => null,
        'status'          => null,
        'log'             => null,
        'branch'          => null,
        'created'         => null,
        'started'         => null,
        'finished'        => null,
        'committer_email' => null,
        'commit_message'  => null,
        'extra'           => null,
    ];

    /**
     * @var array
     */
    protected $getters = [
        // Direct property getters:
        'id'              => 'getId',
        'project_id'      => 'getProjectId',
        'commit_id'       => 'getCommitId',
        'status'          => 'getStatus',
        'log'             => 'getLog',
        'branch'          => 'getBranch',
        'created'         => 'getCreated',
        'started'         => 'getStarted',
        'finished'        => 'getFinished',
        'committer_email' => 'getCommitterEmail',
        'commit_message'  => 'getCommitMessage',
        'extra'           => 'getExtra',

        // Foreign key getters:
        'Project' => 'getProject',
    ];

    /**
     * @var array
     */
    protected $setters = [
        // Direct property setters:
        'id'              => 'setId',
        'project_id'      => 'setProjectId',
        'commit_id'       => 'setCommitId',
        'status'          => 'setStatus',
        'log'             => 'setLog',
        'branch'          => 'setBranch',
        'created'         => 'setCreated',
        'started'         => 'setStarted',
        'finished'        => 'setFinished',
        'committer_email' => 'setCommitterEmail',
        'commit_message'  => 'setCommitMessage',
        'extra'           => 'setExtra',

        // Foreign key setters:
        'Project' => 'setProject',
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
        'project_id' => [
            'type'    => 'int',
            'length'  => 11,
            'default' => null,
        ],
        'commit_id' => [
            'type'    => 'varchar',
            'length'  => 50,
            'default' => null,
        ],
        'status' => [
            'type'    => 'int',
            'length'  => 11,
            'default' => null,
        ],
        'log' => [
            'type'     => 'mediumtext',
            'nullable' => true,
            'default'  => null,
        ],
        'branch' => [
            'type'    => 'varchar',
            'length'  => 250,
            'default' => 'master',
        ],
        'created' => [
            'type'     => 'datetime',
            'nullable' => true,
            'default'  => null,
        ],
        'started' => [
            'type'     => 'datetime',
            'nullable' => true,
            'default'  => null,
        ],
        'finished' => [
            'type'     => 'datetime',
            'nullable' => true,
            'default'  => null,
        ],
        'committer_email' => [
            'type'     => 'varchar',
            'length'   => 512,
            'nullable' => true,
            'default'  => null,
        ],
        'commit_message' => [
            'type'     => 'text',
            'nullable' => true,
            'default'  => null,
        ],
        'extra' => [
            'type'     => 'text',
            'nullable' => true,
            'default'  => null,
        ],
    ];

    /**
     * @var array
     */
    public $indexes = [
        'PRIMARY'    => ['unique' => true, 'columns' => 'id'],
        'project_id' => ['columns' => 'project_id'],
        'idx_status' => ['columns' => 'status'],
    ];

    /**
     * @var array
     */
    public $foreignKeys = [
        'build_ibfk_1' => [
            'local_col' => 'project_id',
            'update'    => 'CASCADE',
            'delete'    => 'CASCADE',
            'table'     => 'project',
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
     * Get the value of ProjectId / project_id.
     *
     * @return int
     */
    public function getProjectId()
    {
        $rtn = $this->data['project_id'];

        return $rtn;
    }

    /**
     * Get the value of CommitId / commit_id.
     *
     * @return string
     */
    public function getCommitId()
    {
        $rtn = $this->data['commit_id'];

        return $rtn;
    }

    /**
     * Get the value of Status / status.
     *
     * @return int
     */
    public function getStatus()
    {
        $rtn = $this->data['status'];

        return $rtn;
    }

    /**
     * Get the value of Log / log.
     *
     * @return string
     */
    public function getLog()
    {
        $rtn = $this->data['log'];

        return $rtn;
    }

    /**
     * Get the value of Branch / branch.
     *
     * @return string
     */
    public function getBranch()
    {
        $rtn = $this->data['branch'];

        return $rtn;
    }

    /**
     * Get the value of Created / created.
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        $rtn = $this->data['created'];

        if (!empty($rtn)) {
            $rtn = new \DateTime($rtn);
        }

        return $rtn;
    }

    /**
     * Get the value of Started / started.
     *
     * @return \DateTime
     */
    public function getStarted()
    {
        $rtn = $this->data['started'];

        if (!empty($rtn)) {
            $rtn = new \DateTime($rtn);
        }

        return $rtn;
    }

    /**
     * Get the value of Finished / finished.
     *
     * @return \DateTime
     */
    public function getFinished()
    {
        $rtn = $this->data['finished'];

        if (!empty($rtn)) {
            $rtn = new \DateTime($rtn);
        }

        return $rtn;
    }

    /**
     * Get the value of CommitterEmail / committer_email.
     *
     * @return string
     */
    public function getCommitterEmail()
    {
        $rtn = $this->data['committer_email'];

        return $rtn;
    }

    /**
     * Set the value of Id / id. Must not be null.
     *
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
     * Set the value of ProjectId / project_id. Must not be null.
     *
     * @param $value int
     */
    public function setProjectId($value)
    {
        $this->validateNotNull('ProjectId', $value);
        $this->validateInt('ProjectId', $value);

        if ($this->data['project_id'] === $value) {
            return;
        }

        $this->data['project_id'] = $value;

        $this->setModified('project_id');
    }

    /**
     * Set the value of CommitId / commit_id. Must not be null.
     *
     * @param $value string
     */
    public function setCommitId($value)
    {
        $this->validateNotNull('CommitId', $value);
        $this->validateString('CommitId', $value);

        if ($this->data['commit_id'] === $value) {
            return;
        }

        $this->data['commit_id'] = $value;

        $this->setModified('commit_id');
    }

    /**
     * Set the value of Status / status. Must not be null.
     *
     * @param $value int
     */
    public function setStatus($value)
    {
        $this->validateNotNull('Status', $value);
        $this->validateInt('Status', $value);

        if ($this->data['status'] === $value) {
            return;
        }

        $this->data['status'] = $value;

        $this->setModified('status');
    }

    /**
     * Set the value of Log / log.
     *
     * @param $value string
     */
    public function setLog($value)
    {
        $this->validateString('Log', $value);

        if ($this->data['log'] === $value) {
            return;
        }

        $this->data['log'] = $value;

        $this->setModified('log');
    }

    /**
     * Set the value of Branch / branch. Must not be null.
     *
     * @param $value string
     */
    public function setBranch($value)
    {
        $this->validateNotNull('Branch', $value);
        $this->validateString('Branch', $value);

        if ($this->data['branch'] === $value) {
            return;
        }

        $this->data['branch'] = $value;

        $this->setModified('branch');
    }

    /**
     * Set the value of Created / created.
     *
     * @param $value \DateTime
     */
    public function setCreated($value)
    {
        $this->validateDate('Created', $value);

        if ($this->data['created'] === $value) {
            return;
        }

        $this->data['created'] = $value;

        $this->setModified('created');
    }

    /**
     * Set the value of Started / started.
     *
     * @param $value \DateTime
     */
    public function setStarted($value)
    {
        $this->validateDate('Started', $value);

        if ($this->data['started'] === $value) {
            return;
        }

        $this->data['started'] = $value;

        $this->setModified('started');
    }

    /**
     * Set the value of Finished / finished.
     *
     * @param $value \DateTime
     */
    public function setFinished($value)
    {
        $this->validateDate('Finished', $value);

        if ($this->data['finished'] === $value) {
            return;
        }

        $this->data['finished'] = $value;

        $this->setModified('finished');
    }

    /**
     * Set the value of CommitterEmail / committer_email.
     *
     * @param $value string
     */
    public function setCommitterEmail($value)
    {
        $this->validateString('CommitterEmail', $value);

        if ($this->data['committer_email'] === $value) {
            return;
        }

        $this->data['committer_email'] = $value;

        $this->setModified('committer_email');
    }

    /**
     * Set the value of CommitMessage / commit_message.
     *
     * @param $value string
     */
    public function setCommitMessage($value)
    {
        $this->validateString('CommitMessage', $value);

        if ($this->data['commit_message'] === $value) {
            return;
        }

        $this->data['commit_message'] = $value;

        $this->setModified('commit_message');
    }

    /**
     * Set the value of Extra / extra.
     *
     * @param $value string
     */
    public function setExtra($value)
    {
        $this->validateString('Extra', $value);

        if ($this->data['extra'] === $value) {
            return;
        }

        $this->data['extra'] = $value;

        $this->setModified('extra');
    }

    /**
     * Get the Project model for this Build by Id.
     *
     * @return \PHPCensor\Model\Project
     */
    public function getProject()
    {
        $key = $this->getProjectId();

        if (empty($key)) {
            return null;
        }

        return Factory::getStore('Project', 'PHPCensor')->getById($key);
    }

    /**
     * Set Project - Accepts an ID, an array representing a Project or a Project model.
     *
     * @param $value mixed
     */
    public function setProject($value)
    {
        // Is this an instance of Project?
        if ($value instanceof Project) {
            return $this->setProjectObject($value);
        }

        // Is this an array representing a Project item?
        if (is_array($value) && !empty($value['id'])) {
            return $this->setProjectId($value['id']);
        }

        // Is this a scalar value representing the ID of this foreign key?
        return $this->setProjectId($value);
    }

    /**
     * Set Project - Accepts a Project model.
     *
     * @param $value Project
     */
    public function setProjectObject(Project $value)
    {
        return $this->setProjectId($value->getId());
    }

    /**
     * Get BuildError models by BuildId for this Build.
     *
     * @return \PHPCensor\Model\BuildError[]
     */
    public function getBuildBuildErrors()
    {
        return Factory::getStore('BuildError', 'PHPCensor')->getByBuildId($this->getId());
    }

    /**
     * Get BuildMeta models by BuildId for this Build.
     *
     * @return \PHPCensor\Model\BuildMeta[]
     */
    public function getBuildBuildMetas()
    {
        return Factory::getStore('BuildMeta', 'PHPCensor')->getByBuildId($this->getId());
    }

    const STATUS_PENDING = 0;
    const STATUS_RUNNING = 1;
    const STATUS_SUCCESS = 2;
    const STATUS_FAILED  = 3;

    public $currentBuildPath;

    /**
    * Get link to commit from another source (i.e. Github)
    */
    public function getCommitLink()
    {
        return '#';
    }

    /**
    * Get link to branch from another source (i.e. Github)
    */
    public function getBranchLink()
    {
        return '#';
    }

    /**
     * Return a template to use to generate a link to a specific file.
     * 
     * @return null
     */
    public function getFileLinkTemplate()
    {
        return null;
    }

    /**
    * Send status updates to any relevant third parties (i.e. Github)
    */
    public function sendStatusPostback()
    {
        return;
    }

    /**
     * @return string
     */
    public function getProjectTitle()
    {
        $project = $this->getProject();
        return $project ? $project->getTitle() : "";
    }

    /**
     * Store build metadata
     */
    public function storeMeta($key, $value)
    {
        $value = json_encode($value);
        Factory::getStore('Build')->setMeta($this->getProjectId(), $this->getId(), $key, $value);
    }

    /**
     * Is this build successful?
     */
    public function isSuccessful()
    {
        return ($this->getStatus() === self::STATUS_SUCCESS);
    }

    /**
     * @param Builder $builder
     * @param string  $buildPath
     *
     * @return bool
     */
    protected function handleConfig(Builder $builder, $buildPath)
    {
        $build_config = $this->getProject()->getBuildConfig();

        if (empty($build_config)) {
            if (file_exists($buildPath . '/.php-censor.yml')) {
                $build_config = file_get_contents($buildPath . '/.php-censor.yml');
            } elseif (file_exists($buildPath . '/.phpci.yml')) {
                $build_config = file_get_contents($buildPath . '/.phpci.yml');
            } elseif (file_exists($buildPath . '/phpci.yml')) {
                $build_config = file_get_contents($buildPath . '/phpci.yml');
            } else {
                $build_config = $this->getZeroConfigPlugins($builder);
            }
        }

        // for YAML configs from files/DB
        if (is_string($build_config)) {
            $yamlParser   = new YamlParser();
            $build_config = $yamlParser->parse($build_config);
        }

        $builder->setConfigArray($build_config);

        return true;
    }

    /**
     * Get an array of plugins to run if there's no .php-censor.yml file.
     * @param Builder $builder
     * @return array
     */
    protected function getZeroConfigPlugins(Builder $builder)
    {
        $pluginDir = SRC_DIR . 'Plugin' . DIRECTORY_SEPARATOR;
        $dir = new \DirectoryIterator($pluginDir);

        $config = [
            'build_settings' => [
                'ignore' => [
                    'vendor',
                ]
            ]
        ];

        foreach ($dir as $item) {
            if ($item->isDot()) {
                continue;
            }

            if (!$item->isFile()) {
                continue;
            }

            if ($item->getExtension() != 'php') {
                continue;
            }

            $className = '\PHPCensor\Plugin\\'.$item->getBasename('.php');

            $reflectedPlugin = new \ReflectionClass($className);

            if (!$reflectedPlugin->implementsInterface('\PHPCensor\ZeroConfigPluginInterface')) {
                continue;
            }

            foreach (['setup', 'test', 'complete', 'success', 'failure'] as $stage) {
                if ($className::canExecute($stage, $builder, $this)) {
                    $config[$stage][$className::pluginName()] = [
                        'zero_config' => true
                    ];
                }
            }
        }

        return $config;
    }

    /**
     * Return a value from the build's "extra" JSON array.
     * @param null $key
     * @return mixed|null|string
     */
    public function getExtra($key = null)
    {
        $data = json_decode($this->data['extra'], true);

        if (is_null($key)) {
            $rtn = $data;
        } elseif (isset($data[$key])) {
            $rtn = $data[$key];
        } else {
            $rtn = null;
        }

        return $rtn;
    }

    /**
     * Returns the commit message for this build.
     * @return string
     */
    public function getCommitMessage()
    {
        $rtn = htmlspecialchars($this->data['commit_message']);

        return $rtn;
    }

    /**
     * Allows specific build types (e.g. Github) to report violations back to their respective services.
     * @param Builder $builder
     * @param $plugin
     * @param $message
     * @param int $severity
     * @param null $file
     * @param null $lineStart
     * @param null $lineEnd
     * @return BuildError
     */
    public function reportError(
        Builder $builder,
        $plugin,
        $message,
        $severity = BuildError::SEVERITY_NORMAL,
        $file = null,
        $lineStart = null,
        $lineEnd = null
    ) {
        unset($builder);

        $error = new BuildError();
        $error->setBuild($this);
        $error->setCreatedDate(new \DateTime());
        $error->setPlugin($plugin);
        $error->setMessage($message);
        $error->setSeverity($severity);
        $error->setFile($file);
        $error->setLineStart($lineStart);
        $error->setLineEnd($lineEnd);

        return Factory::getStore('BuildError')->save($error);
    }

    /**
     * Return the path to run this build into.
     *
     * @return string|null
     */
    public function getBuildPath()
    {
        if (!$this->getId()) {
            return null;
        }

        if (empty($this->currentBuildPath)) {
            $buildDirectory         = $this->getId() . '_' . substr(md5(microtime(true)), 0, 5);
            $this->currentBuildPath = RUNTIME_DIR . 'builds' . DIRECTORY_SEPARATOR . $buildDirectory . DIRECTORY_SEPARATOR;
        }

        return $this->currentBuildPath;
    }

    /**
     * Removes the build directory.
     */
    public function removeBuildDirectory()
    {
        // Get the path and remove the trailing slash as this may prompt PHP
        // to see this as a directory even if it's a link.
        $buildPath = rtrim($this->getBuildPath(), '/');

        if (!$buildPath || !is_dir($buildPath)) {
            return;
        }

        if (is_link($buildPath)) {
            // Remove the symlink without using recursive.
            exec(sprintf('rm "%s"', $buildPath));
        } else {
            exec(sprintf('rm -Rf "%s"', $buildPath));
        }
    }

    /**
     * Get the number of seconds a build has been running for.
     * @return int
     */
    public function getDuration()
    {
        $start = $this->getStarted();

        if (empty($start)) {
            return 0;
        }

        $end = $this->getFinished();

        if (empty($end)) {
            $end = new \DateTime();
        }

        return $end->getTimestamp() - $start->getTimestamp();
    }

    /**
     * Create a working copy by cloning, copying, or similar.
     * 
     * @param Builder $builder
     * @param string  $buildPath
     * 
     * @return boolean
     */
    public function createWorkingCopy(Builder $builder, $buildPath)
    {
        return false;
    }
}
