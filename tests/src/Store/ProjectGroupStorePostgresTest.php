<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Store;

use PHPCensor\Model\ProjectGroup;
use PHPCensor\Store;
use PHPCensor\Store\ProjectGroupStore;
use Tests\PHPCensor\BasePostgresTestCase;

class ProjectGroupStorePostgresTest extends BasePostgresTestCase
{
    private Store $store;

    protected function setUp(): void
    {
        parent::setUp();

        $this->store = new ProjectGroupStore($this->databaseManager);
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
            'project_groups' => [
                [
                    'title'       => 'group 1',
                    'create_date' => '2014-01-01 01:01:00',
                    'user_id'     => 1,
                ],
                [
                    'title'       => 'group 2',
                    'create_date' => '2015-01-01 01:01:00',
                    'user_id'     => 1,
                ],
                [
                    'title'       => 'group 3',
                    'create_date' => '2016-01-01 01:01:00',
                    'user_id'     => 1,
                ],
                [
                    'title'       => 'group 4',
                    'create_date' => '2017-01-01 01:01:00',
                    'user_id'     => 1,
                ],
                [
                    'title'       => 'group 5',
                    'create_date' => '2018-01-01 01:01:00',
                    'user_id'     => 2,
                ],
                [
                    'title'       => 'group 6',
                    'create_date' => '2018-02-01 01:01:00',
                    'user_id'     => 3,
                ],
                [
                    'title'       => 'group 7',
                    'create_date' => '2018-03-01 01:01:00',
                    'user_id'     => 4,
                ],
            ],
        ];
    }

    public function testGetByTitleSuccess(): void
    {
        /** @var ProjectGroup $newModel */
        $model = $this->store->getByTitle('group 5');

        self::assertInstanceOf(ProjectGroup::class, $model);
        self::assertEquals(5, $model->getId());
        self::assertEquals('group 5', $model->getTitle());
        self::assertEquals(2, $model->getUserId());
    }

    public function testGetByTitleFailed(): void
    {
        /** @var ProjectGroup $newModel */
        $model = $this->store->getByTitle('group 10');

        self::assertEquals(null, $model);
    }
}
