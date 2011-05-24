<?php

include('underscore.php');

class FunctionsTestClass {
  public static function methodA() {}
  public static function methodB() {}
}

class UnderscoreTest extends PHPUnit_Framework_TestCase {
  
  public function testValues() {
    $tests = array(
      // val, expected
      array(array(1, 2), array(1, 2)),
      array(array('a'=>1, 'b'=>2), array(1, 2)),
      array((object) array('c'=>3, 'd'=>4), array(3, 4))
    );
    foreach($tests as $test) {
      $this->assertEquals($test[1], _::values($test[0]));
    }
  }
  
  public function testFunctions() {
    $this->assertEquals(array('methodA', 'methodB'), _::functions(new FunctionsTestClass));
  }
  
  public function testIsEqual() {
    $tests = array(
      // a, b, expected
      array(1, 1, true),
      array(1, '1', false),
      array(array(), null, false),
      array(array(), array(0), false),
      array(array(), array(), true),
      array(new StdClass, new StdClass, true),
      array(true, true, true)
    );
    foreach($tests as $test) {
      $this->assertEquals($test[2], _::isEqual($test[0], $test[1]));
    }
  }
}