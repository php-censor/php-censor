<?php

namespace Tests\PHPCensor\Model;

use PHPCensor\Exception\HttpException\ValidationException;
use PHPCensor\Model\Build;

/**
 * Unit tests for the Build model class.
 *
 * @author Dan Cryer <dan@block8.co.uk>
 */
class BuildTest extends \PHPUnit\Framework\TestCase
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
            'id'              => null,
            'project_id'      => 100,
            'commit_id'       => null,
            'status'          => null,
            'log'             => null,
            'branch'          => 'master',
            'tag'             => null,
            'create_date'     => null,
            'start_date'      => null,
            'finish_date'     => null,
            'committer_email' => null,
            'commit_message'  => null,
            'extra'           => null,
            'environment'     => null,
            'source'          => Build::SOURCE_UNKNOWN,
            'user_id'         => 0,
        ], $build->getDataArray());

        try {
            $build = new Build([
                'project_id' => 101,
                'branch'     => 'dev',
                'unknown'    => 'unknown',
            ]);
        } catch (\InvalidArgumentException $e) {
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
        } catch (ValidationException $e) {
            self::assertEquals(
                'Column "log" must be a string.',
                $e->getMessage()
            );
        }

        $build->setSource(Build::SOURCE_WEBHOOK_PULL_REQUEST);
        self::assertEquals(Build::SOURCE_WEBHOOK_PULL_REQUEST, $build->getSource());

        try {
            $build->setSource('5');
        } catch (ValidationException $e) {
            self::assertEquals(
                'Column "source" must be an integer.',
                $e->getMessage()
            );
        }

        try {
            $build->setId(null);
        } catch (ValidationException $e) {
            self::assertEquals(
                'Column "id" must not be null.',
                $e->getMessage()
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
        $build->setStatus(Build::STATUS_PENDING);
        self::assertFalse($build->isSuccessful());

        $build->setStatus(Build::STATUS_RUNNING);
        self::assertFalse($build->isSuccessful());

        $build->setStatus(Build::STATUS_FAILED);
        self::assertFalse($build->isSuccessful());

        $build->setStatus(Build::STATUS_SUCCESS);
        self::assertTrue($build->isSuccessful());
    }

    public function testExecute_TestBuildExtra()
    {
        $info = [
            'item1' => 'Item One',
            'item2' => 2,
        ];

        $build = new Build();
        $build->setExtra(json_encode($info));

        self::assertEquals('Item One', $build->getExtra('item1'));
        self::assertEquals(2, $build->getExtra('item2'));
        self::assertNull($build->getExtra('item3'));
        self::assertEquals($info, $build->getExtra());

        $build->setExtraValue('item3', 'Item Three');

        self::assertEquals('Item One', $build->getExtra('item1'));
        self::assertEquals('Item Three', $build->getExtra('item3'));

        $build->setExtraValues([
            'item3' => 'Item Three New',
            'item4' => 4,
        ]);

        self::assertEquals('Item One', $build->getExtra('item1'));
        self::assertEquals('Item Three New', $build->getExtra('item3'));
        self::assertEquals(4, $build->getExtra('item4'));

        self::assertEquals([
            'item1' => 'Item One',
            'item2' => 2,
            'item3' => 'Item Three New',
            'item4' => 4,
        ], $build->getExtra());
    }
}
