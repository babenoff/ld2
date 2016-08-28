<?php
/**
 * QueryBuilder.php at ld2.my
 * Created by Babenoff at 28.08.16 - 1:25
 * Maksim Babenko <mb.babenoff@yandex.ru>
 */

namespace LD2\QueryBuilder;

/**
 * Class QueryBuilder
 * Factory
 * @package LD2\QueryBuilder
 */
class QueryBuilder
{
    /**
     * @param mixed $tables
     * @return SelectQuery
     */
    public function select($tables){
        return new SelectQuery($tables);
    }

    /**
     * @param mixed $tables
     * @return UpdateQuery
     */
    public function update($tables){
        return new UpdateQuery($tables);
    }

    /**
     * @param $tables
     * @param bool $on_duplicate_update
     * @return InsertQuery
     */
    public function insert($tables, $on_duplicate_update = false){
        return new InsertQuery($tables, $on_duplicate_update);
    }

    /**
     * @param mixed $tables
     * @return DeleteQuery
     */
    public function delete($tables){
        return new DeleteQuery($tables);
    }
}