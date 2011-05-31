<?php

function _($item) {
  $_ = new _;
  $_->_wrapped = $item;
  return $_;
}

class _ {
  
  public static function each($collection, $iterator) {
    if(is_null($collection)) return;
  private $_chained = false;
  public $_wrapped = null;
  
  private function _wrap($val) {
    if(isset($this) && $this->_chained) {
      $this->_wrapped = $val;
      return $this;
    }
    return $val;
  }
  
  private function _wrapArgs($caller_args) {
    $filled_args = array();
    if(isset($this) && $this->_chained) $filled_args[] =& $this->_wrapped;
    if(count($caller_args) > 0) {
      foreach($caller_args as $k=>$v) {
        $filled_args[] = $v;
      }
    }
    
    return array_pad($filled_args, count($caller_args) - 1, null);
  }
  
  public function chain() {
    $this->_chained = true;
    return $this;
  }
    if(!self::isArray($collection) && !is_object($collection)) $collection = str_split((string) $collection);
    
    $collection = (array) $collection;
    if(_::size($collection) === 0) return;
    
    foreach($collection as $k=>$v) {
      call_user_func($iterator, $v, $k, $collection);
    }
  }
  
  public static function map($collection, $iterator) {
    if(is_null($collection)) return array();
    if(!self::isArray($collection) && !is_object($collection)) $collection = str_split((string) $collection);
    
    $collection = (array) $collection;
    if(_::size($collection) === 0) return array();
    
    $return = array();
    foreach($collection as $k=>$v) {
      $return[] = call_user_func($iterator, $v, $k, $collection);
    }
    return $return;
  }
  
  public static function reduce($collection, $iterator, $memo=null) {
    if(!is_object($collection) && !is_array($collection)) {
      if(is_null($memo)) throw new Exception('Invalid object');
      else return $memo;
    }
    
    return array_reduce($collection, $iterator, $memo);
  }
  
  public static function reduceRight($collection, $iterator, $memo=null) {
    if(!is_object($collection) && !is_array($collection)) {
      if(is_null($memo)) throw new Exception('Invalid object');
      else return $memo;
    }
    
    krsort($collection);
    return self::reduce($collection, $iterator, $memo);
  }
  
  public static function pluck($collection, $key) {
    if(!self::isArray($collection) && !is_object($collection)) $collection = str_split((string) $collection);
    
    $return = array();
    foreach($collection as $item) {
      foreach($item as $k=>$v) {
        if($k === $key) $return[] = $v;
      }
    }
    return $return;
  }
  
  public static function includ($collection, $val) {
    if(!self::isArray($collection) && !is_object($collection)) $collection = str_split((string) $collection);
    
    $collection = (array) $collection;
    return is_int(array_search($val, $collection, true));
  }
  
  public static function any($collection, $iterator=null) {
    if(!self::isArray($collection) && !is_object($collection)) $collection = str_split((string) $collection);
    
    if(!is_null($iterator)) $collection = self::map($collection, $iterator);
    if(count($collection) === 0) return false;
    
    return is_int(array_search(true, $collection, false));
  }
  
  public static function all($collection, $iterator=null) {
    if(!self::isArray($collection) && !is_object($collection)) $collection = str_split((string) $collection);
    
    if(!is_null($iterator)) $collection = self::map($collection, $iterator);
    $collection = (array) $collection;
    if(count($collection) === 0) return true;
    
    return is_bool(array_search(false, $collection, false));
  }
  
  public static function select($collection, $iterator) {
    if(!self::isArray($collection) && !is_object($collection)) $collection = str_split((string) $collection);
    
    $return = array();
    foreach($collection as $val) {
      if(call_user_func($iterator, $val)) $return[] = $val;
    }
    return $return;
  }
  
  public static function reject($collection, $iterator) {
    if(!self::isArray($collection) && !is_object($collection)) $collection = str_split((string) $collection);
    
    $return = array();
    foreach($collection as $val) {
      if(!call_user_func($iterator, $val)) $return[] = $val;
    }
    return $return;
  }
  
  public static function detect($collection, $iterator) {
    if(!self::isArray($collection) && !is_object($collection)) $collection = str_split((string) $collection);
    
    foreach($collection as $val) {
      if(call_user_func($iterator, $val)) return $val;
    }
    return false;
  }
  
  public static function size($collection) {
    if(!self::isArray($collection) && !is_object($collection)) $collection = str_split((string) $collection);
    
    return count((array) $collection);
  }
  
