<?php

class Session {
  public $session_store;
  public static $instance = false;
  
  public static function &getInstance(&$store=null) {
    if(!self::$instance && !empty($store)) {
      self::$instance = new self($store);
    }
    return self::$instance;
  }
  
  public static function initialize() {
    $cookie = new CookieStore(array(
        "session_key" => CupcakeConfig::get("session_key"),
        "secret"      => CupcakeConfig::get("secret")
      ));
    self::getInstance($cookie);
  }
  
  public function __construct(&$session_store) {
    $this->session_store = $session_store;
  }
  
  public function set($key, $value=null) {
    $this->session_store->set($key, $value);
  }
  
  public function get($key) {
    return $this->session_store->get($key);
  }
  
  public function clear() {
    $this->session_store->clear();
  }
  
  public function save() {
    return $this->session_store->save();
  }
  
  public function load() {
    $session_key = CupcakeConfig::get("session_key");
    if(!empty($session_key)) {
    
      if(array_key_exists($session_key, $_COOKIE)) {
        $cookie = $_COOKIE[$session_key];
      } else {
        $cookie = "";
      }
        $this->session_store->load_session($cookie);
      
    }
  }
}

?>