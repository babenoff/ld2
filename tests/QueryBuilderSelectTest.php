<?php
/**
 * QueryBuilderSelectTest.php at ld2.my
 * Created by Babenoff at 28.08.16 - 1:36
 * Maksim Babenko <mb.babenoff@yandex.ru>
 */

namespace Test;


use InvalidArgumentException;
use LD2\QueryBuilder\Additional\Aggregate;
use LD2\QueryBuilder\Additional\AllFields;
use LD2\QueryBuilder\Additional\AndOp;
use LD2\QueryBuilder\Additional\Condition;
use LD2\QueryBuilder\Additional\Field;
use LD2\QueryBuilder\Additional\NotOp;
use LD2\QueryBuilder\Additional\OrOp;
use LD2\QueryBuilder\Additional\QBTable;
use LD2\QueryBuilder\Additional\SqlFunction;
use LD2\QueryBuilder\SelectQuery;
use LogicException;
use PHPUnit\Framework\TestCase;

class QueryBuilderSelectTest extends TestCase
{
    public function testSelectAllFromOneTable()
    {
        $q = new SelectQuery(['test']);
        $this->assertEquals('SELECT `t0`.* FROM `test` AS `t0`', $q->sql());
        $this->assertEquals(0, count($q->parameters()));
        $q = new SelectQuery(['test']);
        $q->setSelect([new AllFields()], true);
        $this->assertEquals('SELECT DISTINCT `t0`.* FROM `test` AS `t0`', $q->sql());
        $this->assertEquals(0, count($q->parameters()));
        $q = new SelectQuery([new QBTable('test')]);
        $this->assertEquals('SELECT `t0`.* FROM `test` AS `t0`', $q->sql());
        $this->assertEquals(0, count($q->parameters()));
    }
    public function testSelectSomeFromOneTable()
    {
        $q = new SelectQuery(['test']);
        $q->setWhere(new Condition('=', new Field('somefield'), 35));
        $q->setLimit(10, 2);
        $this->assertEquals('SELECT `t0`.* FROM `test` AS `t0` WHERE `t0`.`somefield` = :p1 LIMIT 10 OFFSET 2', $q->sql());
        $params = $q->parameters();
        $this->assertEquals(35, $params[':p1']);
    }
    public function testNestedConditions()
    {
        $q = new SelectQuery(['test']);
        $q->setWhere(new AndOp([
            new Condition('>', new Field('id'), 12),
            new OrOp([
                new Condition('=', new Field('status'), 'demolished'),
                new NotOp(
                    new Condition('<', new Field('age'), 5)
                )
            ])
        ]));
        $this->assertEquals('SELECT `t0`.* FROM `test` AS `t0` WHERE (`t0`.`id` > :p1 AND (`t0`.`status` = :p2 OR NOT (`t0`.`age` < :p3)))', $q->sql());
    }
    public function testNotOp()
    {
        try {
            new NotOp(array(
                new Condition('=', new Field('test'), 1),
                new Condition('=', new Field('test'), 2),
            ));
            $this->fail(); // exception should happen
        } catch (InvalidArgumentException $e) {
        }
    }
    public function testInCondition()
    {
        $q = new SelectQuery(array('test'));
        $q->setWhere(new Condition('in', new Field('id'), array(1, 3, 5)));
        $this->assertEquals('SELECT `t0`.* FROM `test` AS `t0` WHERE `t0`.`id` IN (1, 3, 5)', $q->sql());
    }
    public function testSelectSpecificFields()
    {
        $q = new SelectQuery(array('test', 'test2'));
        $q->setSelect(array(new AllFields(), new Field('id', 1)));
        $this->assertEquals('SELECT `t0`.*, `t1`.`id` FROM `test` AS `t0`, `test2` AS `t1`', $q->sql());
    }
    public function testAlias()
    {
        $field1 = new Field('id', 0, 'test');
        $q = new SelectQuery(array('test', 'test2'));
        $q->setSelect(array($field1, new AllFields(1)));
        $q->setWhere(new Condition('=', $field1, '2'));
        $this->assertEquals('SELECT `t0`.`id` AS `test`, `t1`.* FROM `test` AS `t0`, `test2` AS `t1` WHERE `test` = :p1', $q->sql());
    }
    public function testWhereNull()
    {
        $q = new SelectQuery(array('test'));
        $q->setWhere(new AndOp(array(
            new Condition('=', new Field('a'), null),
            new Condition('<>', new Field('b'), null),
        )));
        $this->assertEquals('SELECT `t0`.* FROM `test` AS `t0` WHERE (`t0`.`a` IS NULL AND `t0`.`b` IS NOT NULL)', $q->sql());
        $q->setWhere();
        $this->assertEquals('SELECT `t0`.* FROM `test` AS `t0`', $q->sql());
    }
    public function testSelectWrongs()
    {
        try {
            $q = new SelectQuery('test');
            $q->setSelect(array());
            $this->fail("noone is allowed to select nothing!");
        } catch (InvalidArgumentException $e) {
        }
        try {
            $q = new SelectQuery('test');
            $q->setSelect(array('field1'), 'test');
            $this->fail("second params should be boolean!");
        } catch (InvalidArgumentException $e) {
        }
        try {
            $q = new SelectQuery(array(123));
            $this->fail("tables param should be either string or QBTable!");
        } catch (LogicException $e) {
        }
    }
    public function testAggregate()
    {
        $q = new SelectQuery('test');
        $q->setSelect(new Aggregate('count'));
        $this->assertEquals('SELECT COUNT(*) FROM `test` AS `t0`', $q->sql());
        // This should throw exception, as count accept only '*' not 't0.*'
        try {
            new Aggregate('count', new AllFields());
            fail();
        } catch (InvalidArgumentException $e) {
        }
    }
    public function testGroupBy()
    {
        $group_by = array(new Field('year'));
        $q = new SelectQuery(array('test'));
        $q->setGroupby($group_by);
        $this->assertEquals('SELECT `t0`.* FROM `test` AS `t0` GROUP BY `t0`.`year`', $q->sql());
        $q->setHaving(new Condition('>', new Aggregate('count', new Field('commit')), 20));
        $this->assertEquals('SELECT `t0`.* FROM `test` AS `t0` GROUP BY `t0`.`year` HAVING COUNT(`t0`.`commit`) > :p1', $q->sql());
        $q->setHaving();
        $this->assertEquals('SELECT `t0`.* FROM `test` AS `t0` GROUP BY `t0`.`year`', $q->sql());
        $this->assertEquals(var_export($group_by, true), var_export($q->showGroupBy(), true)); // check required by BebopCMS(tm)
    }
    public function testGroupByAggregate()
    {
        $group_by = new Aggregate('count', new Field('user'), true, 'c');
        $field = new Field('very_long_identifier', 0, 'url');
        $q = new SelectQuery('test');
        $q->setSelect(array($group_by, $field));
        $q->setGroupby(array($group_by));
        $q->setOrderBy(array($field));
        $this->assertEquals('SELECT COUNT(DISTINCT `t0`.`user`) AS `c`, `t0`.`very_long_identifier` AS `url` FROM `test` AS `t0` GROUP BY `c` ORDER BY `url` ASC', $q->sql());
    }
    public function testOrderByFunction()
    {
        $f = new SqlFunction('year', new Field('stamp'), 'year');
        $q = new SelectQuery('test');
        $q->setSelect(array($f, new Field('profit')));
        $q->setOrderBy(array($f));
        $this->assertEquals('SELECT YEAR(`t0`.`stamp`) AS `year`, `t0`.`profit` FROM `test` AS `t0` ORDER BY `year` ASC', $q->sql());
    }
    public function testOrderBy()
    {
        $q = new SelectQuery('test');
        $q->setOrderBy(array(new Field('id')), array(true));
        $this->assertEquals('SELECT `t0`.* FROM `test` AS `t0` ORDER BY `t0`.`id` DESC', $q->sql());
    }
    public function testInfo()
    {
        $tbls = array('test1', 'test2', 'test3');
        $q = new SelectQuery($tbls);
        $this->assertEquals(var_export($tbls, true), var_export($q->showTables(), true));
        $condition = new Condition('=', new Field('id'), 1);
        $q->setWhere($condition);
        $this->assertEquals(var_export($condition, true), var_export($q->showConditions(), true));
    }
    public function testMultiSchema()
    {
        $q = new SelectQuery(new QBTable('test', 'db2'));
        $this->assertEquals('SELECT `t0`.* FROM `db2`.`test` AS `t0`', $q->sql());
    }
    public function testUseIndex()
    {
        $q = new SelectQuery('test');
        $q->setIndices(array('abc', 'def'));
        $this->assertEquals('SELECT `t0`.* FROM `test` AS `t0` USE INDEX (`abc`, `def`)', $q->sql());
    }
    public function testSelectWithSubquery()
    {
        $field1 = new Field('id', 0);
        $subq = new SelectQuery(array('sub_test'));
        $subq->setSelect(array($field1));
        $q = new SelectQuery(array('test'));
        $q->setWhere(new Condition('in', new Field('id'), $subq));
        $this->assertEquals('SELECT `t0`.* FROM `test` AS `t0` WHERE `t0`.`id` IN (SELECT `t0`.`id` FROM `sub_test` AS `t0`)', $q->sql());
    }
    /**
     * testing nested subquery (query, which uses subquery, which uses subquery) and checking, that conditions are correct (for use in prepared queries)
     */
    public function testSubqueryWithConditions()
    {
        $field1 = new Field('id', 0);
        $subq2 = new SelectQuery(array('bans'));
        $subq2->setSelect(array($field1));
        $subq2->setWhere(new Condition('=', new Field('abc'), 10));
        $subq = new SelectQuery(array('users'));
        $subq->setSelect(array($field1));
        $subq->setWhere(new AndOp(
            new Condition('>', new Field('age'), 14),
            new Condition('in', new Field('id'), $subq2),
            new Condition('=', new Field('regdt'), '2009-01-01')
        ));
        $q = new SelectQuery(array('posts'));
        $q->setWhere(new AndOp(
            new Condition('>', new Field('comment'), 1),
            new Condition('in', new Field('user_id'), $subq),
            new Condition('=', new Field('funny'), 1)
        ));
        $this->assertEquals('SELECT `t0`.* FROM `posts` AS `t0` WHERE (`t0`.`comment` > :p1 AND `t0`.`user_id` IN (SELECT `t0`.`id` FROM `users` AS `t0` WHERE (`t0`.`age` > :p2 AND `t0`.`id` IN (SELECT `t0`.`id` FROM `bans` AS `t0` WHERE `t0`.`abc` = :p3) AND `t0`.`regdt` = :p4)) AND `t0`.`funny` = :p5)', $q->sql());
    }
}
