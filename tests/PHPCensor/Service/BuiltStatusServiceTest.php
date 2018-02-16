<?php

namespace PHPCensor\Service\Tests;

use PHPCensor\Model\Build;
use PHPCensor\Model\Project;
use PHPCensor\Service\BuildStatusService;

/**
 * Unit tests for the ProjectService class.
 *
 * @author Dan Cryer <dan@block8.co.uk>
 */
class BuildStatusServiceTest extends \PHPUnit\Framework\TestCase
{
    const BRANCH = 'master';

    /** @var  Project */
    protected $project;

    protected $timezone;

    public function setUp()
    {
        $project = new Project();
        $project->setId(3);
        $project->setBranch(self::BRANCH);
        $project->setTitle('Test');

        $this->project = $project;
        $this->timezone = date_default_timezone_get();

        date_default_timezone_set('UTC');
    }

    public function tearDown()
    {
        date_default_timezone_set($this->timezone);
    }

    /**
     * @param $configId
     * @param bool $setProject
     * @return Build
     */
    protected function getBuild($configId, $setProject = true)
    {
        $config = [
            '1' => [
                'status'         => Build::STATUS_RUNNING,
                'id'             => 77,
                'finishDateTime' => null,
                'startedDate'    => '2014-10-25 21:20:02',
                'previousBuild'  => null,
            ],
            '2' => [
                'status'         => Build::STATUS_RUNNING,
                'id'             => 78,
                'finishDateTime' => null,
                'startedDate'    => '2014-10-25 21:20:02',
                'previousBuild'  => 4,
            ],
            '3' => [
                'status'         => Build::STATUS_SUCCESS,
                'id'             => 7,
                'finishDateTime' => '2014-10-25 21:50:02',
                'startedDate'    => '2014-10-25 21:20:02',
                'previousBuild'  => null,
            ],
            '4' => [
                'status'         => Build::STATUS_FAILED,
                'id'             => 13,
                'finishDateTime' => '2014-10-13 13:13:13',
                'previousBuild'  => null,
            ],
            '5' => [
                'status'         => Build::STATUS_PENDING,
                'id'             => 1000,
                'finishDateTime' => '2014-12-25 21:12:21',
                'previousBuild'  => 3,
            ]
        ];

        $build = new Build();
        $build->setId($config[$configId]['id']);
        $build->setBranch(self::BRANCH);
        $build->setStatus($config[$configId]['status']);
        if ($config[$configId]['finishDateTime']) {
            $build->setFinishDate(new \DateTime($config[$configId]['finishDateTime']));
        }
        if (!empty($config[$configId]['startedDate'])) {
            $build->setStartDate(new \DateTime('2014-10-25 21:20:02'));
        }

        $project = $this->getProjectMock($config[$configId]['previousBuild'], $setProject);

        $build->setProjectObject($project);

        return $build;
    }

    /**
     * @param null|int $prevBuildId
     * @param bool $setProject
     * @return Project
     */
    protected function getProjectMock($prevBuildId = null, $setProject = true) {

        $project = $this
            ->getMockBuilder('PHPCensor\Model\Project')
            ->setMethods(['getLatestBuild'])
            ->getMock();

        $prevBuild = ($prevBuildId) ? $this->getBuild($prevBuildId, false) : null;

        $project->expects($this->any())
            ->method('getLatestBuild')
            ->will($this->returnValue($prevBuild));

        /* @var $project Project */

        $project->setId(3);
        $project->setBranch(self::BRANCH);
        $project->setTitle('Test');

        if ($setProject) {
            $this->project = $project;
        }

        return $project;

    }

    /**
     * @dataProvider finishedProvider
     *
     * @param int $buildConfigId
     * @param array $expectedResult
     */
    public function testFinished($buildConfigId, array $expectedResult)
    {
        $build = $this->getBuild($buildConfigId);
        $service = new BuildStatusService(self::BRANCH, $this->project, $build);
        $service->setUrl('http://php-censor.local/');
        self::assertEquals($expectedResult, $service->toArray());
    }

    public function finishedProvider()
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
                ]
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
                ]
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
                ]
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
                ]
            ],
            'pending' => [
                5,
                [
                    'name'            => 'Test / master',
                    'activity'        => 'Pending',
                    'lastBuildLabel'  => 7,
                    'lastBuildStatus' => 'Success',
                    'lastBuildTime'   => '2014-10-25T21:50:02+0000',
                    'webUrl'          => 'http://php-censor.local/build/view/1000',
                ]
            ],
        ];
    }
}
