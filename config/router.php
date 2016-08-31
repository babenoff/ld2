<?php
/**
 * Copyright (c) 2016. Maksim Babenko <mb.babenoff@yandex.ru>
 */

use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

$routeCollection = new \Symfony\Component\Routing\RouteCollection();
$loader = new YamlFileLoader(new FileLocator(__DIR__));
$r = new \Symfony\Component\Routing\Router($loader, "routes.yml", [], new RequestContext("/"));
$request = Request::createFromGlobals();

try {
    $params = $r->matchRequest($request);
} catch (\Symfony\Component\Routing\Exception\ResourceNotFoundException $e){
    $params = $r->match("/err404");
}
$reqParams = [];
foreach ($params as $key => $val){
    if(substr($key,0,1)!="_"){
        array_push($reqParams, $val);
    }
}
list($controller, $action) = explode(":", $params["_controller"]);
if(!class_exists($controller)) {
    $controller = "LD2Controller\\ErrorContoller";
    $action = "err404Action";
}
$class = new $controller();
$class->setApp($app);
$class->setRouter($r);

call_user_func_array([$class, $action], $reqParams);






