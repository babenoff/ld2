<?php
/**
 * QBTable.php at ld2.my
 * Created by Babenoff at 28.08.16 - 0:44
 * Maksim Babenko <mb.babenoff@yandex.ru>
 */

namespace LD2\QueryBuilder\Additional;

/**
 * Class QBTable
 * @package LD2\QueryBuilder\Additional
 */
class QBTable
{
    private $table_name = null;
    private $db_name = null;
    /**
     * Designated constructor of table-object.
     *
     * @param string $table_name
     * @param string $db_name
     */
    public function __construct($table_name, $db_name = null)
    {
        $this->table_name = $table_name;
        $this->db_name = $db_name;
    }
    /**
     * accessor, which returns sql-friendly (escaped) string-representation of table
     *
     * @return string
     */
    public function __toString()
    {
        $res = '';
        if (null !== $this->db_name) {
            $res .= '`'.$this->db_name.'`.';
        }
        $res .= '`'.$this->table_name.'`';
        return $res;
    }
    /**
     * accessor, which returns raw table-name (without database-name)
     *
     * @return string
     */
    public function getTable()
    {
        return $this->table_name;
    }
}