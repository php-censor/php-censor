<?php

declare(strict_types = 1);

namespace PHPCensor\Store;

use Exception;
use PDO;
use PHPCensor\Exception\HttpException;
use PHPCensor\Model\BuildError;
use PHPCensor\Store;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class BuildErrorStore extends Store
{
    protected string $tableName = 'build_errors';

    protected ?string $modelName = '\PHPCensor\Model\BuildError';

    protected ?string $primaryKey = 'id';

    /**
     * @param mixed  $key
     * @param string $useConnection
     *
     * @return BuildError|null
     *
     * @throws HttpException
     */
    public function getByPrimaryKey($key, string $useConnection = 'read'): ?BuildError
    {
        return $this->getById($key, $useConnection);
    }

    /**
     * Get a single BuildError by Id.
     *
     * @param int $id
     * @param string  $useConnection
     *
     * @return null|BuildError
     *
     * @throws HttpException
     */
    public function getById($id, $useConnection = 'read')
    {
        if (\is_null($id)) {
            throw new HttpException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        $query = 'SELECT * FROM {{' . $this->tableName . '}} WHERE {{id}} = :id LIMIT 1';
        $stmt = $this->databaseManager->getConnection($useConnection)->prepare($query);
        $stmt->bindValue(':id', $id);

        if ($stmt->execute()) {
            if ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return new BuildError($this->storeRegistry, $data);
            }
        }

        return null;
    }

    /**
     * Get multiple BuildError by BuildId.
     *
     * @param int         $buildId
     * @param int|null    $limit
     * @param int         $offset
     * @param string|null $plugin
     * @param int|null    $severity
     * @param string|null $isNew
     *
     * @return array
     *
     * @throws HttpException
     */
    public function getByBuildId($buildId, $limit = null, $offset = 0, $plugin = null, $severity = null, $isNew = null)
    {
        if (\is_null($buildId)) {
            throw new HttpException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        $query = 'SELECT * FROM {{' . $this->tableName . '}} WHERE {{build_id}} = :build_id';
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
        $stmt = $this->databaseManager->getConnection()->prepare($query);
        $stmt->bindValue(':build_id', $buildId);
        if ($plugin) {
            $stmt->bindValue(':plugin', $plugin, PDO::PARAM_STR);
        }
        if (null !== $severity) {
            $stmt->bindValue(':severity', (int)$severity, PDO::PARAM_INT);
        }
        if (null !== $limit) {
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        }
        if ($offset) {
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        }

        if ($stmt->execute()) {
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $map = function ($item) {
                return new BuildError($this->storeRegistry, $item);
            };
            $rtn = \array_map($map, $res);

            $count = \count($rtn);

            return ['items' => $rtn, 'count' => $count];
        } else {
            return ['items' => [], 'count' => 0];
        }
    }

    /**
     * Gets the total number of errors for a given build.
     *
     * @param int         $buildId
     * @param string|null $plugin
     * @param int|null    $severity
     * @param string|null $isNew
     *
     * @return int
     *
     * @throws Exception
     */
    public function getErrorTotalForBuild($buildId, $plugin = null, $severity = null, $isNew = null)
    {
        $query = 'SELECT COUNT(*) AS {{total}} FROM {{' . $this->tableName . '}} WHERE {{build_id}} = :build';

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

        $stmt = $this->databaseManager->getConnection('read')->prepare($query);

        $stmt->bindValue(':build', $buildId, PDO::PARAM_INT);

        if ($plugin) {
            $stmt->bindValue(':plugin', $plugin, PDO::PARAM_STR);
        }

        if (null !== $severity) {
            $stmt->bindValue(':severity', (int)$severity, PDO::PARAM_INT);
        }

        if ($stmt->execute()) {
            $res = $stmt->fetch(PDO::FETCH_ASSOC);

            return (int)$res['total'];
        } else {
            return 0;
        }
    }

    /**
     * @param int $buildId
     * @param int $severity
     * @param string  $isNew
     *
     * @return array
     */
    public function getKnownPlugins($buildId, $severity = null, $isNew = '')
    {
        $query = 'SELECT DISTINCT {{plugin}} from {{' . $this->tableName . '}} WHERE {{build_id}} = :build';

        if (null !== $severity) {
            $query .= ' AND {{severity}} = :severity';
        }

        if ('only_new' === $isNew) {
            $query .= ' AND {{is_new}} = true';
        } elseif ('only_old' === $isNew) {
            $query .= ' AND {{is_new}} = false';
        }

        $stmt = $this->databaseManager->getConnection('read')->prepare($query);
        $stmt->bindValue(':build', $buildId);
        if (null !== $severity) {
            $stmt->bindValue(':severity', (int)$severity, PDO::PARAM_INT);
        }

        if ($stmt->execute()) {
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $map = function ($item) {
                return $item['plugin'];
            };
            return \array_map($map, $res);
        } else {
            return [];
        }
    }

    /**
     * @param int $buildId
     * @param string  $plugin
     * @param string  $isNew
     *
     * @return array
     */
    public function getKnownSeverities($buildId, $plugin = '', $isNew = '')
    {
        $query = 'SELECT DISTINCT {{severity}} FROM {{' . $this->tableName . '}} WHERE {{build_id}} = :build';

        if ($plugin) {
            $query .= ' AND {{plugin}} = :plugin';
        }

        if ('only_new' === $isNew) {
            $query .= ' AND {{is_new}} = true';
        } elseif ('only_old' === $isNew) {
            $query .= ' AND {{is_new}} = false';
        }

        $stmt = $this->databaseManager->getConnection('read')->prepare($query);
        $stmt->bindValue(':build', $buildId);
        if ($plugin) {
            $stmt->bindValue(':plugin', $plugin);
        }

        if ($stmt->execute()) {
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $map = function ($item) {
                return (int)$item['severity'];
            };

            return \array_map($map, $res);
        } else {
            return [];
        }
    }

    /**
     * Check if a build error is new.
     *
     * @param int $projectId
     * @param string $hash
     *
     * @return bool
     * @throws Exception
     */
    public function getIsNewError($projectId, $hash)
    {
        $query = '
            SELECT COUNT(*) AS {{total}} FROM {{' . $this->tableName . '}} AS be
                LEFT JOIN {{builds}} AS b ON be.build_id = b.id
                WHERE be.hash = :hash AND b.project_id = :project';


        $stmt = $this->databaseManager->getConnection('read')->prepare($query);

        $stmt->bindValue(':project', $projectId);
        $stmt->bindValue(':hash', $hash);

        if ($stmt->execute()) {
            $res = $stmt->fetch(PDO::FETCH_ASSOC);

            return (0 === (int)$res['total']);
        }

        return true;
    }

    /**
     * @param $buildId
     * @return array
     * @throws Exception
     */
    public function getErrorAmountPerPluginForBuild($buildId)
    {
        $query = '
            SELECT {{plugin}}, COUNT(*) AS {{amount}}
            FROM {{' . $this->tableName . '}}
            WHERE {{build_id}} = :build
            GROUP BY {{plugin}}
        ';

        $stmt = $this->databaseManager->getConnection('read')->prepare($query);
        $stmt->bindValue(':build', $buildId);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }
}
