<?php

class CustomArray implements ArrayAccess {

  private $_data;

  public function offsetExists($offset) {
    return array_key_exists($offset, $this->_data);
  }

  public function offsetGet($offset) {
    return $this->_data[$offset];
  }

  public function offsetSet($offset, $value) {
    $this->_data[$offset] = $value;
  }

  public function offsetUnset($offset) {
    unset($this->_data[$offset]);
  }

}
