<?php

declare(strict_types=1);

namespace Tests\PHPCensor;

use Phinx\Config\Config as PhinxConfig;
use Phinx\Console\Command\Migrate;
use PHPCensor\ArrayConfiguration;
use PHPCensor\Common\Application\ConfigurationInterface;
use PHPCensor\DatabaseManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class BasePostgresTestCase extends TestCase
{
    protected ?\PDO $connection = null;

    protected ConfigurationInterface $configuration;
    protected DatabaseManager $databaseManager;

    protected function generatePhinxConfig(): PhinxConfig
    {
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

        return new PhinxConfig($phinxSettings);
    }

    protected function migrateDatabaseScheme(): void
    {
        try {
            (new Migrate())
                ->setConfig($this->generatePhinxConfig())
                ->setName('php-censor-migrations:migrate')
                ->run(new ArgvInput([]), new ConsoleOutput(OutputInterface::VERBOSITY_QUIET));
        } catch (\Throwable $e) {
            if (!env('SKIP_DB_TESTS')) {
                throw $e;
            }
        }
    }

    protected function getTestData(): array
    {
        return [];
    }

    protected function migrateDatabaseData(): void
    {
        $testData = $this->getTestData();
        foreach ($testData as $table => $data) {
            $fieldNames = \array_keys($data[0]);
            foreach ($fieldNames as &$fieldName) {
                $fieldName = \sprintf('"%s"', $fieldName);
            }
            unset($fieldName);
            $fieldsString = \implode(',', $fieldNames);

            $recordStrings = [];
            foreach ($data as $record) {
                foreach ($record as &$fieldValue) {
                    if (\is_string($fieldValue)) {
                        $fieldValue = \sprintf("'%s'", $fieldValue);
                    }
                }
                unset($fieldValue);
                $recordStrings[] = '(' . \implode(',', $record) . ')';
            }
            $recordsStrings = \implode(',', $recordStrings);

            $query = \sprintf('INSERT INTO "%s" (%s) VALUES %s', $table, $fieldsString, $recordsStrings);

            $this->connection->exec($query);
        }
    }

    protected function dropTables(): void
    {
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
    }

    protected function generateAppConfiguration(): ConfigurationInterface
    {
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

        return new ArrayConfiguration($configurationArray);
    }

    protected function setUp(): void
    {
        parent::setUp();

        try {
            $this->connection = new \PDO(
                'pgsql:host=127.0.0.1;dbname=' . env('POSTGRESQL_DBNAME'),
                env('POSTGRESQL_USER'),
                env('POSTGRESQL_PASSWORD')
            );

            $this->dropTables();
            $this->migrateDatabaseScheme();
            $this->migrateDatabaseData();
        } catch (\Throwable $e) {
            if (!env('SKIP_DB_TESTS')) {
                throw $e;
            }

            $this->connection = null;
        }

        $this->getConnection();

        $this->configuration   = $this->generateAppConfiguration();
        $this->databaseManager = new DatabaseManager($this->configuration);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        if (null !== $this->connection) {
            $this->dropTables();

            $this->connection = null;
        }
    }

    protected function getConnection(): ?\PDO
    {
        if (null === $this->connection) {
            if (env('SKIP_DB_TESTS')) {
                $this->markTestSkipped('Test skipped because PostgreSQL database/user/extension doesn\'t exist.');
            } else {
                $this->fail('Test failed because PostgreSQL database/user/extension doesn\'t exist.');
            }
        }

        return $this->connection;
    }
}
