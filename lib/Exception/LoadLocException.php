<?php
/**
 * Copyright 2016 Maksim Babenko <mb.babenoff@yandex.ru>
 * LoadLocException.php at ld2.my
 * Created by Babenoff at 29.08.16 - 8:37
 */

namespace LD2\Exception;


class LoadLocException extends RuntimeExcetion
{
    public function __construct($locId)
    {
        $msg = sprintf("Locations %s han not loaded", $locId);
        parent::__construct($msg);
    }
}