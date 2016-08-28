<?php
/**
 * app.php
 * Created by Babenoff at 26.08.16 - 23:38
 */
$config = require "config/config.php";
require "config/container.php";
require "config/event_dispatcher.php";
$app = new \LD2\App();

$headers = [
    \LD2\View::NO_CACHE,
    \LD2\View::DATE_LAST,
];
if(strtok(getenv("HTTP_USER_AGENT")) != "Mozilla"){
    array_push($headers, \LD2\View::HTML);
} else {
    array_push($headers, \LD2\View::XHTML);
}

$view = new \LD2\View($headers);
$app->setView($view);
/** @var \Symfony\Component\DependencyInjection\ContainerBuilder $container */

//$userService = $container->get("user_service");
/** @var \LD2\Service\IUserService $us */
$us = $container->get("user_service");

$tester = $us->findByUsername("tester");
//$app->setPdo($container->get("pdo"));
$app->setContainer($container);

$app->run();