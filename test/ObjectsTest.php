<?php

include_once(__DIR__ . '/../underscore.php');

// @see testFunctions()
class FunctionsTestClass {
  const FOO = 'BAR';
  public static $_foo = 'bar';
  public static function methodA() {}
  public static function methodB() {}
  private function _methodC() {}
}

class UnderscoreObjectsTest extends PHPUnit_Framework_TestCase {
  
  public function testKeys() {
    // from js
    $this->assertEquals(array('one', 'two'), _::keys((object) array('one'=>1, 'two'=>2)), 'can extract the keys from an object');
    
    $a = array(1=>0);
    $this->assertEquals(array(1), _::keys($a), 'is not fooled by sparse arrays');
    
    $actual = 'underscore';
    try { $actual = _::keys(null); } catch(Exception $e) {}
    $this->assertEquals('underscore', $actual, 'throws an exception for null values');
    
    $actual = 'underscore';
    try { $actual = _::keys(UNDERSCORE_FOO); } catch(Exception $e) {}
    $this->assertEquals('underscore', $actual, 'throws an exception for undefined values');
    
    $actual = 'underscore';
    try { $actual = _::keys(1); } catch(Exception $e) {}
    $this->assertEquals('underscore', $actual, 'throws an exception for number primitives');
    
    $actual = 'underscore';
    try { $actual = _::keys('a'); } catch(Exception $e) {}
    $this->assertEquals('underscore', $actual, 'throws an exception for string primitives');
    
    $actual = 'underscore';
    try { $actual = _::keys(true); } catch(Exception $e) {}
    $this->assertEquals('underscore', $actual, 'throws an exception for boolean primitives');
    
    // extra
    $this->assertEquals(array('one', 'two'), _::keys(array('one'=>1, 'two'=>2)), 'can extract the keys from an array');
  }
  
  public function testValues() {
    // from js
    $items = array('one'=>1, 'two'=>2);
    $this->assertEquals(array(1,2), _::values((object) $items), 'can extract the values from an object');
    
    // extra
    $this->assertEquals(array(1,2), _::values($items));
    $this->assertEquals(array(1), _::values(array(1)));
  }
  
  public function testFunctions() {
    // from js doesn't really apply here because in php function aren't truly first class citizens
    
    // extra
    $this->assertEquals(array('methodA', 'methodB'), _::functions(new FunctionsTestClass));
  }
  
  public function testIsEqual() {
    // from js
    $moe = (object) array(
      'name' => 'moe',
      'lucky'=> array(13, 27, 34)
    );
    $clone = (object) array(
      'name' => 'moe',
      'lucky'=> array(13, 27, 34)
    );
    $this->assertFalse($moe === $clone, 'basic equality between objects is false');
    $this->assertTrue(_::isEqual($moe, $clone), 'deep equality is true');
    
    // @todo
    /*
    ok(_(moe).isEqual(clone), 'OO-style deep equality works');
    ok(!_.isEqual(5, NaN), '5 is not equal to NaN');
    ok(NaN != NaN, 'NaN is not equal to NaN (native equality)');
    ok(NaN !== NaN, 'NaN is not equal to NaN (native identity)');
    ok(!_.isEqual(NaN, NaN), 'NaN is not equal to NaN');
    ok(_.isEqual(new Date(100), new Date(100)), 'identical dates are equal');
    ok(_.isEqual((/hello/ig), (/hello/ig)), 'identical regexes are equal');
    ok(!_.isEqual(null, [1]), 'a falsy is never equal to a truthy');
    ok(!_.isEqual({x: 1, y: undefined}, {x: 1, z: 2}), 'objects with the same number of undefined keys are not equal');
    ok(!_.isEqual(_({x: 1, y: undefined}).chain(), _({x: 1, z: 2}).chain()), 'wrapped objects are not equal');
    equals(_({x: 1, y: 2}).chain().isEqual(_({x: 1, y: 2}).chain()).value(), true, 'wrapped objects are equal');
    */
  }
  
