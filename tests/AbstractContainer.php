<?php
/**
 * Copyright (c) 2016. Maksim Babenko <mb.babenoff@yandex.ru>
 */

namespace Test;


use Symfony\Component\DependencyInjection\ContainerBuilder;

class AbstractContainer extends AbstractDatabaseTest
{
    /**
     * @var ContainerBuilder
     */
    protected $container;

    public function setUp()
    {
        parent::setUp();
        $container = new \Symfony\Component\DependencyInjection\ContainerBuilder();
        $locator = new \Symfony\Component\Config\FileLocator(ROOT."/config");
        $loader = new \Symfony\Component\DependencyInjection\Loader\YamlFileLoader($container, $locator);
        $loader->load("container.yml");
        $this->container = $container;
    }

    /**
     * @return ContainerBuilder
     */
    public function getContainer()
    {
        return $this->container;
    }

    public function testTrue(){
        $this->assertTrue(true);
    }
}
