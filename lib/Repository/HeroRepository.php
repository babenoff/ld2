<?php
/**
 * Copyright (c) 2016. Maksim Babenko <mb.babenoff@yandex.ru>
 */

namespace LD2\Repository;


use LD2\Exception\HeroRepositoryException;
use LD2\Database;
use LD2\Exception\GameException;
use LD2\Exception\LocationsRepositoryException;
use LD2\Exception\RepositoryException;
use LD2\QueryBuilder\Additional\AllFields;
use LD2\QueryBuilder\Additional\Condition;
use LD2\QueryBuilder\Additional\Field;

class HeroRepository extends BaseRepository implements IHeroRepository
{

    /**
     * @param int $id
     * @return array
     * @throws HeroRepositoryException
     */
    public function getHeroById(int $id):array
    {
        return $this->_getHeroByField("id", $id);
    }

    /**
     * @param string $title
     * @return array
     */
    public function getHeroByTitle(string $title):array
    {
        return $this->_getHeroByField("title", $title);
    }

    /**
     * @param string $username
     * @return array
     */
    public function getHeroesByUsername(string $username):array
    {
        try{
            $res = $this->_getHeroByField("username", $username, true);
        } catch (HeroRepositoryException $e){
            $res = [];
        }
        return $res;
    }

    /**
     * @param string $username
     * @param string $title
     * @return array
     */
    /*public function getHeroByUsernameAndTitle(string $username, string $title):array
    {
        // TODO: Implement getHeroByUsernameAndTitle() method.
    }*/

    /**
     * @param string $locId
     * @return array
     */
    public function getHeroByLocationId(string $locId):array
    {
        try{
            $res = $this->_getHeroByField("location_id", $locId, true);
        } catch (HeroRepositoryException $e){
            $res = [];
        }
        return $res;
    }

    /**
     * @param array $data
     * @return bool
     * @throws HeroRepositoryException
     * @internal param $hero
     */
    public function update(array $data):bool
    {
        if(isset($data["id"])) {
            try {
                foreach ($data as $k => $v){
                    if(is_array($v)){
                        $data[$k] = serialize($v);
                    }
                }
                $res = $this->_update($data, new Condition(Condition::EQ, new Field("id"), $data["id"]));
                return $res;
            } catch (RepositoryException $e) {
                throw new HeroRepositoryException($e->getMessage(), $e->getCode(), $e);
            }
        } else {
            $mess = "the data must contain id";
            throw new HeroRepositoryException($mess);
        }
    }

    /**
     * @param array $data
     * @return bool
     * @throws HeroRepositoryException
     * @internal param $hero
     */
    public function remove(array $data):bool
    {
        $r = $this->getPdo()->queryBuilder()->delete($this->getTables());
        $r->setWhere(
            new Condition(Condition::EQ, new Field("id"), $data["id"])
        );
        $stmt = $this->getPdo()->prepare($r->sql());
        $params = $r->parameters();
        $res = $stmt->execute($params);
        if(!$res){
            $errInfo = $stmt->errorInfo();
            throw new HeroRepositoryException($errInfo[2]);
        } else {
            return $res;
        }
    }

    /**
     * @param array $data
     * @return bool
     * @throws HeroRepositoryException
     * @internal param $hero
     */
    public function create(array $data):bool
    {
        try{
            $this->getHeroByTitle($data["title"]);
            throw new HeroRepositoryException(sprintf("hero whit title %s already exists", $data["title"]));
        } catch (HeroRepositoryException $e){
            $in = $this->getPdo()->queryBuilder()->insert($this->getTables());
            foreach ($data as $k => $v){
                if(is_array($v)){
                    $data[$k] = serialize($v);
                }
            }
            $in->setValues($data);
            $stmt = $this->getPdo()->prepare($in->sql());
            $params = $in->parameters();
            $res = $stmt->execute($params);
            if(!$res){
                $errInfo = $stmt->errorInfo();
                throw new HeroRepositoryException($errInfo[2]);
            } else {
                return $res;
            }
        }
    }

    /**
     * @param \PDOStatement $query
     * @param bool $many
     * @return mixed
     * @throws HeroRepositoryException
     */
    private function _getHero(\PDOStatement $query, $many = false)
    {
        $heroes = $query->fetchAll(\PDO::FETCH_ASSOC);
        if (count($heroes) > 0) {
            return (false === $many) ? $heroes[0] : $heroes;
        } else {
            throw new HeroRepositoryException("hero not found");
        }
    }

    private function _getHeroByField($fieldId, $fieldVal, $many = false):array
    {
        $field = new Field($fieldId);
        $select = $this->getPdo()->queryBuilder()->select($this->getTables());
        $select->setSelect([new AllFields()]);
        $select->setWhere(
            new Condition(Condition::EQ, new Field($fieldId), $fieldVal)
        );
        $sql  = $select->sql();
        $stmt = $this->getPdo()->prepare($sql);
        $params = $select->parameters();
        $res = $stmt->execute($params);
        try{
            return $this->_getHero($stmt, $many);
        } catch (HeroRepositoryException $e){
            throw new HeroRepositoryException(sprintf("Hero with %s %s not found", $fieldId, $fieldVal));
        }
    }

    protected function getConstraintColumns():array
    {
        return $this->getNotNullColumns("heroes");
    }

    protected function getUpdateColumnsClackList():array
    {
        return ["username"];
    }

    /**
     * @param int $id
     * @return array
     */
    public function findById(int $id):array
    {
        return $this->_getHeroByField("id", $id);
    }
}