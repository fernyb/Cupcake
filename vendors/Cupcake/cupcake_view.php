<?php

class CupcakeView {
  public $request_uri;
  public $controller;
  public $params = array();
  public $view_params = array();
  public $layout = "layouts/application";
  public $template;
  public $helper = false;
  public $content_type = "text/html";
  public $format = "html";
  public $ext = "php";
  
  public function __construct($request_uri, $params=array(), $view_params=array()) {
    $this->request_uri = $request_uri;
    $this->params      = $params;
    $this->view_params = $view_params;
    $this->template    = $params["controller"] ."/". $params["action"];
    
    if(CupcakeImport::helper($params["controller"])) {
      $this->helper = $params['controller'] ."Helper";
    } else {
      $this->helper = false;
    }
  }
  
  public function render() {
    $layout = $this->content_for_template();
    $body   = $this->content_for_layout($layout);
    
    // Needed for testing
    if(CUPCAKE_ENV === "test") {
      $dispatcher = CupcakeDispatcherTest::getInstance();
      $dispatcher->__params      = $this->view_params();
      $dispatcher->__view_params = $this->view_params;
      $dispatcher->__template    = $this->template;
      $dispatcher->__layout      = $this->layout;
      
      # Action might be false positive and controller aswell.
      $dispatcher->__controller  = $this->controller;
      $dispatcher->__action      = $this->params["action"];
      $dispatcher->__request_uri = $this->request_uri;
      $dispatcher->__body        = $body;
      return;
    } 
    if(!empty($this->content_type)) {
      Header::set("Content-Type", $this->content_type);
      Header::send();
    }
    echo $body;
    exit;
  }
  
  public function file_extension() {
    return ($this->format .".". $this->ext);
  }
  
  public function content_for_template() {
    $start = microseconds();
    $params = $this->view_params();
    ob_start();
    if(CupcakeImport::view($this->template, $this->file_extension(), $params) === false) {
      CupcakeImport::view("exceptions/not_found", $this->file_extension(), $params);
      CupcakeLogger::render("Rendering template within exceptions/not_found (". (microseconds() - $start) ." ms)\n");
    } else {
      CupcakeLogger::render("Rendering template within {$this->template} (". (microseconds() - $start) ." ms)\n");
    }
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
  }
  
  public function content_for_layout($content="") {
    $start = microseconds();  
    $params = $this->view_params();
    $params = array_merge($params, array("content_for_layout" => $content));    
    ob_start();
    if(!CupcakeImport::view($this->layout, $this->file_extension(), $params)) {
      echo $content;
    }
    $output = ob_get_contents();
    ob_end_clean();
    CupcakeLogger::render("Rendering {$this->layout} (". (microseconds() - $start) ." ms)\n");
    CupcakeLogger::new_line();
          
    return $output;
  }
  
  public function render_partial($partial_name, $options=array()) {
    $start   = microseconds();
    $params  = $this->view_params();
    
    if(array_key_exists("locals", $options)) {
      if(is_array($options["locals"])) {
        $params = array_merge($params, $options["locals"]);
      }
    }
    
    # When partial_name has a slash assume they 
    # know what template they are looking for.
    # Otherwise look for the partial name in the default location.
    if(strpos($partial_name, "/") > 0) {
      $segments = explode("/", $partial_name);
      $segments[count($segments) - 1] = "_" . end($segments); 
      $partial = join("/", $segments);
    } else {
      $partial = $params["params"]["controller"] ."/". "_{$partial_name}";
    }
    ob_start();
    CupcakeImport::view($partial, $this->file_extension(), $params);
    $output = ob_get_contents();
    ob_end_clean();
    CupcakeLogger::render("Rendering {$partial} (". (microseconds() - $start) ." ms)\n");      
    return $output;
  }
  
  public function view_params() {
    $params = array("params"      => $this->params, 
                    "layout"      => $this->layout,
                    "template"    => $this->template,
                    "request_uri" => $this->request_uri,
                    "view"        => $this
                    );
  
    if($this->helper !== false) {
      $params = array_merge($params, array("helper" => new $this->helper($this->params)));
    }
    
    $params = array_merge($this->view_params, $params);
      
    return $params;
  }
}

?>