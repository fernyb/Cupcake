<?php

describe("Router -> getInstance", function(){
  it("return an instance of Router", function(){
	  $router = new Router();
	  assert_equal(get_class($router), get_class($router->getInstance()));
  });
});


describe("Router -> connect", function(){
  it("returns an array", function(){
    $routes = Router::connect("/", array("controller" => "main", "action" => "show"));
    
    assert_equal(is_array($routes), true);
  });
  
  it("returns an array of arrays", function(){
    $routes = Router::connect("/", array("controller" => "main", "action" => "show"));
    foreach($routes as $route) {
      assert_equal(is_array($route), true);
    }
  });
  
  it("has the route path", function(){
    Router::clearRoutes();
    $routes = Router::connect("/user/profile", array("controller" => "main", "action" => "show"));
    
    assert_equal($routes[0][0], "/user/profile");
  });
  
  it("has the controller name", function(){
    Router::clearRoutes();
    $routes = Router::connect("/user/profile", array("controller" => "main", "action" => "show"));
    $default = $routes[0][1];
    
    assert_equal($default["controller"], "main");
  });
  
  it("has the controller name", function(){
    Router::clearRoutes();
    $routes = Router::connect("/user/profile", array("controller" => "main", "action" => "show"));
    $default = $routes[0][1];
    
    assert_equal($default["action"], "show");
  });
  
  it("sets action as index when blank", function(){
    Router::clearRoutes();
    $routes = Router::connect("/user/profile", array("controller" => "main"));
    $default = $routes[0][1];
    
    assert_equal($default["action"], "index");    
  });

});


describe("Router -> url", function(){
  it("returns full translated url with base path", function() {
    $router = new Router();
    $response = $router->url("/products/item/1");
    assert_equal($response, "/products/item/1");
  });
});



?>