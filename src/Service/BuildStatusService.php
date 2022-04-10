<?php

declare(strict_types=1);

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

    private ?Build $build;

    private string $url;

    private array $finishedStatusIds = [
        Build::STATUS_SUCCESS,
        Build::STATUS_FAILED,
    ];

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

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function getBuild(): Build
    {
        return $this->build;
    }

    /**
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

    public function getActivity(): string
    {
        if (\in_array($this->build->getStatus(), $this->finishedStatusIds, true)) {
            return 'Sleeping';
        } elseif ($this->build->getStatus() == Build::STATUS_PENDING) {
            return 'Pending';
        }

        return 'Building';
    }

    public function getName(): string
    {
        return $this->project->getTitle() . ' / ' . $this->branch;
    }

    public function isFinished(): bool
    {
        if (\in_array($this->build->getStatus(), $this->finishedStatusIds, true)) {
            return true;
        }

        return false;
    }

    public function getFinishedBuildInfo(): ?Build
    {
        if ($this->isFinished()) {
            return $this->build;
        } elseif ($this->prevService) {
            return $this->prevService->getBuild();
        }

        return null;
    }

    public function getLastBuildLabel(): string
    {
        if ($buildInfo = $this->getFinishedBuildInfo()) {
            return (string)$buildInfo->getId();
        }

        return '';
    }

    public function getLastBuildTime(): string
    {
        $dateFormat = 'Y-m-d\\TH:i:sO';
        if ($buildInfo = $this->getFinishedBuildInfo()) {
            return ($buildInfo->getFinishDate()) ? $buildInfo->getFinishDate()->format($dateFormat) : '';
        }

        return '';
    }

    public function getLastBuildStatus(): string
    {
        if ($build = $this->getFinishedBuildInfo()) {
            if (Build::STATUS_SUCCESS === $build->getStatus()) {
                return 'Success';
            }

            return 'Failure';
        }

        return '';
    }

    public function getBuildUrl(): string
    {
        return $this->url . 'build/view/' . $this->build->getId();
    }

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
