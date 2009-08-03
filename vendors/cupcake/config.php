<?php

class Config {
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
    return $c->values[$k];
  }
  
  public static function load_environment() {
    include_once CONFIG_DIR ."/environment.php";
    include_once CONFIG_DIR ."/environments/". CUPCAKE_ENV .".php";
    Logger::info("*** Cupcake using environment: ". CUPCAKE_ENV);
  }
}

?>