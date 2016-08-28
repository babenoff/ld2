<?php
/**
 * MQB_Field.php at ld2.my
 * Created by Babenoff at 28.08.16 - 0:43
 * Maksim Babenko <mb.babenoff@yandex.ru>
 */

namespace LD2\QueryBuilder\Additional;

/**
 * Interface MQB_Field
 * @package LD2\QueryBuilder\Additional
 */
interface MQB_Field
{
    /**
     * used for generation of "prepared" SQL-queries. Supposed to be used recursively, and add parameters to the end of $parameters stack
     *
     * @param array $parameters
     * @return string
     */
    public function getSql(array &$parameters);
    /**
     * returns "alias" name of field
     *
     * @return string
     * @author Jimi Dini
     */
    public function getAlias();
}