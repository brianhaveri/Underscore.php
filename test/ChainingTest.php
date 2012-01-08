<?php

class UnderscoreChainingTest extends PHPUnit_Framework_TestCase {
  
  public function testMapFlattenReduce() {
    // from js
    $lyrics = array(
      "I'm a lumberjack and I'm okay",
      "I sleep all night and I work all day",
      "He's a lumberjack and he's okay",
      "He sleeps all night and he works all day"
    );
    $counts = __($lyrics)->chain()
      ->map(function($line) { return str_split($line); })
      ->flatten()
      ->reduce(function($hash, $l) {
        if(!is_array($hash)) $hash = array();
        $hash[$l] = array_key_exists($l, $hash) ? $hash[$l] : 0;
        $hash[$l]++;
        return $hash;
      })
      ->value();
    $this->assertTrue($counts['a'] === 16 && $counts['e'] === 10, 'counted all the letters in the song');
  }
  
  public function testSelectRejectSortBy() {
    // from js
    $numbers = array(1,2,3,4,5,6,7,8,9,10);
    $numbers = __($numbers)->chain()
                           ->select(function($n) { return $n % 2 === 0; })
                           ->reject(function($n) { return $n % 4 === 0; })
                           ->sortBy(function($n) { return -$n; })
                           ->value();
    $this->assertEquals(array(10, 6, 2), $numbers, 'filtered and reversed the numbers in OO-style call');
    
    $numbers = array(1,2,3,4,5,6,7,8,9,10);
    $numbers = __::chain($numbers)->select(function($n) { return $n % 2 === 0; })
                                  ->reject(function($n) { return $n % 4 === 0; })
                                  ->sortBy(function($n) { return -$n; })
                                  ->value();
    $this->assertEquals(array(10, 6, 2), $numbers, 'filtered and reversed the numbers in static call');
  }
  
  public function testChain() {
    // docs
    $numbers = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10);
    $result = __($numbers)->chain()
      ->select(function($n) { return $n < 5; })
      ->reject(function($n) { return $n === 3; })
      ->sortBy(function($n) { return -$n; })
      ->value();
    $this->assertEquals(array(4, 2, 1), $result);
  }
  
  public function testValue() {
    // docs
    $this->assertEquals(array(1, 2, 3), __(array(1, 2, 3))->value());
  }
}