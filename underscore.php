<?php

/**
 * Underscore.php v1.3.1
 * Copyright (c) 2011 Brian Haveri
 * Underscore.php is licensed under the MIT license
 * Underscore.php was inspired by and borrowed from Underscore.js
 * For docs, license, tests, and downloads, see: http://brianhaveri.github.com/Underscore.php
 */

// Returns an instance of __ for OO-style calls
function __($item=null) {
  $__ = new __;
  if(func_num_args() > 0) $__->_wrapped = $item;
  return $__;
}

// Underscore.php
class __ {
  
  // Start the chain
  private $_chained = false; // Are we in a chain?
  public function chain($item=null) {
    list($item) = self::_wrapArgs(func_get_args(), 1);
    
    $__ = (isset($this) && isset($this->_chained) && $this->_chained) ? $this : __($item);
    $__->_chained = true;
    return $__;
  }
  
  
  // End the chain
  public function value() {
    return (isset($this)) ? $this->_wrapped : null;
  }
  
  
  // Invoke the iterator on each item in the collection
  public function each($collection=null, $iterator=null) {
    list($collection, $iterator) = self::_wrapArgs(func_get_args(), 2);
    
    if(is_null($collection)) return self::_wrap(null);
    
    $collection = (array) self::_collection($collection);
    if(count($collection) === 0) return self::_wrap(null);
    
    foreach($collection as $k=>$v) {
      call_user_func($iterator, $v, $k, $collection);
    }
    return self::_wrap(null);
  }
  
  
  // Return an array of values by mapping each item through the iterator
  // map alias: collect
  public function collect($collection=null, $iterator=null) { return self::map($collection, $iterator); }
  public function map($collection=null, $iterator=null) {
    list($collection, $iterator) = self::_wrapArgs(func_get_args(), 2);
    
    if(is_null($collection)) return self::_wrap(array());
    
    $collection = (array) self::_collection($collection);
    if(count($collection) === 0) self::_wrap(array());
    
    $return = array();
    foreach($collection as $k=>$v) {
      $return[] = call_user_func($iterator, $v, $k, $collection);
    }
    return self::_wrap($return);
  }
  
  
  // Reduce a collection to a single value
  // reduce aliases: foldl, inject
  public function foldl($collection=null, $iterator=null, $memo=null) { return self::reduce($collection, $iterator, $memo); }
  public function inject($collection=null, $iterator=null, $memo=null) { return self::reduce($collection, $iterator, $memo); }
  public function reduce($collection=null, $iterator=null, $memo=null) {
    list($collection, $iterator, $memo) = self::_wrapArgs(func_get_args(), 3);
    
    if(!is_object($collection) && !is_array($collection)) {
      if(is_null($memo)) throw new Exception('Invalid object');
      else return self::_wrap($memo);
    }
    
    return self::_wrap(array_reduce($collection, $iterator, $memo));
  }
  
  
  // Right-associative version of reduce
  // reduceRight alias: foldr
  public function foldr($collection=null, $iterator=null, $memo=null) { return self::reduceRight($collection, $iterator, $memo); }
  public function reduceRight($collection=null, $iterator=null, $memo=null) {
    list($collection, $iterator, $memo) = self::_wrapArgs(func_get_args(), 3);
    
    if(!is_object($collection) && !is_array($collection)) {
      if(is_null($memo)) throw new Exception('Invalid object');
      else return self::_wrap($memo);
    }
    
    krsort($collection);
    
    $__ = new self;
    return self::_wrap($__->reduce($collection, $iterator, $memo));
  }
  
  
  // Extract an array of values for a given property
  public function pluck($collection=null, $key=null) {
    list($collection, $key) = self::_wrapArgs(func_get_args(), 2);
    
    $collection = (array) self::_collection($collection);
        
    $return = array();
    foreach($collection as $item) {
      foreach($item as $k=>$v) {
        if($k === $key) $return[] = $v;
      }
    }
    return self::_wrap($return);
  }
  
  
  // Does the collection contain this value?
  // includ alias: contains
  public function contains($collection=null, $val=null) { return self::includ($collection, $val); }
  public function includ($collection=null, $val=null) {
    list($collection, $val) = self::_wrapArgs(func_get_args(), 2);
    
    $collection = (array) self::_collection($collection);
    
    return self::_wrap((array_search($val, $collection, true) !== false));
  }
  
  
  // Invoke the named function over each item in the collection, optionally passing arguments to the function
  public function invoke($collection=null, $function_name=null, $arguments=null) {
    $args = self::_wrapArgs(func_get_args(), 2);
    $__ = new self;
    list($collection, $function_name) = $__->first($args, 2);
    $arguments = $__->rest(func_get_args(), 2);
    
    // If passed an array or string, return an array
    // If passed an object, return an object
    $is_obj = is_object($collection);
    $result = (empty($arguments)) ? array_map($function_name, (array) $collection) : array_map($function_name, (array) $collection, $arguments);
    if($is_obj) $result = (object) $result;
    
    return self::_wrap($result);
  }
  
  
  // Does any values in the collection meet the iterator's truth test?
  // any alias: some
  public function some($collection=null, $iterator=null) { return self::any($collection, $iterator); }
  public function any($collection=null, $iterator=null) {
    list($collection, $iterator) = self::_wrapArgs(func_get_args(), 2);
    
    $collection = self::_collection($collection);
    
    $__ = new self;
    if(!is_null($iterator)) $collection = $__->map($collection, $iterator);
    if(count($collection) === 0) return self::_wrap(false);
    
    return self::_wrap(is_int(array_search(true, $collection, false)));
  }
  
  
  // Do all values in the collection meet the iterator's truth test?
  // all alias: every
  public function every($collection=null, $iterator=null) { return self::all($collection, $iterator); }
  public function all($collection=null, $iterator=null) {
    list($collection, $iterator) = self::_wrapArgs(func_get_args(), 2);
    
    $collection = self::_collection($collection);
    
    $__ = new self;
    if(!is_null($iterator)) $collection = $__->map($collection, $iterator);
    $collection = (array) $collection;
    if(count($collection) === 0) return true;
    
    return self::_wrap(is_bool(array_search(false, $collection, false)));
  }
  
  
  // Return an array of values that pass the truth iterator test
  // filter alias: select
  public function select($collection=null, $iterator=null) { return self::filter($collection, $iterator); }
  public function filter($collection=null, $iterator=null) {
    list($collection, $iterator) = self::_wrapArgs(func_get_args(), 2);
    
    $collection = self::_collection($collection);
    
    $return = array();
    foreach($collection as $val) {
      if(call_user_func($iterator, $val)) $return[] = $val;
    }
    return self::_wrap($return);
  }
  
  
  // Return an array where the items failing the truth test are removed
  public function reject($collection=null, $iterator=null) {
    list($collection, $iterator) = self::_wrapArgs(func_get_args(), 2);
    
    $collection = self::_collection($collection);
    
    $return = array();
    foreach($collection as $val) {
      if(!call_user_func($iterator, $val)) $return[] = $val;
    }
    return self::_wrap($return);
  }
  
  
  // Return the value of the first item passing the truth iterator test
  // find alias: detect
  public function detect($collection=null, $iterator=null) { return self::find($collection, $iterator); }
  public function find($collection=null, $iterator=null) {
    list($collection, $iterator) = self::_wrapArgs(func_get_args(), 2);
    
    $collection = self::_collection($collection);
    
    foreach($collection as $val) {
      if(call_user_func($iterator, $val)) return $val;
    }
    return self::_wrap(false);
  }
  
  
  // How many items are in this collection?
  public function size($collection=null) {
    list($collection) = self::_wrapArgs(func_get_args(), 1);
    
    $collection = self::_collection($collection);
    
    return self::_wrap(count((array) $collection));
  }
  
  
  // Get the first element of an array. Passing n returns the first n elements.
  // first alias: head
  public function head($collection=null, $n=null) { return self::first($collection, $n); }
  public function first($collection=null, $n=null) {
    list($collection, $n) = self::_wrapArgs(func_get_args(), 2);
    
    $collection = self::_collection($collection);
    
    if($n === 0) return self::_wrap(array());
    if(is_null($n)) return self::_wrap(current(array_splice($collection, 0, 1, true)));
    return self::_wrap(array_splice($collection, 0, $n, true));
  }
  
  
  // Get the rest of the array elements. Passing n returns from that index onward.
  public function tail($collection=null, $index=null) { return self::rest($collection, $index); }
  public function rest($collection=null, $index=null) {
    list($collection, $index) = self::_wrapArgs(func_get_args(), 2);
    if(is_null($index)) $index = 1;
    
    $collection = self::_collection($collection);
    
    return self::_wrap(array_splice($collection, $index));
  }
  
  
  // Return everything but the last array element. Passing n excludes the last n elements.
  public function initial($collection=null, $n=null) {
    list($collection, $n) = self::_wrapArgs(func_get_args(), 2);
    
    $collection = (array) self::_collection($collection);
    
    if(is_null($n)) $n = 1;
    $first_index = count($collection) - $n;
    $__ = new self;
    return self::_wrap($__->first($collection, $first_index));
  }
  
  
  // Get the last element from an array. Passing n returns the last n elements.
  public function last($collection=null, $n=null) {
    list($collection, $n) = self::_wrapArgs(func_get_args(), 2);
    $collection = self::_collection($collection);
    
    if($n === 0) $result = array();
    elseif($n === 1 || is_null($n)) $result = array_pop($collection);
    else {
      $__ = new self;
      $result = $__->rest($collection, -$n);
    }
    
    return self::_wrap($result);
  }
  
  
  // Return a copy of the array with falsy values removed
  public function compact($collection=null) {
    list($collection) = self::_wrapArgs(func_get_args(), 1);
    
    $collection = self::_collection($collection);
    
    $__ = new self;
    return self::_wrap($__->select($collection, function($val) {
      return (bool) $val;
    }));
  }
  
  
  // Flattens a multidimensional array
  public function flatten($collection=null, $shallow=null) {
    list($collection, $shallow) = self::_wrapArgs(func_get_args(), 2);
    
    $collection = self::_collection($collection);
    
    $return = array();
    if(count($collection) > 0) {
      foreach($collection as $item) {
        if(is_array($item)) {
          $__ = new self;
          $return = array_merge($return, ($shallow) ? $item : $__->flatten($item));
        }
        else $return[] = $item;
      }
    }
    return self::_wrap($return);
  }
  
  
  // Returns a copy of the array with all instances of val removed
  public function without($collection=null, $val=null) {
    $args = self::_wrapArgs(func_get_args(), 1);
    $collection = $args[0];
    $collection = self::_collection($collection);
    
    $num_args = count($args);
    if($num_args === 1) return self::_wrap($collection);
    if(count($collection) === 0) return self::_wrap($collection);
    
    $__ = new self;
    $removes = $__->rest($args);
    foreach($removes as $remove) {
      $remove_keys = array_keys($collection, $remove, true);
      if(count($remove_keys) > 0) {
        foreach($remove_keys as $key) {
          unset($collection[$key]);
        }
      }
    }
    return self::_wrap($collection);
  }
  
  
  // Return an array of the unique values
  // uniq alias: unique
  public function unique($collection=null, $is_sorted=null, $iterator=null) { return self::uniq($collection, $is_sorted, $iterator); }
  public function uniq($collection=null, $is_sorted=null, $iterator=null) {
    list($collection, $is_sorted, $iterator) = self::_wrapArgs(func_get_args(), 3);
    
    $collection = self::_collection($collection);
    
    $return = array();
    if(count($collection) === 0) return self::_wrap($return);
    
    $calculated = array();
    foreach($collection as $item) {
      $val = (!is_null($iterator)) ? $iterator($item) : $item;
      if(is_bool(array_search($val, $calculated, true))) {
        $calculated[] = $val;
        $return[] = $item;
      }
    }
    
    return self::_wrap($return);
  }
  
  
  // Returns an array containing the intersection of all the arrays
  public function intersection($array=null) {
    $arrays = self::_wrapArgs(func_get_args(), 1);
    
    if(count($arrays) === 1) return self::_wrap($array);
    
    $__ = new self;
    $return = $__->first($arrays);
    foreach($__->rest($arrays) as $next) {
      if(!$__->isArray($next)) $next = str_split((string) $next);
      
      $return = array_intersect($return, $next);
    }
    
    return self::_wrap(array_values($return));
  }
  
  
  // Merge together multiple arrays
  public function union($array=null) {
    $arrays = self::_wrapArgs(func_get_args(), 1);
    
    if(count($arrays) === 1) return self::_wrap($array);
    
    $__ = new self;
    return self::_wrap($__->flatten(array_values(array_unique(call_user_func_array('array_merge', $arrays)))));
  }
  
  
  // Get the difference between two arrays
  public function difference($array_one=null, $array_two=null) {
    $arrays = self::_wrapArgs(func_get_args(), 1);
    
    return self::_wrap(array_values(call_user_func_array('array_diff', $arrays)));
  }
  
  
  // Get the index of the first match
  public function indexOf($collection=null, $item=null) {
    list($collection, $item) = self::_wrapArgs(func_get_args(), 2);
    
    $collection = self::_collection($collection);
    
    $key = array_search($item, $collection, true);
    return self::_wrap((is_bool($key)) ? -1 : $key);
  }
  
  
  // Get the index of the last match
  public function lastIndexOf($collection=null, $item=null) {
    list($collection, $item) = self::_wrapArgs(func_get_args(), 2);
    
    $collection = self::_collection($collection);
    
    krsort($collection);
    $__ = new self;
    return self::_wrap($__->indexOf($collection, $item));
  }
  
  
  // Returns an array of integers from start to stop (exclusive) by step
  public function range($stop=null) {
    $args = self::_wrapArgs(func_get_args(), 1);
    
    $__ = new self;
    $args = $__->reject($args, function($val) {
      return is_null($val);
    });
    
    $num_args = count($args);
    switch($num_args) {
      case 1: 
        list($start, $stop, $step) = array(0, $args[0], 1);
        break;
      case 2:
        list($start, $stop, $step) = array($args[0], $args[1], 1);
        if($stop < $start) return self::_wrap(array());
        break;
      default:
        list($start, $stop, $step) = array($args[0], $args[1], $args[2]);
        if($step > 0 && $step > $stop) return self::_wrap(array($start));
    }
    $results = range($start, $stop, $step);
    
    // Switch inclusive to exclusive
    if($step > 0 && $__->last($results) >= $stop) array_pop($results);
    elseif($step < 0 && $__->last($results) <= $stop) array_pop($results);
    
    return self::_wrap($results);
  }
  
  
  // Merges arrays
  public function zip($array=null) {
    $arrays = self::_wrapArgs(func_get_args());
    $num_arrays = count($arrays);
    if($num_arrays === 1) return self::_wrap($array);
    
    $__ = new self;
    $num_return_arrays = $__->max($__->map($arrays, function($array) {
      return count($array);
    }));
    $return_arrays = $__->range($num_return_arrays);
    foreach($return_arrays as $k=>$v) {
      if(!is_array($return_arrays[$k])) $return_arrays[$k] = array();
      
      foreach($arrays as $a=>$array) {
        $return_arrays[$k][$a] = array_key_exists($k, $array) ? $array[$k] : null;
      }
    }
    
    return self::_wrap($return_arrays);
  }
  
  
  // Get the max value in the collection
  public function max($collection=null, $iterator=null) {
    list($collection, $iterator) = self::_wrapArgs(func_get_args(), 2);
    
    if(is_null($iterator)) return self::_wrap(max($collection));
    
    $results = array();
    foreach($collection as $k=>$item) {
      $results[$k] = $iterator($item);
    }
    arsort($results);
    $__ = new self;
    $first_key = $__->first(array_keys($results));
    return $collection[$first_key];
  }
  
  
  // Get the min value in the collection
  public function min($collection=null, $iterator=null) {
    list($collection, $iterator) = self::_wrapArgs(func_get_args(), 2);
    
    if(is_null($iterator)) return self::_wrap(min($collection));
    
    $results = array();
    foreach($collection as $k=>$item) {
      $results[$k] = $iterator($item);
    }
    asort($results);
    $__ = new self;
    $first_key = $__->first(array_keys($results));
    return self::_wrap($collection[$first_key]);
  }
  
  
  // Sort the collection by return values from the iterator
  public function sortBy($collection=null, $iterator=null) {
    list($collection, $iterator) = self::_wrapArgs(func_get_args(), 2);
    
    $results = array();
    foreach($collection as $k=>$item) {
      $results[$k] = $iterator($item);
    }
    asort($results);
    foreach($results as $k=>$v) {
      $results[$k] = $collection[$k];
    }
    return self::_wrap(array_values($results));
  }
  
  
  // Group the collection by return values from the iterator
  public function groupBy($collection=null, $iterator=null) {
    list($collection, $iterator) = self::_wrapArgs(func_get_args(), 2);
    
    $result = array();
    $collection = (array) $collection;
    foreach($collection as $k=>$v) {
      $key = (is_callable($iterator)) ? $iterator($v, $k) : $v[$iterator];
      if(!array_key_exists($key, $result)) $result[$key] = array();
      $result[$key][] = $v;
    }
    return $result;
  }
  
  
  // Returns the index at which the value should be inserted into the sorted collection
  public function sortedIndex($collection=null, $value=null, $iterator=null) {
    list($collection, $value, $iterator) = self::_wrapArgs(func_get_args(), 3);
    
    $collection = (array) self::_collection($collection);
    $__ = new self;
    
    $calculated_value = (!is_null($iterator)) ? $iterator($value) : $value;
    
    while(count($collection) > 1) {
      $midpoint = floor(count($collection) / 2);
      $midpoint_values = array_slice($collection, $midpoint, 1);
      $midpoint_value = $midpoint_values[0];
      $midpoint_calculated_value = (!is_null($iterator)) ? $iterator($midpoint_value) : $midpoint_value;
      
      $collection = ($calculated_value < $midpoint_calculated_value) ? array_slice($collection, 0, $midpoint, true) : array_slice($collection, $midpoint, null, true);
    }
    $keys = array_keys($collection);
    
    return self::_wrap(current($keys) + 1);
  }
  
