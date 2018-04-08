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
 * A service that listens for creation, duplication and deletion of builds for web notification UI.
 */
class WebNotificationService
{
    /**
     * Similar to BuildController::formatBuilds() but uses
     * pure object to be used for rendering web notifications.
     * @param  array $builds
     * @return array  Formatted builds
     * @see \PHPCensor\Controller\WidgetLastBuildsController::webNotificationUpdate().
     */
    public static function formatBuilds($builds)
    {
        $rtn = ['count' => $builds['count'], 'items' => []];

        foreach ($builds['items'] as $buildItem) {
            $build = self::formatBuild($buildItem);
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
    public static function formatBuild($build)
    {
        if (empty($build) || is_null($build)) {
            return [];
        }
        $status = $build->getStatus();
        $datePerformed = '';
        $dateFinished = '';

        /*
            BUG: Lang::out() automatically renders the values for
            either 'created_x' or 'started_x' instead of just
            returning them.
        */
        if ($status === Build::STATUS_PENDING) {
            $datePerformed = 'Created: ' . $build->getCreateDate()->format('H:i');
        }
        elseif ($status === Build::STATUS_RUNNING) {
            $datePerformed = 'Started: ' . $build->getStartDate()->format('H:i');
        }

        if (!is_null($build->getFinishDate())) {
            $dateFinished = 'Finished: ' . $build->getFinishDate()->format('H:i');
        }

        return [
            'branch'          => $build->getBranch(),
            'url'             => APP_URL .
                                 'build/view/' .
                                 $build->getId(),
            'committer_email' => $build->getCommitterEmail(),
            'img_src'         => 'https://www.gravatar.com/avatar/' .
                                 md5($build->getCommitterEmail()) .
                                 '?d=mm&s=40',
            'project_title'   => $build->getProject()->getTitle(),
            'status'          => $status,
            'date_performed'  => $datePerformed,
            'date_finished'   => $dateFinished
        ];
    }
}
