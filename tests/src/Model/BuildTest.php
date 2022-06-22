<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Model;

use PHPCensor\Common\Build\BuildInterface;
use PHPCensor\Common\Exception\InvalidArgumentException;
use PHPCensor\DatabaseManager;
use PHPCensor\Model;
use PHPCensor\Model\Build;
use PHPCensor\Model\Build\GitBuild;
use PHPCensor\Model\Build\GithubBuild;
use PHPCensor\Model\Build\GitlabBuild;
use PHPCensor\Model\Build\GogsBuild;
use PHPCensor\Model\Project;
use PHPCensor\Service\ProjectService;
use PHPCensor\Store\ProjectStore;
use PHPCensor\StoreRegistry;
use PHPUnit\Framework\TestCase;
use PHPCensor\Common\Application\ConfigurationInterface;

/**
 * Unit tests for the Build model class.
 *
 * @author Dan Cryer <dan@block8.co.uk>
 */
class BuildTest extends TestCase
{
    private StoreRegistry $storeRegistry;
    private DatabaseManager $databaseManager;
    private ProjectService $projectService;

    protected function setUp(): void
    {
        $configuration   = $this->getMockBuilder(ConfigurationInterface::class)->getMock();
        $this->databaseManager = $this
            ->getMockBuilder(DatabaseManager::class)
            ->setConstructorArgs([$configuration])
            ->getMock();
        $this->storeRegistry = $this
            ->getMockBuilder(StoreRegistry::class)
            ->setConstructorArgs([$this->databaseManager])
            ->getMock();

        $projectStore = $this
            ->getMockBuilder(ProjectStore::class)
            ->setConstructorArgs([$this->databaseManager, $this->storeRegistry])
            ->getMock();

        $this->projectService = new ProjectService($this->storeRegistry, $projectStore);
    }

    public function testConstruct(): void
    {
        $build = new Build($this->storeRegistry);

        self::assertInstanceOf(Model::class, $build);
        self::assertInstanceOf(Build::class, $build);

        $build = new Build($this->storeRegistry, [
            'project_id' => 100,
            'branch'     => 'master',
        ]);

        self::assertEquals([
            'id'                    => null,
            'parent_id'             => null,
            'project_id'            => 100,
            'commit_id'             => null,
            'status'                => null,
            'log'                   => null,
            'branch'                => 'master',
            'tag'                   => null,
            'create_date'           => null,
            'start_date'            => null,
            'finish_date'           => null,
            'committer_email'       => null,
            'commit_message'        => null,
            'extra'                 => [],
            'environment_id'        => null,
            'source'                => BuildInterface::SOURCE_UNKNOWN,
            'user_id'               => null,
            'errors_total'          => null,
            'errors_total_previous' => null,
            'errors_new'            => null,
            'test_coverage'          => null,
            'test_coverage_previous' => null,
        ], $build->getDataArray());

        try {
            new Build($this->storeRegistry, [
                'project_id' => 101,
                'branch'     => 'dev',
                'unknown'    => 'unknown',
            ]);
        } catch (InvalidArgumentException $e) {
            self::assertEquals(
                'Model "PHPCensor\Model\Build" doesn\'t have field "unknown"',
                $e->getMessage()
            );
        }

        $build = new Build($this->storeRegistry);
        $build->setLog('log');
        self::assertEquals('log', $build->getLog());

        $build->setLog(null);
        self::assertEquals(null, $build->getLog());

        try {
            $build->setLog([]);
        } catch (\Throwable $e) {
            self::assertInstanceOf(
                \TypeError::class,
                $e
            );
        }

        $build->setSource(BuildInterface::SOURCE_WEBHOOK_PULL_REQUEST_CREATED);
        self::assertEquals(BuildInterface::SOURCE_WEBHOOK_PULL_REQUEST_CREATED, $build->getSource());

        try {
            $build->setSource(50);
        } catch (InvalidArgumentException $e) {
            self::assertStringStartsWith(
                'Column "source" must be one of:',
                $e->getMessage()
            );
        }

        try {
            $build->setId(null);
        } catch (\Throwable $e) {
            self::assertInstanceOf(
                \TypeError::class,
                $e
            );
        }
    }

    public function testExecute_TestBaseBuildDefaults(): void
    {
        $build = new Build($this->storeRegistry);
        self::assertEquals('#', $build->getCommitLink());
        self::assertEquals('#', $build->getBranchLink());
        self::assertEquals(null, $build->getFileLinkTemplate());
    }

    public function testExecute_TestIsSuccessful(): void
    {
        $build = new Build($this->storeRegistry);
        $build->setStatusPending();
        self::assertFalse($build->isSuccessful());

        $build->setStatusRunning();
        self::assertFalse($build->isSuccessful());

        $build->setStatusFailed();
        self::assertFalse($build->isSuccessful());

        $build->setStatusSuccess();
        self::assertTrue($build->isSuccessful());
    }

    public function testExecute_TestBuildExtra(): void
    {
        $info = [
            'item1' => 'Item One',
            'item2' => 2,
        ];

        $build = new Build($this->storeRegistry);
        $build->setExtra($info);

        self::assertEquals('Item One', $build->getExtra('item1'));
        self::assertEquals(2, $build->getExtra('item2'));
        self::assertNull($build->getExtra('item3'));
        self::assertEquals($info, $build->getExtra());

        $build->addExtraValue('item3', 'Item Three');

        self::assertEquals('Item One', $build->getExtra('item1'));
        self::assertEquals('Item Three', $build->getExtra('item3'));
    }