  public static function first($collection, $n=null) {
    if(!self::isArray($collection) && !is_object($collection)) $collection = str_split((string) $collection);
    
    if($n === 0) return array();
    if(is_null($n)) return current(array_splice($collection, 0, 1, true));
    return array_splice($collection, 0, $n, true);
  }
  
  public static function rest($collection, $index=1) {
    if(!self::isArray($collection) && !is_object($collection)) $collection = str_split((string) $collection);
    
    return array_splice($collection, $index);
  }
  
  public static function last($collection) {
    if(!self::isArray($collection) && !is_object($collection)) $collection = str_split((string) $collection);
    
    return array_pop($collection);
  }
  
  public static function compact($collection) {
    if(!self::isArray($collection) && !is_object($collection)) $collection = str_split((string) $collection);
    
    return self::select($collection, function($val) {
      return (bool) $val;
    });
  }
  
  public static function flatten($collection) {
    if(!self::isArray($collection) && !is_object($collection)) $collection = str_split((string) $collection);
    
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
    if(!self::isArray($collection) && !is_object($collection)) $collection = str_split((string) $collection);
    
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
    if(!self::isArray($collection) && !is_object($collection)) $collection = str_split((string) $collection);
    
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
      if(!self::isArray($next)) $next = str_split((string) $next);
      
      $return = array_intersect($return, $next);
    }
    return $return;
  }
  
  public static function indexOf($collection, $item) {
    if(!self::isArray($collection) && !is_object($collection)) $collection = str_split((string) $collection);
    
    $key = array_search($item, $collection, true);
    return (is_bool($key)) ? -1 : $key;
  }
  
  public static function lastIndexOf($collection, $item) {
    if(!self::isArray($collection) && !is_object($collection)) $collection = str_split((string) $collection);
    
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
    if(is_null($iterator)) return min($collection);
    
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
    if(!is_object($collection) && !is_array($collection)) throw new Exception('Invalid object');
    
    return array_keys((array) $collection);
  }
  
  public static function values($collection) {
    return array_values((array) $collection);
  }
  
  public static function extend($object) {
    $num_args = func_num_args();
    if($num_args === 1) return $object;
    
    $is_object = is_object($object);
    $array = (array) $object;
    $extensions = self::rest(func_get_args());
    foreach($extensions as $extension) {
      $extension = (array) $extension;
      $array = array_merge($array, $extension);
    }
    return ($is_object) ? (object) $array : $array;
  }
  
  public static function defaults($object) {
    $num_args = func_num_args();
    if($num_args === 1) return $object;
    
    $is_object = is_object($object);
    $array = (array) $object;
    $extensions = self::rest(func_get_args());
    foreach($extensions as $extension) {
      $extension = (array) $extension;
      $array = array_merge($extension, $array);
    }
    return ($is_object) ? (object) $array : $array;
  }
  
  public static function functions($object) {
    return get_class_methods(get_class($object));
  }
  
  public static function clon(&$object) {
    $clone = null;
    if(is_array($object)) $clone = (array) clone (object) $object;
    if(!$clone) $clone = clone $object;
    
    // shallow copy object
    if(is_object($clone) && count($clone) > 0) {
      foreach($clone as $k=>$v) {
        if(is_array($v) || is_object($v)) $clone->$k =& $object->$k;
      }
    }
    
    // shallow copy array
    if(is_array($clone) && count($clone) > 0) {
      foreach($clone as $k=>$v) {
        if(is_array($v) || is_object($v)) $clone[$k] =& $object[$k];
      }
    }
    return $clone;
  }
  
  public static function isEqual($a, $b) {
    if($a === $b) return true;
    if(gettype($a) !== gettype($b)) return false;
    
    if($a == $b) return true;
    
    if(is_object($a) || is_array($a)) {
      $keys_equal = self::isEqual(self::keys($a), self::keys($b));
      $values_equal = self::isEqual(self::values($a), self::values($b));
      return ($keys_equal && $values_equal);
    }
    
    return false;
  }
  
  public static function isEmpty($item) {
    return (is_array($item) || is_object($item)) ? !((bool) count((array) $item)) : (!(bool) $item);
  }
  
  public static function isArray($item) {
    return is_array($item);
  }
  
  public static function isString($string) {
    return is_string($string);
  }
  
  public static function isNumber($number) {
    return ((is_int($number) || is_float($number)) && !is_nan($number) && !is_infinite($number));
  }
  
  public static function isBoolean($bool) {
    return is_bool($bool);
  }
  
  public static function isFunction($function) {
    return (is_object($function) && is_callable($function));
  }
  
  public static function isDate($date) {
    return (is_object($date) && get_class($date) === 'DateTime');
  }
  
  public static function isNaN($nan) {
    return is_nan($nan);
  }
  
  public static function isUndefined($val=null) {
    if(is_null($val))   return false;
    if(is_bool($val))   return false;
    if(is_int($val))    return false;
    if(is_object($val)) return false;
    if(is_array($val))  return false;
    if(is_string($val)) return false;
    if(is_nan($val))    return false;
    return !isset($val);
  }
  
  public static function identity() {
    $args = func_get_args();
    if(is_array($args)) return $args[0];
    
    return function($x) {
      return $x;
    };
  }
  
  public static function uniqueId($prefix='') {
    $prefix = (strlen($prefix) > 0) ? $prefix . '_' : $prefix;
    return uniqid($prefix);
  }
  
  public static function times($n, $iterator) {
    for($i=0; $i<$n; $i++) $iterator($i);
  }
  
  private static $_instance;
  private $_mixins = array();
  
  public static function getInstance() {
    if(!isset(self::$_instance)) {
      $c = __CLASS__;
      self::$_instance = new $c;
    }
    return self::$_instance;
  }
    
  public static function mixin($functions) {
    $mixins =& self::getInstance()->_mixins;
    foreach($functions as $name=>$function) {
      $mixins[$name] = $function;
    }
  }
  
  public static function __callStatic($name, $arguments) {
    $mixins =& self::getInstance()->_mixins;
    return call_user_func_array($mixins[$name], $arguments);
  }
  
  public static function template($subject, $context=null) {
    $return = function($context) use (&$subject) {
      $result = $subject;
      foreach($context as $term=>$replacement) {
        $term_patterns = array(
          '<%= ' . $term . ' %>'
        );
        foreach($term_patterns as $term_pattern) {
          $result = str_replace($term_pattern, addslashes($replacement), $result);
        }
      }
      return $result;
    };
    
    if($context) echo $return();
    return $return;
  }
  
  public $_memoized = array();
  // @todo add $hashFunction support
  public static function memoize($function, $hashFunction=null) {
    $_instance = self::getInstance();
    
    return function() use ($function, &$_instance){
      $args = func_get_args();
      $key = md5(join('_', array(
        var_export($function, true),
        var_export($args, true)
      )));
      if(!array_key_exists($key, $_instance->_memoized)) {
        $_instance->_memoized[$key] = call_user_func_array($function, $args);
      }
      return $_instance->_memoized[$key];
    };
  }
  
  public $_throttled = array();
  public static function throttle($function, $wait) {
    $_instance = self::getInstance();
    
    return function() use ($function, $wait, &$_instance) {
      $key = md5(var_export($function, true));
      $microtime = microtime(true);
      $ready_to_call = (!array_key_exists($key, $_instance->_throttled) || $microtime >= $_instance->_throttled[$key]);
      if($ready_to_call) {
        $next_callable_time = $microtime + ($wait / 1000);
        $_instance->_throttled[$key] = $next_callable_time;
        return call_user_func_array($function, func_get_args());
      }
    };
  }
  
  public $_onced = array();
  public static function once($function) {
    $_instance = self::getInstance();
    
    return function() use ($function, &$_instance){
      $key = md5(var_export($function, true));
      if(!array_key_exists($key, $_instance->_onced)) {
        $_instance->_onced[$key] = call_user_func_array($function, func_get_args());
      }
      return $_instance->_onced[$key];
    };
  }
  
  public static function wrap($function, $wrapper) {
    return function() use ($wrapper, $function) {
      $args = array_merge(array($function), func_get_args());
      return call_user_func_array($wrapper, $args);
    };
  }
  
  public static function compose() {
    $functions = func_get_args();
    
    return function() use ($functions) {
      $args = func_get_args();
      foreach($functions as $function) {
        $args[0] = call_user_func_array($function, $args);
      }
      return $args[0];
    };
  }
  
  public $_aftered = array();
  public static function after($count, $function) {
    $_instance = self::getInstance();
    $key = md5(mt_rand());
    
    return function() use ($function, &$_instance, $count, $key) {
      if(!array_key_exists($key, $_instance->_aftered)) $_instance->_aftered[$key] = 0;
      $_instance->_aftered[$key] += 1;
      
      if($_instance->_aftered[$key] >= $count) return call_user_func_array($function, func_get_args());
    };
  }
}