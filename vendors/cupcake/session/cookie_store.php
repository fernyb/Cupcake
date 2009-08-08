<?php

class CookieStore {
  const MAX = 4096;
  public $cookie_options = array();
  public $params = array();
  
  public function set($key, $value=null) {
    $this->params[$key] = $value;
  }
  
  public function get($key) {
    return $this->params[$key];
  }
  
  public function clear() {
    $this->params = array();
  }
  
  public function restore() {
    
  }
}

?>