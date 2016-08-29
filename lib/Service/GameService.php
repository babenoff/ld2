<?php
/**
 * Copyright (c) 2016. Maksim Babenko <mb.babenoff@yandex.ru>
 */

namespace LD2\Service;


use LD2\Database;
use LD2\Exception\GameException;
use LD2\QueryBuilder\Additional\AllFields;
use LD2\QueryBuilder\Additional\Condition;
use LD2\QueryBuilder\Additional\Field;

class GameService implements IGameService
{
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

    /**
     * @param $id
     * @return array
     * @throws GameException
     */
    public function getLocById($id):array
    {
        return $this->_get("id", $id);
    }

    /**
     * @param $locationId
     * @return array
     * @throws GameException
     */
    public function getLocByLocationId($locationId):array
    {
        return $this->_get("location_id", $locationId);
    }

    /**
     * @param string $locId
     * @param array $location
     * @return bool
     * @deprecated
     */
    public function update(string $locId, array $location):bool
    {
        $update = $this->getPdo()->queryBuilder()->update(["game"]);
        $update->setWhere(new Condition(Condition::EQ, new Field("location_id"), 1));
        $update->setValues($location);
        $stmt = $this->getPdo()->prepare($update->sql());
        $this->_bindParams($stmt, $update->parameters());
        return $stmt->execute();
    }

    /**
     * @param string $locId
     * @param array $location
     * @throws GameException
     * @return bool
     */
    public function save(string $locId, array $location):bool
    {
        $insert = $this->getPdo()->queryBuilder()->insert(["game"], true);
        $insert->setValues($location);
        $insert->setWhere(new Condition(
            Condition::EQ,
            new Field("location_id", 0, "lid"),
            $locId
        ));
        $sql = $insert->sql();
        $params = $insert->parameters();
        $stmt = $this->getPdo()->prepare($sql);
        $this->_bindParams($stmt, $params);
        return $stmt->execute();
    }

    /**
     * @param \PDOStatement $stmt
     * @param $params
     */
    private function _bindParams(&$stmt, $params){
        foreach ($params as $key=>$val) {
            $stmt->bindValue($key, $val);
        }
    }

    private function _get($field, $val, array $fields = [])
    {
        $select = $this->getPdo()->queryBuilder()->select(["game"]);
        if (count($fields) < 1) {
            $select->setSelect([new AllFields()]);
        } else {
            $selects = [];
            for ($i = 0; $i < count($fields); $i++) {
                array_push($selects, new Field($fields[$i]));
            }
            $select->setSelect($selects);
        }
        $select->setWhere(new Condition(Condition::EQ, $field, $val));
        $stmt = $this->getPdo()->prepare($select->sql());
        $this->_bindParams($stmt, $select->parameters());
        $stmt->execute();
        $res = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!is_array($res)) {
            throw new GameException(sprintf("Loc id%s not exists into database", $val));
        }
        return $res;
    }

    /**
     * @param string $prefix
     * @param $x
     * @param $y
     * @param array $blackList
     * @return array
     */
    private function _buildNeigboringLocIds(string $prefix, $x, $y, array $blackList = [])
    {
        $locs = [];
        for ($i = -1; $i < 2; $i++) {
            $xx = $x;
            $xx += $i;
            for ($j = -1; $j < 2; $j++) {
                $yy = $y;
                $yy += $j;
                $coord = implode("x", [$xx, $yy]);
                $lId = implode("_", [$prefix, $coord]);
                if (!in_array($lId, $blackList)) {
                    array_push($locs, $lId);
                }
            }
        }
        return $locs;
    }

    /**
     * @param $locId
     * @return array
     */
    public function getNeighboringLocsIds(string $locId):array
    {
        list($prefix, $coord) = explode("_", $locId);
        list($x, $y) = explode("x", $coord);
        $locs = $this->_buildNeigboringLocIds($prefix, $x, $y, [$locId]);
        return $locs;
    }
}