<?php
require_once dirname(__FILE__) . "/../spec_helper.php";

describe("View -> __constructor", function(){
  it("sets the controller class variable to an instance of a controller", function(){
    $controller = new MainController();
    $view = new View($controller);
    assert_equal(get_class($view->controller), get_class($controller));
  });
  
  it("sets view variables", function(){
    $controller = new MainController();
    $controller->viewVars = array("action" => "show", "controller" => "main");
    $view = new View($controller);
    assert_equal($view->viewVars, $controller->viewVars);
  });
});


describe("View -> action_view", function(){
  it("returns file name to render when action exists", function(){
    $controller = new MainController();
    $controller->viewVars = array("action" => "show", "controller" => "main");
    $view = new View($controller);
    $view->viewPath = APP_BASE_URL . DS . "views/main";
    $filename = $view->action_view("show");
    assert_match("/views\/main\/show.html.php/", $filename);
  });
  
  it("returns file name to render when action is missing", function(){
    $controller = new MainController();
    $controller->viewVars = array("action" => "no-page", "controller" => "main");
    $view = new View($controller);
    $view->viewPath = APP_BASE_URL . DS . "views/main";
    $filename = $view->action_view("no-page");
    assert_match("/views\/main\/missing.html.php/", $filename);
  });
});


describe("View -> data_for_layout", function(){
  it("returns an array", function(){
    $controller = new MainController();
    $controller->viewVars = array("action" => "no-page", "controller" => "main");
    $view = new View($controller);
    $data = $view->data_for_layout(array("name" => "Fernando"));
    assert_equal(is_array($data), true);
  });
  
  it("must have array keys give as argument", function(){
    $controller = new MainController();
    $controller->viewVars = array("action" => "no-page", "controller" => "main");
    $view = new View($controller);
    $data = $view->data_for_layout(array("name" => "Fernando"));
    assert_equal(array_key_exists("name", $data), true);
  });
});


describe("View -> layout_path", function(){
  it("returns the path to the layouts directory", function(){
    $controller = new MainController();
    $controller->viewVars = array("action" => "no-page", "controller" => "main");
    $view = new View($controller);
    $layout_path = $view->layout_path();
    assert_match("/app\/views\/layouts$/", $layout_path);
  });
});


describe("View -> layout_view", function(){
  it("returns the name for layout file", function(){
    $controller = new MainController();
    $controller->viewVars = array("action" => "no-page", "controller" => "main");
    $view = new View($controller);
    $filename = $view->layout_view("application");
    assert_equal("application", $filename);
  });
  
  it("returns filename for missing layout file", function() {
    $controller = new MainController();
    $controller->viewVars = array("action" => "no-page", "controller" => "main");
    $view = new View($controller);
    $filename = $view->layout_view("no-page");
    assert_equal("missing", $filename);  
  });
  
  it("returns filename for missing layout using \$this->layout", function() {
    $controller = new MainController();
    $controller->viewVars = array("action" => "no-page", "controller" => "main");
    $view = new View($controller);
    $view->layout = "my-layout";
    $filename = $view->layout_view();
    assert_equal("missing", $filename);  
  });

  it("returns filename for layout based using \$this->layout", function() {
    $controller = new MainController();
    $controller->viewVars = array("action" => "no-page", "controller" => "main");
    $view = new View($controller);
    $view->layout = "application";
    $filename = $view->layout_view();
    assert_equal("application", $filename);  
  });  
  
  it("returns filename for missing layout when filename with extension is not found", function(){
    $controller = new MainController();
    $controller->viewVars = array("action" => "no-page", "controller" => "main");
    $view = new View($controller);
    $view->layout = "application";
    $view->ext = ".layout.php";
    $filename = $view->layout_view();
    assert_equal("missing", $filename);   
  });
});


describe("View -> render_view", function(){
  it("renders the action view", function(){
    $controller = new MainController();
    $controller->viewVars = array("action" => "show", "controller" => "main");
    $view = new View($controller);
    $view->viewPath = APP_BASE_URL . DS . "views/main";
    $output = $view->render_view("main", "show");
    assert_not_null($output);
  });
});

?>