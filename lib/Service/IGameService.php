<?php
/**
 * Copyright (c) 2016. Maksim Babenko <mb.babenoff@yandex.ru>
 */

namespace LD2\Service;
use LD2\Exception\GameException;


/**
 * Interface IGameService
 * @package LD2\Service
 */
interface IGameService
{
    /**
     * @param $id
     * @return array
     * @throws GameException
     */
    public function getLocById($id):array;

    /**
     * @param $locationId
     * @return array
     * @throws GameException
     */
    public function getLocByLocationId($locationId):array;

    /**
     * @param string $locId
     * @param array $location
     * @return bool
     */
    public function update(string $locId, array $location):bool;

    /**
     * @param string $locId
     * @param array $location
     * @return bool
     */
    public function save(string $locId, array $location):bool;

    /**
     * @param string $locId
     * @return array
     */
    public function getNeighboringLocsIds(string $locId):array;
}