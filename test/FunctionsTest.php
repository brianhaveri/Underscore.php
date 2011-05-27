<?php

include_once(__DIR__ . '/../underscore.php');

class UnderscoreFunctionsTest extends PHPUnit_Framework_TestCase {
  
  public function testMemoize() {
    // from js
    $fib = function($n) use (&$fib) {
      return $n < 2 ? $n : $fib($n - 1) + $fib($n - 2);
    };
    $fastFib = _::memoize($fib);
    $this->assertEquals(55, $fib(10), 'a memoized version of fibonacci produces identical results');
    $this->assertEquals(55, $fastFib(10), 'a memoized version of fibonacci produces identical results');
    
    $o = function($str) { return $str; };
    $fastO = _::memoize($o);
    $this->assertEquals('toString', $o('toString'), 'checks hasOwnProperty');
    $this->assertEquals('toString', $fastO('toString'), 'checks hasOwnProperty');
  
    // extra
    $name = function() { return 'moe'; };
    $fastName = _::memoize($name);
    $this->assertEquals('moe', $name(), 'works with no parameters');
    $this->assertEquals('moe', $fastName(), 'works with no parameters');
    
    $names = function($one, $two, $three) {
      return join(', ', array($one, $two, $three));
    };
    $fastNames = _::memoize($names);
    $this->assertEquals('moe, larry, curly', $names('moe', 'larry', 'curly'), 'works with multiple parameters');
    $this->assertEquals('moe, larry, curly', $fastNames('moe', 'larry', 'curly'), 'works with multiple parameters');
  }
  
  public function testThrottle() {
    // from js
    $counter = 0;
    $incr = function() use (&$counter) { $counter++; };
    $throttledIncr = _::throttle($incr, 100);
    $throttledIncr(); $throttledIncr(); $throttledIncr();
    usleep(120 * 1000); $throttledIncr();
    usleep(140 * 1000); $throttledIncr();
    usleep(220 * 1000); $throttledIncr();
    usleep(240 * 1000); $throttledIncr();
    $this->assertEquals(5, $counter, 'incr was throttled');
  }
  
  public function testOnce() {
    $num = 0;
    $increment = _::once(function() use (&$num) { return $num++; });
    $increment();
    $increment();
    $this->assertEquals(1, $num);
  }
}