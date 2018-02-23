<?php

namespace PHPCensor\Model;

use PHPCensor\Builder;
use PHPCensor\Store\BuildErrorStore;
use Symfony\Component\Yaml\Parser as YamlParser;
use PHPCensor\Model;
use b8\Store\Factory;

/**
 * @author Dan Cryer <dan@block8.co.uk>
 */
class Build extends Model
{
    const STAGE_SETUP    = 'setup';
    const STAGE_TEST     = 'test';
    const STAGE_DEPLOY   = 'deploy';
    const STAGE_COMPLETE = 'complete';
    const STAGE_SUCCESS  = 'success';
    const STAGE_FAILURE  = 'failure';
    const STAGE_FIXED    = 'fixed';
    const STAGE_BROKEN   = 'broken';

    const STATUS_PENDING = 0;
    const STATUS_RUNNING = 1;
    const STATUS_SUCCESS = 2;
    const STATUS_FAILED  = 3;

    const SOURCE_UNKNOWN              = 0;
    const SOURCE_MANUAL_WEB           = 1;
    const SOURCE_MANUAL_CONSOLE       = 2;
    const SOURCE_PERIODICAL           = 3;
    const SOURCE_WEBHOOK              = 4;
    const SOURCE_WEBHOOK_PULL_REQUEST = 5;

    /**
     * @var string
     */
    protected $tableName = 'build';

    /**
     * @var integer
     */
    protected $newErrorsCount = null;

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
        'tag'             => null,
        'create_date'     => null,
        'start_date'      => null,
        'finish_date'     => null,
        'committer_email' => null,
        'commit_message'  => null,
        'extra'           => null,
        'environment'     => null,
        'source'          => Build::SOURCE_UNKNOWN,
        'user_id'         => 0,
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
        'tag'             => 'getTag',
        'create_date'     => 'getCreateDate',
        'start_date'      => 'getStartDate',
        'finish_date'     => 'getFinishDate',
        'committer_email' => 'getCommitterEmail',
        'commit_message'  => 'getCommitMessage',
        'extra'           => 'getExtra',
        'environment'     => 'getEnvironment',
        'source'          => 'getSource',
        'user_id'         => 'getUserId',

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
        'setTag'          => 'setTag',
        'create_date'     => 'setCreateDate',
        'start_date'      => 'setStartDate',
        'finish_date'     => 'setFinishDate',
        'committer_email' => 'setCommitterEmail',
        'commit_message'  => 'setCommitMessage',
        'extra'           => 'setExtra',
        'environment'     => 'setEnvironment',
        'source'          => 'setSource',
        'user_id'         => 'setUserId',

