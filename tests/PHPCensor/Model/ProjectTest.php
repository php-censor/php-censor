<?php

namespace Tests\PHPCensor\Model;

use PHPCensor\Model\Project;
use PHPCensor\Model;

/**
 * Unit tests for the Project model class.
 * 
 * @author Dan Cryer <dan@block8.co.uk>
 */
class ProjectTest extends \PHPUnit\Framework\TestCase
{
    public function testExecute_TestIsAValidModel()
    {
        $project = new Project();
        $this->assertTrue($project instanceof \b8\Model);
        $this->assertTrue($project instanceof Model);
    }

    public function testExecute_TestGitDefaultBranch()
    {
        $project = new Project();
        $project->setType('git');

        $this->assertEquals('master', $project->getBranch());
    }

    public function testExecute_TestGithubDefaultBranch()
    {
        $project = new Project();
        $project->setType('github');

        $this->assertEquals('master', $project->getBranch());
    }

    public function testExecute_TestGitlabDefaultBranch()
    {
        $project = new Project();
        $project->setType('gitlab');

        $this->assertEquals('master', $project->getBranch());
    }

    public function testExecute_TestBitbucketDefaultBranch()
    {
        $project = new Project();
        $project->setType('bitbucket');

        $this->assertEquals('master', $project->getBranch());
    }

    public function testExecute_TestMercurialDefaultBranch()
    {
        $project = new Project();
        $project->setType('hg');

        $this->assertEquals('default', $project->getBranch());
    }

    public function testExecute_TestProjectAccessInformation()
    {
        $info = [
            'item1' => 'Item One',
            'item2' => 2,
        ];

        $project = new Project();
        $project->setAccessInformation($info);

        $this->assertEquals('Item One', $project->getAccessInformation('item1'));
        $this->assertEquals(2, $project->getAccessInformation('item2'));
        $this->assertNull($project->getAccessInformation('item3'));
        $this->assertEquals($info, $project->getAccessInformation());
    }
}
