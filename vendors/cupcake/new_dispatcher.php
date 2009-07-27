<?php

class NewDispatcher {
  
  private $router = false;
  
  public function __construct(&$router=null) {
    if($router) {
      $this->router = $router;
    } else {
      throw new Exception("Dispatcher: Missing Router");
    }
  }
  
  public function dispatch($request) {
    $request_uri = $request['REQUEST_URI'];
    $this->handle($request_uri);
  }
  
  private function handle($uri) {
    $path   = $this->base_path($uri);
    $query  = $this->query_string($uri);
    $params = $this->params();
    
    if($route  = $this->router->find_route($path)) {
      $params = array_merge($params, $route[1]);
      var_dump($params);
    } else {
      throw new Exception("Dispatcher: Route Not Found");
    }
  }// end function
  
  private function query_string($uri) {
    if(strpos($uri, '?') !== false) {
      return substr($uri, strpos($uri, "?") + 1, strlen($uri));
    }
    return null;
  }
 
  private function params() {
    $_REQUEST['url'] = $_GET['url'] = $_POST['url'] = null;
    $params = array();
    foreach(array_merge($_GET, $_POST) as $k => $v) {
      if($v !== null) {
        $params[$k] = $v;
      }
    }
    return $params;
  }
   
  private function base_path($uri) {
    if($this->query_string($uri) !== null) {
      return substr($uri, 0, strpos($uri, "?"));
    }
    return $uri;
  }
}

?>