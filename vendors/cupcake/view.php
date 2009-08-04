<?php

class View {
  public $request_uri;
  public $controller;
  public $params = array();
  public $view_params = array();
  public $layout;
  public $template;
  public $ext = "html.php";
  
  public function __construct($request_uri, $params=array(), $view_params=array()) {
    $this->request_uri = $request_uri;
    $this->params      = $params;
    $this->view_params = $view_params;
    $this->layout      = "layouts/application";
    $this->template    = $params["controller"] ."/". $params["action"];
  }
  
  public function render() {
    $layout = $this->content_for_template();
    $body   = $this->content_for_layout($layout);
    echo $body;
  }
  
  public function content_for_template() {
    $start = microseconds();
    $params = $this->view_params();
    ob_start();
    if(Import::view($this->template, $this->ext, $params) === false) {
      Import::view("exceptions/not_found", $this->ext, $params);
      Logger::render("Rendering template within exceptions/not_found (". (microseconds() - $start) ." ms)\n");
    } else {
      Logger::render("Rendering template within {$this->template} (". (microseconds() - $start) ." ms)\n");
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
    if(!Import::view($this->layout, $this->ext, $params)) {
      echo $content;
    }
    $output = ob_get_contents();
    ob_end_clean();
    Logger::render("Rendering {$this->layout} (". (microseconds() - $start) ." ms)\n");      
    return $output;
  }
  
  public function render_partial($partial_name, $options=array()) {
    $start   = microseconds();
    $params  = $this->view_params();
    if(is_array($options["locals"])) {
      $params = array_merge($params, $options["locals"]);
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
    Import::view($partial, $this->ext, $params);
    $output = ob_get_contents();
    ob_end_clean();
    Logger::render("Rendering {$partial} (". (microseconds() - $start) ." ms)\n");      
    return $output;
  }
  
  public function view_params() {
    $params = array("params"      => $this->params, 
                    "layout"      => $this->layout,
                    "template"    => $this->template,
                    "request_uri" => $this->request_uri,
                    "view"        => $this
                    );
    $params = array_merge($this->view_params, $params);       
    return $params;
  }
}

?>