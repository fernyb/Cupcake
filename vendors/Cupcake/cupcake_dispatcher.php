<?php
/**
* dispatcher.php
* 
* Handle the REQUEST_URI to determine which controller and method to call.
*
* @author Fernando Barajas <fernyb@fernyb.net>
* @version 1.0
* @package cupcake-core
*/

class CupcakeDispatcher {
  static $instance = false;
  private $router;
  private $controller;
  
  static public function &getInstance() {
    if(self::$instance === false) {
      self::$instance = new self();
    }
    return self::$instance;
  }
  
  /**
  * Sets the Router Property
  */
  public function setRouter(&$router) {
    $this->router = $router;
  }
  
  public function router() {
    return $this->router;
  }
  
  public static function run() {
    self::load_environment();
    self::load_lib_files();    
    CupcakeLogger::info("Dispatcher run!");
    CupcakeSession::initialize();
    self::handle();
  }
  
  # Handle the current request
  static function handle() {
    $d = self::getInstance();
    $request_uri = $d->env("REQUEST_URI");
    $uri         = $d->request_base_uri($request_uri);
    $params      = $d->params_for_request($uri);
    $d->dispatch_to($uri, $params);
  }

  # Dispatches the request parameters to the appropriate controller
  public function dispatch_to($uri, $params=array()) {
    CupcakeLogger::info("Dispatcher, dispatch_to: $uri");
    if(empty($this->controller)) {
      $this->controller = new CupcakeController($uri);
    }
    $this->controller->handle_request($params);
  }
  
  # Load the environment
  public static function load_environment() {
    include_once CONFIG_DIR ."/environment.php";
    include_once CONFIG_DIR ."/environments/". CUPCAKE_ENV .".php";    
    CupcakeLogger::info("*** Cupcake using environment: ". CUPCAKE_ENV);
    include_once CONFIG_DIR ."/routes.php";
    include_once CONFIG_DIR ."/mime_types.php";
  }
  
  # Load lib files
  public static function load_lib_files() {
    if (is_dir(ROOT_PATH ."/lib")) {  
      $dir_iterator = new RecursiveDirectoryIterator(ROOT_PATH ."/lib");
      $iterator = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::SELF_FIRST);
      foreach ($iterator as $file) {
        if (is_file($file)) {
          include_once $file;
        }
      }
    }
  }
    
  # Returns an array of parameters. 
  # Returns false when no route is found
  # Merges the request parameters with the route parameters.
  # route parameters cannot be overided.
  public function params_for_request($uri) {
    if($router_params  = $this->find_route($uri)) {
      $params = $this->params();
      $params = array_merge($params, $router_params);
      return $params;  
    }
    return false;
  }
  
  # Returns an array of route parameters when route is found.
  # Otherwise returns false when no route is found.
  public function find_route($uri) {
    if(empty($this->router)) {
      $this->setRouter(CupcakeRouter::getInstance());
    }
    return $this->router->find_route($uri);
  }
  
  
  # Returns the base uri as a string.
  public function request_base_uri($uri) {
    if($pos = strpos($uri, "?")) {
      return substr($uri, 0, $pos);
    }
    return $uri;
  }
  
  # Returns an array of parameters from GET and POST
  public function params() {
    $new_params = array_merge($_GET, $_POST);
    $params = array();
    foreach($new_params as $k => $v) {
      if($k != "url") {
        $params[$k] = $v;
      }
    }
    return $params;
  }
  
  # Returns an environment variable.
  public function env($k) {
    return env($k);
  }
}

?>