<?php

declare(strict_types = 1);

namespace PHPCensor;

class DatabaseConnection
{
    private \PDO $pdoConnection;

    private string $sequencePattern;

    /**
     * @param string      $dsn
     * @param string|null $username
     * @param string|null $password
     * @param array|null  $options
     */
    public function __construct(
        string $dsn,
        ?string $username = null,
        ?string $password = null,
        ?array $options = null,
        string $sequencePattern = '%s_id_seq'
    ) {
        $this->sequencePattern = $sequencePattern;
        $this->pdoConnection   = new \PDO($dsn, $username, $password, $options);
    }

    public function getPdo(): \PDO
    {
        return $this->pdoConnection;
    }

    public function quoteNames(string $query): string
    {
        $driver  = $this->pdoConnection->getAttribute(\PDO::ATTR_DRIVER_NAME);
        $pattern = '\1';
        if (DatabaseManager::MYSQL_TYPE === $driver) {
            $pattern = '`\1`';
        } elseif (DatabaseManager::MYSQL_TYPE === $driver) {
            $pattern = '"\1"';
        }

        return \preg_replace('#{{(.*?)}}#m', $pattern, $query);
    }

    public function lastInsertId(string $tableName): int
    {
        if (DatabaseManager::POSTGRESQL_TYPE === $this->pdoConnection->getAttribute(\PDO::ATTR_DRIVER_NAME)) {
            return (int)$this->pdoConnection->lastInsertId(\sprintf("\"{$this->sequencePattern}\"", $tableName));
        }

        return (int)$this->pdoConnection->lastInsertId();
    }

    /**
     * @param string $query
     * @param array  $options
     *
     * @return false|\PDOStatement
     */
    public function prepare(string $query, array $options = [])
    {
        return $this->pdoConnection->prepare($this->quoteNames($query), $options);
    }

    /**
     * @param string $statement
     *
     * @return int|false
     */
    public function exec(string $statement)
    {
        return $this->pdoConnection->exec($statement);
    }

    /**
     * @param string $statement
     *
     * @return false|\PDOStatement
     */
    public function query(string $statement)
    {
        return $this->pdoConnection->query($statement);
    }

    public function beginTransaction(): bool
    {
        return $this->pdoConnection->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->pdoConnection->commit();
    }

    public function rollBack(): bool
    {
        return $this->pdoConnection->rollBack();
    }

    public function inTransaction(): bool
    {
        return $this->pdoConnection->inTransaction();
    }
}
