<?php
/**
 * Copyright 2016 Maksim Babenko <mb.babenoff@yandex.ru>
 * Event.php at ld2.my
 * Created by Babenoff at 28.08.16 - 9:32
 */

namespace LD2\Event;

/**
 * Class Event
 * @package LD2\Event
 */
abstract class Event
{
    abstract public static function getId(): string ;
}