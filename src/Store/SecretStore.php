<?php

declare(strict_types=1);

namespace PHPCensor\Store;

use PHPCensor\Common\Repository\SecretRepositoryInterface;
use PHPCensor\Exception\HttpException;
use PHPCensor\Model\Secret;
use PHPCensor\Store;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class SecretStore extends Store implements SecretRepositoryInterface
{
    protected string $tableName = 'secrets';

    protected string $modelName = Secret::class;

    /**
     * {@inheritDoc}
     */
    public function getByNames(array $names): array
    {
        if (empty($names)) {
            throw new HttpException('Values passed to ' . __FUNCTION__ . ' cannot be empty.');
        }

        $query = \sprintf(
            'SELECT * FROM {{%s}} WHERE {{name}} IN (%s)',
            $this->tableName,
            ("'" . \implode('\', \'', $names) . "'")
        );
        $stmt = $this->databaseManager->getConnection()->prepare($query);

        $rtn = [];
        if ($stmt->execute()) {
            while ($data = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $rtn[$data['name']] = new Secret($this->storeRegistry, $data);
            }
        }

        return $rtn;
    }
}
