<?php

include_once(__DIR__ . '/../underscore.php');

class UnderscoreCollectionsTest extends PHPUnit_Framework_TestCase {
  
  public function testEach() {
    // from js
    $test =& $this;
    _::each(array(1,2,3), function($num, $i) use ($test) {
      $test->assertEquals($num, $i+1, 'each iterators provide value and iteration count');
    });
    
    $answers = array();
    $context = (object) array('multiplier'=>5);
    _::each(array(1,2,3), function($num) use (&$answers, $context) {
      $answers[] = $num * $context->multiplier;
    });
    $this->assertEquals(array(5,10,15), $answers, 'context object property accessed');
    
    $answers = array();
    $obj = (object) array('one'=>1, 'two'=>2, 'three'=>3);
    _::each($obj, function($value, $key) use (&$answers) {
      $answers[] = $key;
    });
    $this->assertEquals(array('one','two','three'), $answers, 'iterating over objects works');
    
    $answer = null;
    _::each(array(1,2,3), function($num, $index, $arr) use (&$answer) {
      if(_::includ($arr, $num)) $answer = true;
    });
    $this->assertTrue($answer, 'can reference the original collection from inside the iterator');
    
    $answers = 0;
    _::each(null, function() use (&$answers) {
      $answers++;
    });
    $this->assertEquals(0, $answers, 'handles a null property');
    
    // extra
    $test =& $this;
    _(array(1,2,3))->each(function($num, $i) use ($test) {
      $test->assertEquals($num, $i+1, 'each iterators provide value and iteration count within OO-style call');
    });
    
    // @todo
    /*
    answers = [];
    _.forEach([1, 2, 3], function(num){ answers.push(num); });
    equals(answers.join(', '), '1, 2, 3', 'aliased as "forEach"');
    */
  }
  
  public function testMap() {
    // from js
    $this->assertEquals(array(2,4,6), _::map(array(1,2,3), function($num) {
      return $num * 2;
    }), 'doubled numbers');
    
    $ifnull = _::map(null, function() {});
    $this->assertTrue(is_array($ifnull) && count($ifnull) === 0, 'handles a null property');
    
    $multiplier = 3;
    $func = function($num) use ($multiplier) { return $num * $multiplier; };
    $tripled = _::map(array(1,2,3), $func);
    $this->assertEquals(array(3,6,9), $tripled);
    
    $doubled = _(array(1,2,3))->map(function($num) { return $num * 2; });
    $this->assertEquals(array(2,4,6), $doubled, 'OO-style doubled numbers');
  }
  
  public function testDetect() {
    // from js
    $this->assertEquals(2, _::detect(array(1,2,3), function($num) { return $num * 2 === 4; }), 'found the first "2" and broke the loop');
    
    // extra
    $iterator = function($n) { return $n % 2 === 0; };
    $this->assertEquals(2, _::detect(array(1, 2, 3, 4, 5, 6), $iterator));
    $this->assertEquals(false, _::detect(array(1, 3, 5), $iterator));
    $this->assertEquals(false, _(array(1,3,5))->detect($iterator), 'works with OO-style calls');
  }
  
  public function testSelect() {
    // from js
    $evens = _::select(array(1,2,3,4,5,6), function($num) { return $num % 2 === 0; });
    $this->assertEquals(array(2, 4, 6), $evens, 'selected each even number');
    
    // extra
    $odds = _(array(1,2,3,4,5,6))->select(function($num) { return $num % 2 !== 0; });
    $this->assertEquals(array(1,3,5), $odds, 'works with OO-style calls');
    
    // @todo
    /*
    evens = _.filter([1, 2, 3, 4, 5, 6], function(num){ return num % 2 == 0; });
    equals(evens.join(', '), '2, 4, 6', 'aliased as "filter"');
    */
  }
  
