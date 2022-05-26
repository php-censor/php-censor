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
    protected string $tableName = 'project_groups';

    protected string $modelName = ProjectGroup::class;

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
                return new ProjectGroup($data);
            }
        }

        return null;
    }
}
