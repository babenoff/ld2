<?php
/**
 * Copyright (c) 2016. Maksim Babenko <mb.babenoff@yandex.ru>
 */

namespace LD2;


use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Routing\Router;

class BaseController
{
    /**
     * @var App
     */
    private $app;
    /**
     * @var Router
     */
    protected $router;
    public function __construct()
    {
        $this->before();
    }

    protected function before(){}
    protected function after(){}

    public function __destruct()
    {
        $this->after();
    }

    /**
     * @return App
     */
    public function getApp(): App
    {
        return $this->app;
    }

    /**
     * @param App $app
     */
    public function setApp(App $app)
    {
        $this->app = $app;
    }

    /**
     * @return Router
     */
    public function getRouter(): Router
    {
        return $this->router;
    }

    /**
     * @param Router $router
     */
    public function setRouter(Router $router)
    {
        $this->router = $router;
    }

    public function forward($name){
        $path = $this->generate($name);
        try {
            $params = $this->router->match($path);
        } catch (\Symfony\Component\Routing\Exception\ResourceNotFoundException $e){
            $params = $this->router->match("/err404");
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
        $class->setApp($this->getApp());
        $class->setRouter($this->getRouter());
        call_user_func_array([$class, $action], $reqParams);
    }

    protected function generate($name, $options = [], $ref = Router::RELATIVE_PATH){
        return "/".$this->router->getGenerator()->generate($name, $options, $ref);
    }
}