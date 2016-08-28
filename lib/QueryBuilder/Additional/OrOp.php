<?php
/**
 * OrOp.php at ld2.my
 * Created by Babenoff at 28.08.16 - 0:46
 * Maksim Babenko <mb.babenoff@yandex.ru>
 */

namespace LD2\QueryBuilder\Additional;


/**
 * Class, which implements SQLs "OR" operator
 *
 * @package LD2\QueryBuilder\Additional
 */
class OrOp extends Operator
{
    /**
     * Designated constructor.
     * Takes either single parameter — array of MQB_Conditions or several parameters-MQB_Conditions
     *
     * @param string|array $content,...
     */
    public function __construct($content)
    {
        if (func_num_args() > 1)
            parent::__construct(func_get_args());
        else
            parent::__construct($content);
        $this->startSql = "(";
        $this->implodeSql = " OR ";
        $this->endSql = ")";
    }
    public function getSql(array &$parameters)
    {
        $content = $this->getContent();
        // shortcut
        if (count($content) == 1)
            return $content[0]->getSql($parameters);
        return parent::getSql($parameters);
    }
}