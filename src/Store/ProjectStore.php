<?php

declare(strict_types = 1);

namespace PHPCensor\Store;

use Exception;
use PDO;
use PHPCensor\Exception\HttpException;
use PHPCensor\Model\Project;
use PHPCensor\Store;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class ProjectStore extends Store
{
    protected string $tableName  = 'projects';

    protected ?string $modelName  = '\PHPCensor\Model\Project';

    protected ?string $primaryKey = 'id';

    /**
     * Get a Project by primary key (Id)
     *
     * @param int    $key
     * @param string $useConnection
     *
     * @return Project|null
     */
    public function getByPrimaryKey(int $key, string $useConnection = 'read'): ?Project
    {
        return $this->getById($key, $useConnection);
    }

    /**
     * Get a single Project by Id.
     *
     * @param int    $id
     * @param string $useConnection
     *
     * @return Project|null
     *
     * @throws HttpException
     */
    public function getById(int $id, string $useConnection = 'read'): ?Project
    {
        if (\is_null($id)) {
            throw new HttpException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        $query = 'SELECT * FROM {{' . $this->tableName . '}} WHERE {{id}} = :id LIMIT 1';
        $stmt  = $this->databaseManager->getConnection($useConnection)->prepare($query);
        $stmt->bindValue(':id', $id);

        if ($stmt->execute()) {
            if ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return new Project($this->storeRegistry, $data);
            }
        }

        return null;
    }

    /**
     * Get a single Project by Ids.
     *
     * @param int[] $values
     * @param string    $useConnection
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
                $rtn[$data['id']] = new Project($this->storeRegistry, $data);
            }
        }

        return $rtn;
    }

    /**
     * Get multiple Project by Title.
     *
     * @param string  $title
     * @param int $limit
     * @param string  $useConnection
     *
     * @return array
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

            $map = function ($item) {
                return new Project($this->storeRegistry, $item);
            };
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
     *
     * @return array
     */
    public function getKnownBranches(int $projectId): array
    {
        $query = 'SELECT {{branch}}, COUNT(1) AS {{count}} from {{builds}} WHERE {{project_id}} = :pid GROUP BY {{branch}} ORDER BY {{count}} DESC';
        $stmt  = $this->databaseManager->getConnection('read')->prepare($query);
        $stmt->bindValue(':pid', $projectId);

        if ($stmt->execute()) {
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $map = function ($item) {
                return $item['branch'];
            };

            return \array_map($map, $res);
        } else {
            return [];
        }
    }

    /**
     * Get a list of all projects, ordered by their title.
     *
     * @param bool $archived
     *
     * @return array
     */
    public function getAll(bool $archived = false): array
    {
        $archived = (int)$archived;

        $query = 'SELECT * FROM {{' . $this->tableName . '}} WHERE {{archived}} = :archived ORDER BY {{title}} ASC';
        $stmt  = $this->databaseManager->getConnection('read')->prepare($query);

        $stmt->bindValue(':archived', $archived);

        if ($stmt->execute()) {
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $map = function ($item) {
                return new Project($this->storeRegistry, $item);
            };
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
     * @param int $groupId
     * @param bool $archived
     * @param int $limit
     * @param string  $useConnection
     *
     * @return array
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
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $map = function ($item) {
                return new Project($this->storeRegistry, $item);
            };
            $rtn = \array_map($map, $res);

            $count = \count($rtn);

            return ['items' => $rtn, 'count' => $count];
        } else {
            return ['items' => [], 'count' => 0];
        }
    }
}
