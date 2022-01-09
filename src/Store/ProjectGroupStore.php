<?php

declare(strict_types=1);

namespace PHPCensor\Store;

use PDO;
use PHPCensor\Exception\HttpException;
use PHPCensor\Model\ProjectGroup;
use PHPCensor\Store;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class ProjectGroupStore extends Store
{
    protected string $tableName  = 'project_groups';

    protected ?string $modelName  = '\PHPCensor\Model\ProjectGroup';

    protected ?string $primaryKey = 'id';

    /**
     * Get a ProjectGroup by primary key (Id)
     */
    public function getByPrimaryKey(int $key, string $useConnection = 'read'): ?ProjectGroup
    {
        return $this->getById($key, $useConnection);
    }

    /**
     * Get a single ProjectGroup by Id.
     *
     * @throws HttpException
     */
    public function getById(int $id, string $useConnection = 'read'): ?ProjectGroup
    {
        if (\is_null($id)) {
            throw new HttpException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        $query = 'SELECT * FROM {{' . $this->tableName . '}} WHERE {{id}} = :id LIMIT 1';
        $stmt  = $this->databaseManager->getConnection($useConnection)->prepare($query);

        $stmt->bindValue(':id', $id);

        if ($stmt->execute()) {
            if ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return new ProjectGroup($this->storeRegistry, $data);
            }
        }

        return null;
    }

    /**
     * Get a single ProjectGroup by title.
     *
     * @throws HttpException
     */
    public function getByTitle(string $title, string $useConnection = 'read'): ?ProjectGroup
    {
        if (\is_null($title)) {
            throw new HttpException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        $query = 'SELECT * FROM {{' . $this->tableName . '}} WHERE {{title}} = :title LIMIT 1';
        $stmt  = $this->databaseManager->getConnection($useConnection)->prepare($query);

        $stmt->bindValue(':title', $title);

        if ($stmt->execute()) {
            if ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return new ProjectGroup($this->storeRegistry, $data);
            }
        }

        return null;
    }
}
