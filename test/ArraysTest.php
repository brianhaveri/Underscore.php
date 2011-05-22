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
}