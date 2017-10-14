<?php

namespace PHPCensor\Store;

use b8\Database;
use PHPCensor\Model\BuildError;
use b8\Exception\HttpException;
use PHPCensor\Store;

class BuildErrorStore extends Store
{
    protected $tableName  = 'build_error';
    protected $modelName  = '\PHPCensor\Model\BuildError';
    protected $primaryKey = 'id';

    /**
     * Get a BuildError by primary key (Id)
     *
     * @param integer $key
     * @param string  $useConnection
     *
     * @return null|BuildError
     */
    public function getByPrimaryKey($key, $useConnection = 'read')
    {
        return $this->getById($key, $useConnection);
    }

    /**
     * Get a single BuildError by Id.
     *
     * @param integer $id
     * @param string  $useConnection
     *
     * @return null|BuildError
     *
     * @throws HttpException
     */
    public function getById($id, $useConnection = 'read')
    {
        if (is_null($id)) {
            throw new HttpException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        $query = 'SELECT * FROM {{build_error}} WHERE {{id}} = :id LIMIT 1';
        $stmt = Database::getConnection($useConnection)->prepareCommon($query);
        $stmt->bindValue(':id', $id);

        if ($stmt->execute()) {
            if ($data = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                return new BuildError($data);
            }
        }

        return null;
    }

    /**
     * Get multiple BuildError by BuildId.
     *
     * @param integer $buildId
     * @param integer $limit
     * @param string  $useConnection
     *
     * @return array
     *
     * @throws HttpException
     */
    public function getByBuildId($buildId, $limit = 1000, $useConnection = 'read')
    {
        if (is_null($buildId)) {
            throw new HttpException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }


        $query = 'SELECT * FROM {{build_error}} WHERE {{build_id}} = :build_id LIMIT :limit';
        $stmt = Database::getConnection($useConnection)->prepareCommon($query);
        $stmt->bindValue(':build_id', $buildId);
        $stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);

        if ($stmt->execute()) {
            $res = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $map = function ($item) {
                return new BuildError($item);
            };
            $rtn = array_map($map, $res);

            $count = count($rtn);

            return ['items' => $rtn, 'count' => $count];
        } else {
            return ['items' => [], 'count' => 0];
        }
    }

    /**
     * Get a list of errors for a given build, since a given time.
     *
     * @param integer $buildId
     * @param string  $since date string
     *
     * @return array
     */
    public function getErrorsForBuild($buildId, $since = null)
    {
        $query = 'SELECT * FROM {{build_error}} WHERE {{build_id}} = :build';

        if (!is_null($since)) {
            $query .= ' AND created_date > :since';
        }

        $query .= ' LIMIT 15000';

        $stmt = Database::getConnection('read')->prepareCommon($query);

        $stmt->bindValue(':build', $buildId, \PDO::PARAM_INT);

        if (!is_null($since)) {
            $stmt->bindValue(':since', $since);
        }

        if ($stmt->execute()) {
            $res = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $map = function ($item) {
                return new BuildError($item);
            };
            $rtn = array_map($map, $res);

            return $rtn;
        } else {
            return [];
        }
    }

    /**
     * Gets the total number of errors for a given build.
     *
     * @param integer $buildId
     *
     * @return array
     */
    public function getErrorTotalForBuild($buildId)
    {
        $query = 'SELECT COUNT(*) AS {{total}} FROM {{build_error}}
                    WHERE {{build_id}} = :build';

        $stmt = Database::getConnection('read')->prepareCommon($query);

        $stmt->bindValue(':build', $buildId, \PDO::PARAM_INT);

        if ($stmt->execute()) {
            $res = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $res['total'];
        } else {
            return [];
        }
    }
}
