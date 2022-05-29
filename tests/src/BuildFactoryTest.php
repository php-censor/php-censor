<?php

declare(strict_types=1);

namespace Tests\PHPCensor;

use PHPCensor\BuildFactory;
use PHPCensor\Common\Application\ConfigurationInterface;
use PHPCensor\Configuration;
use PHPCensor\DatabaseManager;
use PHPCensor\Model\Build;
use PHPCensor\Model\Project;
use PHPCensor\Store\BuildStore;
use PHPCensor\Store\ProjectStore;
use PHPCensor\StoreRegistry;
use PHPUnit\Framework\TestCase;
use PHPCensor\Model\Build\LocalBuild;
use PHPCensor\Model\Build\GitBuild;
use PHPCensor\Model\Build\GitlabBuild;
use PHPCensor\Model\Build\GithubBuild;
use PHPCensor\Model\Build\BitbucketBuild;
use PHPCensor\Model\Build\GogsBuild;
use PHPCensor\Model\Build\HgBuild;
use PHPCensor\Model\Build\BitbucketHgBuild;
use PHPCensor\Model\Build\BitbucketServerBuild;
use PHPCensor\Model\Build\SvnBuild;

class BuildFactoryTest extends TestCase
{
    private ConfigurationInterface $configuration;
    private DatabaseManager $databaseManager;
    private StoreRegistry $storeRegistry;
    private ProjectStore $projectStore;
    private BuildFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configuration = new Configuration('');

        $this->databaseManager = $this
            ->getMockBuilder(DatabaseManager::class)
            ->setConstructorArgs([$this->configuration])
            ->getMock();

        $this->storeRegistry = $this
            ->getMockBuilder(StoreRegistry::class)
            ->setConstructorArgs([$this->databaseManager])
            ->getMock();

        $this->projectStore = $this
            ->getMockBuilder(ProjectStore::class)
            ->setConstructorArgs([$this->databaseManager, $this->storeRegistry])
            ->getMock();

        $this->factory = new BuildFactory($this->configuration, $this->storeRegistry);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function testConstruct(): void
    {
        self::assertInstanceOf(BuildFactory::class, $this->factory);
    }

    public function testGetBuildDefault(): void
    {
        $this->storeRegistry
            ->method('get')
            ->with('Project')
            ->willReturn($this->projectStore);

        $rawBuild = new Build($this->storeRegistry, []);
        $build = $this->factory->getBuild($rawBuild);

        self::assertInstanceOf(Build::class, $build);
    }

    /**
     * @dataProvider buildTypesProvider
     */
    public function testGetBuild(string $buildType, string $expectedBuildClass): void
    {
        $this->storeRegistry
            ->method('get')
            ->with('Project')
            ->willReturn($this->projectStore);

        $rawBuild = new Build($this->storeRegistry, ['project_id' => 10]);

        $this->projectStore
            ->method('getById')
            ->with(10)
            ->willReturn(new Project($this->storeRegistry, ['type' => $buildType]));

        $build = $this->factory->getBuild($rawBuild);

        self::assertInstanceOf($expectedBuildClass, $build);
    }

    public function buildTypesProvider(): array
    {
        return [
            [Project::TYPE_LOCAL, LocalBuild::class],
            [Project::TYPE_GIT, GitBuild::class],
            [Project::TYPE_GITHUB, GithubBuild::class],
            [Project::TYPE_BITBUCKET, BitbucketBuild::class],
            [Project::TYPE_GITLAB, GitlabBuild::class],
            [Project::TYPE_GOGS, GogsBuild::class],
            [Project::TYPE_HG, HgBuild::class],
            [Project::TYPE_BITBUCKET_HG, BitbucketHgBuild::class],
            [Project::TYPE_BITBUCKET_SERVER, BitbucketServerBuild::class],
            [Project::TYPE_SVN, SvnBuild::class],
            ['unknown', Build::class],
        ];
    }

    public function testGetBuildById(): void
    {
        $rawBuild = new Build($this->storeRegistry, ['project_id' => 10]);

        $buildStore = $this
            ->getMockBuilder(BuildStore::class)
            ->setConstructorArgs([$this->databaseManager, $this->storeRegistry])
            ->getMock();

        $build = new Build($this->storeRegistry, ['id' => 222]);

        $buildStore
            ->method('getById')
            ->with(20)
            ->willReturn($build);

        $this->storeRegistry
            ->method('get')
            ->with('Build')
            ->willReturn($buildStore);

        $buildByFactory = $this->factory->getBuildById(20);

        self::assertEquals($build, $buildByFactory);
    }

    public function testGetBuildByIdWithEmptyBuild(): void
    {
        $buildStore = $this
            ->getMockBuilder(BuildStore::class)
            ->setConstructorArgs([$this->databaseManager, $this->storeRegistry])
            ->getMock();

        $buildStore
            ->method('getById')
            ->with(20)
            ->willReturn(null);

        $this->storeRegistry
            ->method('get')
            ->with('Build')
            ->willReturn($buildStore);

        $buildByFactory = $this->factory->getBuildById(20);

        self::assertEquals(null, $buildByFactory);
    }
}