  public function testReject() {
    // from js
    $odds = _::reject(array(1,2,3,4,5,6), function($num) { return $num % 2 === 0; });
    $this->assertEquals(array(1, 3, 5), $odds, 'rejected each even number');
    
    // extra
    $evens = _(array(1,2,3,4,5,6))->reject(function($num) { return $num % 2 !== 0; });
    $this->assertEquals(array(2,4,6), $evens, 'works with OO-style calls');
  }
  
  public function testAll() {
    // from js
    $this->assertTrue(_::all(array(), _::identity()), 'the empty set');
    $this->assertTrue(_::all(array(true, true, true), _::identity()), 'all true values');
    $this->assertFalse(_::all(array(true, false, true), _::identity()), 'one false value');
    $this->assertTrue(_::all(array(0, 10, 28), function($num) { return $num % 2 === 0;  }), 'even numbers');
    $this->assertFalse(_::all(array(0, 11, 28), function($num) { return $num % 2 === 0;  }), 'odd numbers');
    
    // extra
    $this->assertTrue(_::all(array()));
    $this->assertFalse(_::all(array(null)));
    $this->assertFalse(_::all(0));
    $this->assertFalse(_::all('0'));
    $this->assertFalse(_::all(array(0,1)));
    $this->assertTrue(_::all(array(1)));
    $this->assertTrue(_::all(array('1')));
    $this->assertTrue(_::all(array(1,2,3,4)));
    $this->assertTrue(_(array(1,2,3,4))->all(), 'works with OO-style calls');
    $this->assertTrue(_(array(true, true, true))->all(_::identity()));
    
    // @todo
    /*
    ok(_.every([true, true, true], _.identity), 'aliased as "every"');
    */
  }
  
  public function testAny() {
    // from js
    $this->assertFalse(_::any(array()), 'the empty set');
    $this->assertFalse(_::any(array(false, false, false)), 'all false values');
    $this->assertTrue(_::any(array(false, false, true)), 'one true value');
    $this->assertFalse(_::any(array(1, 11, 29), function($num) { return $num % 2 === 0; }), 'all odd numbers');
    $this->assertTrue(_::any(array(1, 10, 29), function($num) { return $num % 2 === 0; }), 'an even number');
    
    // extra
    $this->assertFalse(_::any(array()));
    $this->assertFalse(_::any(array(null)));
    $this->assertFalse( _::any(array(0)));
    $this->assertFalse(_::any(array('0')));
    $this->assertTrue(_::any(array(0, 1)));
    $this->assertTrue(_::any(array(1)));
    $this->assertTrue(_::any(array('1')));
    $this->assertTrue(_::any(array(1,2,3,4)));
    $this->assertTrue(_(array(1,2,3,4))->any(), 'works with OO-style calls');
    $this->assertFalse(_(array(1,11,29))->any(function($num) { return $num % 2 === 0; }));
    
    // @todo
    /*
    ok(_.some([false, false, true]), 'aliased as "some"');
    */
  }
  
  public function testInclud() {
    // from js
    $this->assertTrue(_::includ(array(1,2,3), 2), 'two is in the array');
    $this->assertFalse(_::includ(array(1,3,9), 2), 'two is not in the array');
    $this->assertTrue(_(array(1,2,3))->includ(2), 'OO-style includ');
    
    // extra
    $collection = array(true, false, 0, 1, -1, 'foo', array(), array('meh'));
    $this->assertTrue(_::includ($collection, true));
    $this->assertTrue(_::includ($collection, false));
    $this->assertTrue(_::includ($collection, 0));
    $this->assertTrue(_::includ($collection, 1));
    $this->assertTrue(_::includ($collection, -1));
    $this->assertTrue(_::includ($collection, 'foo'));
    $this->assertTrue(_::includ($collection, array()));
    $this->assertTrue(_::includ($collection, array('meh')));
    $this->assertFalse(_::includ($collection, 'true'));
    $this->assertFalse(_::includ($collection, '0'));
    $this->assertFalse(_::includ($collection, '1'));
    $this->assertFalse(_::includ($collection, '-1'));
    $this->assertFalse(_::includ($collection, 'bar'));
    $this->assertFalse(_::includ($collection, 'Foo'));
    
    // @todo
    /*
    ok(_.contains({moe:1, larry:3, curly:9}, 3) === true, '_.include on objects checks their values');
    */
  }
  
