<?php

namespace PHPCensor\Model;

use PHPCensor\Builder;
use PHPCensor\Store\Factory;
use PHPCensor\Store\ProjectStore;
use PHPCensor\Store\BuildErrorStore;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Parser as YamlParser;
use PHPCensor\Model\Base\Build as BaseBuild;

/**
 * @author Dan Cryer <dan@block8.co.uk>
 */
class Build extends BaseBuild
{
    const STAGE_SETUP    = 'setup';
    const STAGE_TEST     = 'test';
    const STAGE_DEPLOY   = 'deploy';
    const STAGE_COMPLETE = 'complete';
    const STAGE_SUCCESS  = 'success';
    const STAGE_FAILURE  = 'failure';
    const STAGE_FIXED    = 'fixed';
    const STAGE_BROKEN   = 'broken';

    /**
     * @var array
     */
    public static $pullRequestSources = [
        self::SOURCE_WEBHOOK_PULL_REQUEST_CREATED,
        self::SOURCE_WEBHOOK_PULL_REQUEST_UPDATED,
        self::SOURCE_WEBHOOK_PULL_REQUEST_APPROVED,
        self::SOURCE_WEBHOOK_PULL_REQUEST_MERGED,
    ];

    /**
     * @var array
     */
    public static $webhookSources = [
        self::SOURCE_WEBHOOK_PUSH,
        self::SOURCE_WEBHOOK_PULL_REQUEST_CREATED,
        self::SOURCE_WEBHOOK_PULL_REQUEST_UPDATED,
        self::SOURCE_WEBHOOK_PULL_REQUEST_APPROVED,
        self::SOURCE_WEBHOOK_PULL_REQUEST_MERGED,
    ];

    /**
     * @var integer
     */
    protected $newErrorsCount = null;

    /**
     * @var string
     */
    protected $buildDirectory;

    /**
     * @var string
     */
    protected $buildBranchDirectory;

