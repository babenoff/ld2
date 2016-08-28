<?php
/**
 * Copyright 2016 Maksim Babenko <mb.babenoff@yandex.ru>
 * IUserService.php at ld2.my
 * Created by Babenoff at 28.08.16 - 9:19
 */

namespace LD2\Service;


use Exception\UserServiceException;
use LD2\ISave;

/**
 * Interface IUserService
 * @package LD2\Service
 */
interface IUserService extends ISave
{
    /**
     * @param string $username
     * @return array
     * @throws  UserServiceException
     */
    public function findByUsername($username);

    /**
     * @param string $email
     * @return array
     * @throws  UserServiceException
     */
    public function findByEmail($email);

    /**
     * @param string $username
     * @return \ArrayIterator
     */
    public function getHeroes($username);

}