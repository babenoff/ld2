<?php
/**
 * Operator.php at ld2.my
 * Created by Babenoff at 28.08.16 - 0:44
 * Maksim Babenko <mb.babenoff@yandex.ru>
 */

namespace LD2\QueryBuilder\Additional;
use InvalidArgumentException;


/**
 * generic Operator class. You can't instantiate this directly
 *
 * @package LD2\QueryBuilder\Additional
 */
class Operator implements MQB_Condition
{
    private $content = array();
    protected $startSql;
    protected $implodeSql;
    protected $endSql;
    protected function __construct(array $content)
    {
        $this->setContent($content);
    }
    /**
     * Specifies array of MQB_Conditions, which should be used as the content of Operator
     *
     * @param array $content
     * @return void
     * @throws InvalidArgumentException
     */
    public function setContent(array $content)
    {
        foreach ($content as $c) {
            if (!is_object($c) or !($c instanceof MQB_Condition)) {
                throw new InvalidArgumentException("Operators should be given valid Operators or Conditions as parameters");
            }
        }
        $this->content = $content;
    }
    /**
     * accessor, which returns internal content-array
     *
     * @return array
     */
    public function getContent()
    {
        return $this->content;
    }

    public function getSql(array &$parameters)
    {
        $sqlparts = array();
        foreach ($this->content as $c) {
            $sqlparts[] = $c->getSql($parameters);
        }
        $parts = implode($this->implodeSql, $sqlparts);
        if (empty($parts))
            return '';
        return $this->startSql.$parts.$this->endSql;
    }
}
