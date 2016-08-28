<?php
/**
 * Copyright 2016 Maksim Babenko <mb.babenoff@yandex.ru>
 * IRegService.php at ld2.my
 * Created by Babenoff at 28.08.16 - 22:00
 */

namespace LD2\Service;


interface IRegistrationService
{
    /**
     * @param string $username
     * @param string $password
     * @param string $email
     * @return bool
     */
    public function createUser($username, $password, $email);

    /**
     * @param string $username
     * @param string $title
     * @return bool
     */
    public function createHero($username, $title);
}