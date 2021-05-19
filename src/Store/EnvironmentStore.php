<?php

namespace PHPCensor\Store;

use Exception;
use PDO;
use PHPCensor\Exception\HttpException;
use PHPCensor\Model\Environment;
use PHPCensor\Store;

class EnvironmentStore extends Store
{
    /**
     * @var string
     */
    protected $tableName = 'environments';

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
     * @param int $key
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
     * @param int $id
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

        $query = 'SELECT * FROM {{' . $this->tableName . '}} WHERE {{id}} = :id LIMIT 1';
        $stmt  = $this->databaseManager->getConnection($useConnection)->prepare($query);
        $stmt->bindValue(':id', $id);

        if ($stmt->execute()) {
            if ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return new Environment($data);
            }
        }

        return null;
    }

    /**
     * Get a single Environment by Name.
     *
     * @param string $name
     * @param int    $projectId
     * @param string $useConnection
     *
     * @return null|Environment
     *
     * @throws HttpException
     */
    public function getByNameAndProjectId($name, $projectId, $useConnection = 'read')
    {
        if (is_null($name)) {
            throw new HttpException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        $query = 'SELECT * FROM {{' . $this->tableName . '}} WHERE {{name}} = :name AND {{project_id}} = :project_id LIMIT 1';
        $stmt  = $this->databaseManager->getConnection($useConnection)->prepare($query);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':project_id', $projectId);

        if ($stmt->execute()) {
            if ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return new Environment($data);
            }
        }

        return null;
    }

    /**
     * Get multiple Environment by Project id.
     *
     * @param int $projectId
     * @param string  $useConnection
     *
     * @return array
     *
     * @throws Exception
     */
    public function getByProjectId($projectId, $useConnection = 'read')
    {
        if (is_null($projectId)) {
            throw new HttpException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        $query = 'SELECT * FROM {{' . $this->tableName . '}} WHERE {{project_id}} = :project_id';
        $stmt  = $this->databaseManager->getConnection($useConnection)->prepare($query);

        $stmt->bindValue(':project_id', $projectId);

        if ($stmt->execute()) {
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
