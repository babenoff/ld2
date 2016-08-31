<?php
/**
 * Copyright (c) 2016. Maksim Babenko <mb.babenoff@yandex.ru>
 */

namespace LD2\Repository;


use LD2\Exception\LocationsRepositoryException;
use LD2\Exception\RepositoryException;
use LD2\QueryBuilder\Additional\Condition;
use LD2\QueryBuilder\Additional\Field;
use LD2\Repository\BaseRepository;
use LD2\Repository\ILocationRepository;

class LocationsRepository extends BaseRepository implements ILocationRepository
{

    protected function getConstraintColumns():array
    {
        return [
            "location_id"
        ];
    }

    /**
     * @param string $location_id
     * @return array
     * @throws LocationsRepositoryException
     */
    public function findByLocationId(string $location_id):array
    {
        try {
            $location = $this->_getByField("location_id", $location_id);
            return $location;
        } catch (RepositoryException $e) {
            $mess = "location with location_id %s not found";
            throw new LocationsRepositoryException(sprintf($mess, $location_id));
        }
    }

    /**
     * @param int $id
     * @return array
     * @throws LocationsRepositoryException
     */
    public function findById(int $id):array
    {
        try {
            $location = $this->_getByField("id", $id);
            return $location;
        } catch (RepositoryException $e) {
            $mess = "location with id %d not found";
            throw new LocationsRepositoryException(sprintf($mess, $id));
        }
    }

    /**
     * @param array $data
     * @return bool
     * @throws LocationsRepositoryException
     */
    public function create(array $data):bool
    {
        if ($this->_checkData($data) and $this->checkLocId($data["location_id"])) {
            try {
                $this->findByLocationId($data["location_id"]);
                $mess = "location with id %s already exists";
                throw new LocationsRepositoryException(sprintf($mess, $data["location_id"]));
            } catch (LocationsRepositoryException $e) {
                try {
                    $res = $this->_create($data);
                    return $res;
                } catch (RepositoryException $e) {
                    throw new LocationsRepositoryException($e->getMessage(), 0, $e);
                }
            }
        } else {
            $mess = "location data is invalid (in insert)";
            throw new LocationsRepositoryException($mess);
        }
    }

    /**
     * @param array $data
     * @return bool
     * @throws LocationsRepositoryException
     */
    public function update(array $data):bool
    {
        if(isset($data["id"]) and $this->checkLocId($data["location_id"])) {
            try {
                $res = $this->_update($data, new Condition(Condition::EQ, new Field("id"), $data["id"]));
                return $res;
            } catch (RepositoryException $e) {
                throw new LocationsRepositoryException($e->getMessage(), $e->getCode(), $e);
            }
        } else {
            $mess = "the data must contain id or invalid location_id";
            throw new LocationsRepositoryException($mess);
        }
    }

    /**
     * @param array $data
     * @return bool
     * @throws LocationsRepositoryException
     */
    public function remove(array $data):bool
    {
        if(isset($data["id"])) {
            try {
                $res = $this->_remove($data["id"]);
                return $res;
            } catch (RepositoryException $e) {
                throw new LocationsRepositoryException($e->getMessage(), $e->getCode(), $e);
            }
        } else {
            $mess = "the data must contain id";
            throw new LocationsRepositoryException($mess);
        }
    }

    /**
     * @param string $locationId
     * @return bool
     * @internal param $locId
     */
    public function checkLocId(string $locationId):bool {
        return (bool)preg_match("/^([a-z0-9]+)_(([0-9]+)x([0-9]+))$/s", $locationId);
    }

    protected function getUpdateColumnsClackList():array
    {
        // TODO: Implement getUpdateColumnsClackList() method.
    }
}