<?php

include_once(__DIR__ . '/../underscore.php');

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
}