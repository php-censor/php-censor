<?php

namespace PHPCensor\Store;

use b8\Database;
use b8\Store\Factory;
use PHPCensor\Model\Build;
use b8\Exception\HttpException;
use PHPCensor\Model\BuildMeta;
use PHPCensor\Store;

/**
 * @author Dan Cryer <dan@block8.co.uk>
 */
class BuildStore extends Store
{
    /**
     * @var string
     */
    protected $tableName  = 'build';

    /**
     * @var string
     */
    protected $modelName  = '\PHPCensor\Model\Build';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Get a Build by primary key (Id)
     *
     * @param integer $key
     * @param string  $useConnection
     *
     * @return null|Build
     */
    public function getByPrimaryKey($key, $useConnection = 'read')
    {
        return $this->getById($key, $useConnection);
    }

    /**
     * Get a single Build by Id.
     *
     * @param integer $id
     * @param string  $useConnection
     *
     * @return Build|null
     *
     * @throws HttpException
     */
    public function getById($id, $useConnection = 'read')
    {
        if (is_null($id)) {
            throw new HttpException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        $query = 'SELECT * FROM {{build}} WHERE {{id}} = :id LIMIT 1';
        $stmt = Database::getConnection($useConnection)->prepareCommon($query);
        $stmt->bindValue(':id', $id);

        if ($stmt->execute()) {
            if ($data = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                return new Build($data);
            }
        }

        return null;
    }

    /**
     * Get multiple Build by ProjectId.
     *
     * @param integer $projectId
     * @param integer $limit
     * @param string  $useConnection
     *
     * @return array
     *
     * @throws HttpException
     */
    public function getByProjectId($projectId, $limit = 1000, $useConnection = 'read')
    {
        if (is_null($projectId)) {
            throw new HttpException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        $query = 'SELECT * FROM {{build}} WHERE {{project_id}} = :project_id LIMIT :limit';
        $stmt = Database::getConnection($useConnection)->prepareCommon($query);
        $stmt->bindValue(':project_id', $projectId);
        $stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);

        if ($stmt->execute()) {
            $res = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $map = function ($item) {
                return new Build($item);
            };
            $rtn = array_map($map, $res);

            $count = count($rtn);

            return ['items' => $rtn, 'count' => $count];
        } else {
            return ['items' => [], 'count' => 0];
        }
    }

    /**
     * Get multiple Build by Status.
     *
     * @param integer $status
     * @param integer $limit
     * @param string  $useConnection
     *
     * @return array
     *
     * @throws HttpException
     */
    public function getByStatus($status, $limit = 1000, $useConnection = 'read')
    {
        if (is_null($status)) {
            throw new HttpException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        $query = 'SELECT * FROM {{build}} WHERE {{status}} = :status LIMIT :limit';
        $stmt = Database::getConnection($useConnection)->prepareCommon($query);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);

        if ($stmt->execute()) {
            $res = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $map = function ($item) {
                return new Build($item);
            };
            $rtn = array_map($map, $res);

            $count = count($rtn);

            return ['items' => $rtn, 'count' => $count];
        } else {
            return ['items' => [], 'count' => 0];
        }
    }

    /**
     * @param integer $limit
     * @param integer $offset
     *
     * @return array
     */
    public function getBuilds($limit = 5, $offset = 0)
    {
        $query = 'SELECT * FROM {{build}} ORDER BY {{id}} DESC LIMIT :limit OFFSET :offset';
        $stmt  = Database::getConnection('read')->prepareCommon($query);

        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);

        if ($stmt->execute()) {
            $res = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $map = function ($item) {
                return new Build($item);
            };
            $rtn = array_map($map, $res);

            return $rtn;
        } else {
            return [];
        }
    }

    /**
     * Return an array of the latest builds for a given project.
     *
     * @param integer|null $projectId
     * @param integer      $limit
     *
     * @return array
     */
    public function getLatestBuilds($projectId = null, $limit = 5)
    {
        if (!is_null($projectId)) {
            $query = 'SELECT * FROM {{build}} WHERE {{project_id}} = :pid ORDER BY {{id}} DESC LIMIT :limit';
        } else {
            $query = 'SELECT * FROM {{build}} ORDER BY {{id}} DESC LIMIT :limit';
        }

        $stmt = Database::getConnection('read')->prepareCommon($query);

        if (!is_null($projectId)) {
            $stmt->bindValue(':pid', $projectId);
        }

        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);

