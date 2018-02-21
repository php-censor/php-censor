<?php

namespace Tests\PHPCensor\Service;

use PHPCensor\Model\Build;
use PHPCensor\Model\Project;
use PHPCensor\Service\BuildService;

/**
 * Unit tests for the ProjectService class.
 *
 * @author Dan Cryer <dan@block8.co.uk>
 */
class BuildServiceTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @var BuildService $testedService
     */
    protected $testedService;

    /**
     * @var \ $mockBuildStore
     */
    protected $mockBuildStore;

    /**
     * @var \ $mockEnvironmentStore
     */
    protected $mockEnvironmentStore;

    public function setUp()
    {
        $this->mockBuildStore = $this->getMockBuilder('PHPCensor\Store\BuildStore')->getMock();
        $this->mockBuildStore->expects($this->any())
                               ->method('save')
                               ->will($this->returnArgument(0));

        $this->mockEnvironmentStore = $this->getMockBuilder('PHPCensor\Store\EnvironmentStore')->getMock();
        $this->mockEnvironmentStore->expects($this->any())
            ->method('getByProjectId')
            ->will($this->returnValue(['items' => [], 'count' => 0]));

        $this->testedService = new BuildService($this->mockBuildStore);
    }

    public function testExecute_CreateBasicBuild()
    {
        $project = $this
            ->getMockBuilder('PHPCensor\Model\Project')
            ->setMethods(['getEnvironmentStore'])
            ->getMock();

        $project->expects($this->any())
            ->method('getEnvironmentStore')
            ->will($this->returnValue($this->mockEnvironmentStore));

        $project->setType('github');
        $project->setId(101);

        $returnValue = $this->testedService->createBuild($project, null);

        self::assertEquals(101, $returnValue->getProjectId());
        self::assertEquals(Build::STATUS_PENDING, $returnValue->getStatus());
        self::assertNull($returnValue->getStartDate());
        self::assertNull($returnValue->getFinishDate());
        self::assertNull($returnValue->getLog());
        self::assertEquals(null, $returnValue->getCommitMessage());
        self::assertNull($returnValue->getCommitterEmail());
        self::assertEquals(['branches' => []], $returnValue->getExtra());
        self::assertEquals('master', $returnValue->getBranch());
        self::assertInstanceOf('DateTime', $returnValue->getCreateDate());
        self::assertEquals('', $returnValue->getCommitId());
        self::assertEquals(Build::SOURCE_UNKNOWN, $returnValue->getSource());
    }

    public function testExecute_CreateBuildWithOptions()
    {
        $project = $this
            ->getMockBuilder('PHPCensor\Model\Project')
            ->setMethods(['getEnvironmentStore'])
            ->getMock();

        $project->expects($this->any())
            ->method('getEnvironmentStore')
            ->will($this->returnValue($this->mockEnvironmentStore));

        $project->setType('hg');
        $project->setId(101);

        $returnValue = $this->testedService->createBuild(
            $project,
            null,
            '123',
            'testbranch',
            null,
            'test@example.com',
            'test'
        );

        self::assertEquals('testbranch', $returnValue->getBranch());
        self::assertEquals('123', $returnValue->getCommitId());
        self::assertEquals('test', $returnValue->getCommitMessage());
        self::assertEquals('test@example.com', $returnValue->getCommitterEmail());
    }

    public function testExecute_CreateBuildWithExtra()
    {
        $project = $this
            ->getMockBuilder('PHPCensor\Model\Project')
            ->setMethods(['getEnvironmentStore'])
            ->getMock();

        $project->expects($this->any())
            ->method('getEnvironmentStore')
            ->will($this->returnValue($this->mockEnvironmentStore));

        $project->setType('bitbucket');
        $project->setId(101);

        $returnValue = $this->testedService->createBuild(
            $project,
            null,
            '',
            null,
            null,
            null,
            null,
            Build::SOURCE_UNKNOWN,
            0,
            ['item1' => 1001]
        );

        self::assertEquals(1001, $returnValue->getExtra('item1'));
    }

    public function testExecute_CreateDuplicateBuild()
    {
        $build = new Build();
        $build->setId(1);
        $build->setProject(101);
        $build->setCommitId('abcde');
        $build->setStatus(Build::STATUS_FAILED);
        $build->setLog('Test');
        $build->setBranch('example_branch');
        $build->setStartDate(new \DateTime());
        $build->setFinishDate(new \DateTime());
        $build->setCommitMessage('test');
        $build->setCommitterEmail('test@example.com');
        $build->setExtra(json_encode(['item1' => 1001]));

        $returnValue = $this->testedService->createDuplicateBuild($build);

        self::assertNotEquals($build->getId(), $returnValue->getId());
        self::assertEquals($build->getProjectId(), $returnValue->getProjectId());
        self::assertEquals($build->getCommitId(), $returnValue->getCommitId());
        self::assertNotEquals($build->getStatus(), $returnValue->getStatus());
        self::assertEquals(Build::STATUS_PENDING, $returnValue->getStatus());
        self::assertNull($returnValue->getLog());
        self::assertEquals($build->getBranch(), $returnValue->getBranch());
        self::assertNotEquals($build->getCreateDate(), $returnValue->getCreateDate());
        self::assertNull($returnValue->getStartDate());
        self::assertNull($returnValue->getFinishDate());
        self::assertEquals('test', $returnValue->getCommitMessage());
        self::assertEquals('test@example.com', $returnValue->getCommitterEmail());
        self::assertEquals($build->getExtra('item1'), $returnValue->getExtra('item1'));
    }

    public function testExecute_DeleteBuild()
    {
        $store = $this->getMockBuilder('PHPCensor\Store\BuildStore')->getMock();
        $store->expects($this->once())
            ->method('delete')
            ->will($this->returnValue(true));

        $service = new BuildService($store);
        $build = new Build();

        self::assertEquals(true, $service->deleteBuild($build));
    }
}
