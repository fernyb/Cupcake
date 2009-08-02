<?php

class Config {
  private static $instance;
  private $values = array();
  
  public static function &getInstance() {
    if(!self::$instance) {
      self::$instance = new self();
    }
    return self::$instance;
  }
  
  public static function set($k, $v) {
    self::$values[$k] = $v;
  }
  
  public static function get($k) {
    return self::$values[$k];
  }
}

?>