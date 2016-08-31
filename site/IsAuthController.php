<?php
/**
 * Copyright (c) 2016. Maksim Babenko <mb.babenoff@yandex.ru>
 */

namespace LD2Controller;


use LD2\BaseController;

class IsAuthController extends BaseController
{
    protected function before()
    {
        parent::before();
        if(!isset($_SESSION["username"])){
            $this->forward($this->generate("login"));
        }
    }
}