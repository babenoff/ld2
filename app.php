<?php
/**
 * app.php
 * Created by Babenoff at 26.08.16 - 23:38
 */
$config = require "config/config.php";
$app = new \LD2\App();

$db = new \LD2\Database(
    $config["database"]["user"],
    $config["database"]["password"],
    $config["database"]["database"],
    $config["database"]["host"],
    $config["database"]["engine"]
);
$app->setPdo($db);

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
$app->setContainer(require "config/container.php");
$app->setEventDispatcher(require "config/event_dispatcher.php");

$app->run();