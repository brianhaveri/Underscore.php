<?php

include_once(__DIR__ . '/../underscore.php');

class UnderscoreArraysTest extends PHPUnit_Framework_TestCase {
  
  public function testFirst() {
    // from js
    $this->assertEquals(1, _::first(array(1,2,3)), 'can pull out the first element of an array');
    $this->assertEquals(array(), _::first(array(1,2,3), 0), 'can pass an index to first');
    $this->assertEquals(array(1, 2), _::first(array(1,2,3), 2), 'can pass an index to first');
    $this->assertEquals(1, _(array(1,2,3))->first(), 'can perform OO-style "first()"');    
    
    $result = _::map(array(array(1,2,3), array(1,2,3)), function($vals) {
      return _::first($vals);
    });
    $this->assertEquals(array(1,1), $result, 'works well with _.map');
    
    $func = function() { return _::first(func_get_args()); };
    $result = $func(4,3,2,1);
    $this->assertEquals(4, $result, 'works on an arguments object');
    
    // extra
    $this->assertEquals(array(1), _::first(array(1,2,3), 1), 'can pass an index of 1 to first');
    $this->assertEquals(array(4,5), _(array(4,5,6,7))->first(2), 'can perform OO-style "first()" with index passed');
  }
  
  public function testRest() {
    $numbers = array(1,2,3,4);
    
    // from js
    $this->assertEquals(array(2,3,4), _::rest($numbers), 'working rest()');
    $this->assertEquals(array(1,2,3,4), _::rest($numbers, 0), 'working rest(0)');
    $this->assertEquals(array(3,4), _::rest($numbers, 2), 'rest can take an index');
    
    $func = function() { return _(func_get_args())->tail(); };
    $result = $func(1,2,3,4);
    $this->assertEquals(array(2,3,4), $result, 'aliased as tail and works on arguments');
    
    $result = _::map(array(array(1,2,3), array(1,2,3)), function($vals) { return _::rest($vals); });
    $this->assertEquals('2,3,2,3', join(',', _::flatten($result)), 'works well with _::map');
    
    // extra
    $this->assertEquals(array('b','c'), _::tail(array('a','b','c')));
  }
  
  public function testLast() {
    // from js
    $this->assertEquals(3, _::last(array(1,2,3)), 'can pull out the last element of an array');
    
    $func = function() { return _(func_get_args())->last(); };
    $result = $func(1,2,3,4);
    $this->assertEquals(4, $result, 'works on arguments');
  }
  
  public function testCompact() {
    $vals = array(0, 1, false, 2, false, 3);
    
    // from js
    $this->assertEquals(3, count(_::compact($vals)), 'can trim out all falsy values');
    
    $func = function() { return count(_(func_get_args())->compact()); };
    $result = $func(0, 1, false, 2, false, 3);
    $this->assertEquals(3, $result, 'works on arguments');
    
    // extra
    $this->assertEquals(array(1, 2, 3), _::compact($vals), 'can remove all falsy values');
  }
  
  public function testFlatten() {
    $list = array(1, array(2), array(3, array(array(array(4)))));
    
    // from js
    $this->assertEquals(array(1,2,3,4), _::flatten($list), 'can flatten nested arrays');
    
    $func = function() { return _::flatten(func_get_args()); };
    $result = $func(1, array(2), array(3, array(array(array(4)))));
    $this->assertEquals(array(1,2,3,4), $result, 'works with arguments');
  }
  
  public function testWithout() {
    $list = array(1, 2, 1, 0, 3, 1, 4);
    
    // from js
    $this->assertEquals(array(1=>2,4=>3,6=>4), _::without($list, 0, 1), 'can remove all instances of an object');
    $list = array(
      (object) array('one'=>1),
      (object) array('two'=>2)
    );
    $this->assertEquals(2, count(_::without($list, (object) array('one'=>1))), 'uses real object identity for comparisons.');
    $this->assertEquals(1, count(_::without($list, $list[0])), 'ditto.');
    
    $func = function() { return _::without(func_get_args(), 0, 1); };
    $result = $func(1, 2, 1, 0, 3, 1, 4);
    $this->assertEquals(array(1=>2,4=>3,6=>4), $result, 'works on an arguments object');
  
    // extra
    $this->assertEquals(array(4,5,6), _(array(4,5,6,7,8))->without(7,8), 'works in OO-style calls');
  }
  
  public function testUniq() {
    // from js
    $list = array(1, 2, 1, 3, 1, 9);
    $this->assertEquals(array(1, 2, 3, 9), _::uniq($list), 'can find the unique values of an unsorted array');
    
    $list = array(1, 1, 1, 2, 2, 3);
    $this->assertEquals(array(1, 2, 3), _::uniq($list), 'can find the unique values of a sorted array faster');
    
    $func = function() { return _::uniq(func_get_args()); };
    $result = $func(1,2,1,3,1,4);
    $this->assertEquals(array(1,2,3,4), $result, 'works on an arguments object');
    
    // extra
    $this->assertEquals(array(4,5,6), _(array(4,5,4,4,5,5,6))->uniq(), 'works with OO call');
  }
  
