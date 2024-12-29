<?php

declare(strict_types=1);

namespace PHPCensor;

use PHPCensor\Common\Exception\Exception;
use PHPCensor\Common\Exception\RuntimeException;
use PHPCensor\Common\Application\ConfigurationInterface;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class DatabaseManager
{
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
                $driver = $this->configuration->get('php-censor.database.type', 'pgsql');
                $dsn    = $driver . ':host=' . $server['host'];

                if (!\array_key_exists('pgsql-sslmode', $server)) {
                    $server['pgsql-sslmode'] = 'prefer';
                }

                $dsn .= ';sslmode=' . $server['pgsql-sslmode'];

                if (isset($server['port'])) {
                    $dsn .= ';port=' . (int)$server['port'];
                }

                $dsn .= ';dbname=' . $this->configuration->get('php-censor.database.name', '');

                $pdoOptions = [
                    \PDO::ATTR_PERSISTENT => false,
                    \PDO::ATTR_ERRMODE    => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_TIMEOUT    => 2,
                ];

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

            if (!$connection) {
                throw new RuntimeException('Could not connect to any ' . $type . ' servers.');
            }

            $this->connections[$type] = $connection;
        }

        return $this->connections[$type];
    }
}
