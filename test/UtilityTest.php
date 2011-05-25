<?php

include_once(__DIR__ . '/../underscore.php');

class UnderscoreUtilityTest extends PHPUnit_Framework_TestCase {
  
  public function testIdentity() {
    // from js
    $moe = array('name'=>'moe');
    $moe_obj = (object) $moe;
    $this->assertEquals($moe, _::identity($moe));
    $this->assertEquals($moe_obj, _::identity($moe_obj));
  }
}