  public function testIntersect() {
    // from js
    $stooges = array('moe', 'curly', 'larry');
    $leaders = array('moe', 'groucho');
    $this->assertEquals(array('moe'), _::intersect($stooges, $leaders), 'can take the set intersection of two arrays');
    
    $this->assertEquals(array('moe'), _($stooges)->intersect($leaders), 'can perform an OO-style intersection');
    
    $func = function() use ($leaders) { $args = func_get_args(); return _::intersect($args[0], $leaders); };
    $result = $func($stooges);
    $this->assertEquals(array('moe'), $result, 'works on an arguments object');
  }
  
  public function testZip() {
    // from js
    $names  = array('moe', 'larry', 'curly');
    $ages   = array(30, 40, 50);
    $leaders= array(true);
    $stooges= array(array('moe', 30, true), array('larry', 40, null), array('curly', 50, null));
    $this->assertEquals($stooges, _::zip($names, $ages, $leaders), 'zipped together arrays of different lengths');
    
    // extra
    $this->assertEquals($stooges, _($names)->zip($ages, $leaders), 'can perform OO-style zips of different length arrays');
    
    $numbers = array(1,2,3);
    $letters = array('a','b','c');
    $expected = array(array(1,'a'), array(2,'b'), array(3,'c'));
    $this->assertEquals($expected, _::zip($numbers, $letters), 'can perform normal zips');
    $this->assertEquals($expected, _($numbers)->zip($letters), 'can perform OO-style zips');
  }
  
  public function testIndexOf() {
    // from js
    $numbers = array(1,2,3);
    $this->assertEquals(1, _::indexOf($numbers, 2), 'can compute indexOf');
    $this->assertEquals(-1, _::indexOf(null, 2), 'handles nulls properly');
    
    $numbers = array(10, 20, 30, 40, 50);
    $this->assertEquals(-1, _::indexOf($numbers, 35), '35 is not in the list');
    $this->assertEquals(3, _::indexOf($numbers, 40), '40 is in the list');
    
    $numbers = array(1, 40, 40, 40, 40, 40, 40, 40, 50, 60, 70);
    $this->assertEquals(1, _::indexOf($numbers, 40), '40 is in the list');
    
    $func = function() { return _::indexOf(func_get_args(), 2); };
    $result = $func(1,2,3);
    $this->assertEquals(1, $result, 'works on an arguments object');
    
    // extra
    $this->assertEquals(2, _(array('a','b','c','d'))->indexOf('c'), 'works with OO-style calls');
    $this->assertEquals('b', _(array('a'=>5,'b'=>10,'c'=>15))->indexOf(10), 'works with associative arrays');
    $this->assertEquals(1, _::indexOf('foobar', 'o'), 'works with strings');
  }
  
  public function testLastIndexOf() {
    // from js
    $numbers = array(1, 0, 1, 0, 0, 1, 0, 0, 0);
    $this->assertEquals(5, _::lastIndexOf($numbers, 1), 'can computer lastIndexOf');
    $this->assertEquals(8, _::lastIndexOf($numbers, 0), 'lastIndexOf the other element');
    $this->assertEquals(-1, _::lastIndexOf(null, 2), 'handles nulls properly');
    
    $func = function() { return _::lastIndexOf(func_get_args(), 1); };
    $result = $func(1, 0, 1, 0, 0, 1, 0, 0, 0);
    $this->assertEquals(5, $result, 'works on an arguments object');
    
    // extra
    $this->assertEquals(4, _(array('a','b','c','c','c','d'))->lastIndexOf('c'), 'works with OO-style calls');
    $this->assertEquals('c', _(array('a'=>5,'b'=>10,'c'=>10))->lastIndexOf(10), 'works with associative arrays');
    $this->assertEquals(2, _::lastIndexOf('foobar', 'o'), 'works with strings');
  }
  
  public function testRange() {
    // from js
    $this->assertEquals(array(), _::range(0), 'range with 0 as a first argument generates an empty array');
    $this->assertEquals(array(0,1,2,3), _::range(4), 'range with a single positive argument generates an array of elements 0,1,2,...,n-1');
    $this->assertEquals(array(5,6,7), _::range(5, 8), 'range with two arguments a & b, a<b generates an array of elements a,a+1,a+2,...,b-2,b-1');
    $this->assertEquals(array(), _::range(8, 5), 'range with two arguments a & b, b<a generates an empty array');
    $this->assertEquals(array(3,6,9), _::range(3, 10, 3), 'range with three arguments a & b & c, c < b-a, a < b generates an array of elements a,a+c,a+2c,...,b - (multiplier of a) < c');
    $this->assertEquals(array(3), _::range(3, 10, 15), 'range with three arguments a & b & c, c > b-a, a < b generates an array with a single element, equal to a');
    $this->assertEquals(array(12,10,8), _::range(12, 7, -2), 'range with three arguments a & b & c, a > b, c < 0 generates an array of elements a,a-c,a-2c and ends with the number not less than b');
    $this->assertEquals(array(0, -1, -2, -3, -4, -5, -6, -7, -8, -9), _::range(0, -10, -1), 'final example in the Python docs');
  
    // extra
    $this->assertEquals(array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9), _::range(10));
    $this->assertEquals(array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10), _::range(1, 11));
    $this->assertEquals(array(0, 5, 10, 15, 20, 25), _::range(0, 30, 5));
    $this->assertEquals(array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9), _(10)->range(), 'works in OO-style calls and 1 parameter');
    $this->assertEquals(array(10,11,12), _(10)->range(13), 'works in OO-style calls and 2 parameters');
    $this->assertEquals(array(3,6,9), _(3)->range(10, 3), 'works in OO-style calls and 3 parameters');
  }
}