        // Foreign key setters:
        'Project' => 'setProject',
    ];

    /**
     * @return integer
     */
    public function getId()
    {
        $rtn = $this->data['id'];

        return (integer)$rtn;
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
     * @return integer
     */
    public function getProjectId()
    {
        $rtn = $this->data['project_id'];

        return (integer)$rtn;
    }

    /**
     * @param $value int
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
    public function getCommitId()
    {
        $rtn = $this->data['commit_id'];

        return $rtn;
    }

    /**
     * @param $value string
     */
    public function setCommitId($value)
    {
        $this->validateNotNull('commit_id', $value);
        $this->validateString('commit_id', $value);

        if ($this->data['commit_id'] === $value) {
            return;
        }

        $this->data['commit_id'] = $value;

        $this->setModified('commit_id');
    }

    /**
     * @return integer
     */
    public function getStatus()
    {
        $rtn = $this->data['status'];

        return (integer)$rtn;
    }

    /**
     * @param $value int
     */
    public function setStatus($value)
    {
        $this->validateNotNull('status', $value);
        $this->validateInt('status', $value);

        if ($this->data['status'] === $value) {
            return;
        }

        $this->data['status'] = $value;

        $this->setModified('status');
    }

    /**
     * @return string
     */
    public function getLog()
    {
        $rtn = $this->data['log'];

        return $rtn;
    }

    /**
     * @param $value string
     */
    public function setLog($value)
    {
        $this->validateString('log', $value);

        if ($this->data['log'] === $value) {
            return;
        }

        $this->data['log'] = $value;

        $this->setModified('log');
    }

    /**
     * @return string
     */
    public function getBranch()
    {
        $rtn = $this->data['branch'];

        return $rtn;
    }

    /**
     * @param $value string
     */
    public function setBranch($value)
    {
        $this->validateNotNull('branch', $value);
        $this->validateString('branch', $value);

        if ($this->data['branch'] === $value) {
            return;
        }

        $this->data['branch'] = $value;

        $this->setModified('branch');
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
     * @param $value \DateTime
     */
    public function setCreateDate($value)
    {
        $this->validateDate('create_date', $value);

        if ($this->data['create_date'] === $value) {
            return;
        }

        $this->data['create_date'] = $value;

        $this->setModified('create_date');
    }

    /**
     * @return \DateTime
     */
    public function getStartDate()
    {
        $rtn = $this->data['start_date'];

        if (!empty($rtn)) {
            $rtn = new \DateTime($rtn);
        }

        return $rtn;
    }

    /**
     * @param $value \DateTime
     */
    public function setStartDate($value)
    {
        $this->validateDate('start_date', $value);

        if ($this->data['start_date'] === $value) {
            return;
        }

        $this->data['start_date'] = $value;

        $this->setModified('start_date');
    }

    /**
     * @return \DateTime
     */
    public function getFinishDate()
    {
        $rtn = $this->data['finish_date'];

        if (!empty($rtn)) {
            $rtn = new \DateTime($rtn);
        }

        return $rtn;
    }

    /**
     * @param $value \DateTime
     */
    public function setFinishDate($value)
    {
        $this->validateDate('finish_date', $value);

        if ($this->data['finish_date'] === $value) {
            return;
        }

        $this->data['finish_date'] = $value;

        $this->setModified('finish_date');
    }

    /**
     * @return string
     */
    public function getCommitterEmail()
    {
        $rtn = $this->data['committer_email'];

        return $rtn;
    }

    /**
     * @param $value string
     */
    public function setCommitterEmail($value)
    {
        $this->validateString('committer_email', $value);

        if ($this->data['committer_email'] === $value) {
            return;
        }

        $this->data['committer_email'] = $value;

        $this->setModified('committer_email');
    }

    /**
     * @return string
     */
    public function getCommitMessage()
    {
        $rtn = htmlspecialchars($this->data['commit_message']);

        return $rtn;
    }

    /**
     * @param $value string
     */
    public function setCommitMessage($value)
    {
        $this->validateString('commit_message', $value);

        if ($this->data['commit_message'] === $value) {
            return;
        }

        $this->data['commit_message'] = $value;

        $this->setModified('commit_message');
    }

    /**
     * @return string
     */
    public function getTag()
    {
        $rtn = $this->data['tag'];

        return $rtn;
    }

    /**
     * @param $value string
     */
    public function setTag($value)
    {
        $this->validateString('tag', $value);

        if ($this->data['tag'] === $value) {
            return;
        }

        $this->data['tag'] = $value;

        $this->setModified('tag');
    }

    /**
     * @return string
     */
    public function getSource()
    {
        $rtn = $this->data['source'];

        return (integer)$rtn;
    }

    /**
     * @param $value integer
     */
    public function setSource($value)
    {
        $this->validateInt('source', $value);

        if ($this->data['source'] === $value) {
            return;
        }

        $this->data['source'] = $value;

        $this->setModified('source');
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        $rtn = $this->data['user_id'];

        return (integer)$rtn;
    }

    /**
     * @param $value integer
     */
    public function setUserId($value)
    {
        $this->validateNotNull('user_id', $value);
        $this->validateInt('user_id', $value);

        if ($this->data['user_id'] === $value) {
            return;
        }

        $this->data['user_id'] = $value;

        $this->setModified('user_id');
    }

    /**
     * @return string
     */
    public function getEnvironment()
    {
        $rtn = $this->data['environment'];

        return $rtn;
    }

    /**
     * @param $value string
     */
    public function setEnvironment($value)
    {
        $this->validateString('environment', $value);

        if ($this->data['environment'] === $value) {
            return;
        }

        $this->data['environment'] = $value;

        $this->setModified('environment');
    }

    /**
     * Set the value of status only if it synced with db. Must not be null.
     *
     * @param $value int
     * @return bool
     */
    public function setStatusSync($value)
    {
        $this->validateNotNull('status', $value);
        $this->validateInt('status', $value);

        if ($this->data['status'] !== $value) {
            $store = Factory::getStore('Build');
            if ($store->updateStatusSync($this, $value)) {
                $this->data['status'] = $value;
                return true;
            }
        }
        return false;
    }

    /**
     * Return a value from the build's "extra" JSON array.
     *
     * @param null $key
     *
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
     * @param $value string
     */
    public function setExtra($value)
    {
        $this->validateString('extra', $value);

        if ($this->data['extra'] === $value) {
            return;
        }

        $this->data['extra'] = $value;

        $this->setModified('extra');
    }

    /**
     * Set the value of extra.
     *
     * @param $name string
     * @param $value mixed
     */
    public function setExtraValue($name, $value)
    {
        $extra = json_decode($this->data['extra'], true);
        if ($extra === false) {
            $extra = [];
        }
        $extra[$name] = $value;
        $this->setExtra(json_encode($extra));
    }

    /**
     * Set the values of extra.
     *
     * @param $values mixed
     */
    public function setExtraValues($values)
    {
        $extra = json_decode($this->data['extra'], true);
        if ($extra === false) {
            $extra = [];
        }
        $extra = array_replace($extra, $values);
        $this->setExtra(json_encode($extra));
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
     * Get remote branch (from pull request) from another source (i.e. Github)
     */
    public function getRemoteBranch()
    {
        return $this->getExtra('remote_branch');
    }

    /**
     * Get link to remote branch (from pull request) from another source (i.e. Github)
     */
    public function getRemoteBranchLink()
    {
        return '#';
    }

    /**
     * Get link to tag from another source (i.e. Github)
     */
    public function getTagLink()
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
        return false;
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
        Factory::getStore('Build')->setMeta($this->getId(), $key, $value);
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

            foreach ([Build::STAGE_SETUP, Build::STAGE_TEST] as $stage) {
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
     * Allows specific build types (e.g. Github) to report violations back to their respective services.
     *
     * @param Builder $builder
     * @param string  $plugin
     * @param string  $message
     * @param integer $severity
     * @param string  $file
     * @param integer $lineStart
     * @param integer $lineEnd
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
        $writer = $builder->getBuildErrorWriter();
        $writer->write(
            $plugin,
            $message,
            $severity,
            $file,
            $lineStart,
            $lineEnd
        );
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
            $this->currentBuildPath =
                RUNTIME_DIR .
                'builds' .
                DIRECTORY_SEPARATOR .
                $buildDirectory .
                DIRECTORY_SEPARATOR;
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
     *
     * @return int
     */
    public function getDuration()
    {
        $start = $this->getStartDate();

        if (empty($start)) {
            return 0;
        }

        $end = $this->getFinishDate();

        if (empty($end)) {
            $end = new \DateTime();
        }

        return $end->getTimestamp() - $start->getTimestamp();
    }

    /**
     * get time a build has been running for in hour/minute/seconds format (e.g. 1h 21m 45s)
     *
     * @return string
     */
    public function getPrettyDuration()
    {
        $start = $this->getStartDate();
        if (!$start) {
            $start = new \DateTime();
        }
        $end = $this->getFinishDate();
        if (!$end) {
            $end = new \DateTime();
        }

        $diff = date_diff($start, $end);
        $parts = [];
        foreach (['y', 'm', 'd', 'h', 'i', 's'] as $time_part) {
            if ($diff->{$time_part} != 0) {
                $parts[] = $diff->{$time_part} . ($time_part == 'i' ? 'm' : $time_part);
            }
        }

        return implode(" ", $parts);
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

    /**
     * Create an SSH key file on disk for this build.
     *
     * @param  string $cloneTo
     *
     * @return string
     */
    protected function writeSshKey($cloneTo)
    {
        $keyPath = dirname($cloneTo . '/temp');
        $keyFile = $keyPath . '.key';

        file_put_contents($keyFile, $this->getProject()->getSshPrivateKey());
        chmod($keyFile, 0600);

        return $keyFile;
    }

    /**
     * Create an SSH wrapper script for Svn to use, to disable host key checking, etc.
     *
     * @param string $cloneTo
     * @param string $keyFile
     *
     * @return string
     */
    protected function writeSshWrapper($cloneTo, $keyFile)
    {
        $path        = dirname($cloneTo . '/temp');
        $wrapperFile = $path . '.sh';

        $sshFlags = '-o CheckHostIP=no -o IdentitiesOnly=yes -o StrictHostKeyChecking=no -o PasswordAuthentication=no';

        // Write out the wrapper script for this build:
        $script = <<<OUT
#!/bin/sh
ssh {$sshFlags} -o IdentityFile={$keyFile} $*

OUT;

        file_put_contents($wrapperFile, $script);
        shell_exec('chmod +x "' . $wrapperFile . '"');

        return $wrapperFile;
    }

    /**
     * @return string
     */
    public function getSourceHumanize()
    {
        switch ($this->getSource()) {
            case Build::SOURCE_WEBHOOK:
                return 'source_webhook';
            case Build::SOURCE_WEBHOOK_PULL_REQUEST:
                return 'source_webhook_pull_request';
            case Build::SOURCE_MANUAL_WEB:
                return 'source_manual_web';
            case Build::SOURCE_MANUAL_CONSOLE:
                return 'source_manual_console';
            case Build::SOURCE_PERIODICAL:
                return 'source_periodical';
            case Build::SOURCE_UNKNOWN:
            default:
                return 'source_unknown';
        }
    }

    /**
     * @return integer
     */
    public function getNewErrorsCount()
    {
        if (null === $this->newErrorsCount) {
            /** @var BuildErrorStore $store */
            $store = Factory::getStore('BuildError');

            $this->newErrorsCount = $store->getNewErrorsCount($this->getId());
        }

        return $this->newErrorsCount;
    }
}
