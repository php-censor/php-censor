<?php

declare(strict_types = 1);

namespace PHPCensor\Store;

use PDO;
use PHPCensor\Exception\HttpException;
use PHPCensor\Model\User;
use PHPCensor\Store;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class UserStore extends Store
{
    protected string $tableName  = 'users';

    protected ?string $modelName  = '\PHPCensor\Model\User';

    protected ?string $primaryKey = 'id';

    /**
     * Get a User by primary key (Id)
     *
     * @param int    $key
     * @param string $useConnection
     *
     * @return User|null
     */
    public function getByPrimaryKey(int $key, string $useConnection = 'read'): ?User
    {
        return $this->getById($key, $useConnection);
    }

    /**
     * Get a single User by Id.
     *
     * @param int    $id
     * @param string $useConnection
     *
     * @return User|null
     *
     * @throws HttpException
     */
    public function getById(int $id, string $useConnection = 'read'): ?User
    {
        if (\is_null($id)) {
            throw new HttpException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        $query = 'SELECT * FROM {{' . $this->tableName . '}} WHERE {{id}} = :id LIMIT 1';
        $stmt  = $this->databaseManager->getConnection($useConnection)->prepare($query);
        $stmt->bindValue(':id', $id);

        if ($stmt->execute()) {
            if ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return new User($this->storeRegistry, $data);
            }
        }

        return null;
    }

    /**
     * Get a single User by Email.
     *
     * @param string $email
     *
     * @throws HttpException
     *
     * @return User|null
     */
    public function getByEmail(string $email): ?User
    {
        if (\is_null($email)) {
            throw new HttpException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        $query = 'SELECT * FROM {{' . $this->tableName . '}} WHERE {{email}} = :email LIMIT 1';
        $stmt  = $this->databaseManager->getConnection('read')->prepare($query);

        $stmt->bindValue(':email', $email);

        if ($stmt->execute()) {
            if ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return new User($this->storeRegistry, $data);
            }
        }

        return null;
    }

    /**
     * Get a single User by Email or Name.
     *
     * @param string $emailOrName
     *
     * @throws HttpException
     *
     * @return User
     */
    public function getByEmailOrName(string $emailOrName): ?User
    {
        if (\is_null($emailOrName)) {
            throw new HttpException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        $query = 'SELECT * FROM {{' . $this->tableName . '}} WHERE {{email}} = :value OR {{name}} = :value LIMIT 1';
        $stmt  = $this->databaseManager->getConnection('read')->prepare($query);
        $stmt->bindValue(':value', $emailOrName);

        if ($stmt->execute()) {
            if ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return new User($this->storeRegistry, $data);
            }
        }

        return null;
    }

    /**
     * Get a single User by RememberKey.
     *
     * @param string $rememberKey
     *
     * @throws HttpException
     *
     * @return User
     */
    public function getByRememberKey(string $rememberKey): ?User
    {
        if (\is_null($rememberKey)) {
            throw new HttpException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        $query = 'SELECT * FROM {{' . $this->tableName . '}} WHERE {{remember_key}} = :remember_key LIMIT 1';
        $stmt  = $this->databaseManager->getConnection('read')->prepare($query);
        $stmt->bindValue(':remember_key', $rememberKey);

        if ($stmt->execute()) {
            if ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return new User($this->storeRegistry, $data);
            }
        }

        return null;
    }

    /**
     * Get multiple User by Name.
     *
     * @param string  $name
     * @param int $limit
     * @param string  $useConnection
     *
     * @return array
     *
     * @throws HttpException
     */
    public function getByName(string $name, int $limit = 1000, string $useConnection = 'read'): array
    {
        if (\is_null($name)) {
            throw new HttpException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        $query = 'SELECT * FROM {{' . $this->tableName . '}} WHERE {{name}} = :name LIMIT :limit';
        $stmt  = $this->databaseManager->getConnection($useConnection)->prepare($query);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $map = function ($item) {
                return new User($this->storeRegistry, $item);
            };
            $rtn = \array_map($map, $res);

            $count = \count($rtn);

            return ['items' => $rtn, 'count' => $count];
        } else {
            return ['items' => [], 'count' => 0];
        }
    }
}
