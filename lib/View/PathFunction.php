<?php
/**
 * Copyright (c) 2016. Maksim Babenko <mb.babenoff@yandex.ru>
 */

namespace LD2\View;


use Symfony\Component\Routing\Router;
use Twig_Node;

class PathFunction extends \Twig_SimpleFunction  {
    /**
     * @var Router
     */
    protected $router;

}