<?php
class View {
  var $ext = "html.php";
  var $layout = "application";
  var $output = "";
  var $pageTitle = false;
  var $viewVars = array();
  var $viewPath = false;
  var $controller = false;
  
  var $name = null;
  var $action = null;
  
  function __construct(&$controller) {
		if (is_object($controller)) {
		  $this->controller = clone $controller;
			$this->viewVars = $this->controller->viewVars;
		}
	}
  
  function render($action, $layout, $file) {
    $this->viewPath = APP_BASE_URL . DS . "views". DS ."{$layout}";

    $content = $this->render_view($layout, $action);
    
    $this->output .= $this->render_layout($layout, $content);
    
    return $this->output;
  }
  
  function action_view($action) {
    $ext = $this->ext;
    $view_file = $this->viewPath . DS . "{$action}.{$ext}";
    
    if(file_exists($view_file)) {
      $file = $view_file;
    } else {
      $file = $this->viewPath . DS . "missing.{$ext}";
    }
    return $file;  
  }
  
  function data_for_layout($params=array()) {
     $data = array_merge($params, array(
      'title_for_layout' => $this->pageTitle,
      'content_for_layout' => $this->content,
      'scripts_for_layout' => array()
    ));
    return $data;
  }
  
  function render_view($controller, $action) {
    $file = $this->action_view($action);
    $data_for_layout = $this->data_for_layout($this->viewVars);
  
    extract($data_for_layout, EXTR_SKIP);
    ob_start();
    include $file;
    $output = ob_get_contents();
    ob_end_clean();
  
    return $output;
  }
  
  function layout_path() {
    return realpath(APP_BASE_URL . "/" . "views/layouts");
  }
  
  function layout_view($file=null) {
    $ext = $this->ext;
    $layouts_path = $this->layout_path();
    
    if($file == null) {
      $filename = $this->layout;
    } else {
      $filename = $file;
    }
    
    $layout_file = "{$layouts_path}" . "/" ."{$filename}.{$ext}";
    
    if(file_exists($layout_file)) {
      $file = $this->layout;
    } else {
      $file = "missing";
    }
    return $file;
  }
  
  function render_layout($file=null, $content="", $view_vars=array()) {
    $ext = $this->ext;
    $filename = $this->layout_view($file);
    $layout_path = $this->layout_path();
    
    if ($this->pageTitle !== false) {
			$pageTitle = $this->pageTitle;
		} else {
			$pageTitle = Inflector::humanize($this->viewPath);
		}
		
		$render_file = "{$layout_path}" . "/" . "${filename}.{$ext}";
		
    $data_for_layout = array_merge($this->viewVars, array(
			'title_for_layout' => $pageTitle,
			'content_for_layout' => $content,
			'scripts_for_layout' => array()
		));
    extract($data_for_layout, EXTR_SKIP);
    
    ob_start();
    include $render_file;
    $output = ob_get_contents();
    ob_end_clean();
    
    return $output;
  }
}

?>