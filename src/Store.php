<?php

declare(strict_types = 1);

namespace PHPCensor;

use Exception;
use PDO;
use PHPCensor\Common\Exception\InvalidArgumentException;
use PHPCensor\Common\Exception\RuntimeException;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
abstract class Store
{
    protected ?string $modelName = null;

    protected string $tableName = '';

    protected ?string $primaryKey = null;

    protected DatabaseManager $databaseManager;

    protected StoreRegistry $storeRegistry;

    abstract public function getByPrimaryKey(int $key, string $useConnection = 'read'): ?Model;

    /**
     * @throws RuntimeException
     */
    public function __construct(
        DatabaseManager $databaseManager,
        StoreRegistry $storeRegistry
    ) {
        if (empty($this->primaryKey)) {
            throw new RuntimeException('Save not implemented for this store.');
        }

        $this->databaseManager = $databaseManager;
        $this->storeRegistry   = $storeRegistry;
    }

    /**
     * @param array  $where
     * @param int    $limit
     * @param int    $offset
     * @param array  $order
     * @param string $whereType
     *
     * @return array
     *
     * @throws Common\Exception\Exception
     * @throws InvalidArgumentException
     */
    public function getWhere(
        array $where = [],
        int $limit = 25,
        int $offset = 0,
        array $order = [],
        string $whereType = 'AND'
    ): array {
        $query      = 'SELECT * FROM {{' . $this->tableName . '}}';
        $countQuery = 'SELECT COUNT(*) AS {{count}} FROM {{' . $this->tableName . '}}';

        $wheres = [];
        $params = [];
        foreach ($where as $key => $value) {
            $key = $this->fieldCheck($key);

            if (!\is_array($value)) {
                $params[] = $value;
                $wheres[] = $key . ' = ?';
            }
        }

        if (\count($wheres)) {
            $query .= ' WHERE (' . \implode(' ' . $whereType . ' ', $wheres) . ')';
            $countQuery .= ' WHERE (' . \implode(' ' . $whereType . ' ', $wheres) . ')';
        }

        if (\count($order)) {
            $orders = [];
            foreach ($order as $key => $value) {
                $orders[] = $this->fieldCheck($key) . ' ' . $value;
            }

            $query .= ' ORDER BY ' . \implode(', ', $orders);
        }

        if ($limit) {
            $query .= ' LIMIT ' . $limit;
        }

        if ($offset) {
            $query .= ' OFFSET ' . $offset;
        }

        $stmt = $this->databaseManager->getConnection('read')->prepare($countQuery);
        $stmt->execute($params);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        $count = (int)$res['count'];

        $stmt = $this->databaseManager->getConnection('read')->prepare($query);
        $stmt->execute($params);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $rtn = [];

        foreach ($res as $data) {
            $rtn[] = new $this->modelName($this->storeRegistry, $data);
        }

        return ['items' => $rtn, 'count' => $count];
    }

    /**
     * @param Model $obj
     * @param bool  $saveAllColumns
     *
     * @return Model|null
     *
     * @throws InvalidArgumentException
     */
    public function save(Model $obj, bool $saveAllColumns = false): ?Model
    {
        if (!($obj instanceof $this->modelName)) {
            throw new InvalidArgumentException(get_class($obj) . ' is an invalid model type for this store.');
        }

        $data = $obj->getDataArray();

        if (isset($data[$this->primaryKey])) {
            $rtn = $this->saveByUpdate($obj, $saveAllColumns);
        } else {
            $rtn = $this->saveByInsert($obj, $saveAllColumns);
        }

        return $rtn;
    }

    /**
     * @param Model $obj
     * @param bool  $saveAllColumns
     *
     * @return Model|null
     *
     * @throws Exception
     */
    public function saveByUpdate(Model $obj, bool $saveAllColumns = false): ?Model
    {
        $data     = $obj->getDataArray();
        $modified = ($saveAllColumns) ? \array_keys($data) : $obj->getModified();

        $updates      = [];
        $updateParams = [];
        foreach ($modified as $key) {
            $updates[]      = $key . ' = :' . $key;
            $updateParams[] = [$key, $data[$key]];
        }

        if (\count($updates)) {
            $qs = \sprintf(
                'UPDATE {{%s}} SET %s WHERE {{%s}} = :primaryKey',
                $this->tableName,
                \implode(', ', $updates),
                $this->primaryKey
            );
            $q  = $this->databaseManager->getConnection('write')->prepare($qs);

            foreach ($updateParams as $updateParam) {
                $q->bindValue(':' . $updateParam[0], $updateParam[1]);
            }

            $q->bindValue(':primaryKey', $data[$this->primaryKey]);
            $q->execute();

            $rtn = $this->getByPrimaryKey($data[$this->primaryKey], 'write');
        } else {
            $rtn = $obj;
        }

        return $rtn;
    }

    /**
     * @param Model $obj
     * @param bool  $saveAllColumns
     *
     * @return Model|null
     *
     * @throws Exception
     */
    public function saveByInsert(Model $obj, bool $saveAllColumns = false): ?Model
    {
        $rtn      = null;
        $data     = $obj->getDataArray();
        $modified = ($saveAllColumns) ? \array_keys($data) : $obj->getModified();

        $cols    = [];
        $values  = [];
        $qParams = [];
        foreach ($modified as $key) {
            $cols[]              = $key;
            $values[]            = ':' . $key;
            $qParams[':' . $key] = $data[$key];
        }

        if (\count($cols)) {
            $qs = \sprintf(
                'INSERT INTO {{%s}} (%s) VALUES (%s)',
                $this->tableName,
                \implode(', ', $cols),
                \implode(', ', $values)
            );
            $q = $this->databaseManager->getConnection('write')->prepare($qs);

            if ($q->execute($qParams)) {
                $id  = $this->databaseManager->getConnection('write')->lastInsertId($this->tableName);
                $rtn = $this->getByPrimaryKey($id, 'write');
            }
        }

        return $rtn;
    }

    /**
     * @param Model $obj
     *
     * @return bool
     *
     * @throws Common\Exception\Exception
     * @throws InvalidArgumentException
     */
    public function delete(Model $obj): bool
    {
        if (!($obj instanceof $this->modelName)) {
            throw new InvalidArgumentException(get_class($obj) . ' is an invalid model type for this store.');
        }

        $data = $obj->getDataArray();

        $q = $this->databaseManager->getConnection('write')
            ->prepare(
                \sprintf(
                    'DELETE FROM {{%s}} WHERE {{%s}} = :primaryKey',
                    $this->tableName,
                    $this->primaryKey
                )
            );
        $q->bindValue(':primaryKey', $data[$this->primaryKey]);
        $q->execute();

        return true;
    }

    /**
     * @param string $field
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    protected function fieldCheck(string $field): string
    {
        if (empty($field)) {
            throw new InvalidArgumentException('You cannot have an empty field name.');
        }

        if (\strpos($field, '.') === false) {
            return '{{' . $this->tableName . '}}.{{' . $field . '}}';
        }

        return $field;
    }
}
