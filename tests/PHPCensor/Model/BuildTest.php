<?php

namespace Tests\PHPCensor\Model;

use PHPCensor\Model\Build;
use PHPCensor\Model;

/**
 * Unit tests for the Build model class.
 *
 * @author Dan Cryer <dan@block8.co.uk>
 */
class BuildTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
    }

    public function testExecute_TestIsAValidModel()
    {
        $build = new Build();
        self::assertTrue($build instanceof \b8\Model);
        self::assertTrue($build instanceof Model);
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
