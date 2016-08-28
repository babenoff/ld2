<?php
/**
 * Copyright 2016 Maksim Babenko <mb.babenoff@yandex.ru>
 * TestEvent.php at ld2.my
 * Created by Babenoff at 28.08.16 - 9:54
 */

namespace Test\Event;


use LD2\Event\Event;

class TestEvent extends Event
{
    public $foo;

    public static function getId(): string
    {
        return "test_event";
    }

    /**
     * @return mixed
     */
    public function getFoo()
    {
        return $this->foo;
    }

    /**
     * @param mixed $foo
     */
    public function setFoo($foo)
    {
        $this->foo = $foo;
    }
}