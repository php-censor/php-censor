<?php

declare(strict_types = 1);

namespace PHPCensor\Store;

use PDO;
use PHPCensor\Exception\HttpException;
use PHPCensor\Model\BuildMeta;
use PHPCensor\Store;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class BuildMetaStore extends Store
{
    protected string $tableName  = 'build_metas';

    protected ?string $modelName  = '\PHPCensor\Model\BuildMeta';

    protected ?string $primaryKey = 'id';

    /**
     * Get a BuildMeta by primary key (Id)
     *
     * @param int    $key
     * @param string $useConnection
     *
     * @return null|BuildMeta
     */
    public function getByPrimaryKey(int $key, string $useConnection = 'read'): ?BuildMeta
    {
        return $this->getById($key, $useConnection);
    }

    /**
     * Get a single BuildMeta by Id.
     *
     * @param int    $id
     * @param string $useConnection
     *
     * @return null|BuildMeta
     *
     * @throws HttpException
     */
    public function getById(int $id, string $useConnection = 'read'): ?BuildMeta
    {
        if (\is_null($id)) {
            throw new HttpException('id passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        $query = 'SELECT * FROM {{' . $this->tableName . '}} WHERE {{id}} = :id LIMIT 1';
        $stmt  = $this->databaseManager->getConnection($useConnection)->prepare($query);
        $stmt->bindValue(':id', $id);

        if ($stmt->execute()) {
            if ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return new BuildMeta($this->storeRegistry, $data);
            }
        }

        return null;
    }

    /**
     * @param int $buildId
     * @param string  $key
     *
     * @return null|BuildMeta
     *
     * @throws HttpException
     */
    public function getByKey(int $buildId, string $key): ?BuildMeta
    {
        if (\is_null($buildId)) {
            throw new HttpException('buildId passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        if (!$key) {
            throw new HttpException('key passed to ' . __FUNCTION__ . ' cannot be empty.');
        }

        $query = 'SELECT * FROM {{' . $this->tableName . '}} WHERE {{build_id}} = :build_id AND {{meta_key}} = :meta_key LIMIT 1';
        $stmt  = $this->databaseManager->getConnection('read')->prepare($query);
        $stmt->bindValue(':build_id', $buildId);
        $stmt->bindValue(':meta_key', $key);

        if ($stmt->execute()) {
            if ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return new BuildMeta($this->storeRegistry, $data);
            }
        }

        return null;
    }

    /**
     * Get multiple BuildMeta by BuildId.
     *
     * @param int $buildId
     * @param int $limit
     * @param string  $useConnection
     *
     * @return array
     *
     * @throws HttpException
     */
    public function getByBuildId(int $buildId, int $limit = 1000, string $useConnection = 'read'): array
    {
        if (\is_null($buildId)) {
            throw new HttpException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        $query = 'SELECT * FROM {{' . $this->tableName . '}} WHERE {{build_id}} = :build_id LIMIT :limit';
        $stmt = $this->databaseManager->getConnection($useConnection)->prepare($query);
        $stmt->bindValue(':build_id', $buildId);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $map = function ($item) {
                return new BuildMeta($this->storeRegistry, $item);
            };
            $rtn = \array_map($map, $res);

            $count = \count($rtn);

            return ['items' => $rtn, 'count' => $count];
        } else {
            return ['items' => [], 'count' => 0];
        }
    }

    /**
     * Only used by an upgrade migration to move errors from build_meta to build_error
     *
     * @param int $limit
     *
     * @return array
     */
    public function getErrorsForUpgrade(int $limit): array
    {
        $query = 'SELECT * FROM {{' . $this->tableName . '}}
                    WHERE {{meta_key}} IN (\'phpmd-data\', \'phpcs-data\', \'phpdoccheck-data\', \'technical_debt-data\')
                    ORDER BY {{id}} ASC LIMIT :limit';

        $stmt = $this->databaseManager->getConnection('read')->prepare($query);

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $map = function ($item) {
                return new BuildMeta($this->storeRegistry, $item);
            };

            return \array_map($map, $res);
        } else {
            return [];
        }
    }
}
