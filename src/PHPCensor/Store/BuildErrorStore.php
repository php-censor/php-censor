<?php

namespace PHPCensor\Store;

use PHPCensor\Database;
use PHPCensor\Model\BuildError;
use PHPCensor\Exception\HttpException;
use PHPCensor\Store;

class BuildErrorStore extends Store
{
    /**
     * @var string
     */
    protected $tableName = 'build_error';

    /**
     * @var string
     */
    protected $modelName = '\PHPCensor\Model\BuildError';

    /**
     * @var string
     */
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
     * @param integer $offset
     * @param string  $plugin
     * @param integer $severity
     *
     * @return array
     *
     * @throws HttpException
     */
    public function getByBuildId($buildId, $limit = null, $offset = 0, $plugin = null, $severity = null, $isNew = null)
    {
        if (is_null($buildId)) {
            throw new HttpException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        $query = 'SELECT * FROM {{build_error}} WHERE {{build_id}} = :build_id';
        if ($plugin) {
            $query .= ' AND {{plugin}} = :plugin';
        }
        if (null !== $severity) {
            $query .= ' AND {{severity}} = :severity';
        }

        if ('only_new' === $isNew) {
            $query .= ' AND {{is_new}} = true';
        } elseif ('only_old' === $isNew) {
            $query .= ' AND {{is_new}} = false';
        }

        $query .= ' ORDER BY is_new, severity, plugin';
        if (null !== $limit) {
            $query .= ' LIMIT :limit';
        }
        if ($offset) {
            $query .= ' OFFSET :offset';
        }
        $stmt = Database::getConnection()->prepareCommon($query);
        $stmt->bindValue(':build_id', $buildId);
        if ($plugin) {
            $stmt->bindValue(':plugin', $plugin, \PDO::PARAM_STR);
        }
        if (null !== $severity) {
            $stmt->bindValue(':severity', (integer)$severity, \PDO::PARAM_INT);
        }
        if (null !== $limit) {
            $stmt->bindValue(':limit', (integer)$limit, \PDO::PARAM_INT);
        }
        if ($offset) {
            $stmt->bindValue(':offset', (integer)$offset, \PDO::PARAM_INT);
        }

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
     * Gets the total number of errors for a given build.
     *
     * @param integer $buildId
     * @param string  $plugin
     * @param integer $severity
     * @param string  $isNew
     *
     * @return integer
     */
    public function getErrorTotalForBuild($buildId, $plugin = null, $severity = null, $isNew = null)
    {
        $query = 'SELECT COUNT(*) AS {{total}} FROM {{build_error}} WHERE {{build_id}} = :build';

        if ($plugin) {
            $query .= ' AND {{plugin}} = :plugin';
        }

        if (null !== $severity) {
            $query .= ' AND {{severity}} = :severity';
        }

        if ('only_new' === $isNew) {
            $query .= ' AND {{is_new}} = true';
        } elseif ('only_old' === $isNew) {
            $query .= ' AND {{is_new}} = false';
        }

        $stmt = Database::getConnection('read')->prepareCommon($query);

        $stmt->bindValue(':build', $buildId, \PDO::PARAM_INT);

        if ($plugin) {
            $stmt->bindValue(':plugin', $plugin, \PDO::PARAM_STR);
        }

        if (null !== $severity) {
            $stmt->bindValue(':severity', (integer)$severity, \PDO::PARAM_INT);
        }

        if ($stmt->execute()) {
            $res = $stmt->fetch(\PDO::FETCH_ASSOC);

            return (integer)$res['total'];
        } else {
            return 0;
        }
    }

    /**
     * @param integer $buildId
     * @param integer $severity
     * @param string  $isNew
     *
     * @return array
     */
    public function getKnownPlugins($buildId, $severity = null, $isNew = '')
    {
        $query = 'SELECT DISTINCT {{plugin}} from {{build_error}} WHERE {{build_id}} = :build';

        if (null !== $severity) {
            $query .= ' AND {{severity}} = :severity';
        }

        if ('only_new' === $isNew) {
            $query .= ' AND {{is_new}} = true';
        } elseif ('only_old' === $isNew) {
            $query .= ' AND {{is_new}} = false';
        }

        $stmt = Database::getConnection('read')->prepareCommon($query);
        $stmt->bindValue(':build', $buildId);
        if (null !== $severity) {
            $stmt->bindValue(':severity', (integer)$severity, \PDO::PARAM_INT);
        }

        if ($stmt->execute()) {
            $res = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $map = function ($item) {
                return $item['plugin'];
            };
            $rtn = array_map($map, $res);

            return $rtn;
        } else {
            return [];
        }
    }

    /**
     * @param integer $buildId
     * @param string  $plugin
     * @param string  $isNew
     *
     * @return array
     */
    public function getKnownSeverities($buildId, $plugin = '', $isNew = '')
    {
        $query = 'SELECT DISTINCT {{severity}} FROM {{build_error}} WHERE {{build_id}} = :build';

        if ($plugin) {
            $query .= ' AND {{plugin}} = :plugin';
        }

        if ('only_new' === $isNew) {
            $query .= ' AND {{is_new}} = true';
        } elseif ('only_old' === $isNew) {
            $query .= ' AND {{is_new}} = false';
        }

        $stmt = Database::getConnection('read')->prepareCommon($query);
        $stmt->bindValue(':build', $buildId);
        if ($plugin) {
            $stmt->bindValue(':plugin', $plugin);
        }

        if ($stmt->execute()) {
            $res = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $map = function ($item) {
                return (integer)$item['severity'];
            };
            $rtn = array_map($map, $res);

            return $rtn;
        } else {
            return [];
        }
    }

    /**
     * Check if a build error is new.
     *
     * @param integer $projectId
     * @param string  $hash
     *
     * @return boolean
     */
    public function getIsNewError($projectId, $hash)
    {
        $query = '
            SELECT COUNT(*) AS {{total}} FROM {{build_error}} AS be
                LEFT JOIN {{build}} AS b ON be.build_id = b.id
                WHERE be.hash = :hash AND b.project_id = :project';

        $stmt = Database::getConnection('read')->prepareCommon($query);

        $stmt->bindValue(':project', $projectId);
        $stmt->bindValue(':hash', $hash);

        if ($stmt->execute()) {
            $res = $stmt->fetch(\PDO::FETCH_ASSOC);

            return (0 === (integer)$res['total']);
        }

        return true;
    }

    /**
     * @param integer $buildId
     *
     * @return integer
     */
    public function getNewErrorsCount($buildId)
    {
        $query = 'SELECT COUNT(*) AS {{total}} FROM {{build_error}} WHERE {{build_id}} = :build AND {{is_new}} = true';

        $stmt = Database::getConnection('read')->prepareCommon($query);

        $stmt->bindValue(':build', $buildId);

        if ($stmt->execute()) {
            $res = $stmt->fetch(\PDO::FETCH_ASSOC);

            return (integer)$res['total'];
        }

        return 0;
    }
}
