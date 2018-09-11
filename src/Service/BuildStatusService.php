<?php

namespace PHPCensor\Service;

use PHPCensor\Model\Project;
use PHPCensor\Model\Build;

/**
 * Class BuildStatusService
 */
class BuildStatusService
{
    /**
     * @var BuildStatusService
     */
    protected $prevService = null;

    /**
     * @var Project
     */
    protected $project;

    /**
     * @var string
     */
    protected $branch;

    /**
     * @var Build
     */
    protected $build;

    /**
     * @var  string
     */
    protected $url;

    /**
     * @var array
     */
    protected $finishedStatusIds = [
        Build::STATUS_SUCCESS,
        Build::STATUS_FAILED,
    ];

    /**
     * @param string  $branch
     * @param Project $project
     * @param Build   $build
     * @param boolean $isParent
     */
    public function __construct(
        $branch,
        Project $project,
        Build $build = null,
        $isParent = false
    ) {
        $this->project = $project;
        $this->branch = $branch;
        $this->build = $build;
        if ($this->build) {
            $this->loadParentBuild($isParent);
        }
        if (defined('APP_URL')) {
            $this->setUrl(APP_URL);
        }
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return Build
     */
    public function getBuild()
    {
        return $this->build;
    }

    /**
     * @param boolean $isParent
     *
     * @throws \Exception
     */
    protected function loadParentBuild($isParent = true)
    {
        if ($isParent === false && !$this->isFinished()) {
            $lastFinishedBuild = $this->project->getLatestBuild($this->branch, $this->finishedStatusIds);

            if ($lastFinishedBuild) {
                $this->prevService = new BuildStatusService(
                    $this->branch,
                    $this->project,
                    $lastFinishedBuild,
                    true
                );
            }
        }
    }

    /**
     * @return string
     */
    public function getActivity()
    {
        if (in_array($this->build->getStatus(), $this->finishedStatusIds)) {
            return 'Sleeping';
        } elseif ($this->build->getStatus() == Build::STATUS_PENDING) {
            return 'Pending';
        } elseif ($this->build->getStatus() == Build::STATUS_RUNNING) {
            return 'Building';
        }
        return 'Unknown';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->project->getTitle() . ' / ' . $this->branch;
    }

    /**
     * @return boolean
     */
    public function isFinished()
    {
        if (in_array($this->build->getStatus(), $this->finishedStatusIds)) {
            return true;
        }
        return false;
    }

    /**
     * @return null|Build
     */
    public function getFinishedBuildInfo()
    {
        if ($this->isFinished()) {
            return $this->build;
        } elseif ($this->prevService) {
            return $this->prevService->getBuild();
        }
        return null;
    }

    /**
     * @return int|string
     */
    public function getLastBuildLabel()
    {
        if ($buildInfo = $this->getFinishedBuildInfo()) {
            return $buildInfo->getId();
        }
        return '';
    }

    /**
     * @return string
     */
    public function getLastBuildTime()
    {
        $dateFormat = 'Y-m-d\\TH:i:sO';
        if ($buildInfo = $this->getFinishedBuildInfo()) {
            return ($buildInfo->getFinishDate()) ? $buildInfo->getFinishDate()->format($dateFormat) : '';
        }
        return '';
    }

    /**
     * @param Build $build
     *
     * @return string
     */
    public function getBuildStatus(Build $build)
    {
        switch ($build->getStatus()) {
            case Build::STATUS_SUCCESS:
                return 'Success';
            case Build::STATUS_FAILED:
                return 'Failure';
        }
        return 'Unknown';
    }

    /**
     * @return string
     */
    public function getLastBuildStatus()
    {
        if ($build = $this->getFinishedBuildInfo()) {
            return $this->getBuildStatus($build);
        }
        return '';
    }

    /**
     * @return string
     */
    public function getBuildUrl()
    {
         return $this->url . 'build/view/' . $this->build->getId();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        if (!$this->build) {
            return [];
        }
        return [
            'name'            => $this->getName(),
            'activity'        => $this->getActivity(),
            'lastBuildLabel'  => $this->getLastBuildLabel(),
            'lastBuildStatus' => $this->getLastBuildStatus(),
            'lastBuildTime'   => $this->getLastBuildTime(),
            'webUrl'          => $this->getBuildUrl(),
        ];
    }
}
