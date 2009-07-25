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
    $router = Router::getInstance();
    $response = $router->url("/products/item/1");
    assert_equal($response, "/products/item/1");
  });
  
  it("returns path with query string", function(){
    $router = Router::getInstance();
    $response = $router->url("/products/item/1?status=new");
    assert_equal($response, "/products/item/1?status=new");
  });
  
  it("returns a url string from controller action", function(){
    Router::clearRoutes();
    $url = Router::url(array("controller" => "public", "action" => "show"));
    assert_equal($url, "/public/show/");
  });
  
  it("returns a url string from route", function(){
    Router::clearRoutes();
    Router::connect("/user/profile", array("controller" => "main", "action" => "show"));
    $url = Router::url(array("controller" => "main", "action" => "show"));
    assert_equal($url, "/user/profile");
  });
});

describe("Router -> writeRoute", function(){
  it("returns an array", function(){
    $resp = Router::writeRoute("/product/item/:id", array("controller" => "main", "action" => "product_item"), array("id" => 5));
    assert_equal(is_array($resp), true);
  });
  
  it("builds a route regular expression", function(){
    $resp = Router::writeRoute("/product/item/:id", array("controller" => "main", "action" => "product_item"), array("id" => 5));
    assert_equal($resp[0], "#^/product/item(?:/(5))[\/]*$#");
  });
  
  it("route regular expression matches route", function(){
    $resp = Router::writeRoute("/product/item/:id", array("controller" => "main", "action" => "product_item"), array("id" => 5));
    $regex = $resp[0];
    $url = "/product/item/5";
    assert_match($regex, $url);
  });
  
  it("route regular expression returns matches elements", function(){
    $resp = Router::writeRoute("/product/item/:id", array("controller" => "main", "action" => "product_item"), array("id" => 5));
    $regex = $resp[0];
    $url = "/product/item/5";
    preg_match($regex, $url, $r);
    assert_equal($r[0], $url);
    assert_equal($r[1], 5);
  });
});


?>