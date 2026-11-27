<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Service;

use DateTime;
use Exception;
use PHPCensor\BuildFactory;
use PHPCensor\Common\Application\ConfigurationInterface;
use PHPCensor\DatabaseManager;
use PHPCensor\Model\Build;
use PHPCensor\Model\Project;
use PHPCensor\Service\BuildService;
use PHPCensor\Store\BuildStore;
use PHPCensor\Store\EnvironmentStore;
use PHPCensor\Store\ProjectStore;
use PHPCensor\StoreRegistry;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the ProjectService class.
 *
 * @author Dan Cryer <dan@block8.co.uk>
 */
class BuildServiceTest extends TestCase
{
    private BuildService $testedService;
    private BuildStore $buildStore;
    /**
     * @var ConfigurationInterface&\PHPUnit\Framework\MockObject\Stub
     */
    private \PHPUnit\Framework\MockObject\Stub $configuration;
    private DatabaseManager $databaseManager;
    private StoreRegistry $storeRegistry;
    private EnvironmentStore $environmentStore;
    private BuildFactory $buildFactory;

    protected function setUp(): void
    {
        $this->configuration   = $this->createStub(ConfigurationInterface::class);
        $this->databaseManager = $this
            ->getMockBuilder(DatabaseManager::class)
            ->setConstructorArgs([$this->configuration])
            ->getMock();
        $this->storeRegistry = $this
            ->getMockBuilder(StoreRegistry::class)
            ->setConstructorArgs([$this->databaseManager])
            ->getMock();
        $this->buildStore = $this
            ->getMockBuilder(BuildStore::class)
            ->setConstructorArgs([$this->databaseManager, $this->storeRegistry])
            ->getMock();
        $this->buildStore
            ->method('save')
            ->willReturnArgument(0);

        $this->environmentStore = $this
            ->getMockBuilder(EnvironmentStore::class)
            ->setConstructorArgs([$this->databaseManager, $this->storeRegistry])
            ->getMock();
        $this->environmentStore
            ->method('getByProjectId')
            ->willReturn(['items' => [], 'count' => 0]);

        $projectStore = $this
            ->getMockBuilder(ProjectStore::class)
            ->setConstructorArgs([$this->databaseManager, $this->storeRegistry])
            ->getMock();

        $this->buildFactory = new BuildFactory(
            $this->configuration,
            $this->storeRegistry
        );

        $this->testedService = new BuildService(
            $this->configuration,
            $this->storeRegistry,
            $this->buildFactory,
            $this->buildStore,
            $projectStore
        );
    }

    public function testExecute_CreateBasicBuild(): void
    {
        $project = $this
            ->getMockBuilder(Project::class)
            ->setConstructorArgs([$this->storeRegistry])
            ->onlyMethods(['getEnvironmentStore'])
            ->getMock();

        $project
            ->method('getEnvironmentStore')
            ->willReturn($this->environmentStore);

        $project->setType('github');
        $project->setDefaultBranch('master');
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

    public function testExecute_CreateBuildWithOptions(): void
    {
        $returnValue = $this->testedService->createBuild(
            $this->createProjectForCommitterTest(),
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

    public static function invalidCommitterEmailProvider(): array
    {
        return [
            'html img onerror' => ['<img src=x onerror=alert(1)>'],
            'script tag'       => ['<script>alert(1)</script>@example.com'],
            'email with html'  => ['test@example.com<svg onload=alert(1)>'],
            'not an email'     => ['John Doe'],
            'whitespace'       => ['   '],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('invalidCommitterEmailProvider')]
    public function testExecute_CreateBuildWithInvalidCommitterEmail(string $committerEmail): void
    {
        $returnValue = $this->testedService->createBuild(
            $this->createProjectForCommitterTest(),
            null,
            '123',
            'testbranch',
            null,
            $committerEmail,
            'test'
        );

        self::assertNull($returnValue->getCommitterEmail());
        self::assertEquals('testbranch', $returnValue->getBranch());
        self::assertEquals('123', $returnValue->getCommitId());
        self::assertEquals('test', $returnValue->getCommitMessage());
    }

    private function createProjectForCommitterTest(): Project
    {
        $project = $this
            ->getMockBuilder(Project::class)
            ->setConstructorArgs([$this->storeRegistry])
            ->onlyMethods(['getEnvironmentStore'])
            ->getMock();

        $project
            ->method('getEnvironmentStore')
            ->willReturn($this->environmentStore);

        $project->setType('hg');
        $project->setId(101);

        return $project;
    }

    public function testExecute_CreateBuildWithExtra(): void
    {
        $project = $this
            ->getMockBuilder(Project::class)
            ->setConstructorArgs([$this->storeRegistry])
            ->onlyMethods(['getEnvironmentStore'])
            ->getMock();

        $project
            ->method('getEnvironmentStore')
            ->willReturn($this->environmentStore);

        $project->setType('bitbucket');
        $project->setDefaultBranch('master');
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

    /**
     * @throws Exception
     */
    public function testExecute_CreateDuplicateBuild(): void
    {
        $build = new Build($this->storeRegistry);
        $build->setId(1);
        $build->setProjectId(101);
        $build->setCommitId('abcde');
        $build->setStatusFailed();
        $build->setLog('Test');
        $build->setBranch('example_branch');
        $build->setStartDate(new DateTime());
        $build->setFinishDate(new DateTime());
        $build->setCommitMessage('test');
        $build->setCommitterEmail('test@example.com');
        $build->setExtra(['item1' => 1001]);
        $build->setSource(Build::SOURCE_MANUAL_CONSOLE);

        $returnValue = $this->testedService->createDuplicateBuild($build, Build::SOURCE_MANUAL_REBUILD_CONSOLE);

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
        self::assertEquals(Build::SOURCE_MANUAL_REBUILD_CONSOLE, $returnValue->getSource());
        self::assertEquals($build->getId(), $returnValue->getParentId());
    }

    public function testExecute_DeleteBuild(): void
    {
        $store = $this
            ->getMockBuilder(BuildStore::class)
            ->setConstructorArgs([$this->databaseManager, $this->storeRegistry])
            ->getMock();
        $store->expects($this->once())
            ->method('delete')
            ->willReturn(true);

        $projectStore = $this
            ->getMockBuilder(ProjectStore::class)
            ->setConstructorArgs([$this->databaseManager, $this->storeRegistry])
            ->getMock();

        $service = new BuildService(
            $this->configuration,
            $this->storeRegistry,
            $this->buildFactory,
            $store,
            $projectStore
        );
        $build = new Build($this->storeRegistry);

        self::assertEquals(false, $service->deleteBuild($build));

        $build->setId(22);
        self::assertEquals(true, $service->deleteBuild($build));
    }
}