  // Shuffle the array
  public function shuffle($collection=null) {
    list($collection) = self::_wrapArgs(func_get_args(), 1);
    
    $collection = (array) self::_collection($collection);
    shuffle($collection);
     
    return self::_wrap($collection);
  }
  
  
  // Return the collection as an array
  public function toArray($collection=null) {
    return (array) $collection;
  }
  
  
  // Get the collection's keys
  public function keys($collection=null) {
    list($collection) = self::_wrapArgs(func_get_args(), 1);
    
    if(!is_object($collection) && !is_array($collection)) throw new Exception('Invalid object');
    
    return self::_wrap(array_keys((array) $collection));
  }
  
  
  // Get the collection's values
  public function values($collection=null) {
    list($collection) = self::_wrapArgs(func_get_args(), 1);
    
    return self::_wrap(array_values((array) $collection));
  }
  
  
  // Copy all properties from the source objects into the destination object 
  public function extend($object=null) {
    $args = self::_wrapArgs(func_get_args(), 1);
    
    $num_args = func_num_args();
    if($num_args === 1) return $object;
    
    $is_object = is_object($object);
    $array = (array) $object;
    $__ = new self;
    $extensions = $__->rest(func_get_args());
    foreach($extensions as $extension) {
      $extension = (array) $extension;
      $array = array_merge($array, $extension);
    }
    return self::_wrap(($is_object) ? (object) $array : $array);
  }
  
  
  // Returns the object with any missing values filled in using the defaults.
  public function defaults($object=null) {
    $args = self::_wrapArgs(func_get_args(), 1);
    list($object) = $args;
    
    $num_args = count($args);
    if($num_args === 1) return $object;
    
    $is_object = is_object($object);
    $array = (array) $object;
    $__ = new self;
    $extensions = $__->rest($args);
    foreach($extensions as $extension) {
      $extension = (array) $extension;
      $array = array_merge($extension, $array);
    }
    return self::_wrap(($is_object) ? (object) $array : $array);
  }
  
  
  // Get the names of functions available to the object
  // functions alias: methods
  public function methods($object=null) { return self::functions($object); }
  public function functions($object=null) {
    list($object) = self::_wrapArgs(func_get_args(), 1);
    
    return self::_wrap(get_class_methods(get_class($object)));
  }
  
  
  // Returns a shallow copy of the object
  public function clon(&$object=null) {
    list($object) = self::_wrapArgs(func_get_args(), 1);
    
    $clone = null;
    if(is_array($object)) $clone = (array) clone (object) $object;
    elseif(!is_object($object)) $clone = $object;
    elseif(!$clone) $clone = clone $object;
    
    // shallow copy object
    if(is_object($clone) && count($clone) > 0) {
      foreach($clone as $k=>$v) {
        if(is_array($v) || is_object($v)) $clone->$k =& $object->$k;
      }
    }
    
    // shallow copy array
    elseif(is_array($clone) && count($clone) > 0) {
      foreach($clone as $k=>$v) {
        if(is_array($v) || is_object($v)) $clone[$k] =& $object[$k];
      }
    }
    return self::_wrap($clone);
  }
  
  
  // Invokes the interceptor on the object, then returns the object
  public function tap($object=null, $interceptor=null) {
    list($object, $interceptor) = self::_wrapArgs(func_get_args(), 2);
    
    $interceptor($object);
    return self::_wrap($object);
  }
  
  
  // Does the given key exist?
  public function has($collection=null, $key=null) {
    list($collection, $key) = self::_wrapArgs(func_get_args(), 2);
    
    $collection = (array) self::_collection($collection);
    
    return self::_wrap(array_key_exists($key, $collection));
  }
  
  
  // Are these items equal?
  public function isEqual($a=null, $b=null) {
    list($a, $b) = self::_wrapArgs(func_get_args(), 2);
    
    if(isset($this) && isset($this->_chained) && $this->_chained) $a =& $this;
    
    if($a === $b) return self::_wrap(true);
    if(gettype($a) !== gettype($b)) return self::_wrap(false);
    if(is_callable($a) !== is_callable($b)) return self::_wrap(false);
    
    if($a == $b) return self::_wrap(true);
    
    // Objects and arrays compared by values
    if(is_object($a) || is_array($a)) {
      
      // Do either implement isEqual()?
      if(is_object($a) && isset($a->isEqual)) return self::_wrap($a->isEqual($b));
      if(is_object($b) && isset($b->isEqual)) return self::_wrap($b->isEqual($a));
      if(is_array($a) && array_key_exists('isEqual', $a)) return self::_wrap($a['isEqual']($b));
      if(is_array($b) && array_key_exists('isEqual', $b)) return self::_wrap($b['isEqual']($a));
      
      if(count($a) !== count($b)) return self::_wrap(false);
      
      $__ = new self;
      $keys_equal = $__->isEqual($__->keys($a), $__->keys($b));
      $values_equal = $__->isEqual($__->values($a), $__->values($b));
      return self::_wrap($keys_equal && $values_equal);
    }
    
    return self::_wrap(false);
  }
  
  
  // Is this item empty?
  public function isEmpty($item=null) {
    list($item) = self::_wrapArgs(func_get_args(), 1);
    
    return self::_wrap(is_array($item) || is_object($item)) ? !((bool) count((array) $item)) : (!(bool) $item);
  }
  
  
  // Is this item an object?
  public function isObject($item=null) {
    list($item) = self::_wrapArgs(func_get_args(), 1);
    return self::_wrap(is_object($item));
  }
  
  
  // Is this item an array?
  public function isArray($item=null) {
    list($item) = self::_wrapArgs(func_get_args(), 1);
    return self::_wrap(is_array($item));
  }
  
  
  // Is this item a string?
  public function isString($item=null) {
    list($item) = self::_wrapArgs(func_get_args(), 1);
    return self::_wrap(is_string($item));
  }
  
  
  // Is this item a number?
  public function isNumber($item=null) {
    list($item) = self::_wrapArgs(func_get_args(), 1);
    return self::_wrap((is_int($item) || is_float($item)) && !is_nan($item) && !is_infinite($item));
  }
  
  
  // Is this item a bool?
  public function isBoolean($item=null) {
    list($item) = self::_wrapArgs(func_get_args(), 1);
    return self::_wrap(is_bool($item));
  }
  
  
  // Is this item a function (by type, not by name)?
  public function isFunction($item=null) {
    list($item) = self::_wrapArgs(func_get_args(), 1);
    return self::_wrap(is_object($item) && is_callable($item));
  }
  
  
  // Is this item an instance of DateTime?
  public function isDate($item=null) {
    list($item) = self::_wrapArgs(func_get_args(), 1);
    return self::_wrap(is_object($item) && get_class($item) === 'DateTime');
  }
  
  
  // Is this item a NaN value?
  public function isNaN($item=null) {
    list($item) = self::_wrapArgs(func_get_args(), 1);
    return self::_wrap(is_nan($item));
  }
  
  
  // Returns the same value passed as the argument
  public function identity() {
    $args = self::_wrapArgs(func_get_args(), 1);
    
    if(is_array($args)) return self::_wrap($args[0]);
    
    return self::_wrap(function($x) {
      return $x;
    });
  }
  
  
  // Generate a globally unique id, optionally prefixed
  public $_uniqueId = -1;
  public function uniqueId($prefix=null) {
    list($prefix) = self::_wrapArgs(func_get_args(), 1);
    
    $_instance = self::getInstance();
    $_instance->_uniqueId++;
    
    return (is_null($prefix)) ? self::_wrap($_instance->_uniqueId) : self::_wrap($prefix . $_instance->_uniqueId);
  }
  
  
  // Invokes the iterator n times
  public function times($n=null, $iterator=null) {
    list($n, $iterator) = self::_wrapArgs(func_get_args(), 2);
    if(is_null($n)) $n = 0;
    
    for($i=0; $i<$n; $i++) $iterator($i);
    return self::_wrap(null);
  }
  
  
  // Extend the class with your own functions
  private $_mixins = array();
  public function mixin($functions=null) {
    list($functions) = self::_wrapArgs(func_get_args(), 1);
    
    $mixins =& self::getInstance()->_mixins;
    foreach($functions as $name=>$function) {
      $mixins[$name] = $function;
    }
    return self::_wrap(null);
  }
  
  
  // Allows extending methods in static context
  public static function __callStatic($name, $arguments) {
    $mixins =& self::getInstance()->_mixins;
    return call_user_func_array($mixins[$name], $arguments);
  }
  
