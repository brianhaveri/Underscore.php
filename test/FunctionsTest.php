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
    
    usleep(100000);
    
    // extra
    $counter = 0;
    $incr = function() use (&$counter) { $counter++; };
    $throttledIncr = _($incr)->throttle(100);
    $throttledIncr(); $throttledIncr(); $throttledIncr();
    usleep(120 * 1000); $throttledIncr();
    usleep(140 * 1000); $throttledIncr();
    usleep(220 * 1000); $throttledIncr();
    usleep(240 * 1000); $throttledIncr();
    $this->assertEquals(5, $counter, 'incr was throttled with OO-style call');
  }
  
  public function testOnce() {
    // from js
    $num = 0;
    $increment = _::once(function() use (&$num) { return $num++; });
    $increment();
    $increment();
    $this->assertEquals(1, $num);
  }
  
  public function testWrap() {
    // from js
    $greet = function($name) { return 'hi: ' . $name; };
    $backwards = _::wrap($greet, function($func, $name) { return $func($name) . ' ' . strrev($name); });
    $this->assertEquals('hi: moe eom', $backwards('moe'), 'wrapped the salutation function');
    
    $inner = function() { return 'Hello '; };
    $arr = array('name'=>'Moe');
    $arr['hi'] = _::wrap($inner, function($fn) use ($arr) { return $fn() . $arr['name']; });
    $this->assertEquals('Hello Moe', $arr['hi']());
  }
  
  public function testCompose() {
    // from js
    $greet = function($name) { return 'hi: ' . $name; };
    $exclaim = function($sentence) { return $sentence . '!'; };
    $composed = _::compose($exclaim, $greet);
    $this->assertEquals('hi: moe!', $composed('moe'), 'can compose a function that takes another');
    
    $composed = _::compose($greet, $exclaim);
    $this->assertEquals('hi: moe!', $composed('moe'), 'in this case, the functions are also commutative');
  }
  
  public function testAfter() {
    // from js
    $testAfter = function($afterAmount, $timesCalled) {
      $afterCalled = 0;
      $after = _::after($afterAmount, function() use (&$afterCalled) {
        $afterCalled++;
      });
      while($timesCalled--) $after();
      return $afterCalled;
    };
    
    $this->assertEquals(1, $testAfter(5, 5), 'after(N) should fire after being called N times');
    $this->assertEquals(0, $testAfter(5, 4), 'after(N) should not fire unless called N times');
  }
}