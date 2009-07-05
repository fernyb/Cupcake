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
    $this->viewPath = APP_BASE_URL . "/views/{$layout}";
       
    $this->output .= "Render Action: ". $action ."<br />";
    $this->output .=  "Render Layout: ". $layout ."<br />";
    $this->output .=  "Render File: ". $file ."<br />";
    
    $content = $this->render_view($layout, $action);
    
    $this->output .= $this->render_layout($layout, $content);
    
    return $this->output;
  }
  
  function render_view($controller, $action) {
    $ext = $this->ext;
    $view_file = $this->viewPath . "/{$action}.{$ext}";
 
    $output = "";
    if(file_exists($view_file)) {
      $data_for_layout = array_merge($this->viewVars, array(
        'title_for_layout' => $pageTitle,
        'content_for_layout' => $content,
        'scripts_for_layout' => array()
      ));
      
      extract($data_for_layout, EXTR_SKIP);

      ob_start();
      include "{$view_file}";
      $output = ob_get_contents();
      ob_end_clean();
    }
    return $output;
  }
  
  function render_layout($file=null, $content="", $view_vars=array()) {
    $ext = $this->ext;
    $layouts_path = APP_BASE_URL . "/views/layouts";
    if($file == null) {
      $file = $this->layout;
    }
    if(!file_exists("{$layouts_path}/${file}.{$ext}")) {
      $file = $this->layout;
    } else {
      echo "\n* Could not find layout to render {$file} \n";
    }
    
    if ($this->pageTitle !== false) {
			$pageTitle = $this->pageTitle;
		} else {
			$pageTitle = Inflector::humanize($this->viewPath);
		}
		
    $data_for_layout = array_merge($this->viewVars, array(
			'title_for_layout' => $pageTitle,
			'content_for_layout' => $content,
			'scripts_for_layout' => array()
		));
    extract($data_for_layout, EXTR_SKIP);
    
    ob_start();
    include "{$layouts_path}/${file}.{$ext}";
    $output = ob_get_contents();
    ob_end_clean();
    
    return $output;
  }
}

?>