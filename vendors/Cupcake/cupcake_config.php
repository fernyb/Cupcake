<?php

class CupcakeConfig {
  public static $instance = false;
  public $values = array();
  
  public static function &getInstance() {
    if(self::$instance === false) {
      self::$instance = new self();
    }
    return self::$instance;
  }
  
  public static function set($k, $v) {
    $c = self::getInstance();
    $c->values[$k] = $v;
  }
  
  public static function get($k) {
    $c = self::getInstance();
    return (array_key_exists($k, $c->values) ? $c->values[$k] : null);
  }
}

?>