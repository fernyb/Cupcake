<?php

class Session {
  public $session_store;
  
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
}

?>