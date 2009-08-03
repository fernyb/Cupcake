<?php

class Controller {
  public $controller;
  public $request_uri;
  public $action;
  public $params = array();
  public $view;
  protected $render_called = false;
  
  public function __construct($uri, $params=array()) {
    $this->request_uri = $uri;
    $this->params      = $params;
    $this->controller  = $this;
    $this->action      = $params["action"];
  }
  
  public function handle_request($params) {
    $this->params = $params;
    $controller_name  = Inflector::titleize($params["controller"], "first");
    Logger::process_controller($controller_name, $params["action"], env("REQUEST_METHOD"), $params);
    
    if(Import::controller($params["controller"])) {
      $this->controller = new $controller_name($this->request_uri, $params);
      $action = $params["action"];
    } else {
      $this->controller = new ControllerException($this->request_uri, $params);
      $action = "not_found";
    }
    $this->controller->handle_action($action);
  }

  public function controller_exists($controller_name) {
    return file_exists(CONTROLLER_DIR . "/" . $controller_name .".php");
  }
  
  public function handle_action($action) {
    $this->{$action}();
    $this->render();
  }
  
  public function render($options=array()) {
    if($this->render_called === false) {
      $this->view = new View($this->request_uri, $this->params);
      $this->view->controller = $this->controller;
      if(!empty($options["action"])) {
        $this->view->template = $options["action"];
      }
      $this->render_called = true;
      $this->view->render();
    }
  }
  
  public function not_found() { }
  
  public function set($key, $value) {
    $this->params[$key] = $value;
  }
}

?>