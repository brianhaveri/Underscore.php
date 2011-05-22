<?php

class _ {
  
  public static function map($collection, $iterator) {
    $collection = (array) $collection;
    $return = array();
    foreach($collection as $k=>$v) {
      $return[] = call_user_func($iterator, $v, $k, $collection);
    }
    return $return;
  }
  
  public static function pluck($collection, $key) {
    $return = array();
    foreach($collection as $item) {
      foreach($item as $k=>$v) {
        if($k === $key) $return[] = $v;
      }
    }
    return $return;
  }
  
  public static function includes($collection, $val) {
    $collection = (array) $collection;
    return is_int(array_search($val, $collection, true));
  }
  
  public static function any($collection, $iterator=null) {
    if(!is_null($iterator)) $collection = self::map($collection, $iterator);
    if(count($collection) === 0) return false;
    
    return is_int(array_search(true, $collection, false));
  }
  
  public static function all($collection, $iterator=null) {
    if(!is_null($iterator)) $collection = self::map($collection, $iterator);
    if(count($collection) === 0) return true;
    
    return is_bool(array_search(false, $collection, false));
  }
  
  public static function select($collection, $iterator) {
    $return = array();
    foreach($collection as $val) {
      if(call_user_func($iterator, $val)) $return[] = $val;
    }
    return $return;
  }
  
  public static function reject($collection, $iterator) {
    $return = array();
    foreach($collection as $val) {
      if(!call_user_func($iterator, $val)) $return[] = $val;
    }
    return $return;
  }
  
  public static function detect($collection, $iterator) {
    foreach($collection as $val) {
      if(call_user_func($iterator, $val)) return $val;
    }
    return false;
  }
  
  public static function size($collection) {
    return count($collection);
  }
  
  public static function first($collection, $n=null) {
    if($n === 0) return array();
    if(is_null($n)) return current(array_splice($collection, 0, 1, true));
    return array_splice($collection, 0, $n, true);
  }
  
  public static function rest($collection, $index=1) {
    return array_splice($collection, $index);
  }
  
  public static function last($collection) {
    return array_pop($collection);
  }
  
  public static function compact($collection) {
    return self::select($collection, function($val) {
      return (bool) $val;
    });
  }
  
  public static function flatten($collection) {
    $return = array();
    if(count($collection) > 0) {
      foreach($collection as $item) {
        if(is_array($item)) $return = array_merge($return, self::flatten($item));
        else $return[] = $item;
      }
    }
    return $return;
  }
  
  public static function without($collection, $val) {
    $args = func_get_args();
    $num_args = func_num_args();
    if($num_args === 1) return $collection;
    if(count($collection) === 0) return $collection;
    
    $removes = self::rest($args);
    foreach($removes as $remove) {
      $remove_keys = array_keys($collection, $remove, true);
      if(count($remove_keys) > 0) {
        foreach($remove_keys as $key) {
          unset($collection[$key]);
        }
      }
    }
    return $collection;
  }
  
  public static function uniq($collection) {
    $return = array();
    if(count($collection) === 0) return $return;
    
    foreach($collection as $item) {
      if(is_bool(array_search($item, $return, true))) $return[] = $item;
    }
    return $return;
  }
  
  public static function intersect($array) {
    $arrays = func_get_args();
    if(count($arrays) === 1) return $array;
    
    $return = self::first($arrays);
    foreach(self::rest($arrays) as $next) {
      $return = array_intersect($return, $next);
    }
    return $return;
  }
  
  public static function indexOf($collection, $item) {
    if(!is_array($collection)) return -1;
    
    $key = array_search($item, $collection, true);
    return (is_bool($key)) ? -1 : $key;
  }
  
  public static function lastIndexOf($collection, $item) {
    if(!is_array($collection)) return -1;
    
    krsort($collection);
    return self::indexOf($collection, $item);
  }
  
  public static function range($stop) {
    $num_args = func_num_args();
    $args = func_get_args();
    switch($num_args) {
      case 1: 
        list($start, $stop, $step) = array(0, $args[0], 1);
        break;
      case 2:
        list($start, $stop, $step) = array($args[0], $args[1], 1);
        if($stop < $start) return array();
        break;
      default:
        list($start, $stop, $step) = array($args[0], $args[1], $args[2]);
        if($step > 0 && $step > $stop) return array($start);
    }
    $results = range($start, $stop, $step);
    
    // switch inclusive to exclusive
    if($step > 0 && self::last($results) >= $stop) array_pop($results);
    elseif($step < 0 && self::last($results) <= $stop) array_pop($results);
    
    return $results;
  }
  
  public static function zip($array) {
    $args = func_get_args();
    $num_args = func_num_args();
    if($num_args === 1) return $array;
    
    $return = self::range($num_args);
    foreach($return as $k=>$v) {
      if(!is_array($return[$k])) $return[$k] = array();
      
      foreach($args as $a=>$arg) {
        $return[$k][$a] = $args[$a][$k];
      }
    }
    return $return;
  }
  
  public static function max($collection, $iterator=null) {
    if(is_null($iterator)) return max($collection);
    
    $results = array();
    foreach($collection as $k=>$item) {
      $results[$k] = $iterator($item);
    }
    arsort($results);
    $first_key = self::first(array_keys($results));
    return $collection[$first_key];
  }
  
  public static function min($collection, $iterator=null) {
    if(is_null($iterator)) return max($collection);
    
    $results = array();
    foreach($collection as $k=>$item) {
      $results[$k] = $iterator($item);
    }
    asort($results);
    $first_key = self::first(array_keys($results));
    return $collection[$first_key];
  }
  
  public static function sortBy($collection, $iterator) {
    $results = array();
    foreach($collection as $k=>$item) {
      $results[$k] = $iterator($item);
    }
    asort($results);
    foreach($results as $k=>$v) {
      $results[$k] = $collection[$k];
    }
    return array_values($results);
  }
  
  public static function keys($collection) {
    return array_keys((array) $collection);
  }
  
  public static function values($collection) {
    return array_values((array) $collection);
  }
  
  public static function functions($object) {
    return get_class_methods(get_class($object));
  }
  
  public static function isEqual($a, $b) {
    return (is_object($a)) ? $a == $b : $a === $b;
  }
}