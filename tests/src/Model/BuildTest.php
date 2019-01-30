<?php

namespace Tests\PHPCensor\Model;

use PHPCensor\Exception\InvalidArgumentException;
use PHPCensor\Model\Build;
use PHPCensor\Model\Build\GogsBuild;
use PHPCensor\Model\Project;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the Build model class.
 *
 * @author Dan Cryer <dan@block8.co.uk>
 */
class BuildTest extends TestCase
{
    public function testConstruct()
    {
        $build = new Build();

        self::assertInstanceOf('PHPCensor\Model', $build);
        self::assertInstanceOf('PHPCensor\Model\Build', $build);

        $build = new Build([
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
            'extra'                 => null,
            'environment_id'        => null,
            'source'                => Build::SOURCE_UNKNOWN,
            'user_id'               => null,
            'errors_total'          => null,
            'errors_total_previous' => null,
            'errors_new'            => null,
        ], $build->getDataArray());

        try {
            $build = new Build([
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

        $build = new Build();
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
        $build = new Build();
        self::assertEquals('#', $build->getCommitLink());
        self::assertEquals('#', $build->getBranchLink());
        self::assertEquals(null, $build->getFileLinkTemplate());
    }

    public function testExecute_TestIsSuccessful()
    {
        $build = new Build();
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

        $build = new Build();
        $build->setExtra($info);

        self::assertEquals('Item One', $build->getExtra('item1'));
        self::assertEquals(2, $build->getExtra('item2'));
        self::assertNull($build->getExtra('item3'));
        self::assertEquals($info, $build->getExtra());

        $build->addExtraValue('item3', 'Item Three');

        self::assertEquals('Item One', $build->getExtra('item1'));
        self::assertEquals('Item Three', $build->getExtra('item3'));
    }

    public function testGogsBuildLinks()
    {
        $project = new Project();
        $project->setType(Project::TYPE_GOGS);
        $project->setReference('https://gogs.repository/the-vendor/the-project.git');

        $stub = $this->getMockBuilder(GogsBuild::class)
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
