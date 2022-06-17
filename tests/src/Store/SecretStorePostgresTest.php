<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Store;

use PHPCensor\Model\Secret;
use PHPCensor\Store;
use PHPCensor\Store\SecretStore;
use Tests\PHPCensor\BasePostgresTestCase;

class SecretStorePostgresTest extends BasePostgresTestCase
{
    private Store $store;

    protected function setUp(): void
    {
        parent::setUp();

        $this->store = new SecretStore($this->databaseManager, $this->storeRegistry);
    }

    protected function getTestData(): array
    {
        return [
            'users' => [
                [
                    'email'    => 'user1@test.test',
                    'hash'     => 'abc1',
                    'name'     => 'user 1',
                    'is_admin' => 1,
                ],
                [
                    'email'    => 'user2@test.test',
                    'hash'     => 'abc2',
                    'name'     => 'user 2',
                    'is_admin' => 0,
                ],
                [
                    'email'    => 'user3@test.test',
                    'hash'     => 'abc3',
                    'name'     => 'user 3',
                    'is_admin' => 0,
                ],
                [
                    'email'    => 'user4@test.test',
                    'hash'     => 'abc4',
                    'name'     => 'user 4',
                    'is_admin' => 0,
                ],
            ],
            'secrets' => [
                [
                    'name'        => 'secret 1',
                    'value'       => 'value 1',
                    'create_date' => '2014-01-01 01:01:00',
                    'user_id'     => 1,
                ],
                [
                    'name'        => 'secret 2',
                    'value'       => 'value 2',
                    'create_date' => '2015-01-01 01:01:00',
                    'user_id'     => 1,
                ],
                [
                    'name'        => 'secret 3',
                    'value'       => 'value 3',
                    'create_date' => '2016-01-01 01:01:00',
                    'user_id'     => 1,
                ],
                [
                    'name'        => 'secret 4',
                    'value'       => 'value 4',
                    'create_date' => '2017-01-01 01:01:00',
                    'user_id'     => 1,
                ],
                [
                    'name'        => 'secret 5',
                    'value'       => 'value 5',
                    'create_date' => '2018-01-01 01:01:00',
                    'user_id'     => 2,
                ],
                [
                    'name'        => 'secret 6',
                    'value'       => 'value 6',
                    'create_date' => '2018-02-01 01:01:00',
                    'user_id'     => 3,
                ],
                [
                    'name'        => 'secret 7',
                    'value'       => 'value 7',
                    'create_date' => '2018-03-01 01:01:00',
                    'user_id'     => 4,
                ],
            ],
        ];
    }

    public function testGetByNamesSuccess(): void
    {
        /** @var Secret[] $result */
        $result = $this->store->getByNames(['secret 2', 'secret 5']);

        self::assertCount(2, $result);

        self::assertInstanceOf(Secret::class, $result['secret 2']);
        self::assertEquals('secret 2', $result['secret 2']->getName());
        self::assertEquals('value 2', $result['secret 2']->getValue());
        self::assertEquals(1, $result['secret 2']->getUserId());

        self::assertInstanceOf(Secret::class, $result['secret 5']);
        self::assertEquals('secret 5', $result['secret 5']->getName());
        self::assertEquals('value 5', $result['secret 5']->getValue());
        self::assertEquals(2, $result['secret 5']->getUserId());
    }

    public function testGetByIdFailed(): void
    {
        /** @var Secret[] $result */
        $result = $this->store->getByNames(['secret 20', 'secret 50']);

        self::assertCount(0, $result);
    }
}
