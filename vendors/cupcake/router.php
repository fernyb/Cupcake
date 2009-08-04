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
* @package cupcake-core
*/

class Router {
  
  public $conditions = array();
  private $params = array();
  private $segments;      
  
  static $instance = false;
  public $routes = array();
  
  /**
  * The current path of the match. Only to be used within match and to methods.
  * @var mixed 
  * @access  public
  */
  public $current_path = false;
  
  static function &getInstance() {
    if(!self::$instance) {
      self::$instance = new self();
    }
    return self::$instance;
  }
  
  static function prepare($block) {
    $_this = self::getInstance();
    Logger::new_line();
    Logger::info("*** Compiling Routes");
    $block($_this);
    return $_this;
  }
  
  /**
  * Adds a new match to the routes array. You should then call the to method.
  *
  * @param $path string A Route Path to match. Example: /user/profile/:id
  * @return Object string Returns an instance of Router so it can be chained with to.
  */
  public function match($path) {
    $this->routes[] = array("path" => $path);
    return $this;
  }
  
  /**
  * Adds parameters to the route path from the match method.
  * These parameters become the defaults. 
  * An Exception is thrown when there are no routes available.
  * Must call match before calling to.
  *
  * @param $params Array An array of key value parameters
  * @return Array array Returns an array with the route containing the parameters
  */
  public function to($params=array()) {
    if(count($this->routes) === 0) {
      throw new RouterException("No Routes Available. Must call match before calling to.");
    }
    $current_route_index = (count($this->routes) - 1);
    $route_path = $this->routes[$current_route_index]["path"];
    $this->routes[$current_route_index] = array("path" => $route_path, "params" => $params);
    return $this;
  }
  
  /**
  * Sets the name of the route. It should be called called calling the to method.
  */
  public function name($name) {
    if(count($this->routes) === 0) {
      return false;  
    }
    if(!empty($name)) {
     $route_index = (count($this->routes) - 1);
     $route = $this->routes[$route_index];
     $route["name"] = $name;
     $this->routes[$route_index] = $route;
    }
    return $this->routes[$current_route_index];
  }
  
  /**
  * Returns an Array of paramters. 
  * $params is merged into the route parameters
  */
  public function current_path_params($request_path, $params=array()) {
    $path = $this->route_for($request_path);
    if($path !== false) {
      $route_path = $path[1]['path'];
      $new_params = array_merge($params, $path[1]['params']);
      return array($path[0], $new_params);
    }
    return false;
  }
  
  
  /**
  * Returns an array with the index and parameters for the route
  * @param string $path A URI string Ex: /book/500
  * @return mixed Returns Array when route is found otherwise false
  */
  public function route_for($path) {
    foreach($this->routes as $i => $route) {
      $regexp = $this->route_path_to_regexp($route['path']);
      if(preg_match("/{$regexp}/", $path)) {
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
        if($first_param) {
          $seperator = (count($rgs) === 0 ? "\/?" : "\/");
        } else {
          $seperator = "\/?";
        }
        
        $rgs[] = $v . $seperator;
      }
    }
    if(count($rgs) === 1) {
      $rgs[0] = substr($rgs[0], 0, strlen($rgs[0]) - 2);
    }
    $route_path = join("", $rgs);
    
    if(preg_match("/\/$/", $route_path)) {
      $route_path = substr($route_path, 0, -2);
    }

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
    if(list($index, $orig_params) = $this->match_path($request_path)) {
      $default_params = $this->current_path_params($request_path, $orig_params);
      
      $path = $this->routes[$index]['path'];
      $params  = array_merge($orig_params, $default_params[1]);
      $pattern = $this->arrays_to_regexps(array("path" => $path));
      $match   = preg_match("/". $pattern ."/", $request_path, $values);
      $keys    = $this->param_keys_for_path($path);
      
      array_shift($values);
      $keys_count   = count($keys);
      $values_count = count($values);
      
      if($keys_count === 0 && $values_count === 0) {
        return $params;
      }
      
      if($keys_count > $values_count) {
        foreach($keys as $i => $k) {
          if(empty($values[$i])) {
            $values[$i] = "";
          }
        }
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
    foreach($this->routes as $k => $v) {
      $regexp = $this->route_path_to_regexp($v["path"]);
      if(preg_match("/{$regexp}/", $path)) {
        return array($k, $v["params"]);
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

class RouterException extends Exception { }

?>