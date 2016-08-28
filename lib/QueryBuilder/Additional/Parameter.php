<?php
/**
 * Parameter.php at ld2.my
 * Created by Babenoff at 28.08.16 - 0:53
 * Maksim Babenko <mb.babenoff@yandex.ru>
 */

namespace LD2\QueryBuilder\Additional;

/**
 * Class Parameter
 * @package LD2\QueryBuilder\Additional
 */
class Parameter
{
    private $content;
    /**
     * Creates representation of literal-value, passed as the single parameter
     *
     * @param string|integer|bool|null $content
     */
    public function __construct($content)
    {
        $this->content = $content;
    }
    public function getSql(array &$parameters)
    {
        $number = count($parameters) + 1;
        $parameters[":p".$number] = $this->content;
        return ":p".$number;
    }
    /**
     * accessor, which returns value of parameter
     *
     * @return mixed
     */
    public function getParameters()
    {
        return $this->content;
    }
}