<?php

include('underscore.php');

class UnderscoreTest extends PHPUnit_Framework_TestCase {
  
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