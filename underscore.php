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
  
  public static function first($collection, $n=1) {
    $vals = array_slice($collection, 0, $n, true);
    return ($n === 1) ? current($vals) : $vals;
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
    if(count($args) === 1) return $collection;
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
}