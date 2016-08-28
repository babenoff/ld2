<?php
/**
 * QueryBuilderUpdateTest.php at ld2.my
 * Created by Babenoff at 28.08.16 - 1:39
 * Maksim Babenko <mb.babenoff@yandex.ru>
 */

namespace Test;


use LD2\QueryBuilder\Additional\Condition;
use LD2\QueryBuilder\Additional\Field;
use LD2\QueryBuilder\UpdateQuery;
use LogicException;
use PHPUnit\Framework\TestCase;

class QueryBuilderUpdateTest extends TestCase
{
    public function testEmptyUpdate()
    {
        try {
            $updateQuery = new UpdateQuery(
                ['test']
            );
            $sql = $updateQuery->sql();
            $this->fail("allowed to generate empty query");
        } catch (LogicException $e) {
        }
    }
    public function testFullUpdate()
    {
        $updateQuery = new UpdateQuery(['test']);
        $updateQuery->setValues([
            'qwe' => 'qweqwe'
        ]);
        $this->assertEquals('UPDATE `test` AS `t0` SET `t0`.`qwe` = :p1', $updateQuery->sql());
        $params = $updateQuery->parameters();
        $this->assertEquals('qweqwe', $params[':p1']);
        // shortcut
        $updateQuery = new UpdateQuery('test');
        $updateQuery->qwe = 'qweqwe';
        $this->assertEquals('UPDATE `test` AS `t0` SET `t0`.`qwe` = :p1', $updateQuery->sql());
        $params = $updateQuery->parameters();
        $this->assertEquals('qweqwe', $params[':p1']);
    }
    public function testConditionalUpdate()
    {
        $updateQuery = new UpdateQuery(['test']);
        $updateQuery->setValues([
            'qwe' => 'qweqwe'
        ]);
        $updateQuery->setWhere(new Condition('=', new Field('a'), 'b'));
        $this->assertEquals('UPDATE `test` AS `t0` SET `t0`.`qwe` = :p1 WHERE `t0`.`a` = :p2', $updateQuery->sql());
        $params = $updateQuery->parameters();
        $this->assertEquals('qweqwe', $params[':p1']);
        $this->assertEquals('b', $params[':p2']);
    }
    public function testMultitableUpdate()
    {
        $updateQuery = new UpdateQuery(['test', 'test2']);
        $updateQuery->setValues([
            [new Field('field1'), 'value1'],
            [new Field('field2', 1), 'value2']
        ]);
        $this->assertEquals('UPDATE `test` AS `t0`, `test2` AS `t1` SET `t0`.`field1` = :p1, `t1`.`field2` = :p2', $updateQuery->sql());

        $params = $updateQuery->parameters();
        $this->assertEquals('value1', $params[':p1']);
        $this->assertEquals('value2', $params[':p2']);
    }
    public function testConditionalMultitableUpdate()
    {
        $updateQuery = new UpdateQuery(['test', 'test2']);
        $updateQuery->setValues(array(
            [new Field('field1'), 'value1'],
            [new Field('field2', 1), 'value2']
        ));
        $updateQuery->setWhere(new Condition('<', new Field('date', 1), '2004-10-11'));
        $this->assertEquals('UPDATE `test` AS `t0`, `test2` AS `t1` SET `t0`.`field1` = :p1, `t1`.`field2` = :p2 WHERE `t1`.`date` < :p3', $updateQuery->sql());

        $params = $updateQuery->parameters();
        $this->assertEquals('value1', $params[':p1']);
        $this->assertEquals('value2', $params[':p2']);
        $this->assertEquals('2004-10-11', $params[':p3']);
    }
    public function testLimit()
    {
        $updateQuery = new UpdateQuery('test');
        $updateQuery->setValues([
            'qwe' => 'qweqwe'
        ]);
        $updateQuery->setLimit(10);
        $this->assertEquals('UPDATE `test` AS `t0` SET `t0`.`qwe` = :p1 LIMIT 10', $updateQuery->sql());
        $params = $updateQuery->parameters();
        $this->assertEquals('qweqwe', $params[':p1']);
    }
    public function testOrderBy()
    {
        $updateQuery = new UpdateQuery(['test']);
        $updateQuery->setValues([
            'qwe' => 'qweqwe'
        ]);
        $updateQuery->setLimit(10);
        $updateQuery->setOrderBy([new Field('date')]);
        $this->assertEquals('UPDATE `test` AS `t0` SET `t0`.`qwe` = :p1 ORDER BY `t0`.`date` ASC LIMIT 10', $updateQuery->sql());
        $params = $updateQuery->parameters();
        $this->assertEquals('qweqwe', $params[':p1']);
    }
    public function testOrderLimitOnMultiple()
    {
        try {
            $updateQuery = new UpdateQuery(['test', 'test2', 'test3']);
            $updateQuery->setLimit(10);
            $this->fail('LIMIT should not be allowed on multi-table queries');
        } catch (LogicException $e) {
        }
        try {
            $updateQuery = new UpdateQuery(['test', 'test2', 'test3']);
            $updateQuery->setOrderBy(array(new Field('field1')));
            $this->fail('ORDER BY should not be allowed on multi-table queries');
        } catch (LogicException $e) {
        }
    }
    public function testResettingOfSETClause()
    {
        $updateQuery = new UpdateQuery('test');
        $updateQuery->setValues(['foo' => 'bar']);
        $sql = $updateQuery->sql();
        $this->assertEquals('UPDATE `test` AS `t0` SET `t0`.`foo` = :p1', $sql);
        $updateQuery->setValues([]);
        $updateQuery->baz = 'bar';
        $sql = $updateQuery->sql();
        $this->assertEquals('UPDATE `test` AS `t0` SET `t0`.`baz` = :p1', $sql);
    }
    public function testAlternateSetSyntax()
    {
        $updateQuery = new UpdateQuery('test');
        $updateQuery->setValues([
            ['foo', 'bar'],
            ['baz', 'bar']
        ]);
        $sql = $updateQuery->sql();
        $this->assertEquals('UPDATE `test` AS `t0` SET `t0`.`foo` = :p1, `t0`.`baz` = :p2', $sql);
    }
}
