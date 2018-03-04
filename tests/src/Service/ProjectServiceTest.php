<?php

namespace Tests\PHPCensor\Service;

use PHPCensor\Model\Project;
use PHPCensor\Service\ProjectService;

/**
 * Unit tests for the ProjectService class.
 *
 * @author Dan Cryer <dan@block8.co.uk>
 */
class ProjectServiceTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @var ProjectService $testedService
     */
    protected $testedService;

    /**
     * @var \ $mockProjectStore
     */
    protected $mockProjectStore;

    public function setUp()
    {
        $this->mockProjectStore = $this->getMockBuilder('PHPCensor\Store\ProjectStore')->getMock();
        $this->mockProjectStore->expects($this->any())
                               ->method('save')
                               ->will($this->returnArgument(0));

        $this->testedService = new ProjectService($this->mockProjectStore);
    }

    public function testExecute_CreateBasicProject()
    {
        $returnValue = $this->testedService->createProject('Test Project', 'github', 'block8/phpci', 0);

        self::assertEquals('Test Project', $returnValue->getTitle());
        self::assertEquals('github', $returnValue->getType());
        self::assertEquals('block8/phpci', $returnValue->getReference());
        self::assertEquals('master', $returnValue->getBranch());
    }

    public function testExecute_CreateProjectWithOptions()
    {
        $options = [
            'ssh_private_key'     => 'private',
            'ssh_public_key'      => 'public',
            'allow_public_status' => 1,
            'build_config'        => 'config',
            'branch'              => 'testbranch',
        ];

        $returnValue = $this->testedService->createProject('Test Project', 'github', 'block8/phpci', 0, $options);

        self::assertEquals('private', $returnValue->getSshPrivateKey());
        self::assertEquals('public', $returnValue->getSshPublicKey());
        self::assertEquals('config', $returnValue->getBuildConfig());
        self::assertEquals('testbranch', $returnValue->getBranch());
        self::assertEquals(1, $returnValue->getAllowPublicStatus());
    }

    /**
     * @link https://github.com/Block8/PHPCI/issues/484
     */
    public function testExecute_CreateGitlabProjectWithoutPort()
    {
        $reference = 'git@gitlab.block8.net:block8/phpci.git';
        $returnValue = $this->testedService->createProject('Gitlab', 'gitlab', $reference, 0);

        self::assertEquals('git', $returnValue->getAccessInformation('user'));
        self::assertEquals('gitlab.block8.net', $returnValue->getAccessInformation('domain'));
        self::assertEquals('block8/phpci', $returnValue->getReference());
    }

    public function testExecute_UpdateExistingProject()
    {
        $project = new Project();
        $project->setTitle('Before Title');
        $project->setReference('Before Reference');
        $project->setType('github');

        $returnValue = $this->testedService->updateProject($project, 'After Title', 'bitbucket', 'After Reference');

        self::assertEquals('After Title', $returnValue->getTitle());
        self::assertEquals('After Reference', $returnValue->getReference());
        self::assertEquals('bitbucket', $returnValue->getType());
    }

    public function testExecute_EmptyPublicStatus()
    {
        $project = new Project();
        $project->setAllowPublicStatus(1);

        $options = [
            'ssh_private_key' => 'private',
            'ssh_public_key'  => 'public',
            'build_config'    => 'config',
        ];

        $returnValue = $this->testedService->updateProject($project, 'Test Project', 'github', 'block8/phpci', $options);

        self::assertEquals(0, $returnValue->getAllowPublicStatus());
    }

    public function testExecute_DeleteProject()
    {
        $store = $this->getMockBuilder('PHPCensor\Store\ProjectStore')->getMock();
        $store->expects($this->once())
            ->method('delete')
            ->will($this->returnValue(true));

        $service = new ProjectService($store);
        $project = new Project();

        self::assertEquals(true, $service->deleteProject($project));
    }
}
