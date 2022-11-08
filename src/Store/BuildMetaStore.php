<?php

declare(strict_types=1);

namespace PHPCensor\Store;

use PDO;
use PHPCensor\Exception\HttpException;
use PHPCensor\Model\Build;
use PHPCensor\Model\BuildError;
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
    protected string $tableName = 'build_metas';

    protected string $modelName = BuildMeta::class;

    /**
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

            $map = fn($item) => new BuildMeta($this->storeRegistry, $item);
            $rtn = \array_map($map, $res);

            $count = \count($rtn);

            return ['items' => $rtn, 'count' => $count];
        } else {
            return ['items' => [], 'count' => 0];
        }
    }

    /**
     * Only used by an upgrade migration to move errors from build_meta to build_error
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
            $map = fn($item) => new BuildMeta($this->storeRegistry, $item);

            return \array_map($map, $res);
        } else {
            return [];
        }
    }
}
