<?php

class Session {
  public static $instance = false;
  public $session_store;

  public static function &getInstance() {
    if(!self::$instance) {
      self::$instance = new self();
    }
    return self::$instance;
  }
  
  public static function set($key, $value=null) {
    $s = self::getInstance();
    $s->session_store->set($key, $value);
  }
  
  public static function get($key) {
    $s = self::getInstance();
    return $s->session_store->get($key);
  }
  
  public static function clear() {
    $s = self::getInstance();
    $s->session_store->clear();
  }
}

?>