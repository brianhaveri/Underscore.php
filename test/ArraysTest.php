<?php

include_once(__DIR__ . '/../underscore.php');

class UnderscoreArraysTest extends PHPUnit_Framework_TestCase {
  
  public function testFirst() {
    // from js
    $this->assertEquals(1, _::first(array(1,2,3)), 'can pull out the first element of an array');
    $this->assertEquals(array(), _::first(array(1,2,3), 0), 'can pass an index to first');
    $this->assertEquals(array(1, 2), _::first(array(1,2,3), 2), 'can pass an index to first');
    
    // extra
    $this->assertEquals(array(1), _::first(array(1,2,3), 1), 'can pass an index of 1 to first');
    
    // @todo
    /*
    $this->assertEquals(1, _(array(1,2,3))->first(), 'can perform OO-style "first()"');
    var result = (function(){ return _.first(arguments); })(4, 3, 2, 1);
    equals(result, 4, 'works on an arguments object.');
    result = _.map([[1,2,3],[1,2,3]], _.first);
    equals(result.join(','), '1,1', 'works well with _.map');
    */
  }
  
  public function testRest() {
    $numbers = array(1,2,3,4);
    
    // from js
    $this->assertEquals(array(2,3,4), _::rest($numbers), 'working rest()');
    $this->assertEquals(array(1,2,3,4), _::rest($numbers, 0), 'working rest(0)');
    $this->assertEquals(array(3,4), _::rest($numbers, 2), 'rest can take an index');
    
    // @todo
    /*
    var result = (function(){ return _(arguments).tail(); })(1, 2, 3, 4);
    equals(result.join(', '), '2, 3, 4', 'aliased as tail and works on arguments object');
    result = _.map([[1,2,3],[1,2,3]], _.rest);
    equals(_.flatten(result).join(','), '2,3,2,3', 'works well with _.map');
    */
  }
  
  public function testLast() {
    // from js
    $this->assertEquals(3, _::last(array(1,2,3), 'can pull out the last element of an array'));
    
    // @todo
    /*
    var result = (function(){ return _(arguments).last(); })(1, 2, 3, 4);
    equals(result, 4, 'works on an arguments object');
    */
  }
  
  public function testCompact() {
    $vals = array(0, 1, false, 2, false, 3);
    
    // from js
    $this->assertEquals(3, count(_::compact($vals)), 'can trim out all falsy values');
    
    // extra
    $this->assertEquals(array(1, 2, 3), _::compact($vals), 'can remove all falsy values');
    
    // @todo
    /*
    var result = (function(){ return _(arguments).compact().length; })(0, 1, false, 2, false, 3);
    equals(result, 3, 'works on an arguments object');
    */
  }
  
  public function testFlatten() {
    $list = array(1, array(2), array(3, array(array(array(4)))));
    
    // from js
    $this->assertEquals(array(1,2,3,4), _::flatten($list), 'can flatten nested arrays');
    
    // @todo
    /*
    var result = (function(){ return _.flatten(arguments); })(1, [2], [3, [[[4]]]]);
    equals(result.join(', '), '1, 2, 3, 4', 'works on an arguments object');
    */
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
    
    // @todo
    /*
    var result = (function(){ return _.without(arguments, 0, 1); })(1, 2, 1, 0, 3, 1, 4);
    equals(result.join(', '), '2, 3, 4', 'works on an arguments object');
    */
  }
  
  public function testUniq() {
    // from js
    $list = array(1, 2, 1, 3, 1, 4);
    $this->assertEquals(array(1, 2, 3, 4), _::uniq($list), 'can find the unique values of an unsorted array');
    
    $list = array(1, 1, 1, 2, 2, 3);
    $this->assertEquals(array(1, 2, 3), _::uniq($list), 'can find the unique values of a sorted array faster');
    
    // @todo
    /*
    var result = (function(){ return _.uniq(arguments); })(1, 2, 1, 3, 1, 4);
    equals(result.join(', '), '1, 2, 3, 4', 'works on an arguments object');
    */
  }
  
  public function testIntersect() {
    // from js
    $stooges = array('moe', 'curly', 'larry');
    $leaders = array('moe', 'groucho');
    $this->assertEquals(array('moe'), _::intersect($stooges, $leaders), 'can take the set intersection of two arrays');
    
    // @todo
    /*
    equals(_(stooges).intersect(leaders).join(''), 'moe', 'can perform an OO-style intersection');
    var result = (function(){ return _.intersect(arguments, leaders); })('moe', 'curly', 'larry');
    equals(result.join(''), 'moe', 'works on an arguments object');
    */
  }
  
  public function testZip() {
    // from js
    $names  = array('moe', 'larry', 'curly');
    $ages   = array(30, 40, 50);
    $leaders= array(true);
    $stooges= array(array('moe', 30, true), array('larry', 40, null), array('curly', 50, null));
    $this->assertEquals($stooges, _::zip($names, $ages, $leaders), 'zipped together arrays of different lengths');
  }
}