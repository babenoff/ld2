<?php
/**
 * Copyright 2016 Maksim Babenko <mb.babenoff@yandex.ru>
 * Container.php at ld2.my
 * Created by Babenoff at 28.08.16 - 9:21
 */

namespace LD2;

/**
 * Class Container
 * @package LD2
 */
class Container
{
    /**
     * @var array
     */
    protected $_container = [];

    /**
     * @param string $serviceId
     * @return mixed|null
     */
    public function get($serviceId, $default = null)
    {
        return (isset($this->_container[$serviceId])) ? $this->_container[$serviceId] : $default;
    }

    /**
     * @param $serviceId
     * @param $service
     * @return $this
     */
    public function add($serviceId, $service)
    {
        $this->_container[$serviceId] = $service;
        return $this;
    }

    /**
     * @param string $serviceId
     * @return bool
     */
    public function exists($serviceId){
        return isset($this->_container[$serviceId]);
    }

    /**
     * @param $serviceId
     */
    public function remove($serviceId){
        if($this->exists($serviceId)){
            unset($this->_container[$serviceId]);
        }
    }
}