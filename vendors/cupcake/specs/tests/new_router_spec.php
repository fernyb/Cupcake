<?php

describe("NewRouter -> match", function(){
  it("creates an array of routes with path", function(){
    $r = NewRouter::getInstance();
    $r->match("/new/router");
    ensure((count($r->routes) > 0), "Failed to create an array of routes");
    $r->reset();
  });
  
  it("can build more than one route", function(){
    $r = NewRouter::getInstance();
    $r->match("/new/router");
    $r->match("/another/route");
    ensure((count($r->routes) == 2), "Failed to build more than one route");
    $r->reset();    
  });
  
  it("sets the current path of the route to match", function(){
    $r = NewRouter::getInstance();
    $r->match("/new/current_path");
    assert_equal($r->current_path, "/new/current_path", "Failed to set current_path as the current route to match");
    $r->reset();
  });
  
  it("returns an instance of NewRouter", function(){
    $r = NewRouter::getInstance();
    $resp = $r->match("/new/current_path");
    assert_equal(get_class($resp), "NewRouter", "Failed to return an instance of NewRouter");
    $r->reset();
  });
});

describe("NewRouter -> current_path_params", function(){
  it("returns an array", function(){
    $r = NewRouter::getInstance();
    $r->match("/blog/post");
    $params = $r->current_path_params(array("controller" => "article", "action" => "show"));
    ensure(is_array($params), "Failed to return an array");
    ensure((count($params) == 2), "Failed to return an array with count of 2");
    $r->reset();
  });
  
  it("return an array with merged params from current_path route", function(){
    $r = NewRouter::getInstance();
    $r->match("/blog/post");
    $params = $r->current_path_params(array("controller" => "article", "action" => "show"));
    assert_equal($params[0], 0, "Failed to set the index for current path params");
    assert_equal(array_keys($params[1]), array("path", "params"), "Failed to have path and params for keys");
    $r->reset();
  });
  
  it("has controller and action for params", function(){
    $r = NewRouter::getInstance();
    $r->match("/blog/post");
    $params = $r->current_path_params(array("controller" => "article", "action" => "show"));
    ensure($params[1]['params'], "Params should be an array");
    assert_equal($params[1]['params']['controller'], "article", "Controller params should be article");
    assert_equal($params[1]['params']['action'], "show", "Action should be show");
    $r->reset();    
  });
  
  it("returns false when current_path not found in routes", function(){
    $r = NewRouter::getInstance();
    $r->match("/blog/post");
    $r->current_path = "/something/not_found";
    $params = $r->current_path_params(array("controller" => "article", "action" => "show"));
    assert_equal($params, false, "Failed to return false when current_path is not found in routes");
    $r->reset();  
  });
});

describe("NewRouter -> to", function(){
  it("returns false when current_path is false", function(){
    $r = NewRouter::getInstance();
    $r->current_path = false;
    $resp = $r->to(array("controller" => "main", "action" => "show"));
    assert_equal($resp, false);
    $r->reset();
  });
  
  it("return false when it cannot merge params", function(){
    $r = NewRouter::getInstance();
    $r->match("/blog/post");
    $r->current_path = "/page/not_found";
    $resp = $r->to(array("controller" => "main", "action" => "show"));
    assert_equal($resp, false, "Failed to return false when params cannot merge");    
    $r->reset();
  });
  
  it("returns an array", function(){
    $r = NewRouter::getInstance();
    $r->match("/blog/post");
    $resp = $r->to(array("controller" => "main", "action" => "show"));
    assert_equal(is_array($resp), true, "Failed to return an array");    
    $r->reset();    
  });
  
  it("return array with path", function(){
    $r = NewRouter::getInstance();
    $r->match("/blog/post");
    $resp = $r->to(array("controller" => "main", "action" => "show"));
    assert_equal($resp['path'], "/blog/post", "Failed to have path /blog/post");
    $r->reset();     
  });
  
  
  it("return array with params merged", function(){
    $r = NewRouter::getInstance();
    $r->match("/blog/post");
    $resp = $r->to(array("controller" => "main", "action" => "show"));
    assert_equal($resp['params'], array("controller" => "main", "action" => "show"), "Failed to merge params");
    $r->reset();     
  });
});


