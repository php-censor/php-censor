<?php

namespace PHPCensor\Service;

use b8\Config;
use Pheanstalk\Pheanstalk;
use Pheanstalk\PheanstalkInterface;
use PHPCensor\BuildFactory;
use PHPCensor\Model\Build;
use PHPCensor\Model\Project;
use PHPCensor\Store\BuildStore;

/**
 * The build service handles the creation, duplication and deletion of builds.
 */
class BuildService
{
    /**
     * @var \PHPCensor\Store\BuildStore
     */
    protected $buildStore;

    /**
     * @var boolean
     */
    public $queueError = false;

    /**
     * @param BuildStore $buildStore
     */
    public function __construct(BuildStore $buildStore)
    {
        $this->buildStore = $buildStore;
    }

    /**
     * @param Project     $project
     * @param string      $environment
     * @param string      $commitId
     * @param string|null $branch
     * @param string|null $tag
     * @param string|null $committerEmail
     * @param string|null $commitMessage
     * @param integer     $source
     * @param integer     $userId
     * @param string|null $extra
     *
     * @return \PHPCensor\Model\Build
     */
    public function createBuild(
        Project $project,
        $environment,
        $commitId = '',
        $branch = null,
        $tag = null,
        $committerEmail = null,
        $commitMessage = null,
        $source = Build::SOURCE_UNKNOWN,
        $userId = 0,
        $extra = null
    ) {
        $build = new Build();
        $build->setCreateDate(new \DateTime());
        $build->setProject($project);
        $build->setStatus(Build::STATUS_PENDING);
        $build->setEnvironment($environment);

        $branches = $project->getBranchesByEnvironment($environment);
        $build->setExtraValue('branches', $branches);

        $build->setSource($source);
        $build->setUserId($userId);
        $build->setCommitId((string)$commitId);

        if (!empty($branch)) {
            $build->setBranch($branch);
        } else {
            $build->setBranch($project->getBranch());
        }

        if (!empty($tag)) {
            $build->setTag($tag);
        }

        if (!empty($committerEmail)) {
            $build->setCommitterEmail($committerEmail);
        }

        if (!empty($commitMessage)) {
            $build->setCommitMessage($commitMessage);
        }

        if (!is_null($extra)) {
            $build->setExtraValues($extra);
        }

        /** @var Build $build */
        $build   = $this->buildStore->save($build);
        $buildId = $build->getId();

        if (!empty($buildId)) {
            $build = BuildFactory::getBuild($build);
            $build->sendStatusPostback();
            $this->addBuildToQueue($build);
        }

        return $build;
    }

    /**
     * @param Build $copyFrom
     *
     * @return \PHPCensor\Model\Build
     */
    public function createDuplicateBuild(Build $copyFrom)
    {
        $data = $copyFrom->getDataArray();

        // Clean up unwanted properties from the original build:
        unset($data['id']);
        unset($data['status']);
        unset($data['log']);
        unset($data['start_date']);
        unset($data['finish_date']);

        $build = new Build();
        $build->setValues($data);
        $build->setCreateDate(new \DateTime());
        $build->setStatus(Build::STATUS_PENDING);

        /** @var Build $build */
        $build = $this->buildStore->save($build);

        $buildId = $build->getId();

        if (!empty($buildId)) {
            $build = BuildFactory::getBuild($build);
            $build->sendStatusPostback();
            $this->addBuildToQueue($build);
        }

        return $build;
    }

    /**
     * Delete a given build.
     *
     * @param Build $build
     *
     * @return boolean
     */
    public function deleteBuild(Build $build)
    {
        $build->removeBuildDirectory();
        return $this->buildStore->delete($build);
    }

    /**
     * Takes a build and puts it into the queue to be run (if using a queue)
     * @param Build $build
     */
    public function addBuildToQueue(Build $build)
    {
        $buildId = $build->getId();

        if (empty($buildId)) {
            return;
        }

        $config   = Config::getInstance();
        $settings = $config->get('php-censor.queue', []);

        if (!empty($settings['host']) && !empty($settings['name'])) {
            try {
                $jobData = [
                    'type'     => 'php-censor.build',
                    'build_id' => $build->getId(),
                ];

                $pheanstalk = new Pheanstalk($settings['host']);
                $pheanstalk->useTube($settings['name']);
                $pheanstalk->put(
                    json_encode($jobData),
                    PheanstalkInterface::DEFAULT_PRIORITY,
                    PheanstalkInterface::DEFAULT_DELAY,
                    $config->get('php-censor.queue.lifetime', 600)
                );
            } catch (\Exception $ex) {
                $this->queueError = true;
            }
        }
    }
}
