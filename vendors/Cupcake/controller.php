<?php

class Controller {
  public $controller;
  public $request_uri;
  public $action = null;
  public $params = array();
  public $view_params = array();
  public $view;
  public $helper = null;
  public $before_filter = array();
  public $after_filter = array();
  protected $render_called = false;
  
  public function __construct($uri, $params=array()) {
    $this->request_uri = $uri;
    $this->params      = $params;
    $this->controller  = $this;
    if(!empty($params["action"])) {
      $this->action      = $params["action"];
    }
  }
  
  public function handle_request($params) {
    if(array_key_exists("format", $params) === false) {
      $params["format"] = "html";
    }
    
    $this->params = $params;
    $controller_name  = Inflector::camelize($params["controller"], "first");
    Logger::process_controller($controller_name, $params["action"], env("REQUEST_METHOD"), $params);
    
    if(Import::controller($params["controller"])) {
      Import::helper($params["controller"]);
      $helper_name = "{$controller_name}Helper";
      $this->controller = new $controller_name($this->request_uri, $params);
      if(class_exists($helper_name)) {
        $this->controller->helper = new $helper_name();
      }
      $action = $params["action"];  
    } else {
      $this->controller = new self($this->request_uri, $params);
      $action = "not_found";
      $this->controller->{$action}();
    }
    
    $this->controller->format = $this->params["format"];
    
    $this->controller->handle_action($action);        
  }

  public function controller_exists($controller_name) {
    return file_exists(CONTROLLER_DIR . "/" . $controller_name .".php");
  }

  public function run_filter_methods($filters, $action, $methods) {
    foreach($filters as $key => $value) {
      $filter_method = $value[0];
      if(array_key_exists("skip", $value) && $action === $value["skip"]) {
        continue;
      }
        
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
  
  public function load_session() {
    Session::getInstance()->load();
  }
  
  public function save_session() {
    Session::getInstance()->save();
  }
  
  #
  # handle_action will call the before filter, action and after filter
  # after doing so it will attempt to render the view
  #  
  public function handle_action($action) {
    $start_timer = microseconds();
    $this->load_session();    
    $this->handle_flash_messages();
    $methods = get_class_methods($this);
    # call before filter methods before calling action method
    $this->run_filter_methods($this->before_filter, $action, $methods);
    
    # Search for action Methods
    if(array_key_exists($action, array_flip($methods))) {
      $this->{$action}();
    }
    
    # call after filter methods after calling action method
    $this->run_filter_methods($this->after_filter, $action, $methods);
    
    $this->render();
    Logger::info("Controller Action: ". (microseconds() - $start_timer) ." ms");
  }

  public function render($options=array()) {
    $this->save_session();
    if($this->render_called === false) {
      $this->view = new View($this->request_uri, $this->params, $this->view_params);
      $this->view->controller = $this->controller;
  
      // Figure out the template to use:
      if(!empty($options["action"]) && strpos("/", $options["action"])) {
        $this->view->template = $options["action"];
      } else if(!empty($options["action"])) {
        $this->view->template = $this->params["controller"] ."/". $options["action"];
      }
      
      if(!empty($options["template"])) {
       $parts = explode("/", $options["template"]);
       if(count($parts) === 0) {
         $this->view->template = $this->params["controller"] ."/". $options["template"];
       }
      }
      if(!empty($options["ext"])) {
        $this->view->ext = $options["ext"];
      }
      if(!empty($options["format"])) {
        $this->format = $options["format"];
      }
      
      // Figure out the layout to use:
      if(!empty($options["layout"])) {
        $this->view->layout = "layouts/". $options["layout"];
      }
   
      $this->view->content_type = MimeType::lookup_by_extension($this->format);
      $this->view->format = MimeType::extension_by_mime_type($this->view->content_type);
      
      $this->render_called = true;
      $this->view->render();
    }
  }
  
  public function render_action($action, $options=array()) {
    $this->save_session();
    $this->render(array_merge(array("action" => $action), $options));
  }
  
  public function render_template($template_name, $options=array()) {
    list($template, $format, $ext) = explode(".", $template_name, 3);
    $this->render(array_merge(array(
      "template" => $template,
      "format"   => $format,
      "ext"      => $ext
      ), $options));
  }
  
  public function render_text($text="") {
    $this->save_session();
    echo $text;
    exit;
  }
  
  public function render_html($file) {
    $this->save_session();
    Import::html($file, $this->view_params);
    exit;
  }
  
  public function redirect_to($url, $status=302) {
    $this->save_session();
    $codes = $this->status_code;
    if (!empty($status)) {
			if(is_string($status)) {
				$codes = array_combine(array_values($codes), array_keys($codes));
			}
			if(isset($codes[$status])) {
				$code = $msg = $codes[$status];
				if(is_numeric($status)) { $code = $status; }
				if(is_string($status))  { $msg = $status;  }
				Header::set_raw("HTTP/1.1 {$code} {$msg}");
				$status = "HTTP/1.1 {$code} {$msg}";
			} else {
				$status = null;
			}
		}
	
		if($url !== null) {
		  Header::set("Location", "{$url}");
		}
		if(CUPCAKE_ENV !== "test") {
		  Header::send();
		}		
    exit;
  }
  
  public $status_code = array(
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
  
  
  public function status_code($code) {
    if(array_key_exists($code, $this->status_code)) {
      if(CUPCAKE_ENV !== "test") {
        header("HTTP/1.1 ". $code ." ". $this->status_code[$code]);
      }
    }
  }
  
  public function not_found() { 
    $this->status_code(404);
    $this->render_html("404");
  }
  
  public function set($key, $value) {
    if($key !== "controller" && $key !== "action") {
      $this->view_params[$key] = $value;
    }
  }
  
  public function xhr() {
    return (env("X-Requested-With") === "XMLHttpRequest");
  }
}

?>