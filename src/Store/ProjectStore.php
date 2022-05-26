<?php

declare(strict_types=1);

namespace PHPCensor\Store;

use Exception;
use PDO;
use PHPCensor\DatabaseManager;
use PHPCensor\Exception\HttpException;
use PHPCensor\Model\Environment;
use PHPCensor\Model\Project;
use PHPCensor\Store;
use PHPCensor\StoreRegistry;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class ProjectStore extends Store
{
    protected string $tableName = 'projects';

    protected string $modelName = Project::class;

    private BuildStore $buildStore;
    private EnvironmentStore $environmentStore;

    public function __construct(
        DatabaseManager $databaseManager,
        StoreRegistry $storeRegistry
    ) {
        parent::__construct($databaseManager, $storeRegistry);

        $this->buildStore = $this->storeRegistry->get('Build');
        $this->environmentStore = $this->storeRegistry->get('Environment');
    }

    /**
     * Get a single Project by Ids.
     *
     * @param int[] $values
     *
     * @throws HttpException
     *
     * @return Project[]
     */
    public function getByIds(array $values, string $useConnection = 'read'): array
    {
        if (empty($values)) {
            throw new HttpException('Values passed to ' . __FUNCTION__ . ' cannot be empty.');
        }

        $query = 'SELECT * FROM {{' . $this->tableName . '}} WHERE {{id}} IN (' . \implode(', ', \array_map('intval', $values)).')';
        $stmt  = $this->databaseManager->getConnection($useConnection)->prepare($query);

        $rtn = [];
        if ($stmt->execute()) {
            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $rtn[$data['id']] = new Project($this->buildStore, $this->environmentStore, $data);
            }
        }

        return $rtn;
    }

    /**
     * Get multiple Project by Title.
     *
     * @throws HttpException
     */
    public function getByTitle(string $title, int $limit = 1000, string $useConnection = 'read'): array
    {
        if (\is_null($title)) {
            throw new HttpException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        $query = 'SELECT * FROM {{' . $this->tableName . '}} WHERE {{title}} = :title LIMIT :limit';
        $stmt  = $this->databaseManager->getConnection($useConnection)->prepare($query);
        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $map = fn ($item) => new Project($this->buildStore, $this->environmentStore, $item);
            $rtn = \array_map($map, $res);

            $count = \count($rtn);

            return ['items' => $rtn, 'count' => $count];
        } else {
            return ['items' => [], 'count' => 0];
        }
    }

    /**
     * Returns a list of all branch names.
     *
     * @param $projectId
     */
    public function getKnownBranches(int $projectId): array
    {
        $query = 'SELECT {{branch}}, COUNT(1) AS {{count}} from {{builds}} WHERE {{project_id}} = :pid GROUP BY {{branch}} ORDER BY {{count}} DESC';
        $stmt  = $this->databaseManager->getConnection('read')->prepare($query);
        $stmt->bindValue(':pid', $projectId);

        if ($stmt->execute()) {
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $map = fn ($item) => $item['branch'];

            return \array_map($map, $res);
        } else {
            return [];
        }
    }

    /**
     * Get a list of all projects, ordered by their title.
     */
    public function getAll(string $useConnection = 'read', bool $archived = false): array
    {
        $archived = (int)$archived;

        $query = 'SELECT * FROM {{' . $this->tableName . '}} WHERE {{archived}} = :archived ORDER BY {{title}} ASC';
        $stmt  = $this->databaseManager->getConnection('read')->prepare($query);

        $stmt->bindValue(':archived', $archived);

        if ($stmt->execute()) {
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $map = fn ($item) => new Project($this->buildStore, $this->environmentStore, $item);
            $rtn = \array_map($map, $res);

            $count = \count($rtn);


            return ['items' => $rtn, 'count' => $count];
        } else {
            return ['items' => [], 'count' => 0];
        }
    }

    /**
     * Get multiple Project by GroupId.
     *
     * @throws Exception
     */
    public function getByGroupId(int $groupId, bool $archived = false, int $limit = 1000, string $useConnection = 'read'): array
    {
        if (\is_null($groupId)) {
            throw new HttpException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }
        $archived = (int)$archived;

        $query = 'SELECT * FROM {{' . $this->tableName . '}} WHERE {{group_id}} = :group_id AND {{archived}} = :archived ORDER BY {{title}} LIMIT :limit';
        $stmt  = $this->databaseManager->getConnection($useConnection)->prepare($query);

        $stmt->bindValue(':group_id', $groupId);
        $stmt->bindValue(':archived', $archived);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $map = fn ($item) => new Project($this->buildStore, $this->environmentStore, $item);
            $rtn = \array_map($map, $res);

            $count = \count($rtn);

            return ['items' => $rtn, 'count' => $count];
        } else {
            return ['items' => [], 'count' => 0];
        }
    }
}
