<?php
/**
 * QueryBuilderInsertTest.php at ld2.my
 * Created by Babenoff at 28.08.16 - 1:32
 * Maksim Babenko <mb.babenoff@yandex.ru>
 */

namespace Test;


use InvalidArgumentException;
use LD2\QueryBuilder\InsertQuery;
use PHPUnit\Framework\TestCase;

class QueryBuilderInsertTest extends TestCase
{
    public function testBasic()
    {
        $insertQuery = new InsertQuery([
            'test'
        ]);
        $insertQuery->setValues([
            'field1' => 'value1',
            'field2' => 'value2'
        ]);
        $this->assertEquals('INSERT INTO `test` (`field1`, `field2`) VALUES (:p1, :p2)', $insertQuery->sql());
        $params = $insertQuery->parameters();
        $this->assertEquals('value1', $params[':p1']);
        $this->assertEquals('value2', $params[':p2']);
        // shorthand
        $insertQuery = new InsertQuery('test');
        $insertQuery->field1 = 'value1';
        $insertQuery->field2 = 'value2';
        $this->assertEquals('INSERT INTO `test` (`field1`, `field2`) VALUES (:p1, :p2)', $insertQuery->sql());
        $params = $insertQuery->parameters();
        $this->assertEquals('value1', $params[':p1']);
        $this->assertEquals('value2', $params[':p2']);
    }
    public function testMultitable()
    {
        try {
            $insertQuery = new InsertQuery([
                'test',
                'test2'
            ]);
            $this->assertEquals(true, false);
        } catch (InvalidArgumentException $e) {
        }
    }
    public function testOnDuplicate()
    {
        $insertQuery = new InsertQuery(['test'], true);
        $insertQuery->setValues([
            'id' => '35',
            'field1' => 'value1',
            'field2' => 'value2'
        ]);
        $this->assertEquals('INSERT INTO `test` (`id`, `field1`, `field2`) VALUES (:p1, :p2, :p3) ON DUPLICATE KEY UPDATE `id` = LAST_INSERT_ID(`id`), `field1` = VALUES(`field1`), `field2` = VALUES(`field2`)', $insertQuery->sql());
        $params = $insertQuery->parameters();
        $this->assertEquals('35', $params[':p1']);
        $this->assertEquals('value1', $params[':p2']);
        $this->assertEquals('value2', $params[':p3']);
        $this->assertEquals(3, count($params));
    }
}
