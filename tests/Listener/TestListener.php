<?php
/**
 * Copyright 2016 Maksim Babenko <mb.babenoff@yandex.ru>
 * TestListener.php at ld2.my
 * Created by Babenoff at 28.08.16 - 9:55
 */

namespace Test\Listener;


use Test\Event\TestEvent;

class TestListener
{
    public function onTestEvent(TestEvent $e){
        return $e->getFoo();
    }

    public function onTestEvent2(TestEvent $e, $foo, $baz){
        return md5($foo.$baz.$e::getId());
    }

    public function onTestEvent3(TestEvent $e, $foo, $baz){
        return md5($foo.$baz.$e::getId());
    }
}