        if ($stmt->execute()) {
            $res = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $map = function ($item) {
                return new Build($item);
            };
            $rtn = array_map($map, $res);

            return $rtn;
        } else {
            return [];
        }
    }

    /**
     * Return the latest build for a specific project, of a specific build status.
     *
     * @param integer|null $projectId
     * @param integer      $status
     *
     * @return array|Build
     */
    public function getLastBuildByStatus($projectId = null, $status = Build::STATUS_SUCCESS)
    {
        $query = 'SELECT * FROM {{build}} WHERE {{project_id}} = :pid AND {{status}} = :status ORDER BY {{id}} DESC LIMIT 1';
        $stmt = Database::getConnection('read')->prepareCommon($query);
        $stmt->bindValue(':pid', $projectId);
        $stmt->bindValue(':status', $status);

        if ($stmt->execute()) {
            if ($data = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                return new Build($data);
            }
        } else {
            return [];
        }
    }

    /**
     * Return an array of the latest builds for all projects.
     *
     * @param integer $limit_by_project
     * @param integer $limit_all
     *
     * @return array
     */
    public function getAllProjectsLatestBuilds($limit_by_project = 5, $limit_all = 10)
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
                {{environment}},
                {{tag}}
            FROM {{build}}
            ORDER BY {{id}} DESC
            LIMIT 10000
        ';

        $stmt = Database::getConnection('read')->prepareCommon($query);

        if ($stmt->execute()) {
            $res = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $projects = [];
            $latest = [];
            foreach($res as $item) {
                $project_id = $item['project_id'];
                $environment = $item['environment'];
                if (empty($projects[$project_id])) {
                    $projects[$project_id] = [];
                }
                if (empty($projects[$project_id][$environment])) {
                    $projects[$project_id][$environment] = [
                        'latest' => [],
                        'success' => null,
                        'failed' => null,
                    ];
                }
                $build = null;
                if (count($projects[$project_id][$environment]['latest']) < $limit_by_project) {
                    $build = new Build($item);
                    $projects[$project_id][$environment]['latest'][] = $build;
                }
                if (count($latest) < $limit_all) {
                    if (is_null($build)) {
                        $build = new Build($item);
                    }
                    $latest[] = $build;
                }
                if (empty($projects[$project_id][$environment]['success']) and ($item['status'] == Build::STATUS_SUCCESS)) {
                    if (is_null($build)) {
                        $build = new Build($item);
                    }
                    $projects[$project_id][$environment]['success'] = $build;
                }
                if (empty($projects[$project_id][$environment]['failed']) and ($item['status'] == Build::STATUS_FAILED)) {
                    if (is_null($build)) {
                        $build = new Build($item);
                    }
                    $projects[$project_id][$environment]['failed'] = $build;
                }
            }
            foreach($projects as $idx => $project) {
                $projects[$idx] = array_filter($project, function($val) {
                    return ($val['latest'][0]->getStatus() != Build::STATUS_SUCCESS);
                });
            }
            $projects = array_filter($projects);

            return ['projects' => $projects, 'latest' => $latest];
        } else {
            return [];
        }
    }

    /**
     * Return an array of builds for a given project and commit ID.
     *
     * @param integer $projectId
     * @param string  $commitId
     *
     * @return array
     */
    public function getByProjectAndCommit($projectId, $commitId)
    {
        $query = 'SELECT * FROM {{build}} WHERE {{project_id}} = :project_id AND {{commit_id}} = :commit_id';
        $stmt  = Database::getConnection('read')->prepareCommon($query);

        $stmt->bindValue(':project_id', $projectId);
        $stmt->bindValue(':commit_id', $commitId);

        if ($stmt->execute()) {
            $res = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $map = function ($item) {
                return new Build($item);
            };

            $rtn = array_map($map, $res);

            return ['items' => $rtn, 'count' => count($rtn)];
        } else {
            return ['items' => [], 'count' => 0];
        }
    }

    /**
     * Returns all registered branches for project
     *
     * @param integer $projectId
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getBuildBranches($projectId)
    {
        $query = 'SELECT DISTINCT {{branch}} FROM {{build}} WHERE {{project_id}} = :project_id';
        $stmt = Database::getConnection('read')->prepareCommon($query);
        $stmt->bindValue(':project_id', $projectId);

        if ($stmt->execute()) {
            $res = $stmt->fetchAll(\PDO::FETCH_COLUMN);
            return $res;
        } else {
            return [];
        }
    }

    /**
     * Return build metadata by key, project and optionally build id.
     *
     * @param string       $key
     * @param integer      $projectId
     * @param integer|null $buildId
     * @param string|null  $branch
     * @param integer      $numResults
     *
     * @return array|null
     */
    public function getMeta($key, $projectId, $buildId = null, $branch = null, $numResults = 1)
    {
        $query = 'SELECT bm.build_id, bm.meta_key, bm.meta_value
                    FROM {{build_meta}} AS {{bm}}
                    LEFT JOIN {{build}} AS {{b}} ON b.id = bm.build_id
                    WHERE bm.meta_key = :key AND b.project_id = :projectId';

        // If we're getting comparative meta data, include previous builds
        // otherwise just include the specified build ID:
        if ($numResults > 1) {
            $query .= ' AND bm.build_id <= :buildId ';
        } else {
            $query .= ' AND bm.build_id = :buildId ';
        }

        // Include specific branch information if required:
        if (!is_null($branch)) {
            $query .= ' AND b.branch = :branch ';
        }

        $query .= ' ORDER BY bm.id DESC LIMIT :numResults';

        $stmt = Database::getConnection('read')->prepareCommon($query);
        $stmt->bindValue(':key', $key, \PDO::PARAM_STR);
        $stmt->bindValue(':projectId', (int)$projectId, \PDO::PARAM_INT);
        $stmt->bindValue(':buildId', (int)$buildId, \PDO::PARAM_INT);
        $stmt->bindValue(':numResults', (int)$numResults, \PDO::PARAM_INT);

        if (!is_null($branch)) {
            $stmt->bindValue(':branch', $branch, \PDO::PARAM_STR);
        }

        if ($stmt->execute()) {
            $rtn = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            /** @var \PHPCensor\Store\BuildErrorStore $errorStore */
            $errorStore = Factory::getStore('BuildError');

            $rtn = array_reverse($rtn);
            $rtn = array_map(function ($item) use ($key, $errorStore, $buildId) {
                $item['meta_value'] = json_decode($item['meta_value'], true);
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

            if (!count($rtn)) {
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
     *
     * @param integer $buildId
     * @param string  $key
     * @param string  $value
     */
    public function setMeta($buildId, $key, $value)
    {
        /** @var BuildMetaStore $store */
        $store = Factory::getStore('BuildMeta');
        $meta  = $store->getByKey($buildId, $key);
        if (is_null($meta)) {
            $meta = new BuildMeta();
            $meta->setBuildId($buildId);
            $meta->setMetaKey($key);
        }
        $meta->setMetaValue($value);

        $store->save($meta);
    }

    /**
     * Update status only if it synced with db
     *
     * @param Build   $build
     * @param integer $status
     *
     * @return boolean
     */
    public function updateStatusSync($build, $status)
    {
        try {
            $query = 'UPDATE {{build}} SET status = :status_new WHERE {{id}} = :id AND {{status}} = :status_current';
            $stmt = Database::getConnection('write')->prepareCommon($query);
            $stmt->bindValue(':id', $build->getId(), \PDO::PARAM_INT);
            $stmt->bindValue(':status_current', $build->getStatus(), \PDO::PARAM_INT);
            $stmt->bindValue(':status_new', $status, \PDO::PARAM_INT);
            return ($stmt->execute() && ($stmt->rowCount() == 1));
        } catch (\Exception $e) {
            return false;
        }
    }
}
