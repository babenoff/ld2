<?php
/**
 * Copyright 2016 Maksim Babenko <mb.babenoff@yandex.ru>
 * RegistrationService.php at ld2.my
 * Created by Babenoff at 28.08.16 - 22:04
 */

namespace LD2\Service;


use LD2\Database;

class RegistrationService implements IRegistrationService
{
    /**
     * @var Database
     */
    protected $pdo;

    public function __construct()
    {

    }

    /**
     * @param string $username
     * @param string $password
     * @param string $email
     * @return bool
     */
    public function createUser($username, $password, $email)
    {
        // TODO: Implement createUser() method.
        $insert = $this->pdo->queryBuilder()->insert("users");
        $insert->setValues([
            "username" => $username,
            "password" => password_hash($password, PASSWORD_DEFAULT),
            "email" => $email
        ]);

        $insertSql = $insert->sql();
    }

    /**
     * @param string $username
     * @param string $title
     * @return bool
     */
    public function createHero($username, $title)
    {
        // TODO: Implement createHero() method.
    }

    /**
     * @param Database $pdo
     */
    public function setPdo(Database $pdo)
    {
        $this->pdo = $pdo;
    }
}