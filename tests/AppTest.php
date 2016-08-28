<?php
/**
 * AppTest.php at ld2.my
 * Created by Babenoff at 27.08.16 - 23:08
 * Maksim Babenko <mb.babenoff@yandex.ru>
 */

namespace Test;


use LD2\App;
use LD2\Database;

class AppTest extends AbstractDatabaseTest
{
    protected $app; 
    
    public function testPDO(){
        $this->assertInstanceOf(\PDO::class, $this->getPDO());
        return $this->getPDO();
    }
    /**
     * @param \PDO $pdo
     * @depends testPDO
     * @return App
     */
    public function testApp($pdo){
        $this->assertInstanceOf(\PDO::class, $pdo);
        $app = new App();
        $app->setPdo($pdo);
        $this->app = $app;
        return $app;
    }   
}