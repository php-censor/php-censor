<?php

namespace PHPCensor\Store;

use b8\Database;
use PHPCensor\Model\Environment;
use PHPCensor\Store;
use b8\Exception\HttpException;

class EnvironmentStore extends Store
{
    /**
     * @var string
     */
    protected $tableName = 'environment';

    /**
     * @var string
     */
    protected $modelName = '\PHPCensor\Model\Environment';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Get a Environment by primary key (Id)
     *
     * @param integer $key
     * @param string  $useConnection
     *
     * @return null|Environment
     */
    public function getByPrimaryKey($key, $useConnection = 'read')
    {
        return $this->getById($key, $useConnection);
    }

    /**
     * Get a single Environment by Id.
     *
     * @param integer $id
     * @param string  $useConnection
     *
     * @return null|Environment
     *
     * @throws HttpException
     */
    public function getById($id, $useConnection = 'read')
    {
        if (is_null($id)) {
            throw new HttpException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        $query = 'SELECT * FROM {{environment}} WHERE {{id}} = :id LIMIT 1';
        $stmt = Database::getConnection($useConnection)->prepareCommon($query);
        $stmt->bindValue(':id', $id);

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
     * @param integer $projectId
     * @param string  $useConnection
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getByProjectId($projectId, $useConnection = 'read')
    {
        if (is_null($projectId)) {
            throw new \Exception('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        $query = 'SELECT * FROM {{environment}} WHERE {{project_id}} = :project_id';
        $stmt  = Database::getConnection($useConnection)->prepareCommon($query);

        $stmt->bindValue(':project_id', $projectId);

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
