<?php

namespace PHPCensor\Store;

use b8\Database;
use PHPCensor\Model\Project;
use PHPCensor\Store;
use b8\Exception\HttpException;

/**
 * @author Dan Cryer <dan@block8.co.uk>
 */
class ProjectStore extends Store
{
    /**
     * @var string
     */
    protected $tableName  = 'project';

    /**
     * @var string
     */
    protected $modelName  = '\PHPCensor\Model\Project';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Get a Project by primary key (Id)
     *
     * @param integer $key
     * @param string  $useConnection
     *
     * @return Project|null
     */
    public function getByPrimaryKey($key, $useConnection = 'read')
    {
        return $this->getById($key, $useConnection);
    }

    /**
     * Get a single Project by Id.
     *
     * @param integer $id
     * @param string  $useConnection
     *
     * @return Project|null
     *
     * @throws HttpException
     */
    public function getById($id, $useConnection = 'read')
    {
        if (is_null($id)) {
            throw new HttpException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        $query = 'SELECT * FROM {{project}} WHERE {{id}} = :id LIMIT 1';
        $stmt = Database::getConnection($useConnection)->prepareCommon($query);
        $stmt->bindValue(':id', $id);

        if ($stmt->execute()) {
            if ($data = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                return new Project($data);
            }
        }

        return null;
    }

    /**
     * Get a single Project by Ids.
     *
     * @param integer[] $values
     * @param string    $useConnection
     *
     * @throws HttpException
     *
     * @return Project[]
     */
    public function getByIds($values, $useConnection = 'read')
    {
        if (empty($values)) {
            throw new HttpException('Values passed to ' . __FUNCTION__ . ' cannot be empty.');
        }

        $query = 'SELECT * FROM {{project}} WHERE {{id}} IN ('.implode(', ', array_map('intval', $values)).')';
        $stmt = Database::getConnection($useConnection)->prepareCommon($query);

        $rtn = [];
        if ($stmt->execute()) {
            while ($data = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $rtn[$data['id']] = new Project($data);
            }
        }

        return $rtn;
    }

    /**
     * Get multiple Project by Title.
     *
     * @param string  $title
     * @param integer $limit
     * @param string  $useConnection
     *
     * @return array
     *
     * @throws HttpException
     */
    public function getByTitle($title, $limit = 1000, $useConnection = 'read')
    {
        if (is_null($title)) {
            throw new HttpException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }


        $query = 'SELECT * FROM {{project}} WHERE {{title}} = :title LIMIT :limit';
        $stmt = Database::getConnection($useConnection)->prepareCommon($query);
        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);

        if ($stmt->execute()) {
            $res = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $map = function ($item) {
                return new Project($item);
            };
            $rtn = array_map($map, $res);

            $count = count($rtn);

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
    public function getKnownBranches($projectId)
    {
        $query = 'SELECT DISTINCT {{branch}} from {{build}} WHERE {{project_id}} = :pid';
        $stmt = Database::getConnection('read')->prepareCommon($query);
        $stmt->bindValue(':pid', $projectId);

        if ($stmt->execute()) {
            $res = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $map = function ($item) {
                return $item['branch'];
            };
            $rtn = array_map($map, $res);

            return $rtn;
        } else {
            return [];
        }
    }

    /**
     * Get a list of all projects, ordered by their title.
     *
     * @param boolean $archived
     *
     * @return array
     */
    public function getAll($archived = false)
    {
        $archived = (integer)$archived;

        $query = 'SELECT * FROM {{project}} WHERE {{archived}} = :archived ORDER BY {{title}} ASC';
        $stmt  = Database::getConnection('read')->prepareCommon($query);

        $stmt->bindValue(':archived', $archived);

        if ($stmt->execute()) {
            $res = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $map = function ($item) {
                return new Project($item);
            };
            $rtn = array_map($map, $res);

            $count = count($rtn);


            return ['items' => $rtn, 'count' => $count];
        } else {
            return ['items' => [], 'count' => 0];
        }
    }

    /**
     * Get multiple Project by GroupId.
     *
     * @param integer $groupId
     * @param boolean $archived
     * @param integer $limit
     * @param string  $useConnection
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getByGroupId($groupId, $archived = false, $limit = 1000, $useConnection = 'read')
    {
        if (is_null($groupId)) {
            throw new \Exception('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }
        $archived = (integer)$archived;

        $query = 'SELECT * FROM {{project}} WHERE {{group_id}} = :group_id AND {{archived}} = :archived ORDER BY {{title}} LIMIT :limit';
        $stmt  = Database::getConnection($useConnection)->prepareCommon($query);

        $stmt->bindValue(':group_id', $groupId);
        $stmt->bindValue(':archived', $archived);
        $stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);

        if ($stmt->execute()) {
            $res = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $map = function ($item) {
                return new Project($item);
            };
            $rtn = array_map($map, $res);

            $count = count($rtn);

            return ['items' => $rtn, 'count' => $count];
        } else {
            return ['items' => [], 'count' => 0];
        }
    }
}
