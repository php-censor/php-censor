<?php

namespace Tests\PHPCensor\Model;

use PHPCensor\Common\Exception\InvalidArgumentException;
use PHPCensor\DatabaseManager;
use PHPCensor\Model\Build;
use PHPCensor\Model\Build\GitBuild;
use PHPCensor\Model\Build\GithubBuild;
use PHPCensor\Model\Build\GitlabBuild;
use PHPCensor\Model\Build\GogsBuild;
use PHPCensor\Model\Project;
use PHPCensor\Service\ProjectService;
use PHPCensor\StoreRegistry;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the Build model class.
 *
 * @author Dan Cryer <dan@block8.co.uk>
 */
class BuildTest extends TestCase
{
    protected StoreRegistry $storeRegistry;
    protected DatabaseManager $databaseManager;

    protected function setUp(): void
    {
        $configuration   = $this->getMockBuilder('PHPCensor\ConfigurationInterface')->getMock();
        $this->databaseManager = $this
            ->getMockBuilder('PHPCensor\DatabaseManager')
            ->setConstructorArgs([$configuration])
            ->getMock();
        $this->storeRegistry = $this
            ->getMockBuilder('PHPCensor\StoreRegistry')
            ->setConstructorArgs([$this->databaseManager])
            ->getMock();

        $projectStore = $this
            ->getMockBuilder('PHPCensor\Store\ProjectStore')
            ->setConstructorArgs([$this->databaseManager, $this->storeRegistry])
            ->getMock();

        $this->projectService = new ProjectService($this->storeRegistry, $projectStore);
    }

    public function testConstruct()
    {
        $build = new Build($this->storeRegistry);

        self::assertInstanceOf('PHPCensor\Model', $build);
        self::assertInstanceOf('PHPCensor\Model\Build', $build);

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
            'source'                => Build::SOURCE_UNKNOWN,
            'user_id'               => null,
            'errors_total'          => null,
            'errors_total_previous' => null,
            'errors_new'            => null,
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

        $build->setSource(Build::SOURCE_WEBHOOK_PULL_REQUEST_CREATED);
        self::assertEquals(Build::SOURCE_WEBHOOK_PULL_REQUEST_CREATED, $build->getSource());

        try {
            $build->setSource('5');
        } catch (InvalidArgumentException $e) {
            self::assertEquals(
                'Column "source" must be an int.',
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

    public function testExecute_TestBaseBuildDefaults()
    {
        $build = new Build($this->storeRegistry);
        self::assertEquals('#', $build->getCommitLink());
        self::assertEquals('#', $build->getBranchLink());
        self::assertEquals(null, $build->getFileLinkTemplate());
    }

    public function testExecute_TestIsSuccessful()
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

    public function testExecute_TestBuildExtra()
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

    public function testGitBuildLinks()
    {
        $project = new Project($this->storeRegistry);
        $project->setType(Project::TYPE_GIT);
        $project->setReference('https://git.repository/the-vendor/the-project.git');

        $configuration = $this->getMockBuilder('PHPCensor\ConfigurationInterface')->getMock();

        $stub = $this->getMockBuilder(GitBuild::class)
            ->setConstructorArgs([$configuration, $this->storeRegistry])
            ->setMethods(['getProject', 'getCommitId', 'getBranch'])
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

    public function testGitHubBuildLinks()
    {

        $project = new Project($this->storeRegistry);
        $project->setType(Project::TYPE_GITHUB);
        $project->setReference('git@github.com:php-censor/php-censor.git');
        $project = $this->projectService->processAccessInformation($project);

        $configuration = $this->getMockBuilder('PHPCensor\ConfigurationInterface')->getMock();

        $stub = $this->getMockBuilder(GithubBuild::class)
            ->setConstructorArgs([$configuration, $this->storeRegistry])
            ->setMethods(['getProject', 'getCommitId', 'getBranch', 'getTag'])
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

    public function testGitlabBuildLinks()
    {
        $store = $this
            ->getMockBuilder('PHPCensor\Store\ProjectStore')
            ->setConstructorArgs([$this->databaseManager, $this->storeRegistry])
            ->getMock();
        $service = new ProjectService($this->storeRegistry, $store);

        $project = new Project($this->storeRegistry);
        $project->setType(Project::TYPE_GITLAB);
        $project->setReference('git@gitlab.com:php-censor/php-censor.git');
        $project = $service->processAccessInformation($project);

        $configuration = $this->getMockBuilder('PHPCensor\ConfigurationInterface')->getMock();

        $stub = $this->getMockBuilder(GitlabBuild::class)
            ->setConstructorArgs([$configuration, $this->storeRegistry])
            ->setMethods(['getProject', 'getCommitId', 'getBranch'])
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

    public function testGogsBuildLinks()
    {
        $project = new Project($this->storeRegistry);
        $project->setType(Project::TYPE_GOGS);
        $project->setReference('https://gogs.repository/the-vendor/the-project.git');

        $configuration = $this->getMockBuilder('PHPCensor\ConfigurationInterface')->getMock();

        $stub = $this->getMockBuilder(GogsBuild::class)
            ->setConstructorArgs([$configuration, $this->storeRegistry])
            ->setMethods(['getProject', 'getCommitId', 'getBranch'])
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
