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
  
  public function testUniqueId() {
    // from js
    $ids = array();
    while($i++ < 100) array_push($ids, _::uniqueId());
    $this->assertEquals(count($ids), count(_::uniq($ids)));
    
    // extra
    $this->assertEquals('stooges_', join('', (_::first(_::uniqueId('stooges'), 8))), 'prefix assignment works');
  }
}