<?php
#
# Used for Testing
#
class Request {
  public $controller_class = null;
  public $params;
  public $dispatcher;

  public function post($request_uri, $query=array()) {
    $_POST = $query;
    $this->make_request($request_uri);
  }

  public function get($request_uri, $query=array()) {
    $_GET = $query;
    $this->make_request($request_uri);
  }
  
  public function make_request($request_uri) {
    $_SERVER['REQUEST_URI'] = $request_uri;
    $this->dispatcher = CupcakeDispatcherTest::getInstance();
    CupcakeDispatcherTest::run();    
  }
  
  public function params($key=null) {
    if($key !== null) {
      return $this->dispatcher->__params["params"][$key];
    } else {
      return $this->dispatcher->__params["params"];
    }
  }
  
  public function view_params($key=null) {
    if($key !== null) {
      return $this->dispatcher->__view_params[$key];
    } else {
      return $this->dispatcher->__view_params;
    }
  }
  
  public function template() {
    return $this->dispatcher->__template;
  }
  
  public function layout() {
    return $this->dispatcher->__layout;
  }
  
  public function controller() {
    return $this->dispatcher->__controller;
  }
  
  public function action() {
    return $this->dispatcher->__action;
  }
  
  public function request_uri() {
    return $this->dispatcher->__request_uri;
  }
  
  public function body() {
    return $this->dispatcher->__body;
  }
}


?>