  // Allows extending methods in non-static context
  public function __call($name, $arguments) {
    $mixins =& self::getInstance()->_mixins;
    $arguments = self::_wrapArgs($arguments);
    return call_user_func_array($mixins[$name], $arguments);
  }
  
  
  // Temporary PHP open and close tags used within templates
  // Allows for normal processing of templates even when
  // the developer uses PHP open or close tags for interpolation or evaluation
  const TEMPLATE_OPEN_TAG = '760e7dab2836853c63805033e514668301fa9c47';
  const TEMPLATE_CLOSE_TAG= 'd228a8fa36bd7db108b01eddfb03a30899987a2b';
  
  const TEMPLATE_DEFAULT_EVALUATE   = '/<%([\s\S]+?)%>/';
  const TEMPLATE_DEFAULT_INTERPOLATE= '/<%=([\s\S]+?)%>/';
  const TEMPLATE_DEFAULT_ESCAPE     = '/<%-([\s\S]+?)%>/';
  public $_template_settings = array(
    'evaluate'    => self::TEMPLATE_DEFAULT_EVALUATE,
    'interpolate' => self::TEMPLATE_DEFAULT_INTERPOLATE,
    'escape'      => self::TEMPLATE_DEFAULT_ESCAPE
  );
  
  // Set template settings
  public function templateSettings($settings=null) {
    $_template_settings =& self::getInstance()->_template_settings;
    
    if(is_null($settings)) {
      $_template_settings = array(
        'evaluate'    => self::TEMPLATE_DEFAULT_EVALUATE,
        'interpolate' => self::TEMPLATE_DEFAULT_INTERPOLATE,
        'escape'      => self::TEMPLATE_DEFAULT_ESCAPE
      );
      return true;
    }
    
    foreach($settings as $k=>$v) {
      if(!array_key_exists($k, $_template_settings)) continue;
      
      $_template_settings[$k] = $v;
    }
    return true;
  }
  
  
  // Compile templates into functions that can be evaluated for rendering
  public function template($code=null, $context=null) {
    list($code, $context) = self::_wrapArgs(func_get_args(), 2);

    $class_name = __CLASS__;
    
    $return = self::_wrap(function($context=null) use ($code, $class_name) {
      $ts = $class_name::getInstance()->_template_settings;
      
      // Wrap escaped, interpolated, and evaluated blocks inside PHP tags
      extract((array) $context);
      preg_match_all($ts['escape'], $code, $vars, PREG_SET_ORDER);
      if(count($vars) > 0) {
        foreach($vars as $var) {
          $echo = $class_name::TEMPLATE_OPEN_TAG . ' echo htmlentities(' . trim($var[1]) . '); ' . $class_name::TEMPLATE_CLOSE_TAG;
          $code = str_replace($var[0], $echo, $code);
        }
      }
      preg_match_all($ts['interpolate'], $code, $vars, PREG_SET_ORDER);
      if(count($vars) > 0) {
        foreach($vars as $var) {
          $echo = $class_name::TEMPLATE_OPEN_TAG . ' echo ' . trim($var[1]) . '; ' . $class_name::TEMPLATE_CLOSE_TAG;
          $code = str_replace($var[0], $echo, $code);
        }
      }
      preg_match_all($ts['evaluate'], $code, $vars, PREG_SET_ORDER);
      if(count($vars) > 0) {
        foreach($vars as $var) {
          $echo = $class_name::TEMPLATE_OPEN_TAG . trim($var[1]) . $class_name::TEMPLATE_CLOSE_TAG;
          $code = str_replace($var[0], $echo, $code);
        }
      }
      $code = str_replace($class_name::TEMPLATE_OPEN_TAG, '<?php ', $code);
      $code = str_replace($class_name::TEMPLATE_CLOSE_TAG, '?>', $code);
      
      // Use the output buffer to grab the return value
      $code = 'ob_start(); extract($context); ?>' . $code . '<?php return ob_get_clean();';
      
      $func = create_function('$context', $code);
      return $func((array) $context);
    });
    
    // Return function or call function depending on context
    return self::_wrap(((isset($this) && isset($this->_wrapped) && $this->_wrapped) || !is_null($context)) ? $return($context) : $return);
  }
  
