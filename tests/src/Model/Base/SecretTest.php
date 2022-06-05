<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Model\Base;

use PHPCensor\DatabaseManager;
use PHPCensor\Model;
use PHPCensor\Model\Base\Project;
use PHPCensor\Model\Base\Secret;
use PHPCensor\Model\Base\User;
use PHPCensor\StoreRegistry;
use PHPUnit\Framework\TestCase;
use PHPCensor\Common\Application\ConfigurationInterface;

class SecretTest extends TestCase
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
        $secret = new Secret($this->storeRegistry);

        self::assertInstanceOf(Model::class, $secret);
        self::assertInstanceOf(Secret::class, $secret);

        self::assertEquals([
            'id'          => null,
            'name'        => null,
            'value'       => null,
            'create_date' => null,
            'user_id'     => null,
        ], $secret->getDataArray());
    }

    public function testId(): void
    {
        $secret = new Secret($this->storeRegistry);

        $result = $secret->setId(100);
        self::assertEquals(true, $result);
        self::assertEquals(100, $secret->getId());

        $result = $secret->setId(100);
        self::assertEquals(false, $result);
    }

    public function testName(): void
    {
        $secret = new Secret($this->storeRegistry);

        $result = $secret->setName('name');
        self::assertEquals(true, $result);
        self::assertEquals('name', $secret->getName());

        $result = $secret->setName('name');
        self::assertEquals(false, $result);
    }

    public function testValue(): void
    {
        $secret = new Secret($this->storeRegistry);

        $result = $secret->setValue('value');
        self::assertEquals(true, $result);
        self::assertEquals('value', $secret->getValue());

        $result = $secret->setValue('value');
        self::assertEquals(false, $result);
    }

    public function testCreateDate(): void
    {
        $secret = new Secret($this->storeRegistry);
        self::assertEquals(null, $secret->getCreateDate());

        $secret    = new Secret($this->storeRegistry);
        $createDate = new \DateTime();

        $result = $secret->setCreateDate($createDate);
        self::assertEquals(true, $result);
        self::assertEquals($createDate->getTimestamp(), $secret->getCreateDate()->getTimestamp());

        $result = $secret->setCreateDate($createDate);
        self::assertEquals(false, $result);

        $secret = new Secret($this->storeRegistry, ['create_date' => $createDate->format('Y-m-d H:i:s')]);
        self::assertEquals($createDate->getTimestamp(), $secret->getCreateDate()->getTimestamp());

        $secret = new Secret($this->storeRegistry, ['create_date' => 'Invalid Data']);
        self::assertNull($secret->getCreateDate());
    }

    public function testUserId(): void
    {
        $secret = new Secret($this->storeRegistry);

        $result = $secret->setUserId(300);
        self::assertEquals(true, $result);
        self::assertEquals(300, $secret->getUserId());

        $result = $secret->setUserId(300);
        self::assertEquals(false, $result);
    }
}
