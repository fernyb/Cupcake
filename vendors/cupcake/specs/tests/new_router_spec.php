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
 else if ( preg_match("/^\/music\/?artist\/([^\/.,;?]+)?(?:\/?([^\/.,;?]+)?)$/", $cached_path) ) { 
    return array(1, array("controller" => "music","action" => "artist"));
 } 
 else if ( preg_match("/^\/profile\$/", $cached_path) ) { 
    return array(2, array("controller" => "public","action" => "index"));
 }'));
 
    assert_equal($pattern, htmlentities(trim($code)), "Failed to generate code for three routes");

    $r->reset();
  });  
});


/*
* Test All the possible paths to make sure they match
*/
describe("NewRouter -> arrays_to_regexps", function(){
  it("returns a string", function(){
    $r = NewRouter::getInstance();
    $pattern = $r->arrays_to_regexps(array("path" => "/music/artist/:id(/:artist_name)"));
    
    ensure(is_string($pattern));
    $r->reset();
  });
  
  it("should match /music/artist/:id(/:artist_name) to /music/artist/5/coldplay", function(){
    $r = NewRouter::getInstance();
    $pattern = $r->arrays_to_regexps(array("path" => "/music/artist/:id(/:artist_name)"));
    $match   = preg_match("/". $pattern ."/", "/music/artist/5/coldplay", $matches);
    
    ensure($match, "Failed to match route: /music/artist/5/coldplay using path: /music/artist/:id(/:artist_name) with pattern: $pattern");
    assert_equal($matches[0], "/music/artist/5/coldplay", "Failed to match path");
    assert_equal($matches[1], "5", "Failed to match :id");
    assert_equal($matches[2], "coldplay", "Failed to match :artist_name");
    $r->reset();
  });

  it("should match /music/artist/:id(/:artist_name) to /music/artist/5", function(){
    $r = NewRouter::getInstance();
    $pattern = $r->arrays_to_regexps(array("path" => "/music/artist/:id(/:artist_name)"));
    $match   = preg_match("/". $pattern ."/", "/music/artist/5", $matches);
    
    ensure($match, "Failed to match route: /music/artist/5 using path: /music/artist/:id(/:artist_name) with pattern: $pattern");
    assert_equal($matches[0], "/music/artist/5", "Failed to match path");
    assert_equal($matches[1], "5", "Failed to match :id");
    assert_equal($matches[2], "", "Failure, Tried to over match paths");
    $r->reset();
  });
  
  it("should match /book(/:id) to /book", function(){
    $r = NewRouter::getInstance();
    $pattern = $r->arrays_to_regexps(array("path" => "/book(/:id)"));
    $match   = preg_match("/". $pattern ."/", "/book", $matches);
    
    ensure($match, "Failed to match route /book with path: /book(/:id) using pattern: $pattern");
    $r->reset();
  });
  
  it("should match /book(/:id) to /book/100", function(){
    $r = NewRouter::getInstance();
    $pattern = $r->arrays_to_regexps(array("path" => "/book(/:id)"));
    $match   = preg_match("/". $pattern ."/", "/book/100", $matches);
    
    ensure($match, "Failed to match route /book/100 with path: /book(/:id) using pattern: $pattern");
    $r->reset();
  });    
});


describe("NewRouter -> param_keys_for_path", function(){
  it("should extract :controller, :action, :id for /:controller/:action/:id", function(){
    $r = NewRouter::getInstance();
    $params = $r->param_keys_for_path("/:controller/:action/:id");
    
    assert_equal($params[0], ":controller", "Failed to match :controller");
    assert_equal($params[1], ":action",     "Failed to match :action");
    assert_equal($params[2], ":id",         "Failed to match :id");
    assert_equal(count($params), 3,         "Failed to have 3 keys");
    $r->reset();    
  });  
  
  it("should extract :controller, :action for /:controller/:action", function(){
    $r = NewRouter::getInstance();
    $params = $r->param_keys_for_path("/:controller/:action");
    
    assert_equal($params[0], ":controller", "Failed to match :controller");
    assert_equal($params[1], ":action",     "Failed to match :action");
    assert_equal(count($params), 2,         "Failed to have 2 keys");
    $r->reset();    
  });  
  
  it("should extract :action for /controller/:action", function(){
    $r = NewRouter::getInstance();
    $params = $r->param_keys_for_path("/controller/:action");
    
    assert_equal($params[0], ":action", "Failed to match :action");
    assert_equal(count($params), 1,     "Failed to have 1 keys");
    $r->reset();    
  }); 
    
  it("should extract :id for /book/:id", function(){
    $r = NewRouter::getInstance();
    $params = $r->param_keys_for_path("/book/:id");
    
    assert_equal($params[0], ":id", "Failed to match :id");
    $r->reset();    
  });
  
  it("should extract :id, :artist for /music/:id(/:artist)", function(){
    $r = NewRouter::getInstance();
    $params = $r->param_keys_for_path("/music/:id(/:artist)");
    
    assert_equal($params[0], ":id",     "Failed to match :id");
    assert_equal($params[1], ":artist", "Failed to match :artist");
    assert_equal(count($params), 2,     "Failed to have 2 keys");
    $r->reset();    
  }); 
  
  it("returns an empty array for /:action", function(){
    $r = NewRouter::getInstance();
    $params = $r->param_keys_for_path("/:action");
    assert_equal($params[0], ":action", "Failed to match :action");
    assert_equal(count($params), 1,     "Failed to have 1 key");
    $r->reset();   
  });
  
  it("returns an empty array for (/:action)", function(){
    $r = NewRouter::getInstance();
    $params = $r->param_keys_for_path("(/:action)");
    assert_equal($params[0], ":action", "Failed to match :action");
    assert_equal(count($params), 1,     "Failed to have 1 key");
    $r->reset();   
  });
        
  it("returns an empty array for /", function(){
    $r = NewRouter::getInstance();
    $params = $r->param_keys_for_path("/");
    assert_equal(count($params), 0,     "Failed to have 0 keys");
    $r->reset();   
  });
    
  it("returns an empty array", function(){
    $r = NewRouter::getInstance();
    $params = $r->param_keys_for_path("");
    assert_equal(count($params), 0,     "Failed to have 0 keys");
    $r->reset();   
  });
});

?>