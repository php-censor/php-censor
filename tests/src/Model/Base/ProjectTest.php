<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Model\Base;

use DateTime;
use PHPCensor\Common\Exception\InvalidArgumentException;
use PHPCensor\DatabaseManager;
use PHPCensor\Model;
use PHPCensor\Model\Base\Project;
use PHPCensor\StoreRegistry;
use PHPUnit\Framework\TestCase;
use PHPCensor\Common\Application\ConfigurationInterface;

class ProjectTest extends TestCase
{
    private StoreRegistry $storeRegistry;

    protected function setUp(): void
    {
        $configuration   = $this->getMockBuilder(ConfigurationInterface::class)->getMock();
        $databaseManager = $this
            ->getMockBuilder(DatabaseManager::class)
            ->setConstructorArgs([$configuration])
            ->getMock();
        $this->storeRegistry = $this
            ->getMockBuilder(StoreRegistry::class)
            ->setConstructorArgs([$databaseManager])
            ->getMock();
    }

    public function testConstruct(): void
    {
        $project = new Project($this->storeRegistry);

        self::assertInstanceOf(Model::class, $project);
        self::assertInstanceOf(Project::class, $project);

        self::assertEquals([
            'id'                     => null,
            'title'                  => null,
            'reference'              => null,
            'default_branch'         => null,
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
            'user_id'                => null,
        ], $project->getDataArray());
    }

    public function testId(): void
    {
        $project = new Project($this->storeRegistry);

        $result = $project->setId(100);
        self::assertEquals(true, $result);
        self::assertEquals(100, $project->getId());

        $result = $project->setId(100);
        self::assertEquals(false, $result);
    }

    public function testTitle(): void
    {
        $project = new Project($this->storeRegistry);

        $result = $project->setTitle('title');
        self::assertEquals(true, $result);
        self::assertEquals('title', $project->getTitle());

        $result = $project->setTitle('title');
        self::assertEquals(false, $result);
    }

    public function testReference(): void
    {
        $project = new Project($this->storeRegistry);

        $result = $project->setReference('git://reference');
        self::assertEquals(true, $result);
        self::assertEquals('git://reference', $project->getReference());

        $result = $project->setReference('git://reference');
        self::assertEquals(false, $result);
    }

    public function testBranch(): void
    {
        $project = new Project($this->storeRegistry);

        self::assertEquals(null, $project->getDefaultBranch());

        $result = $project->setDefaultBranch('branch');
        self::assertEquals(true, $result);
        self::assertEquals('branch', $project->getDefaultBranch());

        $result = $project->setDefaultBranch('branch');
        self::assertEquals(false, $result);
    }

    public function testDefaultBranchOnly(): void
    {
        $project = new Project($this->storeRegistry);

        $result = $project->setDefaultBranchOnly(true);
        self::assertEquals(true, $result);
        self::assertEquals(true, $project->getDefaultBranchOnly());

        $result = $project->setDefaultBranchOnly(true);
        self::assertEquals(false, $result);
    }

    public function testSshPrivateKey(): void
    {
        $project = new Project($this->storeRegistry);

        $result = $project->setSshPrivateKey('private-key');
        self::assertEquals(true, $result);
        self::assertEquals('private-key', $project->getSshPrivateKey());

        $result = $project->setSshPrivateKey('private-key');
        self::assertEquals(false, $result);
    }

    public function testSshPublicKey(): void
    {
        $project = new Project($this->storeRegistry);

        $result = $project->setSshPublicKey('public-key');
        self::assertEquals(true, $result);
        self::assertEquals('public-key', $project->getSshPublicKey());

        $result = $project->setSshPublicKey('public-key');
        self::assertEquals(false, $result);
    }

    public function testType(): void
    {
        $project = new Project($this->storeRegistry);

        $result = $project->setType('git');
        self::assertEquals(true, $result);
        self::assertEquals('git', $project->getType());

        $result = $project->setType('git');
        self::assertEquals(false, $result);

        self::expectException(InvalidArgumentException::class);
        $project->setType('invalid-type');
    }

    public function testAccessInformation(): void
    {
        $project = new Project($this->storeRegistry);

        $result = $project->setAccessInformation(['key-1' => 'value-1', 'key-2' => 'value-2']);
        self::assertEquals(true, $result);
        self::assertEquals(['key-1' => 'value-1', 'key-2' => 'value-2'], $project->getAccessInformation());
        self::assertEquals('value-1', $project->getAccessInformation('key-1'));
        self::assertEquals(null, $project->getAccessInformation('key-3'));

        $result = $project->setAccessInformation(['key-1' => 'value-1', 'key-2' => 'value-2']);
        self::assertEquals(false, $result);
    }

    public function testBuildConfig(): void
    {
        $project = new Project($this->storeRegistry);

        $result = $project->setBuildConfig('config');
        self::assertEquals(true, $result);
        self::assertEquals('config', $project->getBuildConfig());

        $result = $project->setBuildConfig('config');
        self::assertEquals(false, $result);
    }

    public function testOverwriteBuildConfig(): void
    {
        $project = new Project($this->storeRegistry);

        $result = $project->setOverwriteBuildConfig(false);
        self::assertEquals(true, $result);
        self::assertEquals(false, $project->getOverwriteBuildConfig());

        $result = $project->setOverwriteBuildConfig(false);
        self::assertEquals(false, $result);
    }

    public function testAllowPublicStatus(): void
    {
        $project = new Project($this->storeRegistry);

        $result = $project->setAllowPublicStatus(true);
        self::assertEquals(true, $result);
        self::assertEquals(true, $project->getAllowPublicStatus());

        $result = $project->setAllowPublicStatus(true);
        self::assertEquals(false, $result);
    }

    public function testArchived(): void
    {
        $project = new Project($this->storeRegistry);

        $result = $project->setArchived(true);
        self::assertEquals(true, $result);
        self::assertEquals(true, $project->getArchived());

        $result = $project->setArchived(true);
        self::assertEquals(false, $result);
    }

    public function testGroupId(): void
    {
        $project = new Project($this->storeRegistry);

        $result = $project->setGroupId(200);
        self::assertEquals(true, $result);
        self::assertEquals(200, $project->getGroupId());

        $result = $project->setGroupId(200);
        self::assertEquals(false, $result);
    }

    public function testCreateDate(): void
    {
        $project = new Project($this->storeRegistry);
        self::assertEquals(null, $project->getCreateDate());

        $project    = new Project($this->storeRegistry);
        $createDate = new DateTime();

        $result = $project->setCreateDate($createDate);
        self::assertEquals(true, $result);
        self::assertEquals($createDate->getTimestamp(), $project->getCreateDate()->getTimestamp());

        $result = $project->setCreateDate($createDate);
        self::assertEquals(false, $result);

        $project = new Project($this->storeRegistry, ['create_date' => $createDate->format('Y-m-d H:i:s')]);
        self::assertEquals($createDate->getTimestamp(), $project->getCreateDate()->getTimestamp());

        $project = new Project($this->storeRegistry, ['create_date' => 'Invalid Data']);
        self::assertNull($project->getCreateDate());
    }

    public function testUserId(): void
    {
        $project = new Project($this->storeRegistry);

        $result = $project->setUserId(300);
        self::assertEquals(true, $result);
        self::assertEquals(300, $project->getUserId());

        $result = $project->setUserId(300);
        self::assertEquals(false, $result);
    }
}
