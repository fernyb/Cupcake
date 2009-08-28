<?php

class Header {
  public $headers = array();
  public $raw_headers = array();
  static $instance = false;
  
  static function &getInstance() {
    if(self::$instance === false) {
      self::$instance = new self();
    }
    return self::$instance;
  }
  
  static function set_raw($string) {
    $that = self::getInstance();
    array_push($that->raw_headers, $string);
  }
  
  static function set($name, $value) {
    $that = self::getInstance();
    $that->headers[$name] = $value;
  }
  
  static function send() {
    $that = self::getInstance();
    foreach($that->raw_headers as $header) {
      header("{$header}");
    }
    foreach($that->headers as $key => $value) {
      header("{$key}: {$value}");
    }
  }
}

?>