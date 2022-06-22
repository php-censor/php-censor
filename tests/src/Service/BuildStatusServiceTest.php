<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Service;

use DateTime;
use PHPCensor\Common\Build\BuildInterface;
use PHPCensor\DatabaseManager;
use PHPCensor\Model\Build;
use PHPCensor\Model\Project;
use PHPCensor\Service\BuildStatusService;
use PHPCensor\StoreRegistry;
use PHPUnit\Framework\TestCase;
use PHPCensor\Common\Application\ConfigurationInterface;

/**
 * Unit tests for the ProjectService class.
 *
 * @author Dan Cryer <dan@block8.co.uk>
 */
class BuildStatusServiceTest extends TestCase
{
    private const BRANCH = 'master';

    private Project $project;
    private string $timezone;
    private StoreRegistry $storeRegistry;

    protected function setUp(): void
    {
        $configuration   = $this->getMockBuilder(ConfigurationInterface::class)->getMock();
        $databaseManager = $this
            ->getMockBuilder(DatabaseManager::class)
            ->setConstructorArgs([$configuration])
            ->getMock();
        $this->storeRegistry = $this
            ->getMockBuilder(StoreRegistry::class)
            ->setConstructorArgs([$databaseManager])
            ->getMock();

        $project = new Project($this->storeRegistry);
        $project->setId(3);
        $project->setDefaultBranch(self::BRANCH);
        $project->setTitle('Test');

        $this->project = $project;
        $this->timezone = \date_default_timezone_get();

        \date_default_timezone_set('UTC');
    }

    protected function tearDown(): void
    {
        \date_default_timezone_set($this->timezone);
    }

    protected function getBuild(int $configId, bool $setProject = true): ?Build
    {
        $config = [
            1 => [
                'status'         => BuildInterface::STATUS_RUNNING,
                'id'             => 77,
                'finishDateTime' => null,
                'startedDate'    => '2014-10-25 21:20:02',
                'previousBuild'  => null,
            ],
            2 => [
                'status'         => BuildInterface::STATUS_RUNNING,
                'id'             => 78,
                'finishDateTime' => null,
                'startedDate'    => '2014-10-25 21:20:02',
                'previousBuild'  => 4,
            ],
            3 => [
                'status'         => BuildInterface::STATUS_SUCCESS,
                'id'             => 7,
                'finishDateTime' => '2014-10-25 21:50:02',
                'startedDate'    => '2014-10-25 21:20:02',
                'previousBuild'  => null,
            ],
            4 => [
                'status'         => BuildInterface::STATUS_FAILED,
                'id'             => 13,
                'finishDateTime' => '2014-10-13 13:13:13',
                'previousBuild'  => null,
            ],
            5 => [
                'status'         => BuildInterface::STATUS_PENDING,
                'id'             => 1000,
                'finishDateTime' => '2014-12-25 21:12:21',
                'previousBuild'  => 3,
            ]
        ];

        if (!isset($config[$configId])) {
            return null;
        }

        $build = new Build($this->storeRegistry);
        $build->setId($config[$configId]['id']);
        $build->setBranch(self::BRANCH);

        $build->setStatus($config[$configId]['status']);

        if ($config[$configId]['finishDateTime']) {
            $build->setFinishDate(new DateTime($config[$configId]['finishDateTime']));
        }
        if (!empty($config[$configId]['startedDate'])) {
            $build->setStartDate(new DateTime('2014-10-25 21:20:02'));
        }

        $project = $this->getProjectMock($config[$configId]['previousBuild'], $setProject);

        $build->setProjectId($project->getId());

        return $build;
    }

    protected function getProjectMock(?int $prevBuildId = null, bool $setProject = true): Project
    {
        $project = $this
            ->getMockBuilder(Project::class)
            ->setConstructorArgs([$this->storeRegistry])
            ->onlyMethods(['getLatestBuild'])
            ->getMock();

        $prevBuild = ($prevBuildId) ? $this->getBuild($prevBuildId, false) : null;

        $project->expects($this->any())
            ->method('getLatestBuild')
            ->will($this->returnValue($prevBuild));

        /* @var $project Project */

        $project->setId(3);
        $project->setDefaultBranch(self::BRANCH);
        $project->setTitle('Test');

        if ($setProject) {
            $this->project = $project;
        }

        return $project;
    }

    /**
     * @dataProvider finishedProvider
     */
    public function testFinished(int $buildConfigId, array $expectedResult): void
    {
        $build = $this->getBuild($buildConfigId);

        $service = new BuildStatusService(self::BRANCH, $this->project, $build);
        $service->setUrl('http://php-censor.local/');

        self::assertEquals($expectedResult, $service->toArray());
    }

    public function finishedProvider(): array
    {
        return [
            'buildingStatus' => [
                1,
                [
                    'name'            => 'Test / master',
                    'activity'        => 'Building',
                    'lastBuildLabel'  => '',
                    'lastBuildStatus' => '',
                    'lastBuildTime'   => '',
                    'webUrl'          => 'http://php-censor.local/build/view/77',
                ],
            ],
            'buildingStatusWithPrev' => [
                2,
                [
                    'name'            => 'Test / master',
                    'activity'        => 'Building',
                    'lastBuildLabel'  => 13,
                    'lastBuildStatus' => 'Failure',
                    'lastBuildTime'   => '2014-10-13T13:13:13+0000',
                    'webUrl'          => 'http://php-censor.local/build/view/78',
                ],
            ],
            'successStatus' => [
                3,
                [
                    'name'            => 'Test / master',
                    'activity'        => 'Sleeping',
                    'lastBuildLabel'  => 7,
                    'lastBuildStatus' => 'Success',
                    'lastBuildTime'   => '2014-10-25T21:50:02+0000',
                    'webUrl'          => 'http://php-censor.local/build/view/7',
                ],
            ],
            'failureStatus' => [
                4,
                [
                    'name'            => 'Test / master',
                    'activity'        => 'Sleeping',
                    'lastBuildLabel'  => 13,
                    'lastBuildStatus' => 'Failure',
                    'lastBuildTime'   => '2014-10-13T13:13:13+0000',
                    'webUrl'          => 'http://php-censor.local/build/view/13',
                ],
            ],
            'pendingStatus' => [
                5,
                [
                    'name'            => 'Test / master',
                    'activity'        => 'Pending',
                    'lastBuildLabel'  => 7,
                    'lastBuildStatus' => 'Success',
                    'lastBuildTime'   => '2014-10-25T21:50:02+0000',
                    'webUrl'          => 'http://php-censor.local/build/view/1000',
                ],
            ],
            'pendingStatusWithoutBuild' => [
                10,
                [],
            ],
        ];
    }
}
