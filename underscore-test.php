<?php

include('underscore.php');

class FunctionsTestClass {
  public static function methodA() {}
  public static function methodB() {}
}

class UnderscoreTest extends PHPUnit_Framework_TestCase {
  
  public function testSize() {
    $tests = array(
      // val, expected
      array(array(), 0),
      array(array(1), 1),
      array(array(1, 2, 3), 3)
    );
    foreach($tests as $test) {
      $this->assertEquals($test[1], _::size($test[0]));
    }
  }
  
  public function testMin() {
    $stooges = array(
      array('name'=>'moe',   'age'=>40),
      array('name'=>'larry', 'age'=>50),
      array('name'=>'curly', 'age'=>60)
    );
    $this->assertEquals($stooges[0], _::min($stooges, function($stooge) { return $stooge['age']; }));
    $this->assertEquals($stooges[2], _::min($stooges, function($stooge) { return $stooge['name']; }));
  }
  
  public function testSortBy() {
    $stooges = array(
      array('name'=>'moe',   'age'=>40),
      array('name'=>'larry', 'age'=>50),
      array('name'=>'curly', 'age'=>60)
    );
    $this->assertEquals($stooges, _::sortBy($stooges, function($stooge) { return $stooge['age']; }));
    $this->assertEquals(array($stooges[2], $stooges[1], $stooges[0]), _::sortBy($stooges, function($stooge) { return $stooge['name']; }));
    $this->assertEquals(array(5, 4, 6, 3, 1, 2), _::sortBy(array(1, 2, 3, 4, 5, 6), function($num) { return sin($num); }));
  }
  
  public function testKeys() {
    $tests = array(
      // val, expected
      array(array(1, 2), array(0, 1)),
      array(array('a'=>1, 'b'=>2), array('a', 'b')),
      array((object) array('c'=>3, 'd'=>4), array('c', 'd'))
    );
    foreach($tests as $test) {
      $this->assertEquals($test[1], _::keys($test[0]));
    }
  }
  
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