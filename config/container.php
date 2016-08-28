<?php
/**
 * Copyright 2016 Maksim Babenko <mb.babenoff@yandex.ru>
 * container.php at ld2.my
 * Created by Babenoff at 28.08.16 - 9:39
 */


$container = new \Symfony\Component\DependencyInjection\ContainerBuilder();
$locator = new \Symfony\Component\Config\FileLocator(dirname(__FILE__));
$loader = new \Symfony\Component\DependencyInjection\Loader\YamlFileLoader($container, $locator);
$loader->load("container.yml");

/*$db = new \LD2\Database(
    $container->getParameter("db_user"),//$config["database"]["user"],
    $container->getParameter("db_password"),//$config["database"]["password"],
    $container->getParameter("db_dbname"),//$config["database"]["database"],
    $container->getParameter("db_host"),//$config["database"]["host"],
    $container->getParameter("db_engine")//$config["database"]["engine"]
);*/
//$app->setPdo($db);