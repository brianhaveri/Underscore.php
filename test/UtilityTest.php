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
  
  public function testTimes() {
    $vals = array();
    _::times(3, function($i) use (&$vals) { $vals[] = $i; });
    $this->assertEquals(array(0,1,2), $vals, 'is 0 indexed');
    
    // @todo
    /*
    vals = [];
    _(3).times(function (i) { vals.push(i); });
    ok(_.isEqual(vals, [0,1,2]), "works as a wrapper");
    */
  }
  
  public function testMixin() {
    _::mixin(array(
      'myReverse' => function($string) {
        $chars = str_split($string);
        krsort($chars);
        return join('', $chars);
      }
    ));
    $this->assertEquals('aecanap', _::myReverse('panacea'), 'mixed in a function to _');
    
    // @todo
    /*
    equals(_('champ').myReverse(), 'pmahc', 'mixed in a function to the OOP wrapper');
    */
  }
}