<?php

declare(strict_types=1);

namespace PHPCensor\Store;

use Exception;
use PDO;
use PHPCensor\Exception\HttpException;
use PHPCensor\Model\Build;
use PHPCensor\Model\BuildMeta;
use PHPCensor\Store;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class BuildStore extends Store
{
    protected string $tableName  = 'builds';

    protected ?string $modelName  = '\PHPCensor\Model\Build';

    protected ?string $primaryKey = 'id';

    /**
     * Get a Build by primary key (Id)
     */
    public function getByPrimaryKey(int $key, string $useConnection = 'read'): ?Build
    {
        return $this->getById($key, $useConnection);
    }

    /**
     * Get a single Build by Id.
     *
     * @throws HttpException
     */
    public function getById(int $id, string $useConnection = 'read'): ?Build
    {
        if (\is_null($id)) {
            throw new HttpException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        $query = 'SELECT * FROM {{' . $this->tableName . '}} WHERE {{id}} = :id LIMIT 1';
        $stmt  = $this->databaseManager->getConnection($useConnection)->prepare($query);
        $stmt->bindValue(':id', $id);

        if ($stmt->execute()) {
            if ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return new Build($this->storeRegistry, $data);
            }
        }

        return null;
    }

    /**
     * Get multiple Build by ProjectId.
     *
     * @throws HttpException
     */
    public function getByProjectId(int $projectId, int $limit = 1000, string $useConnection = 'read'): array
    {
        if (\is_null($projectId)) {
            throw new HttpException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        $query = 'SELECT * FROM {{' . $this->tableName . '}} WHERE {{project_id}} = :project_id LIMIT :limit';
        $stmt  = $this->databaseManager->getConnection($useConnection)->prepare($query);
        $stmt->bindValue(':project_id', $projectId);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $map = function ($item) {
                return new Build($this->storeRegistry, $item);
            };
            $rtn = \array_map($map, $res);

            $count = \count($rtn);

            return ['items' => $rtn, 'count' => $count];
        } else {
            return ['items' => [], 'count' => 0];
        }
    }

    /**
     * Get multiple Build by Status.
     *
     * @throws HttpException
     */
    public function getByStatus(int $status, int $limit = 1000, string $useConnection = 'read'): array
    {
        if (\is_null($status)) {
            throw new HttpException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        $query = 'SELECT * FROM {{' . $this->tableName . '}} WHERE {{status}} = :status ORDER BY {{create_date}} ASC LIMIT :limit';
        $stmt  = $this->databaseManager->getConnection($useConnection)->prepare($query);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $map = function ($item) {
                return new Build($this->storeRegistry, $item);
            };
            $rtn = \array_map($map, $res);

            $count = \count($rtn);

            return ['items' => $rtn, 'count' => $count];
        } else {
            return ['items' => [], 'count' => 0];
        }
    }

    public function getBuilds(int $limit = 5, int $offset = 0): array
    {
        $query = 'SELECT * FROM {{' . $this->tableName . '}} ORDER BY {{id}} DESC LIMIT :limit OFFSET :offset';
        $stmt  = $this->databaseManager->getConnection('read')->prepare($query);

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $map = function ($item) {
                return new Build($this->storeRegistry, $item);
            };

            return \array_map($map, $res);
        } else {
            return [];
        }
    }

    /**
     * @throws Exception
     */
    public function getLatestBuildByProjectAndBranch(int $projectId, string $branch): ?Build
    {
        $query = 'SELECT * FROM {{' . $this->tableName . '}} WHERE {{project_id}} = :project_id AND {{branch}} = :branch ORDER BY {{id}} DESC';
        $stmt  = $this->databaseManager->getConnection('read')->prepare($query);

        $stmt->bindValue(':project_id', $projectId);
        $stmt->bindValue(':branch', $branch);

        if ($stmt->execute() && $data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return new Build($this->storeRegistry, $data);
        }

        return null;
    }

    /**
     * Return an array of the latest builds for a given project.
     *
     * @throws Exception
     */
    public function getLatestBuilds(?int $projectId = null, int $limit = 5): array
    {
        if (!\is_null($projectId)) {
            $query = 'SELECT * FROM {{' . $this->tableName . '}} WHERE {{project_id}} = :pid ORDER BY {{id}} DESC LIMIT :limit';
        } else {
            $query = 'SELECT * FROM {{' . $this->tableName . '}} ORDER BY {{id}} DESC LIMIT :limit';
        }

        $stmt = $this->databaseManager->getConnection('read')->prepare($query);

        if (!\is_null($projectId)) {
            $stmt->bindValue(':pid', $projectId);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $map = function ($item) {
                return new Build($this->storeRegistry, $item);
            };

            return \array_map($map, $res);
        } else {
            return [];
        }
    }

    /**
     * Return the latest build for a specific project, of a specific build status.
     */
    public function getLastBuildByStatus(?int $projectId = null, int $status = Build::STATUS_SUCCESS): ?Build
    {
        $query = 'SELECT * FROM {{' . $this->tableName . '}} WHERE {{project_id}} = :pid AND {{status}} = :status ORDER BY {{id}} DESC LIMIT 1';
        $stmt  = $this->databaseManager->getConnection('read')->prepare($query);
        $stmt->bindValue(':pid', $projectId);
        $stmt->bindValue(':status', $status);

        if ($stmt->execute()) {
            if ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return new Build($this->storeRegistry, $data);
            }
        }

        return null;
    }

    /**
     * Return an array of the latest builds for all projects.
     */
    public function getAllProjectsLatestBuilds(int $limitByProject = 5, int $limitAll = 10): array
    {
        // don't fetch log field - contain many data
        $query = '
            SELECT
                {{id}},
                {{project_id}},
                {{commit_id}},
                {{status}},
                {{branch}},
                {{create_date}},
                {{start_date}},
                {{finish_date}},
                {{committer_email}},
                {{commit_message}},
                {{extra}},
                {{environment_id}},
                {{tag}}
            FROM {{' . $this->tableName . '}}
            ORDER BY {{id}} DESC
            LIMIT 10000
        ';

        $stmt = $this->databaseManager->getConnection('read')->prepare($query);

        if ($stmt->execute()) {
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $projects = [];
            $latest   = [];
            foreach ($res as $item) {
                $projectId = $item['project_id'];
                $environment = $item['environment_id'];
                if (empty($projects[$projectId])) {
                    $projects[$projectId] = [];
                }
                if (empty($projects[$projectId][$environment])) {
                    $projects[$projectId][$environment] = [
                        'latest' => [],
                        'success' => null,
                        'failed' => null,
                    ];
                }
                $build = null;
                if (\count($projects[$projectId][$environment]['latest']) < $limitByProject) {
                    $build = new Build($this->storeRegistry, $item);
                    $projects[$projectId][$environment]['latest'][] = $build;
                }
                if (\count($latest) < $limitAll) {
                    if (\is_null($build)) {
                        $build = new Build($this->storeRegistry, $item);
                    }
                    $latest[] = $build;
                }
                if (empty($projects[$projectId][$environment]['success']) && Build::STATUS_SUCCESS === $item['status']) {
                    if (\is_null($build)) {
                        $build = new Build($this->storeRegistry, $item);
                    }
                    $projects[$projectId][$environment]['success'] = $build;
                }
                if (empty($projects[$projectId][$environment]['failed']) && Build::STATUS_FAILED === $item['status']) {
                    if (\is_null($build)) {
                        $build = new Build($this->storeRegistry, $item);
                    }
                    $projects[$projectId][$environment]['failed'] = $build;
                }
            }

            foreach ($projects as $idx => $project) {
                $projects[$idx] = \array_filter($project, function ($val) {
                    return ($val['latest'][0]->getStatus() != Build::STATUS_SUCCESS);
                });
            }

            $projects = \array_filter($projects);

            return ['projects' => $projects, 'latest' => $latest];
        } else {
            return [];
        }
    }

    /**
     * Return an array of builds for a given project and commit ID.
     */
    public function getByProjectAndCommit(int $projectId, string $commitId): array
    {
        $query = 'SELECT * FROM {{' . $this->tableName . '}} WHERE {{project_id}} = :project_id AND {{commit_id}} = :commit_id';
        $stmt  = $this->databaseManager->getConnection('read')->prepare($query);

        $stmt->bindValue(':project_id', $projectId);
        $stmt->bindValue(':commit_id', $commitId);

        if ($stmt->execute()) {
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $map = function ($item) {
                return new Build($this->storeRegistry, $item);
            };

            $rtn = \array_map($map, $res);

            return ['items' => $rtn, 'count' => \count($rtn)];
        } else {
            return ['items' => [], 'count' => 0];
        }
    }

    /**
     * Returns all registered branches for project
     *
     * @throws Exception
     */
    public function getBuildBranches(int $projectId): array
    {
        $query = 'SELECT DISTINCT {{branch}} FROM {{' . $this->tableName . '}} WHERE {{project_id}} = :project_id';
        $stmt  = $this->databaseManager->getConnection('read')->prepare($query);
        $stmt->bindValue(':project_id', $projectId);

        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } else {
            return [];
        }
    }

    /**
     * Return build metadata by key, project and optionally build id.
     */
    public function getMeta(string $key, ?int $projectId, ?int $buildId = null, ?string $branch = null, int $numResults = 1): ?array
    {
        $query = 'SELECT bm.build_id, bm.meta_key, bm.meta_value
                    FROM {{build_metas}} AS {{bm}}
                    LEFT JOIN {{' . $this->tableName . '}} AS {{b}} ON b.id = bm.build_id
                    WHERE bm.meta_key = :key AND b.project_id = :projectId';

        // If we're getting comparative meta data, include previous builds
        // otherwise just include the specified build ID:
        if ($numResults > 1) {
            $query .= ' AND bm.build_id <= :buildId ';
        } else {
            $query .= ' AND bm.build_id = :buildId ';
        }

        // Include specific branch information if required:
        if (!\is_null($branch)) {
            $query .= ' AND b.branch = :branch ';
        }

        $query .= ' ORDER BY bm.id DESC LIMIT :numResults';

        $stmt = $this->databaseManager->getConnection('read')->prepare($query);
        $stmt->bindValue(':key', $key, PDO::PARAM_STR);
        $stmt->bindValue(':projectId', $projectId, PDO::PARAM_INT);
        $stmt->bindValue(':buildId', $buildId, PDO::PARAM_INT);
        $stmt->bindValue(':numResults', $numResults, PDO::PARAM_INT);

        if (!\is_null($branch)) {
            $stmt->bindValue(':branch', $branch, PDO::PARAM_STR);
        }

        if ($stmt->execute()) {
            $rtn = $stmt->fetchAll(PDO::FETCH_ASSOC);

            /** @var BuildErrorStore $errorStore */
            $errorStore = $this->storeRegistry->get('BuildError');

            $rtn = \array_reverse($rtn);
            $rtn = \array_map(function ($item) use ($key, $errorStore, $buildId) {
                $item['meta_value'] = \json_decode($item['meta_value'], true);
                if ('plugin-summary' === $key) {
                    foreach ($item['meta_value'] as $stage => $stageData) {
                        foreach ($stageData as $plugin => $pluginData) {
                            $item['meta_value'][$stage][$plugin]['errors'] = $errorStore->getErrorTotalForBuild(
                                $buildId,
                                $plugin
                            );
                        }
                    }
                }

                return $item;
            }, $rtn);

            if (!\count($rtn)) {
                return null;
            } else {
                return $rtn;
            }
        } else {
            return null;
        }
    }

    /**
     * Set a metadata value for a given project and build ID.
     */
    public function setMeta(int $buildId, string $key, string $value): void
    {
        /** @var BuildMetaStore $store */
        $store = $this->storeRegistry->get('BuildMeta');
        $meta  = $store->getByKey($buildId, $key);
        if (\is_null($meta)) {
            $meta = new BuildMeta($this->storeRegistry);
            $meta->setBuildId($buildId);
            $meta->setMetaKey($key);
        }
        $meta->setMetaValue($value);

        $store->save($meta);
    }

    public function deleteAllByProject(int $projectId): int
    {
        $q = $this->databaseManager->getConnection('write')
            ->prepare(
                'DELETE FROM {{' . $this->tableName . '}} WHERE {{project_id}} = :project_id'
            );
        $q->bindValue(':project_id', $projectId);
        $q->execute();

        return $q->rowCount();
    }

    /**
     * @throws HttpException
     */
    public function getOldByProject(int $projectId, int $keep = 100): array
    {
        if (\is_null($projectId)) {
            throw new HttpException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        $query = 'SELECT * FROM {{' . $this->tableName . '}} WHERE {{project_id}} = :project_id ORDER BY {{create_date}} DESC LIMIT 1000000 OFFSET :keep';
        $stmt  = $this->databaseManager->getConnection('read')->prepare($query);
        $stmt->bindValue(':project_id', $projectId);
        $stmt->bindValue(':keep', (int)$keep, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $map = function ($item) {
                return new Build($this->storeRegistry, $item);
            };
            $rtn = \array_map($map, $res);

            $count = \count($rtn);

            return ['items' => $rtn, 'count' => $count];
        }

        return ['items' => [], 'count' => 0];
    }

    /**
     * @throws Exception
     */
    public function getNewErrorsCount(int $buildId): int
    {
        $query = 'SELECT COUNT(*) AS {{total}} FROM {{build_errors}} WHERE {{build_id}} = :build_id AND {{is_new}} = true';

        $stmt = $this->databaseManager->getConnection('read')->prepare($query);

        $stmt->bindValue(':build_id', $buildId);

        if ($stmt->execute()) {
            $res = $stmt->fetch(PDO::FETCH_ASSOC);

            return (int)$res['total'];
        }

        return 0;
    }

    /**
     * @throws Exception
     */
    public function getErrorsCount(int $buildId): int
    {
        $query = 'SELECT COUNT(*) AS {{total}} FROM {{build_errors}} WHERE {{build_id}} = :build_id';

        $stmt = $this->databaseManager->getConnection('read')->prepare($query);

        $stmt->bindValue(':build_id', $buildId);

        if ($stmt->execute()) {
            $res = $stmt->fetch(PDO::FETCH_ASSOC);

            return (int)$res['total'];
        }

        return 0;
    }

    /**
     * @throws Exception
     */
    public function getBuildErrorsTrend(int $buildId, int $projectId, string $branch): array
    {
        $query = '
SELECT b.id AS {{build_id}}, count(be.id) AS {{count}} FROM {{' . $this->tableName . '}} AS b
LEFT JOIN {{build_errors}} AS be
ON b.id = be.build_id
WHERE b.project_id = :project_id AND b.branch = :branch AND b.id <= :build_id
GROUP BY b.id
order BY b.id DESC
LIMIT 2';

        $stmt = $this->databaseManager->getConnection('read')->prepare($query);

        $stmt->bindValue(':build_id', $buildId, PDO::PARAM_INT);
        $stmt->bindValue(':project_id', $projectId, PDO::PARAM_INT);
        $stmt->bindValue(':branch', $branch, PDO::PARAM_STR);

        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return [];
    }
}
