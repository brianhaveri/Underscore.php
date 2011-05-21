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
}