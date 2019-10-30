<?php

namespace Tests\PHPCensor\Model;

use PHPCensor\Exception\InvalidArgumentException;
use PHPCensor\Model;
use PHPCensor\Model\Project;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the Project model class.
 *
 * @author Dan Cryer <dan@block8.co.uk>
 */
class ProjectTest extends TestCase
{
    public function testExecute_TestIsAValidModel()
    {
        $project = new Project();
        self::assertTrue($project instanceof Model);

        try {
            $project->setArchived('true');
        } catch (InvalidArgumentException $e) {
            self::assertEquals(
                'Column "archived" must be a bool.',
                $e->getMessage()
            );
        }
    }

    public function testExecute_TestProjectAccessInformation()
    {
        $info = [
            'item1' => 'Item One',
            'item2' => 2,
        ];

        $project = new Project();
        $project->setAccessInformation($info);

        self::assertEquals('Item One', $project->getAccessInformation('item1'));
        self::assertEquals(2, $project->getAccessInformation('item2'));
        self::assertNull($project->getAccessInformation('item3'));
        self::assertEquals($info, $project->getAccessInformation());
    }
}
