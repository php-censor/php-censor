<?php

declare(strict_types=1);

namespace PHPCensor\Store;

use Exception;
use PDO;
use PHPCensor\Exception\HttpException;
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
class BuildErrorStore extends Store
{
    protected string $tableName = 'build_errors';

    protected string $modelName = BuildError::class;

    /**
     * Get multiple BuildError by BuildId.
     *
     * @throws HttpException
     */
    public function getByBuildId(int $buildId, ?int $limit = null, int $offset = 0, ?string $plugin = null, ?int $severity = null, ?string $isNew = null): array
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

            $map = fn ($item) => new BuildError($this->storeRegistry, $item);
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
     * @throws Exception
     */
    public function getErrorTotalForBuild(int $buildId, ?string $plugin = null, ?int $severity = null, ?string $isNew = null): int
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

    public function getKnownPlugins(int $buildId, ?int $severity = null, ?string $isNew = null): array
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
            $map = fn ($item) => $item['plugin'];

            return \array_map($map, $res);
        } else {
            return [];
        }
    }

    public function getKnownSeverities(int $buildId, ?string $plugin = null, ?string $isNew = null): array
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
            $map = fn ($item) => (int)$item['severity'];

            return \array_map($map, $res);
        } else {
            return [];
        }
    }

    /**
     * Check if a build error is new.
     *
     * @throws Exception
     */
    public function getIsNewError(int $projectId, string $hash): bool
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
     * @throws Exception
     */
    public function getErrorAmountPerPluginForBuild(int $buildId): array
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
