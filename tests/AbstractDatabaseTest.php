<?php
namespace Test;
use PHPUnit_Extensions_Database_DataSet_IDataSet;
use PHPUnit_Extensions_Database_DB_IDatabaseConnection;
use PHPUnit_Extensions_Database_TestCase;

/**
 * AbstractDatabaseTest.php at ld2.my
 * Created by Babenoff at 27.08.16 - 22:29
 * Maksim Babenko <mb.babenoff@yandex.ru>
 */
abstract class AbstractDatabaseTest extends PHPUnit_Extensions_Database_TestCase
{
    // only instantiate pdo once for test clean-up/fixture load
    static private $pdo = null;

    // only instantiate PHPUnit_Extensions_Database_DB_IDatabaseConnection once per test
    /**
     * @var null|PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    private $conn = null;

    /**
     * Returns the test database connection.
     *
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    final public function getConnection()
    {
        if($this->conn === null) {
            if(self::$pdo === null){
                self::$pdo = new \LD2\Database(
                    $GLOBALS["DB_USER"],
                    $GLOBALS["DB_PASSWD"],
                    $GLOBALS["DB_DBNAME"],
                    $GLOBALS["DB_HOST"],
                    $GLOBALS["DB_DRIVER"]
                );
            }
            $this->conn = $this->createDefaultDBConnection(self::$pdo, $GLOBALS["DB_DBNAME"]);
        }

        return $this->conn;
    }

    /**
     * @return \PDO
     */
    protected function getPDO(){
        return $this->conn->getConnection();
    }

    /**
     * Returns the test dataset.
     *
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    protected function getDataSet()
    {
        return new \PHPUnit_Extensions_Database_DB_DataSet($this->conn);
    }
}