describe("NewRouterr -> prepare", function(){
  it("returns an instance of NewRouter", function(){
	  $rsp = NewRouter::prepare(function($r){ });
	  assert_equal(get_class($rsp), "NewRouter", "Failed to return an instance of NewRouter");
	  $rsp->reset();
  });
  
  it("creates build routes like merb", function(){
    NewRouter::prepare(function($r){
      $r->match("/")->to(array("controller" => "public", "action" => "index"));
      $r->match("/blog/post")->to(array("controller" => "article", "action" => "show"));
    });
  
    $r = NewRouter::getInstance();
    assert_equal(count($r->routes), 2, "It should have a count of 2 routes");
    $r->reset();
  });
});


describe("NewRouter -> route_for", function(){
  it("returns an array with route index", function(){
    NewRouter::prepare(function($r){
      $r->match("/")->to(array("controller" => "public", "action" => "index"));
      $r->match("/blog/post")->to(array("controller" => "article", "action" => "show"));
    });
    
    $r = NewRouter::getInstance();
    $route = $r->route_for("/");
    assert_equal($route[0], 0, "It should have returned path: /");
    
    $route = $r->route_for("/blog/post");
    assert_equal($route[0], 1, "It should have returned path: /blog/post");
    $r->reset();
  });
  
  it("returns an array with params", function(){
    NewRouter::prepare(function($r){
      $r->match("/")->to(array("controller" => "public", "action" => "index"));
      $r->match("/blog/post")->to(array("controller" => "article", "action" => "show"));
    });
    
    $r = NewRouter::getInstance();
    $route = $r->route_for("/");
    $params = array("/" => array("path" => "/", "params" => array("controller" => "public", "action" => "index")));
    assert_equal($route[1], $params, "It should return an array with params");
  
    $route = $r->route_for("/blog/post");
    $params = array("/blog/post" => array("path" => "/blog/post", "params" => array("controller" => "article", "action" => "show")));
    assert_equal($route[1], $params, "It should return an array with params");
  
    $r->reset();
  });  
});


describe("NewRouter -> compiled_statement", function(){
  it("compiles a statement for one route", function(){
    NewRouter::prepare(function($r){
      $r->match("/")->to(array("controller" => "public", "action" => "index"));
    });
    
    $r = NewRouter::getInstance();
    $code = $r->compiled_statement();
   
    $match = htmlentities(trim('if ( preg_match("/^\/$/", $cached_path) ) { 
    return array(0, array("controller" => "public","action" => "index"));
 }'));
 
    assert_equal($match, htmlentities(trim($code)), "It should generate code");
    $r->reset();
  });
  
    it("compiles statements for two route", function(){
    NewRouter::prepare(function($r){
      $r->match("/")->to(array("controller" => "public", "action" => "index"));
      $r->match("/music/artist/:id(/:artist_name)")->to(array("controller" => "music", "action" => "artist"));
    });
    
    $r = NewRouter::getInstance();
    $code = $r->compiled_statement();
    
    $pattern = htmlentities(trim('if ( preg_match("/^\/$/", $cached_path) ) { 
    return array(0, array("controller" => "public","action" => "index"));
 } 
 else if ( preg_match("/^\/music\/artist\/([^\/.,;?]+)(?:\/([^\/.,;?]+))$/", $cached_path) ) { 
    return array(1, array("controller" => "music","action" => "artist"));
 }'));
 
    assert_equal($pattern, htmlentities(trim($code)), "Failed to generate code for two routes");
    $r->reset();
  });
  
  it("compiles statements for three route", function(){
    NewRouter::prepare(function($r){
      $r->match("/")->to(array("controller" => "public", "action" => "index"));
      $r->match("/music/artist/:id(/:artist_name)")->to(array("controller" => "music", "action" => "artist"));
      $r->match("/profile")->to(array("controller" => "public", "action" => "index"));
    });
    
    $r = NewRouter::getInstance();
    $code = $r->compiled_statement();
  
    $pattern = htmlentities(trim('if ( preg_match("/^\/$/", $cached_path) ) { 
    return array(0, array("controller" => "public","action" => "index"));
 } 
 else if ( preg_match("/^\/music\/artist\/([^\/.,;?]+)(?:\/([^\/.,;?]+))$/", $cached_path) ) { 
    return array(1, array("controller" => "music","action" => "artist"));
 } 
 else if ( preg_match("/^\/profile$/", $cached_path) ) { 
    return array(2, array("controller" => "public","action" => "index"));
 }'));
 
    assert_equal($pattern, htmlentities(trim($code)), "Failed to generate code for two routes");
    $r->reset();
  });  
});

?>