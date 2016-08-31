<?php
/**
 * Copyright (c) 2016. Maksim Babenko <mb.babenoff@yandex.ru>
 */

namespace LD2Controller;


use LD2\BaseController;

class ErrorController extends BaseController
{
    public function err404Action()
    {
        $page =<<<ERROR
Cnраница не найдена
ERROR;
    $this->getApp()->getView()->display($page, [], "Ошибка");
    }
}