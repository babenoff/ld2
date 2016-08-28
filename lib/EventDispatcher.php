<?php
/**
 * Copyright 2016 Maksim Babenko <mb.babenoff@yandex.ru>
 * EventDispatcher.php at ld2.my
 * Created by Babenoff at 28.08.16 - 9:25
 */

namespace LD2;

use LD2\Event\Event;
use LD2\Exception\ClassNotExistsException;
use LD2\Exception\EventDispatcherException;

/**
 * Class EventDispatcher
 * @package LD2
 */
class EventDispatcher
{
    protected $_listeners = [];

    /**
     * @param $eventId
     * @param null $listenerId
     * @return bool
     */
    public function exists($eventId, $listenerId = null)
    {
        if (!isset($this->_listeners[$eventId])) {
            return false;
        } elseif (is_null($listenerId)) {
            return true;
        } else {
            for ($i = 0; $i < $this->countListeners($eventId); $i++) {
                if ($this->_listeners[$eventId][$i][1] == $listenerId) {
                    return true;
                }
            }
            return false;
        }
    }

    /**
     * @param $eventId
     * @return int
     */
    public function countListeners($eventId)
    {
        return count($this->_listeners[$eventId]);
    }

    /**
     * @param $eventId
     * @param array $listener
     * @return $this
     */
    public function addListener($eventId, $listener)
    {
        $this->_createEmptyListener($eventId);
        array_push($this->_listeners[$eventId], $listener);
        return $this;
    }

    /**
     * @param $eventId
     * @param $listenerId
     */
    public function removeListener($eventId, $listenerId = null)
    {
        if ($this->exists($eventId)) {
            if (is_null($listenerId)) {
                unset($this->_listeners[$eventId]);
            } else {
                for ($i = 0; $i < count($this->_listeners[$eventId]); $i++) {
                    if ($this->_listeners[$eventId][$i][1] == $listenerId) {
                        unset($this->_listeners[$eventId][$i]);
                    }
                }
                $this->_ckeckListener($eventId);
            }
        }
    }

    /**
     * @param $eventId
     * @return array
     */
    public function getListeners($eventId)
    {
        return ($this->exists($eventId)) ? $this->_listeners[$eventId] : [];
    }

    /**
     * @param $eventId
     * @param $listenerId
     * @param Event $e
     * @param array $params
     * @throws EventDispatcherException
     * @return callable
     */
    public function emit($eventId, $listenerId, Event $e, $params = [])
    {
        if (!$this->exists($eventId)) {
            throw new EventDispatcherException("Listener {$eventId}:{$listenerId} not found");
        }
        $listeners = $this->getListeners($eventId);
        for ($i = 0; $i < count($listeners); $i++) {
            if ($this->_isThatListener($listeners[$i], $listenerId)) {
                $class = $listeners[$i][0];
                $method = $listeners[$i][1];
                $obj = new $class();
                $bindParams = isset($listeners[$i][2]) ? $listeners[$i][2] : [];
                $injectedParams = [$e];
                if (is_array($params)) {
                    $injectedParams = array_merge($injectedParams, $bindParams, $params);
                }
                return call_user_func_array([$obj, $method], $injectedParams);
            }
        }
    }

    /**
     * @param $listener
     * @param $lid
     * @return bool
     */
    protected function _isThatListener($listener, $lid)
    {
        return $listener[1] == $lid;
    }

    protected function _createEmptyListener($eventId)
    {
        if (!$this->exists($eventId)) {
            $this->_listeners[$eventId] = [];
        }
    }

    protected function _ckeckListener($eventId)
    {
        if (empty($this->_listeners[$eventId])) {
            unset($this->_listeners[$eventId]);
        }
    }
}