  public function testReduce() {
    // from js
    $sum = _::reduce(array(1,2,3), function($sum, $num) { return $sum + $num; }, 0);
    $this->assertEquals(6, $sum, 'can sum up an array');
    
    $context = array('multiplier'=>3);
    $sum = _::reduce(array(1,2,3), function($sum, $num) use ($context) { return $sum + $num * $context['multiplier']; }, 0);
    $this->assertEquals(18, $sum, 'can reduce with a context object');
    
    $sum = _::reduce(array(1,2,3), function($sum, $num) { return $sum + $num; }, 0);
    $this->assertEquals(6, $sum, 'default initial value');
    
    $ifnull = null;
    try { _::reduce(null, function() {}); }
    catch(Exception $e) { $ifnull = $e; }
    $this->assertFalse($ifnull === null, 'handles a null (without initial value) properly');
    
    $this->assertEquals(138, _::reduce(null, function(){}, 138), 'handles a null (with initial value) properly');
    
    $sum = _(array(1,2,3))->reduce(function($sum, $num) { return $sum + $num; });
    $this->assertEquals(6, $sum, 'OO-style reduce');
    
    // @todo
    /*
    sum = _.inject([1, 2, 3], function(sum, num){ return sum + num; }, 0);
    equals(sum, 6, 'aliased as "inject"');
    */
  }
  
  public function testReduceRight() {
    // from js
    $list = _::reduceRight(array('foo', 'bar', 'baz'), function($memo, $str) { return $memo . $str; }, '');
    $this->assertEquals('bazbarfoo', $list, 'can perform right folds');
    
    $ifnull = null;
    try { _::reduceRight(null, function() {}); }
    catch(Exception $e) { $ifnull = $e; }
    $this->assertFalse($ifnull === null, 'handles a null (without initial value) properly');
    
    $this->assertEquals(138, _::reduceRight(null, function(){}, 138), 'handles a null (with initial value) properly');
    
    // extra
    $list = _(array('moe','curly','larry'))->reduceRight(function($memo, $str) { return $memo . $str; }, '');
    $this->assertEquals('larrycurlymoe', $list, 'can perform right folds in OO-style');
    
    // @todo
    /*
    var list = _.foldr(["foo", "bar", "baz"], function(memo, str){ return memo + str; }, '');
    equals(list, 'bazbarfoo', 'aliased as "foldr"');

    var list = _.foldr(["foo", "bar", "baz"], function(memo, str){ return memo + str; });
    equals(list, 'bazbarfoo', 'default initial value');
    */
  }
  
  public function testPluck() {
    // from js
    $people = array(
      array('name'=>'moe', 'age'=>30),
      array('name'=>'curly', 'age'=>50)
    );
    $this->assertEquals(array('moe', 'curly'), _::pluck($people, 'name'), 'pulls names out of objects');
    
    // extra: array
    $stooges = array(
      array('name'=>'moe',   'age'=> 40),
      array('name'=>'larry', 'age'=> 50, 'foo'=>'bar'),
      array('name'=>'curly', 'age'=> 60)
    );
    $this->assertEquals(array('moe', 'larry', 'curly'), _::pluck($stooges, 'name'));
    $this->assertEquals(array(40, 50, 60), _::pluck($stooges, 'age'));
    $this->assertEquals(array('bar'), _::pluck($stooges, 'foo'));
    $this->assertEquals(array('bar'), _($stooges)->pluck('foo'), 'works with OO-style call');
    
    // extra: object
    $stooges_obj = new StdClass;
    foreach($stooges as $stooge) {
      $name = $stooge['name'];
      $stooges_obj->$name = (object) $stooge;
    }
    $this->assertEquals(array('moe', 'larry', 'curly'), _::pluck($stooges, 'name'));
    $this->assertEquals(array(40, 50, 60), _::pluck($stooges, 'age'));
    $this->assertEquals(array('bar'), _::pluck($stooges, 'foo'));
    $this->assertEquals(array('bar'), _($stooges)->pluck('foo'), 'works with OO-style call');
  }
  
