<?php
/**
 * Copyright 2016 Maksim Babenko <mb.babenoff@yandex.ru>
 * TesteEvent.php at ld2.my
 * Created by Babenoff at 28.08.16 - 9:43
 */

namespace LD2\Event;


class TestEvent extends Event
{

    public static function getId(): string
    {
        return "test.event";
    }
}