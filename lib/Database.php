<?php
/**
 * Database.php
 * Created by Babenoff at 26.08.16 - 20:11
 */

namespace LD2;


use LD2\Exception\ClassNotExistsException;
use LD2\Exception\RuntimeExcetion;
use LD2\QueryBuilder\QueryBuilder;

class Database extends \PDO {

    private $engine;
    private $host;
    private $database;
    private $user;
    private $pass;

    protected $debug = false;

    public function __construct($user, $pass, $database, $host="localhost", $engine = "mysql"){
        $this->engine = $engine;
        $this->host = $host;
        $this->database = $database;
        $this->user = $user;
        $this->pass = $pass;
        $dns = $this->engine.':dbname='.$this->database.";host=".$this->host;
        parent::__construct( $dns, $this->user, $this->pass );
    }


    /**
     * @return QueryBuilder
     */
    public function queryBuilder(){
        return new QueryBuilder();
    }

    public function setProfiling(){
        $this->exec("SET PROFILING=1");
    }

    public function getProfilies():array{
        $sql = "SHOW PROFILES;";
        $q = $this->prepare($sql);
        $res = $q->execute();
        if($res) {
            return $q->fetchAll(self::FETCH_ASSOC);
        } else {
            throw new RuntimeExcetion($q->errorInfo()[2]);
        }
    }

    public function getSqlTime():float {
        $p = $this->getProfilies();
        $time = 0;
        foreach ($p as $prof){
            $time+=$prof["Duration"];
        }
        return $time;
    }

    /**
     * @return boolean
     */
    public function isDebug(): bool
    {
        return $this->debug;
    }

    /**
     * @param boolean $debug
     */
    public function setDebug(bool $debug)
    {
        $this->debug = $debug;
    }
}