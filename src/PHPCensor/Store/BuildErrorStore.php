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
     */
    public function getByPrimaryKey($value, $useConnection = 'read')
    {
        return $this->getById($value, $useConnection);
    }

    /**
     * Get a single BuildError by Id.
     * @return null|BuildError
     */
    public function getById($value, $useConnection = 'read')
    {
        if (is_null($value)) {
            throw new HttpException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        $query = 'SELECT * FROM {{build_error}} WHERE {{id}} = :id LIMIT 1';
        $stmt = Database::getConnection($useConnection)->prepareCommon($query);
        $stmt->bindValue(':id', $value);

        if ($stmt->execute()) {
            if ($data = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                return new BuildError($data);
            }
        }

        return null;
    }

    /**
     * Get multiple BuildError by BuildId.
     * @return array
     */
    public function getByBuildId($value, $limit = 1000, $useConnection = 'read')
    {
        if (is_null($value)) {
            throw new HttpException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }


        $query = 'SELECT * FROM {{build_error}} WHERE {{build_id}} = :build_id LIMIT :limit';
        $stmt = Database::getConnection($useConnection)->prepareCommon($query);
        $stmt->bindValue(':build_id', $value);
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
     * @param $buildId
     * @param string $since date string
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
     * @param $buildId
     * @param string $since date string
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
