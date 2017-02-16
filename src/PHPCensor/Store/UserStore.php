<?php

namespace PHPCensor\Store;

use PHPCensor\Store;
use b8\Database;
use b8\Exception\HttpException;
use PHPCensor\Model\User;

/**
 * @author Dan Cryer <dan@block8.co.uk>
 */
class UserStore extends Store
{
    protected $tableName   = 'user';
    protected $modelName   = '\PHPCensor\Model\User';
    protected $primaryKey  = 'id';

    /**
     * Get a User by primary key (Id)
     */
    public function getByPrimaryKey($value, $useConnection = 'read')
    {
        return $this->getById($value, $useConnection);
    }

    /**
     * Get a single User by Id.
     * @return null|User
     */
    public function getById($value, $useConnection = 'read')
    {
        if (is_null($value)) {
            throw new HttpException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        $query = 'SELECT * FROM {{user}} WHERE {{id}} = :id LIMIT 1';
        $stmt = Database::getConnection($useConnection)->prepareCommon($query);
        $stmt->bindValue(':id', $value);

        if ($stmt->execute()) {
            if ($data = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                return new User($data);
            }
        }

        return null;
    }

    /**
     *
     * Get a single User by Email.
     *
     * @param string $value
     *
     * @throws HttpException
     *
     * @return User
     */
    public function getByEmail($value)
    {
        if (is_null($value)) {
            throw new HttpException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        $query = 'SELECT * FROM {{user}} WHERE {{email}} = :email LIMIT 1';
        $stmt  = Database::getConnection()->prepareCommon($query);

        $stmt->bindValue(':email', $value);

        if ($stmt->execute()) {
            if ($data = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                return new User($data);
            }
        }

        return null;
    }

    /**
     *
     * Get a single User by Email or Name.
     *
     * @param string $value
     *
     * @throws HttpException
     *
     * @return User
     */
    public function getByEmailOrName($value)
    {
        if (is_null($value)) {
            throw new HttpException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        $query = 'SELECT * FROM {{user}} WHERE {{email}} = :value OR {{name}} = :value LIMIT 1';
        $stmt = Database::getConnection()->prepareCommon($query);
        $stmt->bindValue(':value', $value);

        if ($stmt->execute()) {
            if ($data = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                return new User($data);
            }
        }

        return null;
    }

    /**
     * Get multiple User by Name.
     * @return array
     */
    public function getByName($value, $limit = 1000, $useConnection = 'read')
    {
        if (is_null($value)) {
            throw new HttpException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        $query = 'SELECT * FROM {{user}} WHERE {{name}} = :name LIMIT :limit';
        $stmt = Database::getConnection($useConnection)->prepareCommon($query);
        $stmt->bindValue(':name', $value);
        $stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);

        if ($stmt->execute()) {
            $res = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $map = function ($item) {
                return new User($item);
            };
            $rtn = array_map($map, $res);

            $count = count($rtn);

            return ['items' => $rtn, 'count' => $count];
        } else {
            return ['items' => [], 'count' => 0];
        }
    }
}
