<?php

require_once dirname(__FILE__) . '/../../SolarUnitTest.config.php';
class Solar_Class_MapTest extends PHPUnit_Framework_TestCase {
    
    public function test__construct()
    {
        $map = Solar::factory('Solar_Class_Map');
        $this->assertType('Solar_Class_Map', $map);
    }
    
    // if we did a full test, it'd be the whole Solar class map,
    // and that's a bit much to keep up with.
    public function testFetch_limited()
    {
        $base = realpath( dirname(__FILE__) . '/../../support/');
        $map = Solar::factory('Solar_Class_Map');
        $actual = $map->fetch($base, 'Solar_Class_Map');
        $expect = array (
            "Solar_Class_Map_DirOne_TestOne" => "$base/Solar/Class/Map/DirOne/TestOne.php",
            "Solar_Class_Map_DirOne_TestTwo" => "$base/Solar/Class/Map/DirOne/TestTwo.php",
            "Solar_Class_Map_TestOne" => "$base/Solar/Class/Map/TestOne.php",
            "Solar_Class_Map_TestTwo" => "$base/Solar/Class/Map/TestTwo.php",
        );

        $this->assertSame($actual, $expect);
    }
}
?>