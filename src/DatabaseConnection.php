<?php

declare(strict_types = 1);

namespace PHPCensor;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class DatabaseConnection
{
    private ?\PDO $pdoConnection = null;

    private string $dsn;

    private ?string $username;

    private ?string $password;

    private ?array $options;

    private string $sequencePattern;

    public function __construct(
        string $dsn,
        ?string $username = null,
        ?string $password = null,
        ?array $options = null,
        string $sequencePattern = '%s_id_seq'
    ) {
        $this->sequencePattern = $sequencePattern;

        $this->dsn      = $dsn;
        $this->username = $username;
        $this->password = $password;
        $this->options  = $options;
    }

    public function getPdo(): \PDO
    {
        if (null === $this->pdoConnection) {
            $this->pdoConnection = new \PDO($this->dsn, $this->username, $this->password, $this->options);
        }

        return $this->pdoConnection;
    }

    public function setPdo(\PDO $pdoConnection): self
    {
        $this->pdoConnection = $pdoConnection;

        return $this;
    }

    public function quoteNames(string $query): string
    {
        $driver  = $this->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME);
        $pattern = '\1';
        if (DatabaseManager::MYSQL_TYPE === $driver) {
            $pattern = '`\1`';
        } elseif (DatabaseManager::POSTGRESQL_TYPE === $driver) {
            $pattern = '"\1"';
        }

        return \preg_replace('#{{(.*?)}}#m', $pattern, $query);
    }

    public function lastInsertId(string $tableName): int
    {
        if (DatabaseManager::POSTGRESQL_TYPE === $this->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME)) {
            return (int)$this->getPdo()->lastInsertId(\sprintf("\"{$this->sequencePattern}\"", $tableName));
        }

        return (int)$this->getPdo()->lastInsertId();
    }

    /**
     * @param string $query
     * @param array  $options
     *
     * @return false|\PDOStatement
     */
    public function prepare(string $query, array $options = [])
    {
        return $this->getPdo()->prepare($this->quoteNames($query), $options);
    }

    /**
     * @param string $statement
     *
     * @return int|false
     */
    public function exec(string $statement)
    {
        return $this->getPdo()->exec($this->quoteNames($statement));
    }

    /**
     * @param string $statement
     *
     * @return false|\PDOStatement
     */
    public function query(string $statement)
    {
        return $this->getPdo()->query($this->quoteNames($statement));
    }

    public function beginTransaction(): bool
    {
        return $this->getPdo()->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->getPdo()->commit();
    }

    public function rollBack(): bool
    {
        return $this->getPdo()->rollBack();
    }

    public function inTransaction(): bool
    {
        return $this->getPdo()->inTransaction();
    }
}
