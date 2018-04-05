<?php

namespace PHPCensor\Service;

use PHPCensor\Config;
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
     * @param array|null  $extra
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
        $build->setProjectId($project->getId());
        $build->setStatus(Build::STATUS_PENDING);
        $build->setEnvironment($environment);

        if (!is_null($extra)) {
            $build->setExtra($extra);
        }

        $branches = $project->getBranchesByEnvironment($environment);
        $build->addExtraValue('branches', $branches);

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
        $build = new Build();
        $build->setProjectId($copyFrom->getProjectId());
        $build->setCommitId($copyFrom->getCommitId());
        $build->setBranch($copyFrom->getBranch());
        $build->setTag($copyFrom->getTag());
        $build->setCommitterEmail($copyFrom->getCommitterEmail());
        $build->setCommitMessage($copyFrom->getCommitMessage());
        $build->setExtra($copyFrom->getExtra());
        $build->setEnvironment($copyFrom->getEnvironment());
        $build->setSource($copyFrom->getSource());
        $build->setUserId($copyFrom->getUserId());
        $build->setCreateDate(new \DateTime());
        $build->setStatus(Build::STATUS_PENDING);

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
     * Delete a given build.
     *
     * @param Build $build
     *
     * @return boolean
     */
    public function deleteBuild(Build $build)
    {
        $build->removeBuildDirectory(true);

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

    /**
     * A similar function as \PHPCensor\Controller\BuildController::formatBuilds() 
     * but uses pure object to be used for rendering UI notifications 
     * via web notification API.
     * @param  array $builds
     * @return array  Formatted builds
     * @see \PHPCensor\Controller\WidgetLastBuildsController::webNotificationUpdate().
     */
    public static function formatWebNotificationBuilds($builds)
    {
        $rtn = ['count' => $builds['count'], 'items' => []];

        foreach ($builds['items'] as $buildItem) {
            $build = self::formatWebNotificationBuild($buildItem);            
            $rtn['items'][$buildItem->getId()]['build'] = $build;
        }

        ksort($rtn['items']);
        return $rtn;
    }

    /**
     * Provides structured keys for web notification.
     * @param  Build  $build
     * @return array
     */
    public static function formatWebNotificationBuild($build)
    {
        if(empty($build) || is_null($build)) return [];
        $status = $build->getStatus();
        $statusDetails = '';

        /*
            BUG: Lang::out() automatically renders the values for
            either 'created_x' or 'started_x' instead of just 
            returning them.
        */
        if($status === Build::STATUS_PENDING)
            $statusDetails = 'Created: ' . $build->getCreateDate()->format('H:i');
        else if($status === Build::STATUS_RUNNING)
            $statusDetails = 'Started: ' . $build->getStartDate()->format('H:i');
        
        return [
            'branch'          => $build->getBranch(),
            'url'             => APP_URL . 'build/view/' . $build->getId(),
            'committer_email' => $build->getCommitterEmail(),
            'img_src'         => 'https://www.gravatar.com/avatar/' . md5($build->getCommitterEmail()) . '?d=mm&s=40',
            'project_title'   => $build->getProject()->getTitle(),
            'status'          => $status,
            'status_details'  => $statusDetails
        ];
    }
}
