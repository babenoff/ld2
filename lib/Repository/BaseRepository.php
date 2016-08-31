<?php
/**
 * Copyright (c) 2016. Maksim Babenko <mb.babenoff@yandex.ru>
 */

namespace LD2\Repository;


use LD2\Database;
use LD2\Exception\RepositoryException;
use LD2\Exception\UserRepositoryException;
use LD2\QueryBuilder\Additional\AllFields;
use LD2\QueryBuilder\Additional\Condition;
use LD2\QueryBuilder\Additional\Field;
use LD2\QueryBuilder\Additional\Operator;
use LD2\QueryBuilder\BasicQuery;
use LD2\QueryBuilder\SelectQuery;

/**
 * Class AbstractRepository
 * @package LD2\Repository
 */
abstract class BaseRepository
{
    protected $_tables = [];
    /**
     * @var Database
     */
    protected $pdo;

    /**
     * @return Database
     */
    public function getPdo(): Database
    {
        return $this->pdo;
    }

    /**
     * @param Database $pdo
     */
    public function setPdo(Database $pdo)
    {
        $this->pdo = $pdo;
    }

    public function setTables(array $tables)
    {
        $this->_tables = $tables;
    }

    /**
     * @return array
     */
    public function getTables(): array
    {
        return $this->_tables;
    }

    /**
     * @param int $id
     * @return array
     */
    public function findById(int $id):array{
        return $this->_getByField("id", $id);
    }

    /**
     * @param $fieldId
     * @param $fieldVal
     * @param array $selects
     * @param bool $many
     * @return array
     * @throws RepositoryException
     */
    protected function _getByField($fieldId, $fieldVal, $selects = [], $many = false)
    {
        if (empty($selects)) {
            array_push($selects, new AllFields());
        }
        $sql = $this->getPdo()->queryBuilder()->select($this->getTables());
        $sql->setSelect($selects);
        $sql->setWhere(
            new Condition(Condition::EQ, new Field($fieldId), $fieldVal)
        );
        $query = $this->getPdo()->prepare($sql->sql());
        $query->execute($sql->parameters());
        $res = $query->fetchAll(\PDO::FETCH_ASSOC);
        if (count($res) > 0) {
            return (false === $many) ? $res[0] : $res;
        } else {
            throw new RepositoryException();
        }
    }

    abstract protected function getConstraintColumns():array;
    abstract protected function getUpdateColumnsClackList():array;

    protected function _checkData(array $data):bool
    {
        $keys = array_keys($data);
        $constraintKeys = $this->getConstraintColumns();
        $iterator = new \ArrayIterator($constraintKeys);
        do {
            if (in_array($iterator->current(), $keys)) {
                $iterator->next();
            } else {
                return false;
            }
        } while ($iterator->valid());
        return true;
    }

    protected function _create(array $data)
    {
        $sql = $this->getPdo()->queryBuilder()->insert($this->getTables());
        $sql->setValues($data);
        $query = $this->getPdo()->prepare($sql->sql());
        return $this->_execute($sql, $query);
    }

    /**
     * @param array $data
     * @param Condition|Operator $conditions
     * @return bool
     * @throws RepositoryException
     */
    protected function _update(array $data, $conditions):bool
    {
        if($this->_checkData($data)) {
            $sql = $this->getPdo()->queryBuilder()->update($this->getTables());
            $sql->setValues($data);
            $sql->setWhere($conditions);
            $pdo = $this->getPdo();
            $query = $pdo->prepare($sql->sql());
            return $this->_execute($sql, $query);
        } else {
            throw new RepositoryException("invalid data in update");
        }
    }

    private function _execute(BasicQuery $sql, \PDOStatement $query){
        $res = $query->execute($sql->parameters());
        if (!$res) {
            $mess = $query->errorInfo()[2];
            throw new RepositoryException($mess);
        }
        return $res;
    }

    protected function _remove($id):bool {
        $sql = $this->getPdo()->queryBuilder()->delete($this->getTables());
        $sql->setWhere(new Condition(Condition::EQ, new Field("id"), $id));
        $query = $this->getPdo()->prepare($sql->sql());
        return $this->_execute($sql, $query);
    }

    protected function getNotNullColumns($table){
        /*return [
            "username",
            "email"
        ];*/
        $sql = "SHOW COLUMNS FROM `$table`";
        $stmt = $this->getPdo()->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $cols = [];

        foreach ($rows as $row){
            if($row["Field"] != "id" and $row["Null"] == "NO" and is_null($row["Default"])){
                array_push($cols, $row["Field"]);
            }
        }
        return $cols;
    }
}