  public function testMax() {
    // from js
    $this->assertEquals(3, _::max(array(1,2,3)), 'can perform a regular max');
    $this->assertEquals(1, _::max(array(1,2,3), function($num) { return -$num; }), 'can performa a computation-based max');
    
    // extra
    $stooges = array(
      array('name'=>'moe',   'age'=>40),
      array('name'=>'larry', 'age'=>50),
      array('name'=>'curly', 'age'=>60)
    );
    $this->assertEquals($stooges[2], _::max($stooges, function($stooge) { return $stooge['age']; }));
    $this->assertEquals($stooges[0], _::max($stooges, function($stooge) { return $stooge['name']; }));
    $this->assertEquals($stooges[0], _($stooges)->max(function($stooge) { return $stooge['name']; }), 'works with OO-style call');
  }
  
  public function testMin() {
    // from js
    $this->assertEquals(1, _::min(array(1,2,3)), 'can perform a regular min');
    $this->assertEquals(3, _::min(array(1,2,3), function($num) { return -$num; }), 'can performa a computation-based max');
    
    // extra
    $stooges = array(
      array('name'=>'moe',   'age'=>40),
      array('name'=>'larry', 'age'=>50),
      array('name'=>'curly', 'age'=>60)
    );
    $this->assertEquals($stooges[0], _::min($stooges, function($stooge) { return $stooge['age']; }));
    $this->assertEquals($stooges[2], _::min($stooges, function($stooge) { return $stooge['name']; }));
    $this->assertEquals($stooges[2], _($stooges)->min(function($stooge) { return $stooge['name']; }), 'works with OO-style call');
  }
  
  public function testSortBy() {
    // from js
    $people = array(
      (object) array('name'=>'curly', 'age'=>50),
      (object) array('name'=>'moe', 'age'=>30)
    );
    $people_sorted = _::sortBy($people, function($person) { return $person->age; });
    $this->assertEquals(array('moe', 'curly'), _::pluck($people_sorted, 'name'), 'stooges sorted by age');
    
    // extra
    $stooges = array(
      array('name'=>'moe',   'age'=>40),
      array('name'=>'larry', 'age'=>50),
      array('name'=>'curly', 'age'=>60)
    );
    $this->assertEquals($stooges, _::sortBy($stooges, function($stooge) { return $stooge['age']; }));
    $this->assertEquals(array($stooges[2], $stooges[1], $stooges[0]), _::sortBy($stooges, function($stooge) { return $stooge['name']; }));
    $this->assertEquals(array(5, 4, 6, 3, 1, 2), _::sortBy(array(1, 2, 3, 4, 5, 6), function($num) { return sin($num); }));
    $this->assertEquals($stooges, _($stooges)->sortBy(function($stooge) { return $stooge['age']; }), 'works with OO-style call');
  }
  
  public function testSize() {
    // from js
    $items = (object) array(
      'one'   =>1,
      'two'   =>2,
      'three' =>3
    );
    $this->assertEquals(3, _::size($items), 'can compute the size of an object');
    
    // extra
    $this->assertEquals(0, _::size(array()));
    $this->assertEquals(1, _::size(array(1)));
    $this->assertEquals(3, _::size(array(1, 2, 3)));
    $this->assertEquals(6, _::size(array(null, false, array(), array(1,2,array('a','b')), 1, 2)));
  }
}