  public function testIsEmpty() {
    // from js
    $this->assertFalse(_::isEmpty(array(1)), 'array(1) is not empty');
    $this->assertTrue(_::isEmpty(array()), 'array() is empty');
    $this->assertFalse(_::isEmpty((object) array('one'=>1), '(object) array("one"=>1) is not empty'));
    $this->assertTrue(_::isEmpty(new StdClass), 'new StdClass is empty');
    $this->assertTrue(_::isEmpty(null), 'null is empty');
    $this->assertTrue(_::isEmpty(''), 'the empty string is empty');
    $this->assertFalse(_::isEmpty('moe'), 'but other strings are not');
    
    $obj = (object) array('one'=>1);
    unset($obj->one);
    $this->assertTrue(_::isEmpty($obj), 'deleting all the keys from an object empties it');
  }
  
  public function testIsArray() {
    // from js
    $this->assertTrue(_::isArray(array(1,2,3)), 'arrays are');
    
    // extra
    $this->assertFalse(_::isArray(null));
    $this->assertTrue(_::isArray(array()));
    $this->assertTrue(_::isArray(array(array(1,2))));
  }
  
  public function testIsString() {
    // from js
    $this->assertTrue(_::isString(join(', ', array(1,2,3))), 'strings are');
    
    // extra
    $this->assertFalse(_::isString(1));
    $this->assertTrue(_::isString(''));
    $this->assertTrue(_::isString('1'));
    $this->assertFalse(_::isString(array()));
    $this->assertFalse(_::isString(null));
  }
  
  public function testIsNumber() {
    // from js
    $this->assertFalse(_::isNumber('string'), 'a string is not a number');
    $this->assertFalse(_::isNumber(null), 'null is not a number');
    $this->assertTrue(_::isNumber(3 * 4 - 7 / 10), 'but numbers are');
    
    // extra
    $this->assertFalse(_::isNumber(acos(8)), 'invalid calculations (nan) are not numbers');
    $this->assertFalse(_::isNumber('1'), 'strings of numbers are not numbers');
    $this->assertFalse(_::isNumber(log(0)), 'infinite values are not numbers');
    $this->assertTrue(_::isNumber(pi()));
    $this->assertTrue(_::isNumber(M_PI));
  }
  
  public function testIsBoolean() {
    // from js
    $this->assertFalse(_::isBoolean(2), 'a number is not a boolean');
    $this->assertFalse(_::isBoolean('string'), 'a string is not a boolean');
    $this->assertFalse(_::isBoolean('false'), 'the string "false" is not a boolean');
    $this->assertFalse(_::isBoolean('true'), 'the string "true" is not a boolean');
    $this->assertFalse(_::isBoolean(null), 'null is not a boolean');
    $this->assertFalse(_::isBoolean(acos(8)), 'nan values are not booleans');
    $this->assertTrue(_::isBoolean(true), 'but true is');
    $this->assertTrue(_::isBoolean(false), 'and so is false');
    
    // extra
    $this->assertFalse(_::isBoolean(array()));
    $this->assertFalse(_::isBoolean(1));
    $this->assertFalse(_::isBoolean(0));
    $this->assertFalse(_::isBoolean(-1));
  }
  
  public function testIsFunction() {
    // from js
    $func = function() {};
    $this->assertFalse(_::isFunction(array(1,2,3)), 'arrays are not functions');
    $this->assertFalse(_::isFunction('moe'), 'strings are not functions');
    $this->assertTrue(_::isFunction($func), 'but functions are');
    
    // extra
    $this->assertFalse(_::isFunction('array_search'), 'strings with names of functions are not functions');
  }
  
  public function testIsDate() {
    // from js
    $this->assertFalse(_::isDate(1), 'numbers are not dates');
    $this->assertFalse(_::isDate(new StdClass), 'objects are not dates');
    
    date_default_timezone_set('America/Denver'); // don't throw error if timezone not set
    $this->assertTrue(_::isDate(new DateTime()), 'but dates are');
    
    // extra
    $this->assertFalse(_::isDate(time()), 'timestamps are not dates');
    $this->assertFalse(_::isDate('Y-m-d H:i:s'), 'date strings are not dates');
  }
  
  public function testIsNaN() {
    // from js
    $this->assertFalse(_::isNaN(null), 'null not not NaN');
    $this->assertFalse(_::isNaN(0), '0 is not NaN');
    $this->assertTrue(_::isNaN(acos(8)), 'but invalid calculations are');
  }
}