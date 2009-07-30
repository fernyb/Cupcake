<?php
/**
* router.php
*  
* A Merb like router in PHP
* Usage:
*
*  Router::prepare(function($r){
*    $r->match("/user/profile")->to(array("controller" => "user", "action" => "show"));
*    $r->match("/user/photo/:id(/:name)")->to(array("controller" => "user", "action" => "show_photo"));
*  });
*
*  $r = Router::getInstance();
*  $r->find_route("/user/profile");           # => Returns array("controller" => "user", "action" => "show");
*  $r->find_route("/user/photo/5");           # => Returns array("controller" => "user", "action" => "show", "id" => "5");
*  $r->find_route("/user/photo/5/funny-day"); # => Returns array("controller" => "user", "action" => "show", "id" => "5", "name" => "funny-day");
*
*
* @author Fernando Barajas <fernyb@fernyb.net>
* @version 1.0
*/

class Router {
  
  private $conditions = array();
  private $params = array();
  private $segments;      
  
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
      $this->params[] = $params;
      $old_params = $this->routes[$index][$this->current_path];
      $this->routes[$index][$this->current_path] = array_merge($old_params, $new_params);
      $this->conditions[] = array("path" => $this->current_path);
      return $this->routes[$index][$this->current_path];
    }
    return false;
  }
  
  public function current_path_params($params=array()) {
    if($path = $this->route_for($this->current_path)) {
      $route_path = array_keys($path[1]);
      $new_params = array_merge(array("params" => $params), $this->routes[$path[0]][$route_path[0]]);
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
  
  public function param_keys_for_path($matcher_path) {
    preg_match_all("/([^\/.,;?]+)/", $matcher_path, $matches);
    $params = array();
    if(count($matches) > 1) {
      foreach($matches[1] as $k => $v) {
        $v = $this->remove_parenthesis($v);
        if($this->is_param_key($v)) {
          $params[] = $v;
        }            
      }
    }
    return $params;
  }
  
  public function remove_parenthesis($value) {
    return preg_replace("(\(|\))", "", $value);
  }
  
  public function is_param_key($value) {
    return preg_match("/^:/", $value);
  }
  
  public function route_path_to_regexp($path) {
    preg_match_all("/([^\/.,;?]+)/", $path, $matches);
    $rgs = array();
    $first_param = false;
    foreach($matches[1] as $k => $v) {
      $v = $this->remove_parenthesis($v);
      if($this->is_param_key($v)) {
        $rgs[] = ($first_param === false ? "([^\/.,;?]+)?" : "(?:\/?([^\/.,;?]+)?)");
        $first_param = true;
      } else {
        $seperator = count($rgs) === 0 ? "\/?" : "\/";
        $rgs[] = $v . $seperator;
      }
    }
    if(count($rgs) === 1) {
      $rgs[0] = substr($rgs[0], 0, strlen($rgs[0]) - 2);
    }
    $route_path = join("", $rgs);
    $regexp = "^\/" . $route_path ."$";
    
    return $regexp;
  }
  
  public function arrays_to_regexps($condition) {
    if(!is_array($condition)) return $condition;
    $delimiter = "/";
    $source = array();
    foreach($condition as $i => $value) {
      $source[] = $this->route_path_to_regexp($value);
    }
    $source = array_unique($source);

    return join("|", $source);
  }
  
  public function map_route_to_params($request_path) {
    if(list($index, $params) = $this->match_path($request_path)) {
      $default_params = $this->current_path_params($params);
    
      $path    = $default_params[1]["path"];
      $params  = array_merge($params, $default_params[1]["params"]);
      $pattern = $this->arrays_to_regexps(array("path" => $path));
      $match   = preg_match("/". $pattern ."/", $request_path, $values);
      $keys    = $this->param_keys_for_path($path);

      array_shift($values);
      
      if(count($keys) > count($values)) {
        return $params;
      }
      
      $request_params = array();
      foreach(array_combine($keys, $values) as $k => $v) {
        $k = preg_replace("/(^:?)/", "", $k);
        $request_params[$k] = $v;
      }
      $merged_params = array_merge($request_params, $params);
      
      return $merged_params;
    }
    return false;
  }
  
  /**
  * Returns an Array of parameters that where matched.
  * Returns false when no match is made.
  * @param string $request_path The Base Request URI string: Ex. /controller/action
  * @return mixed
  */
  public function find_route($request_path=null) {
    if($request_path === null) return false;
    return $this->map_route_to_params($request_path);
  }
  
  private function match_path($path) {
    foreach($this->conditions as $k => $v) {
      $regexp = $this->route_path_to_regexp($v["path"]);
      if(preg_match("/{$regexp}/", $path)) {
        return array($k, $this->params[$k]);
      }
    }
    return false;
  }
  
  public function reset() {
    $this->routes = array();
    $this->current_path = false;
    $this->conditions = array();
    $this->params = array();
  }
}

?>