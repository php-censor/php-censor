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
    protected $tableName   = 'project';
    protected $modelName   = '\PHPCensor\Model\Project';
    protected $primaryKey  = 'id';

    /**
     * Get a Project by primary key (Id)
     */
    public function getByPrimaryKey($value, $useConnection = 'read')
    {
        return $this->getById($value, $useConnection);
    }

    /**
     * Get a single Project by Id.
     * @return null|Project
     */
    public function getById($value, $useConnection = 'read')
    {
        if (is_null($value)) {
            throw new HttpException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        $query = 'SELECT * FROM {{project}} WHERE {{id}} = :id LIMIT 1';
        $stmt = Database::getConnection($useConnection)->prepareCommon($query);
        $stmt->bindValue(':id', $value);

        if ($stmt->execute()) {
            if ($data = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                return new Project($data);
            }
        }

        return null;
    }

    /**
     * Get multiple Project by Title.
     * @return array
     */
    public function getByTitle($value, $limit = 1000, $useConnection = 'read')
    {
        if (is_null($value)) {
            throw new HttpException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }


        $query = 'SELECT * FROM {{project}} WHERE {{title}} = :title LIMIT :limit';
        $stmt = Database::getConnection($useConnection)->prepareCommon($query);
        $stmt->bindValue(':title', $value);
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
     * Returns a list of all branch names PHPCI has run builds against.
     * @param $projectId
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
     * @param integer $value
     * @param boolean $archived
     * @param integer $limit
     * @param string  $useConnection
     * 
     * @return array
     * 
     * @throws \Exception
     */
    public function getByGroupId($value, $archived = false, $limit = 1000, $useConnection = 'read')
    {
        if (is_null($value)) {
            throw new \Exception('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }
        $archived = (integer)$archived;

        $query = 'SELECT * FROM {{project}} WHERE {{group_id}} = :group_id AND {{archived}} = :archived ORDER BY {{title}} LIMIT :limit';
        $stmt  = Database::getConnection($useConnection)->prepareCommon($query);

        $stmt->bindValue(':group_id', $value);
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
