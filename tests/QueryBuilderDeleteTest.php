<?php
/**
 * TestQueryBuilder.php at ld2.my
 * Created by Babenoff at 28.08.16 - 1:11
 * Maksim Babenko <mb.babenoff@yandex.ru>
 */

namespace Test;


use LD2\QueryBuilder\Additional\AndOp;
use LD2\QueryBuilder\Additional\Condition;
use LD2\QueryBuilder\Additional\Field;
use LD2\QueryBuilder\DeleteQuery;
use LogicException;
use PHPUnit\Framework\TestCase;

class QueryBuilderDeleteTest extends TestCase
{
    public function testDeleteSimple(){
        $deleteQuery = new DeleteQuery(["users"]);
        $this->assertEquals("DELETE FROM `users`", $deleteQuery->sql());
    }

    public function testOneOfMultiple(){
        $deleteQuery = new DeleteQuery(["users", "heroes", "locations"]);
        $this->assertEquals("DELETE FROM `t0` USING `users` AS `t0`, `heroes` AS `t1`, `locations` AS `t2`", $deleteQuery->sql());
    }
    public function testSeveralOfMultiple()
    {
        $deleteQuery = new DeleteQuery(['test', 'test2', 'test3'], array(0, 2));
        $this->assertEquals('DELETE FROM `t0`, `t2` USING `test` AS `t0`, `test2` AS `t1`, `test3` AS `t2`', $deleteQuery->sql());
    }
    public function testWhere()
    {
        $deleteQuery = new DeleteQuery([
            'test'
        ]);
        $deleteQuery->setWhere(new AndOp([
            new Condition('=', new Field('group'), 'test'),
            new Condition('=', new Field('author'), null)
        ]));
        $this->assertEquals('DELETE FROM `test` WHERE (`test`.`group` = :p1 AND `test`.`author` IS NULL)', $deleteQuery->sql());
    }
    public function testOrderLimit()
    {
        $deleteQuery = new DeleteQuery([
            'test'
        ]);
        $deleteQuery->setLimit(10);
        $deleteQuery->setOrderby([
            new Field('field1')
        ]);
        $this->assertEquals('DELETE FROM `test` ORDER BY `test`.`field1` ASC LIMIT 10', $deleteQuery->sql());
    }
    public function testOrderLimitOnMultiple()
    {
        try {
            $deleteQuery = new DeleteQuery([
                'test',
                'test2',
                'test3'
            ]);
            $deleteQuery->setLimit(10);
            $this->fail('LIMIT should not be allowed on multi-table queries');
        } catch (LogicException $e) {
        }
        try {
            $deleteQuery = new DeleteQuery([
                'test',
                'test2',
                'test3'
            ]);
            $deleteQuery->setOrderby([
                new Field('field1')
            ]);
            $this->fail('ORDER BY should not be allowed on multi-table queries');
        } catch (LogicException $e) {
        }
    }
}
