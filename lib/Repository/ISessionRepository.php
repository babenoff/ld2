<?php
/**
 * Copyright (c) 2016. Maksim Babenko <mb.babenoff@yandex.ru>
 */

namespace LD2\Repository;


interface ISessionRepository extends IRepository
{
    /**
     * @param string $sid
     * @return array
     */
    public function findBySid(string $sid):array;

    /**
     * @return int
     */
    public function getCountSessions():int;

}