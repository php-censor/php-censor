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
    /**
     * @var string
     */
    protected $tableName  = 'user';

    /**
     * @var string
     */
    protected $modelName  = '\PHPCensor\Model\User';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Get a User by primary key (Id)
     *
     * @param integer $key
     * @param string  $useConnection
     *
     * @return null|User
     */
    public function getByPrimaryKey($key, $useConnection = 'read')
    {
        return $this->getById($key, $useConnection);
    }

    /**
     * Get a single User by Id.
     *
     * @param integer $id
     * @param string  $useConnection
     *
     * @return null|User
     *
     * @throws HttpException
     */
    public function getById($id, $useConnection = 'read')
    {
        if (is_null($id)) {
            throw new HttpException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        $query = 'SELECT * FROM {{user}} WHERE {{id}} = :id LIMIT 1';
        $stmt = Database::getConnection($useConnection)->prepareCommon($query);
        $stmt->bindValue(':id', $id);

        if ($stmt->execute()) {
            if ($data = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                return new User($data);
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
     * @return User
     */
    public function getByEmail($email)
    {
        if (is_null($email)) {
            throw new HttpException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        $query = 'SELECT * FROM {{user}} WHERE {{email}} = :email LIMIT 1';
        $stmt  = Database::getConnection()->prepareCommon($query);

        $stmt->bindValue(':email', $email);

        if ($stmt->execute()) {
            if ($data = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                return new User($data);
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
    public function getByEmailOrName($emailOrName)
    {
        if (is_null($emailOrName)) {
            throw new HttpException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        $query = 'SELECT * FROM {{user}} WHERE {{email}} = :value OR {{name}} = :value LIMIT 1';
        $stmt  = Database::getConnection()->prepareCommon($query);
        $stmt->bindValue(':value', $emailOrName);

        if ($stmt->execute()) {
            if ($data = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                return new User($data);
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
    public function getByRememberKey($rememberKey)
    {
        if (is_null($rememberKey)) {
            throw new HttpException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        $query = 'SELECT * FROM {{user}} WHERE {{remember_key}} = :remember_key LIMIT 1';
        $stmt  = Database::getConnection()->prepareCommon($query);
        $stmt->bindValue(':remember_key', $rememberKey);

        if ($stmt->execute()) {
            if ($data = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                return new User($data);
            }
        }

        return null;
    }

    /**
     * Get multiple User by Name.
     *
     * @param string  $name
     * @param integer $limit
     * @param string  $useConnection
     *
     * @return array
     *
     * @throws HttpException
     */
    public function getByName($name, $limit = 1000, $useConnection = 'read')
    {
        if (is_null($name)) {
            throw new HttpException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        $query = 'SELECT * FROM {{user}} WHERE {{name}} = :name LIMIT :limit';
        $stmt = Database::getConnection($useConnection)->prepareCommon($query);
        $stmt->bindValue(':name', $name);
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
