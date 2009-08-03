<?php

class View {
  public $request_uri;
  public $controller;
  public $params;
  public $layout;
  public $template;
  public $ext = "html.php";
  
  public function __construct($request_uri, $params=array()) {
    $this->request_uri = $request_uri;
    $this->params      = $params;
    $this->layout      = "layouts/application";
    $this->template    = $params["controller"] ."/". $params["action"];
  }
  
  public function render() {
    $layout = $this->content_for_template();
    $body   = $this->content_for_layout($layout);
    echo $body;
  }
  
  public function content_for_template() {
    $params = $this->view_params();
    ob_start();
    if(Import::view($this->template, $this->ext, $params) === false) {
      Import::view("exceptions/not_found", $this->ext, $params);
      Logger::render("Rendering template within exceptions/not_found\n");
    } else {
      Logger::render("Rendering template within {$this->template}\n");
    }
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
  }
  
  public function content_for_layout($content="") {
    Logger::render("Rendering {$this->layout}\n");    
    $params = $this->view_params();
    $params = array_merge($params, array("content_for_layout" => $content));    
    ob_start();
    if(!Import::view($this->layout, $this->ext, $params)) {
      echo $content;
    }
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
  }
  
  public function view_params() {
    $params = array("params"      => $this->params, 
                    "layout"      => $this->layout,
                    "template"    => $this->template,
                    "request_uri" => $this->request_uri,
                    );
    return $params;
  }
}

?>