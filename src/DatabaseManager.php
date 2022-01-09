<?php

declare(strict_types=1);

namespace PHPCensor;

use PHPCensor\Common\Exception\Exception;
use PHPCensor\Common\Exception\RuntimeException;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class DatabaseManager
{
    public const MYSQL_TYPE      = 'mysql';
    public const POSTGRESQL_TYPE = 'pgsql';

    private ConfigurationInterface $configuration;

    private array $connections = [
        'read'  => null,
        'write' => null
    ];

    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @throws Exception
     */
    public function getConnection(string $type = 'read'): DatabaseConnection
    {
        if (null === $this->connections[$type]) {
            $servers = (array)$this->configuration->get("php-censor.database.servers.{$type}", []);
            \shuffle($servers);

            $connection = null;
            while (\count($servers)) {
                $server = \array_shift($servers);
                $driver = $this->configuration->get('php-censor.database.type', self::POSTGRESQL_TYPE);
                $dsn    = $driver . ':host=' . $server['host'];

                if (self::POSTGRESQL_TYPE === $driver) {
                    if (!\array_key_exists('pgsql-sslmode', $server)) {
                        $server['pgsql-sslmode'] = 'prefer';
                    }

                    $dsn .= ';sslmode=' . $server['pgsql-sslmode'];
                }

                if (isset($server['port'])) {
                    $dsn .= ';port=' . (int)$server['port'];
                }

                $dsn .= ';dbname=' . $this->configuration->get('php-censor.database.name', '');

                $pdoOptions = [
                    \PDO::ATTR_PERSISTENT => false,
                    \PDO::ATTR_ERRMODE    => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_TIMEOUT    => 2,
                ];

                if (self::MYSQL_TYPE === $driver) {
                    $pdoOptions[\PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES 'UTF8'";
                }

                try {
                    $connection = new DatabaseConnection(
                        $dsn,
                        $this->configuration->get('php-censor.database.username', ''),
                        $this->configuration->get('php-censor.database.password', ''),
                        $pdoOptions
                    );
                } catch (\PDOException $ex) {
                    $connection = false;
                }

                if ($connection) {
                    break;
                }
            }

            // No connection? Oh dear.
            if (!$connection && $type === 'read') {
                throw new RuntimeException('Could not connect to any ' . $type . ' servers.');
            }

            $this->connections[$type] = $connection;
        }

        return $this->connections[$type];
    }
}
