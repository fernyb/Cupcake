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