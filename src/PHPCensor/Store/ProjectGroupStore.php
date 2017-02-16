<?php

namespace PHPCensor\Store;

use b8\Database;
use b8\Exception\HttpException;
use PHPCensor\Store;
use PHPCensor\Model\ProjectGroup;

class ProjectGroupStore extends Store
{
    protected $tableName   = 'project_group';
    protected $modelName   = '\PHPCensor\Model\ProjectGroup';
    protected $primaryKey  = 'id';

    /**
     * Get a ProjectGroup by primary key (Id)
     */
    public function getByPrimaryKey($value, $useConnection = 'read')
    {
        return $this->getById($value, $useConnection);
    }

    /**
     * Get a single ProjectGroup by Id.
     *
     * @param integer $value
     * @param string  $useConnection
     *
     * @return ProjectGroup|null
     *
     * @throws HttpException
     */
    public function getById($value, $useConnection = 'read')
    {
        if (is_null($value)) {
            throw new HttpException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        $query = 'SELECT * FROM {{project_group}} WHERE {{id}} = :id LIMIT 1';
        $stmt  = Database::getConnection($useConnection)->prepareCommon($query);

        $stmt->bindValue(':id', $value);

        if ($stmt->execute()) {
            if ($data = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                return new ProjectGroup($data);
            }
        }

        return null;
    }

    /**
     * Get a single ProjectGroup by title.
     *
     * @param integer $value
     * @param string  $useConnection
     *
     * @return ProjectGroup|null
     *
     * @throws HttpException
     */
    public function getByTitle($value, $useConnection = 'read')
    {
        if (is_null($value)) {
            throw new HttpException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        $query = 'SELECT * FROM {{project_group}} WHERE {{title}} = :title LIMIT 1';
        $stmt  = Database::getConnection($useConnection)->prepareCommon($query);

        $stmt->bindValue(':title', $value);

        if ($stmt->execute()) {
            if ($data = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                return new ProjectGroup($data);
            }
        }

        return null;
    }
}
