<?php
/**
 * Database.php
 * Created by Babenoff at 26.08.16 - 20:11
 */

namespace LD2;


use LD2\Exception\ClassNotExistsException;
use LD2\QueryBuilder\QueryBuilder;

class Database extends \PDO {

    private $engine;
    private $host;
    private $database;
    private $user;
    private $pass;

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
}