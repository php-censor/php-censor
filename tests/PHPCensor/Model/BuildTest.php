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
        $this->assertTrue($build instanceof \b8\Model);
        $this->assertTrue($build instanceof Model);
    }

    public function testExecute_TestBaseBuildDefaults()
    {
        $build = new Build();
        $this->assertEquals('#', $build->getCommitLink());
        $this->assertEquals('#', $build->getBranchLink());
        $this->assertEquals(null, $build->getFileLinkTemplate());
    }

    public function testExecute_TestIsSuccessful()
    {
        $build = new Build();
        $build->setStatus(Build::STATUS_PENDING);
        $this->assertFalse($build->isSuccessful());

        $build->setStatus(Build::STATUS_RUNNING);
        $this->assertFalse($build->isSuccessful());

        $build->setStatus(Build::STATUS_FAILED);
        $this->assertFalse($build->isSuccessful());

        $build->setStatus(Build::STATUS_SUCCESS);
        $this->assertTrue($build->isSuccessful());
    }

    public function testExecute_TestBuildExtra()
    {
        $info = [
            'item1' => 'Item One',
            'item2' => 2,
        ];

        $build = new Build();
        $build->setExtra(json_encode($info));

        $this->assertEquals('Item One', $build->getExtra('item1'));
        $this->assertEquals(2, $build->getExtra('item2'));
        $this->assertNull($build->getExtra('item3'));
        $this->assertEquals($info, $build->getExtra());
        
        $build->setExtraValue('item3', 'Item Three');

        $this->assertEquals('Item One', $build->getExtra('item1'));
        $this->assertEquals('Item Three', $build->getExtra('item3'));

        $build->setExtraValues([
            'item3' => 'Item Three New',
            'item4' => 4,
        ]);

        $this->assertEquals('Item One', $build->getExtra('item1'));
        $this->assertEquals('Item Three New', $build->getExtra('item3'));
        $this->assertEquals(4, $build->getExtra('item4'));

        $this->assertEquals([
            'item1' => 'Item One',
            'item2' => 2,
            'item3' => 'Item Three New',
            'item4' => 4,
        ], $build->getExtra());
    }
}
