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

class Dispatcher {
  static $instance = false;
  private $router;
  private $request;
  
  public function &getInstance() {
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
  
  static function handle(&$request) {
    $d = self::getInstance();
    if(empty($d->request)) {
      $d->request = $request;
    }
    $request_uri = $d->env("REQUEST_URI");
    $uri = $d->request_base_uri($request_uri);
    $router_params = $d->find_route($uri);
    var_dump($router_params);
  }
  
  public function find_route($uri) {
    if(empty($this->router)) {
      $this->setRouter(Router::getInstance());
    }
    return $this->router->find_route($uri);
  }
  
  public function request_base_uri($uri) {
    if($pos = strpos($uri, "?")) {
      return substr($uri, 0, $pos);
    }
    return $uri;
  }
  
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
  
  public function env($k) {
    return $_SERVER[$k];
  }
}

?>