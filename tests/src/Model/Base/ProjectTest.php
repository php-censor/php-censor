<?php

namespace Tests\PHPCensor\Model\Base;

use PHPCensor\Model\Base\Project;
use PHPUnit\Framework\TestCase;

class ProjectTest extends TestCase
{
    public function testConstruct()
    {
        $project = new Project();

        self::assertInstanceOf('PHPCensor\Model', $project);
        self::assertInstanceOf('PHPCensor\Model\Base\Project', $project);

        self::assertEquals([
            'id'                     => null,
            'title'                  => null,
            'reference'              => null,
            'branch'                 => null,
            'default_branch_only'    => 0,
            'ssh_private_key'        => null,
            'ssh_public_key'         => null,
            'type'                   => null,
            'access_information'     => null,
            'build_config'           => null,
            'overwrite_build_config' => 1,
            'allow_public_status'    => 0,
            'archived'               => 0,
            'group_id'               => 1,
            'create_date'            => null,
            'user_id'                => 0,
        ], $project->getDataArray());
    }

    public function testId()
    {
        $project = new Project();

        $result = $project->setId(100);
        self::assertEquals(true, $result);
        self::assertEquals(100, $project->getId());

        $result = $project->setId(100);
        self::assertEquals(false, $result);
    }

    public function testTitle()
    {
        $project = new Project();

        $result = $project->setTitle('title');
        self::assertEquals(true, $result);
        self::assertEquals('title', $project->getTitle());

        $result = $project->setTitle('title');
        self::assertEquals(false, $result);
    }

    public function testReference()
    {
        $project = new Project();

        $result = $project->setReference('git://reference');
        self::assertEquals(true, $result);
        self::assertEquals('git://reference', $project->getReference());

        $result = $project->setReference('git://reference');
        self::assertEquals(false, $result);
    }

    public function testBranch()
    {
        $project = new Project();

        self::assertEquals('master', $project->getBranch());

        $project->setType('hg');
        self::assertEquals('default', $project->getBranch());

        $project->setType('svn');
        self::assertEquals('trunk', $project->getBranch());

        $project = new Project();

        $result = $project->setBranch('branch');
        self::assertEquals(true, $result);
        self::assertEquals('branch', $project->getBranch());

        $result = $project->setBranch('branch');
        self::assertEquals(false, $result);
    }

    public function testDefaultBranchOnly()
    {
        $project = new Project();

        $result = $project->setDefaultBranchOnly(true);
        self::assertEquals(true, $result);
        self::assertEquals(true, $project->getDefaultBranchOnly());

        $result = $project->setDefaultBranchOnly(true);
        self::assertEquals(false, $result);
    }

    public function testSshPrivateKey()
    {
        $project = new Project();

        $result = $project->setSshPrivateKey('private-key');
        self::assertEquals(true, $result);
        self::assertEquals('private-key', $project->getSshPrivateKey());

        $result = $project->setSshPrivateKey('private-key');
        self::assertEquals(false, $result);
    }

    public function testSshPublicKey()
    {
        $project = new Project();

        $result = $project->setSshPublicKey('public-key');
        self::assertEquals(true, $result);
        self::assertEquals('public-key', $project->getSshPublicKey());

        $result = $project->setSshPublicKey('public-key');
        self::assertEquals(false, $result);
    }

    public function testType()
    {
        $project = new Project();

        $result = $project->setType('git');
        self::assertEquals(true, $result);
        self::assertEquals('git', $project->getType());

        $result = $project->setType('git');
        self::assertEquals(false, $result);

        self::expectException('\PHPCensor\Exception\InvalidArgumentException');
        $project->setType('invalid-type');
    }

    public function testAccessInformation()
    {
        $project = new Project();

        $result = $project->setAccessInformation(['key-1' => 'value-1', 'key-2' => 'value-2']);
        self::assertEquals(true, $result);
        self::assertEquals(['key-1' => 'value-1', 'key-2' => 'value-2'], $project->getAccessInformation());
        self::assertEquals('value-1', $project->getAccessInformation('key-1'));
        self::assertEquals(null, $project->getAccessInformation('key-3'));

        $result = $project->setAccessInformation(['key-1' => 'value-1', 'key-2' => 'value-2']);
        self::assertEquals(false, $result);
    }

    public function testBuildConfig()
    {
        $project = new Project();

        $result = $project->setBuildConfig('config');
        self::assertEquals(true, $result);
        self::assertEquals('config', $project->getBuildConfig());

        $result = $project->setBuildConfig('config');
        self::assertEquals(false, $result);
    }

    public function testOverwriteBuildConfig()
    {
        $project = new Project();

        $result = $project->setOverwriteBuildConfig(false);
        self::assertEquals(true, $result);
        self::assertEquals(false, $project->getOverwriteBuildConfig());

        $result = $project->setOverwriteBuildConfig(false);
        self::assertEquals(false, $result);
    }

    public function testAllowPublicStatus()
    {
        $project = new Project();

        $result = $project->setAllowPublicStatus(true);
        self::assertEquals(true, $result);
        self::assertEquals(true, $project->getAllowPublicStatus());

        $result = $project->setAllowPublicStatus(true);
        self::assertEquals(false, $result);
    }

    public function testArchived()
    {
        $project = new Project();

        $result = $project->setArchived(true);
        self::assertEquals(true, $result);
        self::assertEquals(true, $project->getArchived());

        $result = $project->setArchived(true);
        self::assertEquals(false, $result);
    }

    public function testGroupId()
    {
        $project = new Project();

        $result = $project->setGroupId(200);
        self::assertEquals(true, $result);
        self::assertEquals(200, $project->getGroupId());

        $result = $project->setGroupId(200);
        self::assertEquals(false, $result);
    }

    public function testCreateDate()
    {
        $project = new Project();
        self::assertEquals(null, $project->getCreateDate());

        $project    = new Project();
        $createDate = new \DateTime();

        $result = $project->setCreateDate($createDate);
        self::assertEquals(true, $result);
        self::assertEquals($createDate->getTimestamp(), $project->getCreateDate()->getTimestamp());

        $result = $project->setCreateDate($createDate);
        self::assertEquals(false, $result);
    }

    public function testUserId()
    {
        $project = new Project();

        $result = $project->setUserId(300);
        self::assertEquals(true, $result);
        self::assertEquals(300, $project->getUserId());

        $result = $project->setUserId(300);
        self::assertEquals(false, $result);
    }
}
