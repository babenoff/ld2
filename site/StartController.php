<?php
/**
 * Copyright (c) 2016. Maksim Babenko <mb.babenoff@yandex.ru>
 */

namespace LD2Controller;


use LD2\BaseController;

class StartController extends BaseController
{
    public function indexAction()
    {
        if(!isset($_SESSION["username"])){
            $this->forward("login");
        } else {
            $this->forward("game_main");
        }
    }
}