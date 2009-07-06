<?php
require_once dirname(__FILE__) . "/../spec_helper.php";

describe("Dispatcher -> parseParams", function(){
  it("returns an array", function(){
    $Dispatcher = new Dispatcher();
    $resp = $Dispatcher->parseParams("/users");
    assert_equal(is_array($resp), true);
  });
  
  it("has controller key in array", function(){
    $Dispatcher = new Dispatcher();
    $resp = $Dispatcher->parseParams("/users");
    assert_equal(array_key_exists("controller", $resp), true);
  });
  
  it("has action key in array", function() {
    $Dispatcher = new Dispatcher();
    $resp = $Dispatcher->parseParams("/users");
    assert_equal(array_key_exists("action", $resp), true);
  });
    
  it("has controller key equal to main", function(){
    $Dispatcher = new Dispatcher();
    $resp = $Dispatcher->parseParams("/users");
    assert_equal($resp["controller"], "main");  
  });
  
  it("has action key equal to show", function(){
    $Dispatcher = new Dispatcher();
    $resp = $Dispatcher->parseParams("/");
    assert_equal($resp["action"], "show");  
  });
    
  it("has action key equal to users", function(){
    $Dispatcher = new Dispatcher();
    $resp = $Dispatcher->parseParams("/users");
    assert_equal($resp["action"], "users");  
  });
  
  it("has action key equal to index when no action was given from routes", function() {
    $Dispatcher = new Dispatcher();
    $resp = $Dispatcher->parseParams("/cars");
    assert_equal($resp["action"], "index"); 
  });
  
  it("has url key", function(){
    $Dispatcher = new Dispatcher();
    $params = $Dispatcher->parseParams("/users");
    assert_equal(array_key_exists("url", $params), true);   
  });
  
  it("returns array for params['url']", function(){
    $Dispatcher = new Dispatcher();
    $params = $Dispatcher->parseParams("/users");
    assert_equal(is_array($params['url']), true);
  });
  
  it ("has url in params", function() {
    $_GET["url"] = $url = "/users";
    $Dispatcher = new Dispatcher();
    $params = $Dispatcher->parseParams($_GET["url"]);
    assert_equal($params['url']['url'], $url);
  });
  
  it("return controller for an undefined route", function() {
    $_GET["url"] = $url = "/profile";
    $Dispatcher = new Dispatcher();
    $params = $Dispatcher->parseParams($_GET["url"]);
    assert_equal($params['controller'], "profile");
  });  
  
  it("returns action for an undefined route", function() {
    $_GET["url"] = $url = "/profile";
    $Dispatcher = new Dispatcher();
    $params = $Dispatcher->parseParams($_GET["url"]);
    assert_equal($params['action'], "index");  
  });
});


/*
* Dispatcher -> uri, Really needs to be a smaller method it can be broken down
* so that testing it can be much easier, for now I will leave it as-is.
*/
describe("Dispatcher -> uri", function(){
  it("Returns the REQUEST_URI from the server environment", function(){
    $_GET['url'] = $url = "users/profile";
    $_SERVER['argv'][0] = "url={$url}";
    $Dispatcher = new Dispatcher();
    $uri = $Dispatcher->uri();
    assert_equal($uri, "/{$url}");
  });
});


describe("Dispatcher -> baseUrl", function(){
  it("sets the proper webroot", function(){
    $_GET['url'] = $url = "users/profile";
    $_SERVER['argv'][0] = "url={$url}";
    $Dispatcher = new Dispatcher();
    $Dispatcher->baseUrl();
    
    assert_equal("/", $Dispatcher->webroot);
  });
});


describe("Dispatcher -> dispatch", function(){
    it("has the current url", function() {
      $_GET['url'] = $url = "users/profile";
      $_SERVER['argv'][0] = "url={$url}";
      $Dispatcher = new Dispatcher();
      $Dispatcher->command_line = true;
      $Dispatcher->dispatch($_GET['url']);
      assert_equal("/users/profile", $Dispatcher->here);
    });
});


describe("Dispatcher -> __loadControllerFile", function(){
  it("returns true when controller loads", function() {
    $Dispatcher = new Dispatcher();
    $loaded = $Dispatcher->__loadControllerFile("MainController");
    assert_equal($loaded, true);
  });
  
  it("returns false when controller does not loads", function() {
    $Dispatcher = new Dispatcher();
    $loaded = $Dispatcher->__loadControllerFile("FakerController");
    assert_equal($loaded, false);
  });
});


describe("Dispatcher -> __getController", function(){
  
  it("returns a controller based on \$this->params", function() {
    $Dispatcher = new Dispatcher();
    $Dispatcher->params = array("controller" => "main");
    $controller = $Dispatcher->__getController();
    assert_equal("MainController", get_class($controller));
  });
  
  it("returns a controller given params", function() {
    $Dispatcher = new Dispatcher();
    $controller = $Dispatcher->__getController(array("controller" => "main"));
    assert_equal("MainController", get_class($controller));
  });
  
  it("returns false when fails to load a controller", function() {
    $Dispatcher = new Dispatcher();
    $controller = $Dispatcher->__getController(array("controller" => "faker"));
    assert_equal($controller, false);
  });
  
});



?>