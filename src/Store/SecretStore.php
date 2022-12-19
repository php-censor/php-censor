<?php

declare(strict_types=1);

namespace PHPCensor\Store;

use PHPCensor\Exception\HttpException;
use PHPCensor\Model\Secret;
use PHPCensor\Store;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class SecretStore extends Store
{
    protected string $tableName = 'secrets';

    protected string $modelName = Secret::class;

    /**
     * Get a single Project by Ids.
     *
     * @param string[] $values
     *
     * @throws HttpException
     *
     * @return Secret[]
     */
    public function getByNames(array $values, string $useConnection = 'read'): array
    {
        if (empty($values)) {
            throw new HttpException('Values passed to ' . __FUNCTION__ . ' cannot be empty.');
        }

        $query = \sprintf(
            'SELECT * FROM {{%s}} WHERE {{name}} IN (%s)',
            $this->tableName,
            ("'" . \implode('\', \'', $values) . "'")
        );
        $stmt = $this->databaseManager->getConnection($useConnection)->prepare($query);

        $rtn = [];
        if ($stmt->execute()) {
            while ($data = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $rtn[$data['name']] = new Secret($data);
            }
        }

        return $rtn;
    }
}
