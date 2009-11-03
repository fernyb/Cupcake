<?php

describe("Dispatcher -> request_uri", function(){
  it("returns the request base uri", function(){
    $d = CupcakeDispatcher::getInstance();
    $uri = $d->request_base_uri("/application/action?name=hello");
    assert_equal($uri, "/application/action", "Failed to get base uri: $uri");
  });
  
  it("returns the request base uri when having only /", function(){
    $d = CupcakeDispatcher::getInstance();
    $uri = $d->request_base_uri("/?name=hello");
    assert_equal($uri, "/");
  });
  
  it("returns / when not having query string", function(){
    $d = CupcakeDispatcher::getInstance();
    $uri = $d->request_base_uri("/");
    assert_equal($uri, "/");
  });

  it("returns / when /?name=hello", function(){
    $d = CupcakeDispatcher::getInstance();
    $uri = $d->request_base_uri("/?name=hello");
    assert_equal($uri, "/");
  });  
});

describe("Dispatcher -> setRouter", function(){
  it("sets the router", function(){
    $d = CupcakeDispatcher::getInstance();
    $d->setRouter(Router::getInstance());
    assert_equal(get_class($d->router()), "Router");
  });
});

describe("Dispatcher -> find_route", function(){
  it("returns an array with controller and action", function(){
    Router::map(function($r){
      $r->connect("/:controller/:action", array("controller" => "application", "action" => "method"));
    });
    $d = CupcakeDispatcher::getInstance();
    $d->setRouter(Router::getInstance());
    
    $params = $d->find_route("/application/method");
    
    assert_equal($params["controller"], "application");
    assert_equal($params["action"], "method");
  });

  it("returns an array with controller and action and ids", function(){
    Router::map(function($r){
      $r->connect("/:controller/:action", array("controller" => "application", "action" => "method"));
    });
    $d = CupcakeDispatcher::getInstance();
    $params = $d->find_route("/application/method");
    
    assert_equal($params["controller"], "application");
    assert_equal($params["action"], "method");
  });
});

describe("Dispatcher -> params", function(){
  it("returns an array", function(){
    $d = CupcakeDispatcher::getInstance();
    $params = $d->params();
    assert_array($params);
  });
  
  it("array has keys from \$_GET and \$_POST", function(){
    $_GET['name'] = "Dwight";
    $_POST['id'] = "200";
    $d = CupcakeDispatcher::getInstance();
    $params = $d->params();
    
    assert_array_has_keys($params, array("id", "name"));
    $_GET = array();
    $_POST = array();
  });

  it("array has values from \$_GET and \$_POST", function(){
    $_GET['name'] = "Dwight";
    $_POST['id'] = "200";
    $d = CupcakeDispatcher::getInstance();
    $params = $d->params();
    
    assert_array_has_values($params, array("Dwight", "200"));
    $_GET = array();
    $_POST = array();
  });  
  
  it("removes url from GET and POST params", function(){
    $_GET['url'] = "fhsjdhfk";
    $d = CupcakeDispatcher::getInstance();
    $params = $d->params();
    
    assert_equal(count($params), 0);
    $_GET = array();
    $_POST = array();
  });
});

describe("Dispatcher -> env", function(){
  it("returns the REQUEST_URI", function(){
    $_SERVER['REQUEST_URI'] = "/helloworld";
    $d = CupcakeDispatcher::getInstance();
    $value = $d->env("REQUEST_URI");
    assert_equal($value, "/helloworld");
  });
  
  it("returns null when no value from SERVER", function(){
    $server = $_SERVER;
    $_SERVER = array();
    $d = CupcakeDispatcher::getInstance();
    $value = $d->env("REQUEST_URI");
    assert_null($value);
    $_SERVER = $server;
  });  
});

describe("Dispatcher -> params_for_request", function(){
  it("returns an array", function(){
    Router::getInstance()->reset();
    Router::map(function($r){
      $r->connect("/:controller/:action", array());
    });
    $d = CupcakeDispatcher::getInstance();
    $params = $d->params_for_request("/application/method");
    
    assert_equal($params["controller"], "application");
    assert_equal($params["action"], "method");
  });

  it("merges GET with router params", function(){
    Router::getInstance()->reset();
    $_GET["id"] = 25;
    Router::map(function($r){
      $r->connect("/blog/:action", array("controller" => "application"));
    });
    $d = CupcakeDispatcher::getInstance();
    $params = $d->params_for_request("/blog/method");
    
    assert_equal($params["controller"], "application");
    assert_equal($params["action"], "method");
    assert_equal($params["id"], 25);
    $_GET = array();
  });
  
  it("does not merge params already defined by router", function(){
    Router::getInstance()->reset();
    $_GET["controller"] = "get_application";
    $_GET["user_id"] = 100;
    Router::map(function($r){
      $r->connect("/blog/:action", array("controller" => "application"));
    });
    $d = CupcakeDispatcher::getInstance();
    $params = $d->params_for_request("/blog/method");
    
    assert_not_equal($params["controller"], "get_application");
    assert_equal($params["action"], "method");
    assert_equal($params["user_id"], 100);
    $_GET = array();
  });

  it("returns false when it cannot merge", function(){
    Router::getInstance()->reset();
    $_GET["user_id"] = 100;
    Router::map(function($r){
      $r->connect("/blog/:action", array("controller" => "application"));
    });
    $d = CupcakeDispatcher::getInstance();
    $params = $d->params_for_request("/user/method");
    
    assert_not_equal($params["controller"], "application");
    assert_not_equal($params["action"], "method");
    assert_not_equal($params["user_id"], 100);
    $_GET = array();
  });      
});


?>