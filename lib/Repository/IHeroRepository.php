<?php
/**
 * Copyright (c) 2016. Maksim Babenko <mb.babenoff@yandex.ru>
 */

namespace LD2\Repository;


use LD2\Exportable;

interface IHeroRepository extends IRepository
{
    /**
     * @param int $id
     * @return array
     * @deprecated
     */
    public function getHeroById(int $id):array;
    /**
     * @param string $title
     * @return array
     */
    public function getHeroByTitle(string $title):array;

    /**
     * @param string $username
     * @return array
     */
    public function getHeroesByUsername(string $username):array ;

    /**
     * @param string $locId
     * @return array
     */
    public function getHeroByLocationId(string $locId):array;

    /**
     * @param string $username
     * @param string $title
     * @return array
     */
   /* public function getHeroByUsernameAndTitle(string $username, string $title):array;*/


    /**
     * @param string $locId
     * @param string $title
     * @return array
     */
    /*public function getHeroByLocationIdAndTitle(string $locId, string $title):array;*/

}