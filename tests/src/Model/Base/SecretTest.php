<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Model\Base;

use PHPCensor\DatabaseManager;
use PHPCensor\Model;
use PHPCensor\Model\Base\Secret;
use PHPUnit\Framework\TestCase;

class SecretTest extends TestCase
{
    public function testConstruct(): void
    {
        $secret = new Secret();

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
        $secret = new Secret();

        $result = $secret->setId(100);
        self::assertEquals(true, $result);
        self::assertEquals(100, $secret->getId());

        $result = $secret->setId(100);
        self::assertEquals(false, $result);
    }

    public function testName(): void
    {
        $secret = new Secret();

        $result = $secret->setName('name');
        self::assertEquals(true, $result);
        self::assertEquals('name', $secret->getName());

        $result = $secret->setName('name');
        self::assertEquals(false, $result);
    }

    public function testValue(): void
    {
        $secret = new Secret();

        $result = $secret->setValue('value');
        self::assertEquals(true, $result);
        self::assertEquals('value', $secret->getValue());

        $result = $secret->setValue('value');
        self::assertEquals(false, $result);
    }

    public function testCreateDate(): void
    {
        $secret = new Secret();
        self::assertEquals(null, $secret->getCreateDate());

        $secret    = new Secret();
        $createDate = new \DateTime();

        $result = $secret->setCreateDate($createDate);
        self::assertEquals(true, $result);
        self::assertEquals($createDate->getTimestamp(), $secret->getCreateDate()->getTimestamp());

        $result = $secret->setCreateDate($createDate);
        self::assertEquals(false, $result);

        $secret = new Secret(['create_date' => $createDate->format('Y-m-d H:i:s')]);
        self::assertEquals($createDate->getTimestamp(), $secret->getCreateDate()->getTimestamp());

        $secret = new Secret(['create_date' => 'Invalid Data']);
        self::assertNull($secret->getCreateDate());
    }

    public function testUserId(): void
    {
        $secret = new Secret();

        $result = $secret->setUserId(300);
        self::assertEquals(true, $result);
        self::assertEquals(300, $secret->getUserId());

        $result = $secret->setUserId(300);
        self::assertEquals(false, $result);
    }
}
