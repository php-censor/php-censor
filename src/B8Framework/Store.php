<?php

namespace b8;

abstract class Store
{
    /**
     * @var string
     */
    protected $modelName = null;

    /**
     * @var string
     */
    protected $tableName = null;

    /**
     * @var string
     */
    protected $primaryKey = null;

    /**
     * @param string $key
     * @param string $useConnection
     *
     * @return Model|null
     */
    abstract public function getByPrimaryKey($key, $useConnection = 'read');

    /**
     * @throws \RuntimeException
     */
    public function __construct()
    {
        if (empty($this->primaryKey)) {
            throw new \RuntimeException('Save not implemented for this store.');
        }
    }

    /**
     * @param array   $where
     * @param integer $limit
     * @param integer $offset
     * @param array   $order
     * @param string  $whereType
     *
     * @return array
     */
    public function getWhere(
        $where = [],
        $limit = 25,
        $offset = 0,
        $order = [],
        $whereType = 'AND'
    ) {
        $query      = 'SELECT * FROM {{' . $this->tableName . '}}';
        $countQuery = 'SELECT COUNT(*) AS {{count}} FROM {{' . $this->tableName . '}}';

        $wheres = [];
        $params = [];
        foreach ($where as $key => $value) {
            $key = $this->fieldCheck($key);

            if (!is_array($value)) {
                $params[] = $value;
                $wheres[] = $key . ' = ?';
            }
        }

        if (count($wheres)) {
            $query .= ' WHERE (' . implode(' ' . $whereType . ' ', $wheres) . ')';
            $countQuery .= ' WHERE (' . implode(' ' . $whereType . ' ', $wheres) . ')';
        }

        if (count($order)) {
            $orders = [];
            foreach ($order as $key => $value) {
                $orders[] = $this->fieldCheck($key) . ' ' . $value;
            }

            $query .= ' ORDER BY ' . implode(', ', $orders);
        }

        if ($limit) {
            $query .= ' LIMIT ' . $limit;
        }

        if ($offset) {
            $query .= ' OFFSET ' . $offset;
        }

        $stmt = Database::getConnection('read')->prepareCommon($countQuery);
        $stmt->execute($params);
        $res = $stmt->fetch(\PDO::FETCH_ASSOC);
        $count = (int)$res['count'];

        $stmt = Database::getConnection('read')->prepareCommon($query);
        $stmt->execute($params);
        $res = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $rtn = [];

        foreach ($res as $data) {
            $rtn[] = new $this->modelName($data);
        }

        return ['items' => $rtn, 'count' => $count];
    }

    /**
     * @param Model   $obj
     * @param boolean $saveAllColumns
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     *
     * @return Model|null
     */
    public function save(Model $obj, $saveAllColumns = false)
    {
        if (!($obj instanceof $this->modelName)) {
            throw new \InvalidArgumentException(get_class($obj) . ' is an invalid model type for this store.');
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
     */
    public function saveByUpdate(Model $obj, $saveAllColumns = false)
    {
        $rtn = null;
        $data = $obj->getDataArray();
        $modified = ($saveAllColumns) ? array_keys($data) : $obj->getModified();

        $updates       = [];
        $update_params = [];
        foreach ($modified as $key) {
            $updates[]       = $key . ' = :' . $key;
            $update_params[] = [$key, $data[$key]];
        }

        if (count($updates)) {
            $qs = 'UPDATE {{' . $this->tableName . '}} SET ' . implode(', ', $updates) . ' WHERE {{' . $this->primaryKey . '}} = :primaryKey';
            $q  = Database::getConnection('write')->prepareCommon($qs);

            foreach ($update_params as $update_param) {
                $q->bindValue(':' . $update_param[0], $update_param[1]);
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
     */
    public function saveByInsert(Model $obj, $saveAllColumns = false)
    {
        $rtn      = null;
        $data     = $obj->getDataArray();
        $modified = ($saveAllColumns) ? array_keys($data) : $obj->getModified();

        $cols    = [];
        $values  = [];
        $qParams = [];
        foreach ($modified as $key) {
            $cols[]              = $key;
            $values[]            = ':' . $key;
            $qParams[':' . $key] = $data[$key];
        }

        if (count($cols)) {
            $qs = 'INSERT INTO {{' . $this->tableName . '}} (' . implode(', ', $cols) . ') VALUES (' . implode(', ', $values) . ')';
            $q = Database::getConnection('write')->prepareCommon($qs);

            if ($q->execute($qParams)) {
                $id  = Database::getConnection('write')->lastInsertIdExtended($obj->getTableName());
                $rtn = $this->getByPrimaryKey($id, 'write');
            }
        }

        return $rtn;
    }

    /**
     * @param Model $obj
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     *
     * @return boolean
     */
    public function delete(Model $obj)
    {
        if (!($obj instanceof $this->modelName)) {
            throw new \InvalidArgumentException(get_class($obj) . ' is an invalid model type for this store.');
        }

        $data = $obj->getDataArray();

        $q = Database::getConnection('write')->prepareCommon('DELETE FROM {{' . $this->tableName . '}} WHERE {{' . $this->primaryKey . '}} = :primaryKey');
        $q->bindValue(':primaryKey', $data[$this->primaryKey]);
        $q->execute();

        return true;
    }

    /**
     * @param string $field
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    protected function fieldCheck($field)
    {
        if (empty($field)) {
            throw new \InvalidArgumentException('You cannot have an empty field name.');
        }

        if (strpos($field, '.') === false) {
            return '{{' . $this->tableName . '}}.{{' . $field . '}}';
        }

        return $field;
    }
}