  // Escape
  public function escape($item=null) {
    list($item) = self::_wrapArgs(func_get_args(), 1);
    
    return self::_wrap(htmlentities($item));
  }
  
  
  // Memoizes a function by caching the computed result.
  public $_memoized = array();
  public function memoize($function=null, $hashFunction=null) {
    list($function, $hashFunction) = self::_wrapArgs(func_get_args(), 2);
    
    $_instance = (isset($this) && isset($this->_wrapped)) ? $this : self::getInstance();
    
    return self::_wrap(function() use ($function, &$_instance, $hashFunction) {
      
      // Generate a key based on hashFunction
      $args = func_get_args();
      if(is_null($hashFunction)) $hashFunction = function($function, $args) {
        
        // Try using var_export to identify the function
        return md5(join('_', array(
          var_export($function, true),
          var_export($args, true)
        )));
      };
      $key = $hashFunction($function, $args);
      
      if(!array_key_exists($key, $_instance->_memoized)) {
        $_instance->_memoized[$key] = call_user_func_array($function, $args);
      }
      return $_instance->_memoized[$key];
    });
  }
  
  
  // Throttles a function so that it can only be called once every wait milliseconds
  public $_throttled = array();
  public function throttle($function=null, $wait=null) {
    list($function, $wait) = self::_wrapArgs(func_get_args(), 2);
    
    $_instance = (isset($this) && isset($this->_wrapped)) ? $this : self::getInstance();
    
    return self::_wrap(function() use ($function, $wait, &$_instance) {
      
      // Try using var_export to identify the function
      $key = md5(join('', array(
        var_export($function, true),
        $wait
      )));
      
      $microtime = microtime(true);
      $ready_to_call = (!array_key_exists($key, $_instance->_throttled) || $microtime >= $_instance->_throttled[$key]);
      if($ready_to_call) {
        $next_callable_time = $microtime + ($wait / 1000);
        $_instance->_throttled[$key] = $next_callable_time;
        return call_user_func_array($function, func_get_args());
      }
    });
  }
  
  
  // Creates a version of the function that can only be called once
  public $_onced = array();
  public function once($function=null) {
    list($function) = self::_wrapArgs(func_get_args(), 1);
    
    $_instance = (isset($this) && isset($this->_wrapped)) ? $this : self::getInstance();
    
    return self::_wrap(function() use ($function, &$_instance) {
      
      // Try using var_export to identify the function
      $key = md5(var_export($function, true));
      if(!array_key_exists($key, $_instance->_onced)) {
        $_instance->_onced[$key] = call_user_func_array($function, func_get_args());
      }
      
      return $_instance->_onced[$key];
    });
  }
  
  
  // Wraps the function inside the wrapper function, passing it as the first argument
  public function wrap($function=null, $wrapper=null) {
    list($function, $wrapper) = self::_wrapArgs(func_get_args(), 2);
    
    return self::_wrap(function() use ($wrapper, $function) {
      $args = array_merge(array($function), func_get_args());
      return call_user_func_array($wrapper, $args);
    });
  }
  
  
  // Returns the composition of the functions
  public function compose() {
    $functions = self::_wrapArgs(func_get_args(), 1);
    
    return self::_wrap(function() use ($functions) {
      $args = func_get_args();
      foreach($functions as $function) {
        $args[0] = call_user_func_array($function, $args);
      }
      return $args[0];
    });
  }
  
  
  // Creates a version of the function that will only run after being called count times
  public $_aftered = array();
  public function after($count=null, $function=null) {
    list($count, $function) = self::_wrapArgs(func_get_args(), 2);
    
    $_instance = (isset($this) && isset($this->_wrapped)) ? $this : self::getInstance();
    $key = md5(mt_rand());
    
    $func = function() use ($function, &$_instance, $count, $key) {
      if(!array_key_exists($key, $_instance->_aftered)) $_instance->_aftered[$key] = 0;
      $_instance->_aftered[$key] += 1;
      
      if($_instance->_aftered[$key] >= $count) return call_user_func_array($function, func_get_args());
    };
    return self::_wrap(($count) ? $func : $func());
  }
  
  
  // Singleton
  private static $_instance;
  public function getInstance() {
    if(!isset(self::$_instance)) {
      $c = __CLASS__;
      self::$_instance = new $c;
    }
    return self::$_instance;
  }
  
  
  // All methods should wrap their returns within _wrap
  // because this function understands both OO-style and functional calls
  public $_wrapped; // Value passed from one chained method to the next
  private function _wrap($val) {
    if(isset($this) && isset($this->_chained) && $this->_chained) {
      $this->_wrapped = $val;
      return $this;
    }
    return $val;
  }
  
  
  // All methods should get their arguments from _wrapArgs
  // because this function understands both OO-style and functional calls
  private function _wrapArgs($caller_args, $num_args=null) {
    $num_args = (is_null($num_args)) ? count($caller_args) - 1 : $num_args;
    
    $filled_args = array();
    if(isset($this) && isset($this->_wrapped)) {
      $filled_args[] =& $this->_wrapped;
    }
    if(count($caller_args) > 0) {
      foreach($caller_args as $k=>$v) {
        $filled_args[] = $v;
      }
    }
    
    return array_pad($filled_args, $num_args, null);
  }
  
  
  // Get a collection in a way that supports strings
  private function _collection($collection) {
    return (!is_array($collection) && !is_object($collection)) ? str_split((string) $collection) : $collection;
  }
}
