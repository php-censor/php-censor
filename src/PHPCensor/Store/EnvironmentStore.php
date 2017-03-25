<?php

namespace PHPCensor\Store;

use b8\Database;
use PHPCensor\Model\Environment;
use PHPCensor\Store;
use b8\Exception\HttpException;

class EnvironmentStore extends Store
{
    protected $tableName   = 'environment';
    protected $modelName   = '\PHPCensor\Model\Environment';
    protected $primaryKey  = 'id';

    /**
     * Get a Environment by primary key (Id)
     * @param int $value
     * @param string $useConnection
     * @return null|Environment
     */
    public function getByPrimaryKey($value, $useConnection = 'read')
    {
        return $this->getById($value, $useConnection);
    }

    /**
     * Get a single Environment by Id.
     * @param $value
     * @param string $useConnection
     * @return null|Environment
     * @throws HttpException
     */
    public function getById($value, $useConnection = 'read')
    {
        if (is_null($value)) {
            throw new HttpException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        $query = 'SELECT * FROM {{environment}} WHERE {{id}} = :id LIMIT 1';
        $stmt = Database::getConnection($useConnection)->prepareCommon($query);
        $stmt->bindValue(':id', $value);

        if ($stmt->execute()) {
            if ($data = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                return new Environment($data);
            }
        }

        return null;
    }

    /**
     * Get multiple Environment by Project id.
     * 
     * @param integer $value
     * @param string  $useConnection
     * 
     * @return array
     * 
     * @throws \Exception
     */
    public function getByProjectId($value, $useConnection = 'read')
    {
        if (is_null($value)) {
            throw new \Exception('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        $query = 'SELECT * FROM {{environment}} WHERE {{project_id}} = :project_id';
        $stmt  = Database::getConnection($useConnection)->prepareCommon($query);

        $stmt->bindValue(':project_id', $value);

        if ($stmt->execute()) {
            $res = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $map = function ($item) {
                return new Environment($item);
            };
            $rtn = array_map($map, $res);

            $count = count($rtn);

            return ['items' => $rtn, 'count' => $count];
        } else {
            return ['items' => [], 'count' => 0];
        }
    }
}
