<?php

namespace PHPCensor\Store;

use PHPCensor\Store;
use b8\Database;
use PHPCensor\Model\BuildMeta;
use b8\Exception\HttpException;

class BuildMetaStore extends Store
{
    /**
     * @var string
     */
    protected $tableName  = 'build_meta';

    /**
     * @var string
     */
    protected $modelName  = '\PHPCensor\Model\BuildMeta';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Get a BuildMeta by primary key (Id)
     *
     * @param integer $key
     * @param string  $useConnection
     *
     * @return null|BuildMeta
     */
    public function getByPrimaryKey($key, $useConnection = 'read')
    {
        return $this->getById($key, $useConnection);
    }

    /**
     * Get a single BuildMeta by Id.
     *
     * @param integer $id
     * @param string  $useConnection
     *
     * @return null|BuildMeta
     *
     * @throws HttpException
     */
    public function getById($id, $useConnection = 'read')
    {
        if (is_null($id)) {
            throw new HttpException('id passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        $query = 'SELECT * FROM {{build_meta}} WHERE {{id}} = :id LIMIT 1';
        $stmt = Database::getConnection($useConnection)->prepareCommon($query);
        $stmt->bindValue(':id', $id);

        if ($stmt->execute()) {
            if ($data = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                return new BuildMeta($data);
            }
        }

        return null;
    }

    /**
     * @param integer $buildId
     * @param string  $key
     *
     * @return null|BuildMeta
     *
     * @throws HttpException
     */
    public function getByKey($buildId, $key)
    {
        if (is_null($buildId)) {
            throw new HttpException('buildId passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        if (!$key) {
            throw new HttpException('key passed to ' . __FUNCTION__ . ' cannot be empty.');
        }

        $query = 'SELECT * FROM {{build_meta}} WHERE {{build_id}} = :build_id AND {{meta_key}} = :meta_key LIMIT 1';
        $stmt = Database::getConnection()->prepareCommon($query);
        $stmt->bindValue(':build_id', $buildId);
        $stmt->bindValue(':meta_key', $key);

        if ($stmt->execute()) {
            if ($data = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                return new BuildMeta($data);
            }
        }

        return null;
    }

    /**
     * Get multiple BuildMeta by BuildId.
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

        $query = 'SELECT * FROM {{build_meta}} WHERE {{build_id}} = :build_id LIMIT :limit';
        $stmt = Database::getConnection($useConnection)->prepareCommon($query);
        $stmt->bindValue(':build_id', $buildId);
        $stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);

        if ($stmt->execute()) {
            $res = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $map = function ($item) {
                return new BuildMeta($item);
            };
            $rtn = array_map($map, $res);

            $count = count($rtn);

            return ['items' => $rtn, 'count' => $count];
        } else {
            return ['items' => [], 'count' => 0];
        }
    }

    /**
     * Only used by an upgrade migration to move errors from build_meta to build_error
     *
     * @param integer $limit
     *
     * @return array
     */
    public function getErrorsForUpgrade($limit)
    {
        $query = 'SELECT * FROM {{build_meta}}
                    WHERE {{meta_key}} IN (\'phpmd-data\', \'phpcs-data\', \'phpdoccheck-data\', \'technical_debt-data\')
                    ORDER BY {{id}} ASC LIMIT :limit';

        $stmt = Database::getConnection('read')->prepareCommon($query);

        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);

        if ($stmt->execute()) {
            $res = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $map = function ($item) {
                return new BuildMeta($item);
            };
            $rtn = array_map($map, $res);

            return $rtn;
        } else {
            return [];
        }
    }
}
