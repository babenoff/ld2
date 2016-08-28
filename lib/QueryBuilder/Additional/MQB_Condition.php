<?php
/**
 * MQB_Condition.php at ld2.my
 * Created by Babenoff at 28.08.16 - 0:42
 * Maksim Babenko <mb.babenoff@yandex.ru>
 */

namespace LD2\QueryBuilder\Additional;

/**
 * Interface MQB_Condition
 * @package LD2\QueryBuilder\Additional
 */
interface MQB_Condition
{
    /**
     * used for generation of "prepared" SQL-queries. Supposed to be used recursively, and add parameters to the end of $parameters stack
     *
     * @param array $parameters
     * @return string
     */
    public function getSql(array &$parameters);
}