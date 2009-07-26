<?php

class NewRouter {
  
  static $instance = false;
  public $routes = array();
  public $current_path = false;
  
  static function &getInstance() {
    if(!self::$instance) {
      self::$instance = new self();
    }
    return self::$instance;
  }
  
  static function &prepare($block) {
    $_this = self::getInstance();
    $block($_this);
    return $_this;
  }
  
  public function &match($path) {
    $this->routes[][$path] = array("path" => $path);
    $this->current_path = $path;
    return $this;
  }
  
  public function to($params=array()) {
    if($this->current_path === false) return false;
    
    if(list($index, $new_params) = $this->current_path_params($params)) {
      $old_params = $this->routes[$index][$this->current_path];
      $this->routes[$index][$this->current_path] = array_merge($old_params, $new_params);
      return $this->routes[$index][$this->current_path];
    }
    return false;
  }
  
  public function current_path_params($params=array()) {
    if($path = $this->route_for($this->current_path)) {
      $route_path = array_keys($path[1]);
      $new_params = array_merge($this->routes[$path[0]][$route_path[0]], array("params" => $params));
      return array($path[0], $new_params);
    }
    return false;
  }
  
  public function route_for($path) {
    foreach($this->routes as $i => $r) {
      if(array_key_exists($path, $this->routes[$i])) {
        return array($i, $this->routes[$i]);
      }
    }
    return false;
  }
  
  public function reset() {
    $this->routes = array();
    $this->current_path = false;
  }
}

?>