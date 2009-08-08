<?php

class Controller {
  public $controller;
  public $request_uri;
  public $action;
  public $params = array();
  public $view_params = array();
  public $view;
  public $before_filter = array();
  public $after_filter = array();
  protected $render_called = false;
  
  public function __construct($uri, $params=array()) {
    $this->request_uri = $uri;
    $this->params      = $params;
    $this->controller  = $this;
    $this->action      = $params["action"];
  }
  
  public function handle_request($params) {
    $this->params = $params;
    $controller_name  = Inflector::camelize($params["controller"], "first");
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

  public function run_filter_methods($filters, $action, $methods) {
    foreach($filters as $key => $value) {
      $filter_method = $value[0];
      if(array_search($filter_method, $methods)) {
        if(isset($value["only"]) && $value["only"] === $action) {
          $this->{$filter_method}(); 
        } else {
          $this->{$filter_method}();
        }
      }
    }    
  }
  
  public function handle_flash_messages() {
    foreach($_COOKIE as $k => $v) {
      if(preg_match("/^flash_/", $k)) {
        setcookie($k, "", time() - (3600 * 8));
      }
    }
  }
    
  public function handle_action($action) {
    $this->handle_flash_messages();
    $methods = get_class_methods($this);
    // call before filter methods before calling action method
    $this->run_filter_methods($this->before_filter, $action, $methods);
    
    // Search for action Methods
    if(array_search($action, $methods)) {
      $this->{$action}();
    }
    
    // call after filter methods after calling action method
    $this->run_filter_methods($this->after_filter, $action, $methods);
    $this->render();
  }

  public function render($options=array()) {
    if($this->render_called === false) {
      $this->view = new View($this->request_uri, $this->params, $this->view_params);
      $this->view->controller = $this->controller;
      
      // Figure out the template to use:
      if(!empty($options["action"]) && strpos("/", $options["action"])) {
        $this->view->template = $options["action"];
      } else if(!empty($options["action"])) {
        $this->view->template = $this->params["controller"] ."/". $options["action"];
      }
      
      // Figure out the layout to use:
      if(!empty($options["layout"])) {
        $this->view->layout = "layouts/". $options["layout"];
      }
      
      $this->render_called = true;
      $this->view->render();
    }
  }
  
  public function redirect_to($url, $status=302) {
    if (!empty($status)) {
			$codes = array(
				100 => 'Continue',
				101 => 'Switching Protocols',
				200 => 'OK',
				201 => 'Created',
				202 => 'Accepted',
				203 => 'Non-Authoritative Information',
				204 => 'No Content',
				205 => 'Reset Content',
				206 => 'Partial Content',
				300 => 'Multiple Choices',
				301 => 'Moved Permanently',
				302 => 'Found',
				303 => 'See Other',
				304 => 'Not Modified',
				305 => 'Use Proxy',
				307 => 'Temporary Redirect',
				400 => 'Bad Request',
				401 => 'Unauthorized',
				402 => 'Payment Required',
				403 => 'Forbidden',
				404 => 'Not Found',
				405 => 'Method Not Allowed',
				406 => 'Not Acceptable',
				407 => 'Proxy Authentication Required',
				408 => 'Request Time-out',
				409 => 'Conflict',
				410 => 'Gone',
				411 => 'Length Required',
				412 => 'Precondition Failed',
				413 => 'Request Entity Too Large',
				414 => 'Request-URI Too Large',
				415 => 'Unsupported Media Type',
				416 => 'Requested range not satisfiable',
				417 => 'Expectation Failed',
				500 => 'Internal Server Error',
				501 => 'Not Implemented',
				502 => 'Bad Gateway',
				503 => 'Service Unavailable',
				504 => 'Gateway Time-out'
			);
			if(is_string($status)) {
				$codes = array_combine(array_values($codes), array_keys($codes));
			}
			if(isset($codes[$status])) {
				$code = $msg = $codes[$status];
				if(is_numeric($status)) { $code = $status; }
				if(is_string($status))  { $msg = $status;  }
				$status = "HTTP/1.1 {$code} {$msg}";
			} else {
				$status = null;
			}
		}
		
    if(!empty($status)) {
      header($status);
		}
		if($url !== null) {
		  header("Location: {$url}");
		}
		if(!empty($status) && ($status >= 300 && $status < 400)) {
			header($status);
		}
    exit;
  }
  
  public function not_found() { }
  
  public function set($key, $value) {
    if($key !== "controller" && $key !== "action") {
      $this->view_params[$key] = $value;
    }
  }
}

?>