<?php
/**
 * Copyright 2016 Maksim Babenko <mb.babenoff@yandex.ru>
 * UserService.php at ld2.my
 * Created by Babenoff at 28.08.16 - 20:41
 */

namespace LD2\Service;


use Exception\UserServiceException;
use LD2\Database;
use LD2\EventDispatcher;
use LD2\QueryBuilder\Additional\AllFields;
use LD2\QueryBuilder\Additional\Condition;
use LD2\QueryBuilder\Additional\Field;
use PDO;

class UserService implements IUserService
{
    /**
     * @var Database
     */
    protected $pdo;
    /**
     * @var EventDispatcher
     */
    protected $evd;


    /**
     * @param Database $pdo
     */
    public function setPdo(Database $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param EventDispatcher $evd
     */
    public function setEvd(EventDispatcher $evd)
    {
        $this->evd = $evd;
    }

    /**
     * @return Database
     */
    public function getPdo(): Database
    {
        return $this->pdo;
    }

    /**
     * @return EventDispatcher
     */
    public function getEvd(): EventDispatcher
    {
        return $this->evd;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function update(array $data)
    {
        // TODO: Implement update() method.
    }

    /**
     * @param array $data
     * @return bool
     */
    public function create(array $data)
    {
        // TODO: Implement create() method.
    }

    /**
     * @param mixed $byField
     * @return boolean
     */
    public function delete($byField)
    {
        // TODO: Implement delete() method.
    }

    /**
     * @param string $username
     * @return array
     * @throws UserServiceException
     */
    public function findByUsername($username)
    {
        // TODO: Implement findByUsername() method.
        return $this->_getUser("username", $username);
    }

    /**
     * @param string $email
     * @return array
     * @throws UserServiceException
     */
    public function findByEmail($email)
    {
        // TODO: Implement findByEmail() method.
        return $this->_getUser("email", $email);
    }

    /**
     * @param string $username
     * @return \ArrayIterator
     */
    public function getHeroes($username)
    {
        // TODO: Implement getHeroes() method.
    }

    /**
     * @param $cId
     * @param $cVal
     * @return string
     */
    private function _findQuery($cId, $cVal) {
        $qb = $this->getPdo()->queryBuilder();
        $select = $qb->select(["users"]);
        $select->setSelect([new AllFields()]);
        $select->setWhere(new Condition("=", new Field($cId), $cVal));
        return $select->sql();
    }

    /**
     * @param string $cId
     * @param string $cVal
     * @return array
     * @throws UserServiceException
     */
    private function _getUser($cId, $cVal){
        $stmt = $this->getPdo()->prepare($this->_findQuery($cId, $cVal));
        $stmt->bindValue(":p1", $cVal);
        $stmt->execute();
        $aUser = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$aUser){
            throw new UserServiceException(sprintf("User %s not exists", $cVal));
        }
        return $aUser;
    }
}