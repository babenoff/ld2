<?php
/**
 * Copyright (c) 2016. Maksim Babenko <mb.babenoff@yandex.ru>
 */

namespace LD2\Helper;


class ViewHelper
{
    public static function random($min, $max){
        return random_int($min, $max);
    }
}