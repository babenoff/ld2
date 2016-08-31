<?php
/**
 * Copyright (c) 2016. Maksim Babenko <mb.babenoff@yandex.ru>
 */

namespace LD2\Repository;


use LD2\Exception\RepositoryException;
use LD2\Exception\UserRepositoryException;
use LD2\QueryBuilder\Additional\Condition;
use LD2\QueryBuilder\Additional\Field;

class UserRepository extends BaseRepository implements IUserRepository
{
    /**
     * @param int $id
     * @return array
     * @throws UserRepositoryException
     */
    public function findById(int $id):array
    {
        try {
            $user = $this->_getByField("id", $id);
            return $user;
        } catch (RepositoryException $e){
            $mess = "User with id %d not found";
            throw new UserRepositoryException(sprintf($mess, $id));
        }
    }

    /**
     * @param array $data
     * @return bool
     * @throws UserRepositoryException
     */
    public function create(array $data):bool
    {
        if($this->_checkData($data)) {
            try {
                $this->findByUsername($data["username"]);
                $mess = "User with username %s already exists";
                throw new UserRepositoryException(sprintf($mess, $data["username"]));
            } catch (UserRepositoryException $e) {
                $sql = $this->getPdo()->queryBuilder()->insert($this->getTables());
                $sql->setValues($data);
                $query = $this->getPdo()->prepare($sql->sql());
                $res = $query->execute($sql->parameters());
                return $res;
            }
        } else {
            $mess = "user data is invalid (in insert)";
            throw new UserRepositoryException($mess);
        }
    }

    /**
     * @param array $data
     * @return bool
     * @throws UserRepositoryException
     */
    public function update(array $data):bool
    {
        if($this->_checkData($data) and isset($data["id"])){
            $sql = $this->getPdo()->queryBuilder()->update($this->getTables());
            $id = $data["id"];
            unset($data["id"]);
            $sql->setValues($data);
            $sql->setWhere(new Condition(Condition::EQ, new Field("id"), $id));
            $query = $this->getPdo()->prepare($sql->sql());
            $res = $query->execute($sql->parameters());
            return $res;
        } else {
            $mess = "user data is invalid (in update)";
            throw new UserRepositoryException($mess);
        }
    }

    /**
     * @param array $data
     * @return bool
     * @throws UserRepositoryException
     */
    public function remove(array $data):bool
    {
        if(isset($data["id"])){
            $sql = $this->getPdo()->queryBuilder()->delete($this->getTables());
            $sql->setWhere(new Condition(Condition::EQ, new Field("id"), $data["id"]));
            $query = $this->getPdo()->prepare($sql->sql());
            return $query->execute($sql->parameters());
        } else {
            $mess = "user data has no containts uid (in delete)";
            throw new UserRepositoryException($mess);
        }
    }

    public function findByUsername(string $username):array
    {
        try {
            $user = $this->_getByField("username", $username);
            return $user;
        } catch (RepositoryException $e){
            $mess = "user with username %d not found";
            throw new UserRepositoryException(sprintf($mess, $username));
        }
    }

    public function findByEmail(string $email):array
    {
        try {
            $user = $this->_getByField("email", $email);
            return $user;
        } catch (RepositoryException $e){
            $mess = "user with email %d not found";
            throw new UserRepositoryException(sprintf($mess, $email));
        }
    }

    protected function getConstraintColumns():array
    {
        return [
            "username", "password", "email"
        ];
    }

    protected function getUpdateColumnsClackList():array
    {
        return [];
    }
}