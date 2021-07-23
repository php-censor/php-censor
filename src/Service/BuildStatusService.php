<?php

declare(strict_types = 1);

namespace PHPCensor\Service;

use Exception;
use PHPCensor\Model\Build;
use PHPCensor\Model\Project;
use function GuzzleHttp\Psr7\str;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class BuildStatusService
{
    private ?BuildStatusService $prevService = null;

    private Project $project;

    private string $branch;

    private Build $build;

    private string $url;

    private array $finishedStatusIds = [
        Build::STATUS_SUCCESS,
        Build::STATUS_FAILED,
    ];

    /**
     * @param string  $branch
     * @param Project $project
     * @param Build   $build
     * @param bool    $isParent
     */
    public function __construct(
        string $branch,
        Project $project,
        ?Build $build = null,
        bool $isParent = false
    ) {
        $this->project = $project;
        $this->branch  = $branch;
        $this->build   = $build;
        if ($this->build) {
            $this->loadParentBuild($isParent);
        }
        if (\defined('APP_URL')) {
            $this->setUrl(APP_URL);
        }
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return Build
     */
    public function getBuild(): Build
    {
        return $this->build;
    }

    /**
     * @param bool $isParent
     *
     * @throws Exception
     */
    protected function loadParentBuild(bool $isParent = true): void
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
    public function getActivity(): string
    {
        if (\in_array($this->build->getStatus(), $this->finishedStatusIds)) {
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
    public function getName(): string
    {
        return $this->project->getTitle() . ' / ' . $this->branch;
    }

    /**
     * @return bool
     */
    public function isFinished(): bool
    {
        if (\in_array($this->build->getStatus(), $this->finishedStatusIds)) {
            return true;
        }

        return false;
    }

    /**
     * @return null|Build
     */
    public function getFinishedBuildInfo(): ?Build
    {
        if ($this->isFinished()) {
            return $this->build;
        } elseif ($this->prevService) {
            return $this->prevService->getBuild();
        }

        return null;
    }

    /**
     * @return string
     */
    public function getLastBuildLabel(): string
    {
        if ($buildInfo = $this->getFinishedBuildInfo()) {
            return (string)$buildInfo->getId();
        }

        return '';
    }

    /**
     * @return string
     */
    public function getLastBuildTime(): string
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
    public function getBuildStatus(Build $build): string
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
    public function getLastBuildStatus(): string
    {
        if ($build = $this->getFinishedBuildInfo()) {
            return $this->getBuildStatus($build);
        }

        return '';
    }

    /**
     * @return string
     */
    public function getBuildUrl(): string
    {
        return $this->url . 'build/view/' . $this->build->getId();
    }

    /**
     * @return array
     */
    public function toArray(): array
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