    /**
     * @return Project|null
     */
    public function getProject()
    {
        $projectId = $this->getProjectId();
        if (!$projectId) {
            return null;
        }

        /** @var ProjectStore $projectStore */
        $projectStore = Factory::getStore('Project');

        return $projectStore->getById($projectId);
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function addExtraValue($name, $value)
    {
        $extra = json_decode($this->data['extra'], true);
        if ($extra === false) {
            $extra = [];
        }
        $extra[$name] = $value;
        $this->setExtra($extra);
    }

    /**
     * Set the value of status only if it synced with db. Must not be null.
     *
     * @param integer $value
     *
     * @return boolean
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
     * Get BuildError models by BuildId for this Build.
     *
     * @return \PHPCensor\Model\BuildError[]
     */
    public function getBuildBuildErrors()
    {
        return Factory::getStore('BuildError')->getByBuildId($this->getId());
    }

    /**
     * Get BuildMeta models by BuildId for this Build.
     *
     * @return \PHPCensor\Model\BuildMeta[]
     */
    public function getBuildBuildMetas()
    {
        return Factory::getStore('BuildMeta')->getByBuildId($this->getId());
    }

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
     * @return string|null
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
     *
     * @param string $key
     * @param string $value
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
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function handleConfigBeforeClone(Builder $builder)
    {
        $buildConfig = $this->getProject()->getBuildConfig();

        if ($buildConfig) {
            $yamlParser  = new YamlParser();
            $buildConfig = $yamlParser->parse($buildConfig);

            if ($buildConfig && is_array($buildConfig)) {
                $builder->setConfig($buildConfig);
            }
        }

        return true;
    }

    /**
     * @param Builder $builder
     * @param string  $buildPath
     *
     * @return bool
     *
     * @throws \Exception
     */
    protected function handleConfig(Builder $builder, $buildPath)
    {
        $yamlParser           = new YamlParser();
        $overwriteBuildConfig = $this->getProject()->getOverwriteBuildConfig();
        $buildConfig          = $builder->getConfig();

        $repositoryConfig = $this->getZeroConfigPlugins($builder);
        if (file_exists($buildPath . '/.php-censor.yml')) {
            $repositoryConfig = $yamlParser->parse(
                file_get_contents($buildPath . '/.php-censor.yml')
            );
        } elseif (file_exists($buildPath . '/.phpci.yml')) {
            $repositoryConfig = $yamlParser->parse(
                file_get_contents($buildPath . '/.phpci.yml')
            );
        } elseif (file_exists($buildPath . '/phpci.yml')) {
            $repositoryConfig = $yamlParser->parse(
                file_get_contents($buildPath . '/phpci.yml')
            );
        }

        if (isset($repositoryConfig['build_settings']['clone_depth'])) {
            $builder->logWarning(
                'Option "build_settings.clone_depth" supported only in additional DB project config.' .
                ' Please move this option to DB config from your in-repository config file (".php-censor.yml").'
            );
        }

        if (!$buildConfig) {
            $buildConfig = $repositoryConfig;
        } elseif ($buildConfig && !$overwriteBuildConfig) {
            $buildConfig = array_replace_recursive($repositoryConfig, $buildConfig);
        }

        $builder->setConfig($buildConfig);

        return true;
    }

    /**
     * Get an array of plugins to run if there's no .php-censor.yml file.
     *
     * @param Builder $builder
     *
     * @return array
     */
    protected function getZeroConfigPlugins(Builder $builder)
    {
        $pluginDir = SRC_DIR . 'Plugin/';
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
     * @return string|null
     */
    public function getBuildDirectory()
    {
        if (!$this->getId()) {
            return null;
        }

        $createDate = $this->getCreateDate();
        if (empty($this->buildDirectory)) {
            $this->buildDirectory = $this->getProjectId() . '/' . $this->getId() . '_' . substr(
                md5(($this->getId() . '_' . ($createDate ? $createDate->format('Y-m-d H:i:s') : null))
            ), 0, 8);
        }

        return $this->buildDirectory;
    }

    /**
     * @return string|null
     */
    public function getBuildBranchDirectory()
    {
        if (!$this->getId()) {
            return null;
        }

        $createDate = $this->getCreateDate();
        if (empty($this->buildBranchDirectory)) {
            $this->buildBranchDirectory = $this->getProjectId() . '/' . $this->getBranch() . '_' . substr(
                md5(($this->getBranch() . '_' . ($createDate ? $createDate->format('Y-m-d H:i:s') : null))
            ), 0, 8);
        }

        return $this->buildBranchDirectory;
    }

    /**
     * @return string|null
     */
    public function getBuildPath()
    {
        if (!$this->getId()) {
            return null;
        }

        return RUNTIME_DIR . 'builds/' . $this->getBuildDirectory() . '/';
    }

    /**
     * Removes the build directory.
     *
     * @param boolean $withArtifacts
     */
    public function removeBuildDirectory($withArtifacts = false)
    {
        // Get the path and remove the trailing slash as this may prompt PHP
        // to see this as a directory even if it's a link.
        $buildPath = rtrim($this->getBuildPath(), '/');

        if (!$buildPath || !is_dir($buildPath)) {
            return;
        }

        try {
            $fileSystem = new Filesystem();

            if (is_link($buildPath)) {
                // Remove the symlink without using recursive.
                exec(sprintf('rm "%s"', $buildPath));
            } else {
                $fileSystem->remove($buildPath);
            }

            if ($withArtifacts) {
                $buildDirectory = $this->getBuildDirectory();

                $fileSystem->remove(PUBLIC_DIR . 'artifacts/pdepend/' . $buildDirectory);
                $fileSystem->remove(PUBLIC_DIR . 'artifacts/phpunit/' . $buildDirectory);
            }
        } catch (\Exception $e) {

        }
    }

    /**
     * Get the number of seconds a build has been running for.
     *
     * @return integer
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

        $diff  = date_diff($start, $end);
        $parts = [];
        foreach (['y', 'm', 'd', 'h', 'i', 's'] as $timePart) {
            if ($diff->{$timePart} != 0) {
                $parts[] = $diff->{$timePart} . ($timePart == 'i' ? 'm' : $timePart);
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
     * @param string $cloneTo
     *
     * @return string
     */
    protected function writeSshKey($cloneTo)
    {
        $tempKeyFile = tempnam(sys_get_temp_dir(), 'key_');

        file_put_contents($tempKeyFile, $this->getProject()->getSshPrivateKey());

        return $tempKeyFile;
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
        $sshFlags = '-o CheckHostIP=no -o IdentitiesOnly=yes -o StrictHostKeyChecking=no -o PasswordAuthentication=no';

        // Write out the wrapper script for this build:
        $script = <<<OUT
#!/bin/sh
ssh {$sshFlags} -o IdentityFile={$keyFile} $*

OUT;
        $tempShFile = tempnam(sys_get_temp_dir(), 'sh_');

        file_put_contents($tempShFile, $script);
        shell_exec('chmod +x "' . $tempShFile . '"');

        return $tempShFile;
    }

    /**
     * @return string
     */
    public function getSourceHumanize()
    {
        switch ($this->getSource()) {
            case Build::SOURCE_WEBHOOK_PUSH:
                return 'source_webhook_push';
            case Build::SOURCE_WEBHOOK_PULL_REQUEST_CREATED:
                return 'source_webhook_pull_request_created';
            case Build::SOURCE_WEBHOOK_PULL_REQUEST_UPDATED:
                return 'source_webhook_pull_request_updated';
            case Build::SOURCE_WEBHOOK_PULL_REQUEST_APPROVED:
                return 'source_webhook_pull_request_approved';
            case Build::SOURCE_WEBHOOK_PULL_REQUEST_MERGED:
                return 'source_webhook_pull_request_merged';
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
