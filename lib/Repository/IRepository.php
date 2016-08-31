<?php
/**
 * Copyright (c) 2016. Maksim Babenko <mb.babenoff@yandex.ru>
 */

namespace LD2\Repository;


/**
 * Interface IRepository
 * @package LD2\Repository
 */
interface IRepository
{
    /**
     * @param int $id
     * @return array
     */
    public function findById(int $id):array;

    /**
     * @param array $data
     * @return bool
     */
    public function create(array $data):bool;

    /**
     * @param array $data
     * @return bool
     */
    public function update(array $data):bool;

    /**
     * @param array $data
     * @return bool
     */
    public function remove(array $data):bool;
}