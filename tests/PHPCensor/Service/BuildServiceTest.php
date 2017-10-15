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

        $this->assertEquals(101, $returnValue->getProjectId());
        $this->assertEquals(Build::STATUS_PENDING, $returnValue->getStatus());
        $this->assertNull($returnValue->getStartDate());
        $this->assertNull($returnValue->getFinishDate());
        $this->assertNull($returnValue->getLog());
        $this->assertEquals(null, $returnValue->getCommitMessage());
        $this->assertNull($returnValue->getCommitterEmail());
        $this->assertEquals(['branches' => []], $returnValue->getExtra());
        $this->assertEquals('master', $returnValue->getBranch());
        $this->assertInstanceOf('DateTime', $returnValue->getCreateDate());
        $this->assertEquals('', $returnValue->getCommitId());
        $this->assertEquals(Build::SOURCE_UNKNOWN, $returnValue->getSource());
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

        $this->assertEquals('testbranch', $returnValue->getBranch());
        $this->assertEquals('123', $returnValue->getCommitId());
        $this->assertEquals('test', $returnValue->getCommitMessage());
        $this->assertEquals('test@example.com', $returnValue->getCommitterEmail());
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
            ['item1' => 1001]
        );

        $this->assertEquals(1001, $returnValue->getExtra('item1'));
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

        $this->assertNotEquals($build->getId(), $returnValue->getId());
        $this->assertEquals($build->getProjectId(), $returnValue->getProjectId());
        $this->assertEquals($build->getCommitId(), $returnValue->getCommitId());
        $this->assertNotEquals($build->getStatus(), $returnValue->getStatus());
        $this->assertEquals(Build::STATUS_PENDING, $returnValue->getStatus());
        $this->assertNull($returnValue->getLog());
        $this->assertEquals($build->getBranch(), $returnValue->getBranch());
        $this->assertNotEquals($build->getCreateDate(), $returnValue->getCreateDate());
        $this->assertNull($returnValue->getStartDate());
        $this->assertNull($returnValue->getFinishDate());
        $this->assertEquals('test', $returnValue->getCommitMessage());
        $this->assertEquals('test@example.com', $returnValue->getCommitterEmail());
        $this->assertEquals($build->getExtra('item1'), $returnValue->getExtra('item1'));
    }

    public function testExecute_DeleteBuild()
    {
        $store = $this->getMockBuilder('PHPCensor\Store\BuildStore')->getMock();
        $store->expects($this->once())
            ->method('delete')
            ->will($this->returnValue(true));

        $service = new BuildService($store);
        $build = new Build();

        $this->assertEquals(true, $service->deleteBuild($build));
    }
}
