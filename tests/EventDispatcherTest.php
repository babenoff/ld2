<?php
/**
 * Copyright 2016 Maksim Babenko <mb.babenoff@yandex.ru>
 * EventDispatcherTest.php at ld2.my
 * Created by Babenoff at 28.08.16 - 9:50
 */

namespace Test;


use LD2\EventDispatcher;
use PHPUnit\Framework\TestCase;
use Test\Event\TestEvent;
use Test\Listener\TestListener;

class EventDispatcherTest extends TestCase
{
    /**
     * @var EventDispatcher
     */
    protected $ed;

    protected function setUp()
    {
        parent::setUp();
        $ed = new EventDispatcher();
        $ed->addListener(TestEvent::getId(), [TestListener::class, "onTestEvent"]);
        $ed->addListener(TestEvent::getId(), [TestListener::class, "onTestEvent2"]);
        $ed->addListener(TestEvent::getId(), [TestListener::class, "onTestEvent3", ["foo", "baz"]]);
        $this->ed = $ed;
    }

    public function testExistListener()
    {
        $this->assertTrue($this->ed->exists(TestEvent::getId()));
        $this->assertTrue($this->ed->exists(TestEvent::getId(), "onTestEvent"));
        $this->assertFalse($this->ed->exists(TestEvent::getId(), "onTestEvent1"));
    }

    public function testCountListeners()
    {
        $this->assertEquals(3, $this->ed->countListeners(TestEvent::getId()));
    }

    public function testAddListener()
    {
        $this->ed->addListener("simple_listener", [TestListener::class, "onSimple"]);
        $this->assertTrue($this->ed->exists("simple_listener"));
        $cListeners = $this->ed->countListeners(TestEvent::getId());
        $this->ed->addListener(TestEvent::getId(), [TestListener::class, "onTestEvent2"]);
        $this->assertEquals($cListeners + 1, $this->ed->countListeners(TestEvent::getId()));
        $this->assertInternalType('array', $this->ed->getListeners(TestEvent::getId()));
    }

    public function testRemoveListener()
    {
        $cL = $this->ed->countListeners(TestEvent::getId());
        $this->ed->addListener(TestEvent::getId(), [TestListener::class, "onTestEvent3"]);
        $this->assertEquals($cL + 1, $this->ed->countListeners(TestEvent::getId()));
        $this->ed->removeListener(TestEvent::getId(), "onTestEvent3");
        $this->assertEquals($cL, $this->ed->countListeners(TestEvent::getId()));
        $this->ed->addListener("simple_listener", [TestListener::class, "onSimple"]);
        $this->assertTrue($this->ed->exists("simple_listener"));
        $this->ed->removeListener("simple_listener");
        $this->assertFalse($this->ed->exists("simple_listener"));
    }

    public function testEmit()
    {
        $e = new TestEvent();
        $e->setFoo("foo");
        $this->assertEquals($e->getFoo(), $this->ed->emit(TestEvent::getId(), "onTestEvent", $e));
        //$this->assertEquals("foo", $this->ed->emit($e))
        $eq = md5(
            "foobaz" . $e::getId()
        );
        $this->assertEquals(
            $eq,
            $this->ed->emit(TestEvent::getId(), "onTestEvent2", $e, ["foo", "baz"])
        );
        $this->assertEquals(
            $eq,
            $this->ed->emit(TestEvent::getId(), "onTestEvent3", $e)
        );
    }
}
