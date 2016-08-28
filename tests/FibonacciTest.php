<?php
namespace Test;
/**
 * FibonacciTest.php
 * Created by Babenoff at 27.08.16 - 1:19
 */
class FibonacciTest extends \PHPUnit\Framework\TestCase
{
    public function testGoldenRatio(){
        $gRatio = (1+sqrt(5))/2;
        //echo $gRatio."\n";
        $Fn = (pow($gRatio, 50) - ( - pow($gRatio,-50))) / ( 2* $gRatio - 1 );
        //echo $Fn;
        $this->assertTrue((boolean)$gRatio);
    }

    public function testInterpolation() {
        $maxLevel = 200;
        $expToLevel50 = 4619;
        $expTable = \LD2\Expirience::factory($maxLevel);
        //echo $expTable->getExpToLevel(50);
        $this->assertCount($maxLevel, $expTable, 'Exp table has constraint 200 levels');
        $this->assertEquals($expTable->getMaxLevel(), $maxLevel, 'The maximum level should be 200');
        $this->assertEquals($expTable->getExpToLevel(50), $expToLevel50, 'Experience to level 50 should be equal 4619');
        do{
            $this->assertTrue($expTable->current() > 0, 'Experience to '.$expTable->key().' level should be greater than zero');
            $expTable->next();
        } while($expTable->valid());
    }
}