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
    $this->assertEquals(array('three', 'four'), _(array('three'=>3, 'four'=>4))->keys(), 'can extract the keys from an array using OO-style call');
  
    // docs
    $this->assertEquals(array('name', 'age'), _::keys((object) array('name'=>'moe', 'age'=>40)));
  }
  
  public function testValues() {
    // from js
    $items = array('one'=>1, 'two'=>2);
    $this->assertEquals(array(1,2), _::values((object) $items), 'can extract the values from an object');
    
    // extra
    $this->assertEquals(array(1,2), _::values($items));
    $this->assertEquals(array(1), _::values(array(1)));
    $this->assertEquals(array(1,2), _($items)->values());
    
    // docs
    $this->assertEquals(array('moe', 40), _::values((object) array('name'=>'moe', 'age'=>40)));
  }
  
  public function testExtend() {
    // from js
    $result = _::extend(array(), array('a'=>'b'));
    $this->assertEquals(array('a'=>'b'), $result, 'can extend an array with the attributes of another');
    
    $result = _::extend((object) array(), (object) array('a'=>'b'));
    $this->assertEquals((object) array('a'=>'b'), $result, 'can extend an object with the attributes of another');
    
    $result = _::extend(array('a'=>'x'), array('a'=>'b'));
    $this->assertEquals(array('a'=>'b'), $result, 'properties in source override destination');
    
    $result = _::extend(array('x'=>'x'), array('a'=>'b'));
    $this->assertEquals(array('x'=>'x', 'a'=>'b'), $result, "properties not in source don't get overriden");
    
    $result = _::extend(array('x'=>'x'), array('a'=>'b'), array('b'=>'b'));
    $this->assertEquals(array('x'=>'x', 'a'=>'b', 'b'=>'b'), $result, 'can extend from multiple sources');
    
    $result = _::extend(array('x'=>'x'), array('a'=>'a', 'x'=>2), array('a'=>'b'));
    $this->assertEquals(array('x'=>2, 'a'=>'b'), $result, 'extending from multiple source objects last property trumps');
    
    // extra
    $result = _(array('x'=>'x'))->extend(array('a'=>'a', 'x'=>2), array('a'=>'b'));
    $this->assertEquals(array('x'=>2, 'a'=>'b'), $result, 'extending from multiple source objects last property trumps');
    
    // docs
    $expected = (object) array('name'=>'moe', 'age'=>50);
    $result = _::extend((object) array('name'=>'moe'), (object) array('age'=>50));
    $this->assertEquals($expected, $result);
  }
  
  public function testDefaults() {
    // from js
    $options = array('zero'=>0, 'one'=>1, 'empty'=>'', 'nan'=>acos(8), 'string'=>'string');
    $options = _::defaults($options, array('zero'=>1, 'one'=>10, 'twenty'=>20));
    $this->assertEquals(0, $options['zero'], 'value exists');
    $this->assertEquals(1, $options['one'], 'value exists');
    $this->assertEquals(20, $options['twenty'], 'default applied');
    
    $options_obj = (object) array('zero'=>0, 'one'=>1, 'empty'=>'', 'nan'=>acos(8), 'string'=>'string');
    $options_obj = _::defaults($options_obj, (object) array('zero'=>1, 'one'=>10, 'twenty'=>20));
    $this->assertEquals(0, $options_obj->zero, 'value exists');
    $this->assertEquals(1, $options_obj->one, 'value exists');
    $this->assertEquals(20, $options_obj->twenty, 'default applied');
    
    $options = _::defaults($options, array('empty'=>'full'), array('nan'=>'nan'), array('word'=>'word'), array('word'=>'dog'));
    $this->assertEquals('', $options['empty'], 'value exists');
    $this->assertTrue(_::isNaN($options['nan']), 'NaN is not overridden');
    $this->assertEquals('word', $options['word'], 'new value is added, first one wins');
    
    $options_obj = _::defaults($options_obj, (object) array('empty'=>'full'), (object) array('nan'=>'nan'), (object) array('word'=>'word'), (object) array('word'=>'dog'));
    $this->assertEquals('', $options_obj->empty, 'value exists');
    $this->assertTrue(_::isNaN($options_obj->nan), 'NaN is not overridden');
    $this->assertEquals('word', $options_obj->word, 'new value is added, first one wins');
  
    // extra
    $options = array('zero'=>0, 'one'=>1, 'empty'=>'', 'nan'=>acos(8), 'string'=>'string');
    $options = _($options)->defaults(array('zero'=>1, 'one'=>10, 'twenty'=>20));
    $this->assertEquals(0, $options['zero'], 'value exists');
    $this->assertEquals(1, $options['one'], 'value exists');
    $this->assertEquals(20, $options['twenty'], 'default applied');
    
    // docs
    $food = (object) array('dairy'=>'cheese');
    $defaults = (object) array('meat'=>'bacon');
    $expected = (object) array('dairy'=>'cheese', 'meat'=>'bacon');
    $this->assertEquals($expected, _::defaults($food, $defaults));
  }
  
  public function testFunctions() {
    // from js doesn't really apply here because in php function aren't truly first class citizens
    
    // extra
    $this->assertEquals(array('methodA', 'methodB'), _::functions(new FunctionsTestClass));
    $this->assertEquals(array('methodA', 'methodB'), _(new FunctionsTestClass)->functions());
    $this->assertEquals(array('methodA', 'methodB'), _::methods(new FunctionsTestClass));
    $this->assertEquals(array('methodA', 'methodB'), _(new FunctionsTestClass)->methods());
  }
  
  public function testClon() {
    // from js
    $moe = array('name'=>'moe', 'lucky'=>array(13, 27, 34));
    $clone = _::clon($moe);
    $this->assertEquals('moe', $clone['name'], 'the clone as the attributes of the original');
    
    $moe_obj = (object) $moe;
    $clone_obj = _::clon($moe_obj);
    $this->assertEquals('moe', $clone_obj->name, 'the clone as the attributes of the original');

    $clone['name'] = 'curly';
    $this->assertTrue($clone['name'] === 'curly' && $moe['name'] === 'moe', 'clones can change shallow attributes without affecting the original');
    
    $clone_obj->name = 'curly';
    $this->assertTrue($clone_obj->name === 'curly' && $moe_obj->name === 'moe', 'clones can change shallow attributes without affecting the original');
    
    $clone['lucky'][] = 101;
    $this->assertEquals(101, _::last($moe['lucky']), 'changes to deep attributes are shared with the original');
    
    $clone_obj->lucky[] = 101;
    $this->assertEquals(101, _::last($moe_obj->lucky), 'changes to deep attributes are shared with the original');
  
    // extra
    $foo = array('name'=>'Foo');
    $bar = _($foo)->clon();
    $this->assertEquals('Foo', $bar['name'], 'works with OO-style call');
    
    // docs
    $stooge = (object) array('name'=>'moe');
    $this->assertEquals((object) array('name'=>'moe'), _::clon($stooge));
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
    $this->assertTrue(_($moe)->isEqual($clone), 'OO-style deep equality works');
    $this->assertFalse(_::isEqual(5, acos(8)), '5 is not equal to NaN');
    $this->assertTrue(acos(8) != acos(8), 'NaN is not equal to NaN (native equality)');
    $this->assertTrue(acos(8) !== acos(8), 'NaN is not equal to NaN (native identity)');
    $this->assertFalse(_::isEqual(acos(8), acos(8)), 'NaN is not equal to NaN');
    
    if(class_exists('DateTime')) {
      $timezone = new DateTimeZone('America/Denver');
      $this->assertTrue(_::isEqual(new DateTime(null, $timezone), new DateTime(null, $timezone)), 'identical dates are equal');
    }
    
    $this->assertFalse(_::isEqual(null, array(1)), 'a falsy is never equal to a truthy');
    $this->assertEquals(true, _(array('x'=>1, 'y'=>2))->chain()->isEqual(_(array('x'=>1, 'y'=>2))->chain())->value(), 'wrapped objects are equal');
    
    // docs
    $stooge = (object) array('name'=>'moe');
    $clon = _::clon($stooge);
    $this->assertFalse($stooge === $clon);
    $this->assertTrue(_::isEqual($stooge, $clon));
    
    // @todo Lower memory usage on these
    //$this->assertFalse(_::isEqual(array('x'=>1, 'y'=>null), array('x'=>1, 'z'=>2)), 'objects with the same number of undefined keys are not equal');
    //$this->assertFalse(_::isEqual(_(array('x'=>1, 'y'=>null))->chain(), _(array('x'=>1, 'z'=>2))->chain()), 'wrapped objects are not equal');
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
  
    // extra
    $this->assertFalse(_(array(1))->isEmpty(), 'array(1) is not empty with OO-style call');
    $this->assertTrue(_(array())->isEmpty(), 'array() is empty with OO-style call');
    $this->assertTrue(_(null)->isEmpty(), 'null is empty with OO-style call');
  
    // docs
    $stooge = (object) array('name'=>'moe');
    $this->assertFalse(_::isEmpty($stooge));
    $this->assertTrue(_::isEmpty(new StdClass));
    $this->assertTrue(_::isEmpty((object) array()));
  }
  
  public function testIsArray() {
    // from js
    $this->assertTrue(_::isArray(array(1,2,3)), 'arrays are');
    
    // extra
    $this->assertFalse(_::isArray(null));
    $this->assertTrue(_::isArray(array()));
    $this->assertTrue(_::isArray(array(array(1,2))));
    $this->assertFalse(_(null)->isArray());
    $this->assertTrue(_(array())->isArray());
    
    // docs
    $this->assertTrue(_::isArray(array(1, 2)));
    $this->assertFalse(_::isArray((object) array(1, 2)));
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
    $this->assertFalse(_(1)->isString());
    $this->assertTrue(_('1')->isString());
    $this->assertTrue(_('')->isString());
    
    // docs
    $this->assertTrue(_::isString('moe'));
    $this->assertTrue(_::isString(''));
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
    $this->assertFalse(_(acos(8))->isNumber());
    $this->assertFalse(_('1')->isNumber());
    $this->assertFalse(_(log(0))->isNumber());
    $this->assertTrue(_(pi())->isNumber());
    $this->assertTrue(_(M_PI)->isNumber());
    $this->assertTrue(_(1)->isNumber());
    
    // docs
    $this->assertTrue(_::isNumber(1));
    $this->assertTrue(_::isNumber(2.5));
    $this->assertFalse(_::isNumber('5'));
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
    $this->assertFalse(_(array())->isBoolean());
    $this->assertTrue(_(true)->isBoolean());
    $this->assertTrue(_(false)->isBoolean());
    $this->assertFalse(_(0)->isBoolean());
    
    // docs
    $this->assertFalse(_::isBoolean(null));
    $this->assertTrue(_::isBoolean(true));
    $this->assertFalse(_::isBoolean(0));
  }
  
  public function testIsFunction() {
    // from js
    $func = function() {};
    $this->assertFalse(_::isFunction(array(1,2,3)), 'arrays are not functions');
    $this->assertFalse(_::isFunction('moe'), 'strings are not functions');
    $this->assertTrue(_::isFunction($func), 'but functions are');
    
    // extra
    $this->assertFalse(_::isFunction('array_search'), 'strings with names of functions are not functions');
    $this->assertFalse(_::isFunction(new _));
    $this->assertFalse(_(array(1,2,3))->isFunction());
    $this->assertFalse(_('moe')->isFunction());
    $this->assertTrue(_($func)->isFunction());
    $this->assertFalse(_('array_search')->isFunction());
    $this->assertFalse(_(new _)->isFunction());
  }
  
  public function testIsDate() {
    // from js
    $this->assertFalse(_::isDate(1), 'numbers are not dates');
    $this->assertFalse(_::isDate(new StdClass), 'objects are not dates');
    
    if(class_exists('DateTime')) {
      $timezone = new DateTimeZone('America/Denver');
      $this->assertTrue(_::isDate(new DateTime(null, $timezone)), 'but dates are');
    }
    
    // extra
    $this->assertFalse(_::isDate(time()), 'timestamps are not dates');
    $this->assertFalse(_::isDate('Y-m-d H:i:s'), 'date strings are not dates');
    $this->assertFalse(_(time())->isDate());
    
    if(class_exists('DateTime')) {
      $timezone = new DateTimeZone('America/Denver');
      $this->assertTrue(_(new DateTime(null, $timezone))->isDate(), 'dates are dates with OO-style call');
    }
    
    // docs
    $this->assertFalse(_::isDate(null));
    $this->assertFalse(_::isDate('2011-06-09 01:02:03'));
    if(class_exists('DateTime')) {
      $timezone = new DateTimeZone('America/Denver');
      $this->assertTrue(_::isDate(new DateTime(null, $timezone)));
    }
  }
  
  public function testIsNaN() {
    // from js
    $this->assertFalse(_::isNaN(null), 'null not not NaN');
    $this->assertFalse(_::isNaN(0), '0 is not NaN');
    $this->assertTrue(_::isNaN(acos(8)), 'but invalid calculations are');
    
    // extra
    $this->assertFalse(_(null)->isNan(), 'null is not NaN with OO-style call');
    $this->assertFalse(_(0)->isNan(), '0 is not NaN with OO-style call');
    $this->assertTrue(_(acos(8))->isNaN(), 'but invalid calculations are with OO-style call');
  }
  
  public function testTap() {
    $intercepted = null;
    $interceptor = function($obj) use (&$intercepted) { $intercepted = $obj; };
    $returned = _::tap(1, $interceptor);
    $this->assertEquals(1, $intercepted, 'passed tapped object to interceptor');
    $this->assertEquals(1, $returned, 'returns tapped object');
    
    $returned = _(array(1,2,3))->chain()
      ->map(function($n) { return $n * 2; })
      ->max()
      ->tap($interceptor)
      ->value();
    $this->assertTrue($returned === 6 && $intercepted === 6, 'can use tapped objects in a chain');
  }
}