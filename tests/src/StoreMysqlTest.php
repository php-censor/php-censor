<?php

declare(strict_types=1);

namespace Tests\PHPCensor;

use PHPCensor\Common\Exception\InvalidArgumentException;
use PHPCensor\Model\Project;
use PHPCensor\Model\ProjectGroup;
use PHPCensor\Store;
use PHPCensor\Store\ProjectGroupStore;

class StoreMysqlTest extends BaseMysqlTestCase
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

    public function testGetByIdSuccess(): void
    {
        /** @var ProjectGroup $newModel */
        $model = $this->store->getById(4);

        self::assertInstanceOf(ProjectGroup::class, $model);
        self::assertEquals(4, $model->getId());
        self::assertEquals('group 4', $model->getTitle());
        self::assertEquals(1, $model->getUserId());
    }

    public function testGetByIdFailed(): void
    {
        /** @var ProjectGroup $newModel */
        $model = $this->store->getById(10);

        self::assertEquals(null, $model);
    }

    public function testGetWhere(): void
    {
        $data = $this->store->getWhere([], 3, 1, ['id' => 'DESC']);
        self::assertEquals(7, $data['count']);
        self::assertEquals(3, \count($data['items']));

        self::assertEquals(6, $data['items'][0]->getId());
        self::assertEquals(5, $data['items'][1]->getId());
        self::assertEquals(4, $data['items'][2]->getId());

        $data = $this->store->getWhere(['project_groups.user_id' => 1], 100, 0, ['id' => 'ASC']);
        self::assertEquals(4, $data['count']);
        self::assertEquals(4, \count($data['items']));

        self::assertEquals(1, $data['items'][0]->getId());
        self::assertEquals(2, $data['items'][1]->getId());
        self::assertEquals(3, $data['items'][2]->getId());
        self::assertEquals(4, $data['items'][3]->getId());

        try {
            $data = $this->store->getWhere(['' => 0], 100, 0, ['id' => 'ASC']);
        } catch (InvalidArgumentException $e) {
            self::assertEquals('You cannot have an empty field name.', $e->getMessage());
        }

        try {
            $data = $this->store->getWhere(['unknown' => 0], 1, 0, ['id' => 'ASC']);
        } catch (\PDOException $e) {
            self::assertInstanceOf('\PDOException', $e);
        }
    }

    public function testSaveByInsert()
    {
        $model = new ProjectGroup();

        $model->setTitle('group 8');
        $model->setCreateDate(new \DateTime());
        $model->setUserId(1);

        $this->store->save($model);

        /** @var ProjectGroup $newModel */
        $newModel = $this->store->getById(8);

        self::assertEquals(8, $newModel->getId());
        self::assertEquals('group 8', $newModel->getTitle());
        self::assertEquals(1, $newModel->getUserId());
    }

    public function testSaveByUpdate()
    {
        $model = $this->store->getById(7);
        $model->setTitle('group 100');

        $this->store->save($model);
        $newModel = $this->store->getById(7);

        self::assertEquals(7, $newModel->getId());
        self::assertEquals('group 100', $newModel->getTitle());

        // Without changes
        $model = $this->store->getById(6);

        $this->store->save($model);

        $newModel = $this->store->getById(6);

        self::assertEquals(6, $newModel->getId());
        self::assertEquals('group 6', $newModel->getTitle());

        // Wrong Model
        try {
            $model = new Project();
            $model->setId(10);
            $model->setCreateDate(new \DateTime());
            $model->setUserId(1);

            $this->store->save($model);
        } catch (InvalidArgumentException $e) {
            self::assertEquals(
                'PHPCensor\Model\Project is an invalid model type for this store.',
                $e->getMessage()
            );
        }
    }

    public function testDelete()
    {
        $model = $this->store->getById(5);
        $this->store->delete($model);

        $newModel = $this->store->getById(5);

        self::assertEquals(null, $newModel);

        // Wrong Model
        try {
            $model = new Project();
            $model->setId(20);
            $model->setCreateDate(new \DateTime());
            $model->setUserId(5);

            $this->store->delete($model);
        } catch (InvalidArgumentException $e) {
            self::assertEquals(
                'PHPCensor\Model\Project is an invalid model type for this store.',
                $e->getMessage()
            );
        }
    }
}
