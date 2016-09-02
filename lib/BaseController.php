<?php
/**
 * Copyright (c) 2016. Maksim Babenko <mb.babenoff@yandex.ru>
 */

namespace LD2;


use Gregwar\Captcha\CaptchaBuilder;
use LD2\Helper\ViewHelper;
use LD2\View\PathFunction;
use LD2Controller\ErrorController;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
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
    /**
     * @var Request
     */
    protected $request;
    public function __construct(App $app)
    {
        $this->setApp($app);
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

    public function forward($name, array $options = []){
        $path = $this->generate($name, $options);
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
        $class = new $controller($this->getApp());
        if(!method_exists($class, $action)){
            $class = new ErrorController($this->getApp());
            $action = "err404Action";
        }
        $class->setApp($this->getApp());
        $class->setRouter($this->getRouter());
        $class->setRequest($this->getRequest());
        call_user_func_array([$class, $action], $reqParams);
    }

    public function generate($name, $options = [], $ref = Router::RELATIVE_PATH):string {
        try {
            $url = "/" . $this->router->generate($name, $options, $ref);
        }catch (RouteNotFoundException $e){
            $url = "/". $this->router->generate("/err404");
        }
        return $url;
    }

    public function render(string $template, array $params = []){
        /** @var \Twig_Environment $twig */
        $twig = $this->getApp()->getContainer()->get("twig");
        //$twig->addFunction(new PathFunction('path'))
        $this->addTwigFunctions($twig);
        $this->setBaseParamsToTwig($params);
        ob_start();
        echo $twig->render($template, $params);
        ob_end_flush();
    }

    /**
     * @param \Twig_Environment$twig
     */
    private function addTwigFunctions(\Twig_Environment &$twig){
        $pathFunction =new \Twig_SimpleFunction('path', [$this, 'generate']);
        $twig->addFunction('path', $pathFunction);
        $twig->addFunction('random', new \Twig_SimpleFunction('random', [ViewHelper::class, 'random']));
        $twig->addFunction('captcha', new \Twig_SimpleFunction('captcha', function($code){
            /** @var CaptchaBuilder $captcha */
            $captcha = $this->getApp()->getContainer()->get("captcha");
            $captcha->setPhrase($code);
            $captcha->setBackgroundColor(255,255,255);
            $captcha->build();
            $_SESSION["captcha"] = $code;
            return $captcha->inline();
        }));
    }

    private function setBaseParamsToTwig(array &$params){
        /** @var Database $pdo */
        $pdo = $this->getContainer()->get("pdo");
        $sqlTime = $pdo->getSqlTime();
        $params["gen_time"] = sprintf("ген: %0.4f сек", (microtime(true)-$_SERVER["REQUEST_TIME_FLOAT"]));
        $params["sql_time"] = sprintf("Sql: %0.4f сек", $sqlTime);
        $authors = $this->getContainer()->get("composer.json")->authors;
        $a = "";
        foreach ($authors as $author){
            if($a == ""){
                $a .= $author->name;
            } else {
                $a .= ", ".$author->name.">";
            }
        }
        $params["copyrights"] = $a;
        $params["version"] = $this->getContainer()->get("composer.json")->version;
        $params["title"] = "Лайкдимион";
    }

    /**
     * @return ContainerBuilder
     */
    protected function getContainer(){
        return $this->getApp()->getContainer();
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }
}