<?php
/**
 * Copyright (c) 2016. Maksim Babenko <mb.babenoff@yandex.ru>
 */

namespace LD2Controller;


use LD2\BaseController;

class IsAuthController extends BaseController
{
    protected $sessData = [];

    protected function before()
    {
        parent::before();
        if (!isset($_SESSION["username"])) {
            header("Location: /login");
            exit;
        } else {
            $this->sessData = $_SESSION;
        }
    }

    public function __get($key){
        return (isset($this->sessData[$key])) ? $this->sessData[$key] : NULL;
    }

    public function __set($key, $val){
        $this->sessData[$key] = $val;
        $_SESSION[$key] = $val;
    }

    public function __isset($name)
    {
        return isset($this->sessData[$name]);
    }

}