<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Store;

use Phinx\Config\Config as PhinxConfig;
use Phinx\Console\Command\Migrate;
use PHPCensor\ArrayConfiguration;
use PHPCensor\DatabaseManager;
use PHPCensor\Model\Secret;
use PHPCensor\Store;
use PHPCensor\Store\SecretStore;
use PHPCensor\StoreRegistry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class SecretStoreMysqlTest extends TestCase
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
                    'type'     => 'mysql',
                    'name'     => env('MYSQL_DBNAME'),
                    'username' => env('MYSQL_USER'),
                    'password' => env('MYSQL_PASSWORD'),
                ],
            ],
        ];

        try {
            $this->connection = new \PDO(
                'mysql:host=127.0.0.1;dbname=' . env('MYSQL_DBNAME'),
                env('MYSQL_USER'),
                env('MYSQL_PASSWORD')
            );

            $this->connection->exec('DROP TABLE IF EXISTS `migrations`');
            $this->connection->exec('DROP TABLE IF EXISTS `webhook_requests`');
            $this->connection->exec('DROP TABLE IF EXISTS `build_errors`');
            $this->connection->exec('DROP TABLE IF EXISTS `build_metas`');
            $this->connection->exec('DROP TABLE IF EXISTS `builds`');
            $this->connection->exec('DROP TABLE IF EXISTS `environments`');
            $this->connection->exec('DROP TABLE IF EXISTS `projects`');
            $this->connection->exec('DROP TABLE IF EXISTS `project_groups`');
            $this->connection->exec('DROP TABLE IF EXISTS `secrets`');
            $this->connection->exec('DROP TABLE IF EXISTS `users`');

            $phinxSettings = [
                'paths' => [
                    'migrations' => ROOT_DIR . 'src/Migrations',
                ],
                'environments' => [
                    'default_migration_table' => 'migrations',
                    'default_database'        => 'php-censor',
                    'php-censor'              => [
                        'adapter' => 'mysql',
                        'host' => '127.0.0.1',
                        'name' => env('MYSQL_DBNAME'),
                        'user' => env('MYSQL_USER'),
                        'pass' => env('MYSQL_PASSWORD'),
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
                INSERT INTO `users` (`email`, `hash`, `name`, `is_admin`) VALUES
                ('user1@test.test', 'abc1', 'user 1', 1),
                ('user2@test.test', 'abc2', 'user 2', 0),
                ('user3@test.test', 'abc3', 'user 3', 0),
                ('user4@test.test', 'abc4', 'user 4', 0)
            ");

            $this->connection->exec("
                INSERT INTO `secrets` (`name`, `value`, `create_date`, `user_id`) VALUES
                ('secret 1', 'value 1', '2014-01-01 01:01:00', 1),
                ('secret 2', 'value 2', '2015-01-01 01:01:00', 1),
                ('secret 3', 'value 3', '2016-01-01 01:01:00', 1),
                ('secret 4', 'value 4', '2017-01-01 01:01:00', 1),
                ('secret 5', 'value 5', '2018-01-01 01:01:00', 2),
                ('secret 6', 'value 6', '2018-02-01 01:01:00', 3),
                ('secret 7', 'value 7', '2018-03-01 01:01:00', 4)
            ");
        } catch (\Throwable $e) {
            //var_dump($e);

            $this->connection = null;
        }

        $this->getConnection();

        $configuration       = new ArrayConfiguration($configurationArray);
        $databaseManager     = new DatabaseManager($configuration);
        $this->storeRegistry = new StoreRegistry($databaseManager);

        $this->store = new SecretStore($databaseManager, $this->storeRegistry);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        if (null !== $this->connection) {
            $this->connection->exec('DROP TABLE IF EXISTS `migrations`');
            $this->connection->exec('DROP TABLE IF EXISTS `webhook_requests`');
            $this->connection->exec('DROP TABLE IF EXISTS `build_errors`');
            $this->connection->exec('DROP TABLE IF EXISTS `build_metas`');
            $this->connection->exec('DROP TABLE IF EXISTS `builds`');
            $this->connection->exec('DROP TABLE IF EXISTS `environments`');
            $this->connection->exec('DROP TABLE IF EXISTS `projects`');
            $this->connection->exec('DROP TABLE IF EXISTS `project_groups`');
            $this->connection->exec('DROP TABLE IF EXISTS `secrets`');
            $this->connection->exec('DROP TABLE IF EXISTS `users`');

            $this->connection = null;
        }
    }

    protected function getConnection(): ?\PDO
    {
        if (null === $this->connection) {
            $this->markTestSkipped('Test skipped because MySQL database/user/extension doesn\'t exist.');
        }

        return $this->connection;
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
