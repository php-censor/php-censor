<?php

declare(strict_types=1);

namespace PHPCensor\Store;

use Exception;
use PDO;
use PHPCensor\Exception\HttpException;
use PHPCensor\Model\Environment;
use PHPCensor\Store;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class EnvironmentStore extends Store
{
    protected string $tableName = 'environments';

    protected string $modelName = '\PHPCensor\Model\Environment';

    /**
     * Get a single Environment by Name.
     *
     * @throws HttpException
     */
    public function getByNameAndProjectId(string $name, int $projectId, string $useConnection = 'read'): ?Environment
    {
        if (\is_null($name)) {
            throw new HttpException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        $query = 'SELECT * FROM {{' . $this->tableName . '}} WHERE {{name}} = :name AND {{project_id}} = :project_id LIMIT 1';
        $stmt  = $this->databaseManager->getConnection($useConnection)->prepare($query);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':project_id', $projectId);

        if ($stmt->execute()) {
            if ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return new Environment($this->storeRegistry, $data);
            }
        }

        return null;
    }

    /**
     * Get multiple Environment by Project id.
     *
     * @throws Exception
     */
    public function getByProjectId(int $projectId, string $useConnection = 'read'): array
    {
        if (\is_null($projectId)) {
            throw new HttpException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        $query = 'SELECT * FROM {{' . $this->tableName . '}} WHERE {{project_id}} = :project_id';
        $stmt  = $this->databaseManager->getConnection($useConnection)->prepare($query);

        $stmt->bindValue(':project_id', $projectId);

        if ($stmt->execute()) {
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $map = function ($item) {
                return new Environment($this->storeRegistry, $item);
            };
            $rtn = \array_map($map, $res);

            $count = \count($rtn);

            return ['items' => $rtn, 'count' => $count];
        } else {
            return ['items' => [], 'count' => 0];
        }
    }
}
