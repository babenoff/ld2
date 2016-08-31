<?php
/**
 * app.php
 * Created by Babenoff at 26.08.16 - 23:38
 */
if(!version_compare('7', PHP_VERSION, '<')){
    die("Game need php version 7.0 and more");
}


$config = require "config/config.php";
require "config/container.php";
require "config/event_dispatcher.php";

if (!ini_get('date.timezone')) {
    ini_set('date.timezone', $container->getParameter("timezone"));
}

ini_set('session.gc_maxlifetime', $container->getParameter("session_lifetime"));
session_set_save_handler($container->get("session_repository"), true);
session_start();
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
$view->setContainer($container);

$app->setView($view);
$heroRepo = $container->get("hero_repository");
/** @var \Symfony\Component\DependencyInjection\ContainerBuilder $container */
$app->setContainer($container);

$app->run();
