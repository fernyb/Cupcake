<?php

class Logger {
  static $instance = false;
  public $filename = "cupcake.log";
  public $colors = array(
    'red'       => "\033[31m",
    'green'     => "\033[32m",
    'blue'      => "\033[34m",
    'yellow'    => "\033[33m",
    'default'   => "\033[0m"
  );

  public static function &getInstance() {
    if(self::$instance === false) {
      self::$instance = new self();
    }
    return self::$instance;
  }
  
  public static function debug() {
    
  }
  
  public static function info($message, $color="default") {
    $time = date("r", time());
    $new_message = sprintf("[%s: %s] %s\n", "info", $time, $message);
    $l = self::getInstance();
    $l->write($new_message);
  }
  
  public static function process_controller($controller, $action, $http_method, $params=array()) {
    $time = date("Y-m-d G:i:s", time());
    $new_message = "Processing {$controller}#{$action} (for ". env("REMOTE_ADDR") ." at ". $time .") [{$http_method}]\n";
    $l = self::getInstance();
    $l->write($new_message);
  }
  
  public static function render($message) {
    $l = self::getInstance();
    $l->write($message);
  }
  
  public static function warn() {
    
  }
  
  public static function error() {
    
  }
  
  public static function fatal() {
    
  }
  
  public function new_line() {
    $l = self::getInstance();    
    $message = "\n";
    $file = LOG_DIR ."/". $l->filename;
    if($fp = fopen($file, "a+")) {
      fwrite($fp, $message);
      fclose($fp);
    }    
  }
  
  public function write($message) {
    if(!empty($message) && strlen(trim($message)) > 0) {
      $file = LOG_DIR ."/". $this->filename;
      if($fp = fopen($file, "a+")) {
        fwrite($fp, $message);
        fclose($fp);
      }
    }
  }
  
  public function color_message($message, $color) {
    return ( $this->color[$color] . $message);
  }
}

?>