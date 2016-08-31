<?php
/**
 * Copyright (c) 2016. Maksim Babenko <mb.babenoff@yandex.ru>
 */

namespace LD2\Repository;


interface ILocationRepository extends IRepository
{
    public function findByLocationId(string $location_id):array;

    public function checkLocId(string $locationId):bool;
}