    public function testGitBuildLinks(): void
    {
        $project = new Project($this->storeRegistry);
        $project->setType(Project::TYPE_GIT);
        $project->setReference('https://git.repository/the-vendor/the-project.git');

        $configuration = $this->getMockBuilder(ConfigurationInterface::class)->getMock();

        $stub = $this->getMockBuilder(GitBuild::class)
            ->setConstructorArgs([$configuration, $this->storeRegistry])
            ->onlyMethods(['getProject', 'getCommitId', 'getBranch'])
            ->getMock();

        $stub->method('getProject')
            ->will($this->returnValue($project));

        $stub->method('getCommitId')
            ->will($this->returnValue('abcdef'));

        $stub->method('getBranch')
            ->will($this->returnValue('master'));

        $this->assertEquals('#', $stub->getCommitLink());

        $this->assertEquals('#', $stub->getBranchLink());

        $this->assertEquals(null, $stub->getFileLinkTemplate());
    }

    public function testGitHubBuildLinks(): void
    {
        $project = new Project($this->storeRegistry);
        $project->setType(Project::TYPE_GITHUB);
        $project->setReference('git@github.com:php-censor/php-censor.git');
        $project = $this->projectService->processAccessInformation($project);

        $configuration = $this->getMockBuilder(ConfigurationInterface::class)->getMock();

        $stub = $this->getMockBuilder(GithubBuild::class)
            ->setConstructorArgs([$configuration, $this->storeRegistry])
            ->onlyMethods(['getProject', 'getCommitId', 'getBranch', 'getTag'])
            ->getMock();

        $stub->method('getProject')
            ->will($this->returnValue($project));

        $stub->method('getCommitId')
            ->will($this->returnValue('abcdef'));

        $stub->method('getBranch')
            ->will($this->returnValue('master'));

        $stub->method('getTag')
            ->will($this->returnValue('2.0.7'));

        $this->assertEquals(
            '//github.com/php-censor/php-censor/commit/abcdef',
            $stub->getCommitLink()
        );

        $this->assertEquals(
            '//github.com/php-censor/php-censor/tree/master',
            $stub->getBranchLink()
        );

        $this->assertEquals(
            '//github.com/php-censor/php-censor/blob/abcdef/{FILE}#L{LINE}-L{LINE_END}',
            $stub->getFileLinkTemplate()
        );

        $this->assertEquals(
            '//github.com/php-censor/php-censor/tree/2.0.7',
            $stub->getTagLink()
        );
    }

    public function testGitlabBuildLinks(): void
    {
        $project = new Project($this->storeRegistry);
        $project->setType(Project::TYPE_GITLAB);
        $project->setReference('git@gitlab.com:php-censor/php-censor.git');
        $project = $this->projectService->processAccessInformation($project);

        $configuration = $this->getMockBuilder(ConfigurationInterface::class)->getMock();

        $stub = $this->getMockBuilder(GitlabBuild::class)
            ->setConstructorArgs([$configuration, $this->storeRegistry])
            ->onlyMethods(['getProject', 'getCommitId', 'getBranch'])
            ->getMock();

        $stub->method('getProject')
            ->will($this->returnValue($project));

        $stub->method('getCommitId')
            ->will($this->returnValue('abcdef'));

        $stub->method('getBranch')
            ->will($this->returnValue('master'));

        $this->assertEquals(
            '//gitlab.com/php-censor/php-censor/commit/abcdef',
            $stub->getCommitLink()
        );

        $this->assertEquals(
            '//gitlab.com/php-censor/php-censor/tree/master',
            $stub->getBranchLink()
        );

        $this->assertEquals(
            '//gitlab.com/php-censor/php-censor/blob/abcdef/{FILE}#L{LINE}',
            $stub->getFileLinkTemplate()
        );
    }

    public function testGogsBuildLinks(): void
    {
        $project = new Project($this->storeRegistry);
        $project->setType(Project::TYPE_GOGS);
        $project->setReference('https://gogs.repository/the-vendor/the-project.git');

        $configuration = $this->getMockBuilder(ConfigurationInterface::class)->getMock();

        $stub = $this->getMockBuilder(GogsBuild::class)
            ->setConstructorArgs([$configuration, $this->storeRegistry])
            ->onlyMethods(['getProject', 'getCommitId', 'getBranch'])
            ->getMock();

        $stub->method('getProject')
            ->will($this->returnValue($project));

        $stub->method('getCommitId')
            ->will($this->returnValue('abcdef'));

        $stub->method('getBranch')
            ->will($this->returnValue('master'));

        $this->assertEquals(
            'https://gogs.repository/the-vendor/the-project/commit/abcdef',
            $stub->getCommitLink()
        );

        $this->assertEquals(
            'https://gogs.repository/the-vendor/the-project/src/master',
            $stub->getBranchLink()
        );

        $this->assertEquals(
            'https://gogs.repository/the-vendor/the-project/src/abcdef/{FILE}#L{LINE}',
            $stub->getFileLinkTemplate()
        );
    }
}
