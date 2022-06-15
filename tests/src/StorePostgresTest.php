<?php

declare(strict_types=1);

namespace Tests\PHPCensor;

use Phinx\Config\Config as PhinxConfig;
use Phinx\Console\Command\Migrate;
use PHPCensor\ArrayConfiguration;
use PHPCensor\Common\Exception\InvalidArgumentException;
use PHPCensor\DatabaseManager;
use PHPCensor\Model\Project;
use PHPCensor\Model\ProjectGroup;
use PHPCensor\Store;
use PHPCensor\Store\ProjectGroupStore;
use PHPCensor\StoreRegistry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class StorePostgresTest extends TestCase
{
    private ?\PDO $connection = null;

    private StoreRegistry $storeRegistry;
    private Store $store;

    protected function setUp(): void
    {
        parent::setUp();

        $configurationArray = [
            'php-censor' => [
                'database' => [
                    'servers'  => [
                        'read'  => [
                            ['host' => '127.0.0.1'],
                        ],
                        'write' => [
                            ['host' => '127.0.0.1'],
                        ],
                    ],
                    'type'     => 'pgsql',
                    'name'     => env('POSTGRESQL_DBNAME'),
                    'username' => env('POSTGRESQL_USER'),
                    'password' => env('POSTGRESQL_PASSWORD'),
                ],
            ],
        ];

        try {
            $this->connection = new \PDO(
                'pgsql:host=127.0.0.1;dbname=' . env('POSTGRESQL_DBNAME'),
                env('POSTGRESQL_USER'),
                env('POSTGRESQL_PASSWORD')
            );

            $this->connection->exec('DROP TABLE IF EXISTS "migrations"');
            $this->connection->exec('DROP TABLE IF EXISTS "webhook_requests"');
            $this->connection->exec('DROP TABLE IF EXISTS "build_errors"');
            $this->connection->exec('DROP TABLE IF EXISTS "build_metas"');
            $this->connection->exec('DROP TABLE IF EXISTS "builds"');
            $this->connection->exec('DROP TABLE IF EXISTS "environments"');
            $this->connection->exec('DROP TABLE IF EXISTS "projects"');
            $this->connection->exec('DROP TABLE IF EXISTS "project_groups"');
            $this->connection->exec('DROP TABLE IF EXISTS "secrets"');
            $this->connection->exec('DROP TABLE IF EXISTS "users"');

            $phinxSettings = [
                'paths' => [
                    'migrations' => ROOT_DIR . 'src/Migrations',
                ],
                'environments' => [
                    'default_migration_table' => 'migrations',
                    'default_database'        => 'php-censor',
                    'php-censor'              => [
                        'adapter' => 'pgsql',
                        'host' => '127.0.0.1',
                        'name' => env('POSTGRESQL_DBNAME'),
                        'user' => env('POSTGRESQL_USER'),
                        'pass' => env('POSTGRESQL_PASSWORD'),
                    ],
                ],
            ];
            $phinxConfig = new PhinxConfig($phinxSettings);

            try {
                (new Migrate())
                    ->setConfig($phinxConfig)
                    ->setName('php-censor-migrations:migrate')
                    ->run(new ArgvInput([]), new ConsoleOutput(OutputInterface::VERBOSITY_QUIET));
            } catch (\Throwable $e) {
                //var_dump($e);
            }

            $this->connection->exec("
                INSERT INTO \"users\" (\"email\", \"hash\", \"name\", \"is_admin\") VALUES
                ('user1@test.test', 'abc1', 'user 1', 1),
                ('user2@test.test', 'abc2', 'user 2', 0),
                ('user3@test.test', 'abc3', 'user 3', 0),
                ('user4@test.test', 'abc4', 'user 4', 0)
            ");

            $this->connection->exec("
                INSERT INTO \"project_groups\" (\"title\", \"create_date\", \"user_id\") VALUES
                ('group 1', '2014-01-01 01:01:00', 1),
                ('group 2', '2015-01-01 01:01:00', 1),
                ('group 3', '2016-01-01 01:01:00', 1),
                ('group 4', '2017-01-01 01:01:00', 1),
                ('group 5', '2018-01-01 01:01:00', 2),
                ('group 6', '2018-02-01 01:01:00', 3),
                ('group 7', '2018-03-01 01:01:00', 4)
            ");
        } catch (\Throwable $e) {
            //var_dump($e);

            $this->connection = null;
        }

        $this->getConnection();

        $configuration       = new ArrayConfiguration($configurationArray);
        $databaseManager     = new DatabaseManager($configuration);
        $this->storeRegistry = new StoreRegistry($databaseManager);

        $this->store = new ProjectGroupStore($databaseManager, $this->storeRegistry);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        if (null !== $this->connection) {
            $this->connection->exec('DROP TABLE IF EXISTS "migrations"');
            $this->connection->exec('DROP TABLE IF EXISTS "webhook_requests"');
            $this->connection->exec('DROP TABLE IF EXISTS "build_errors"');
            $this->connection->exec('DROP TABLE IF EXISTS "build_metas"');
            $this->connection->exec('DROP TABLE IF EXISTS "builds"');
            $this->connection->exec('DROP TABLE IF EXISTS "environments"');
            $this->connection->exec('DROP TABLE IF EXISTS "projects"');
            $this->connection->exec('DROP TABLE IF EXISTS "project_groups"');
            $this->connection->exec('DROP TABLE IF EXISTS "secrets"');
            $this->connection->exec('DROP TABLE IF EXISTS "users"');

            $this->connection = null;
        }
    }

    protected function getConnection(): ?\PDO
    {
        if (null === $this->connection) {
            $this->markTestSkipped('Test skipped because PostgreSQL database/user/extension doesn\'t exist.');
        }

        return $this->connection;
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
        $model = new ProjectGroup($this->storeRegistry);

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
            $model = new Project($this->storeRegistry);
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
            $model = new Project($this->storeRegistry);
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
