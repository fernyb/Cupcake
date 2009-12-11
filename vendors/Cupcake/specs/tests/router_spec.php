<?php

describe("Router -> match", function(){
  it("creates an array of routes with path", function(){
    $r = CupcakeRouter::getInstance();
    $r->match("/new/router");
    ensure((count($r->routes) > 0), "Failed to create an array of routes");
    $r->reset();
  });
  
  it("can build more than one route", function(){
    $r = CupcakeRouter::getInstance();
    $r->match("/new/router");
    $r->match("/another/route");
    ensure((count($r->routes) == 2), "Failed to build more than one route");
    $r->reset();    
  });

  it("returns an instance of Router", function(){
    $r = CupcakeRouter::getInstance();
    $resp = $r->connect("/new/current_path", array());
    assert_equal(get_class($resp), "CupcakeRouter", "Failed to return an instance of Router");
    $r->reset();
  });
});

describe("Router -> current_path_params", function(){
  it("returns an array", function(){
    CupcakeRouter::map(function($r){
      $r->connect("/blog/postpage", array("controller" => "article", "action" => "show"));
    });
    $r = CupcakeRouter::getInstance();
    $params = $r->current_path_params("/blog/postpage", array("controller" => "article", "action" => "show"));
    
    ensure(is_array($params), "Failed to return an array");
    ensure((count($params) == 2), "Failed to return an array with count of 2");
    $r->reset();
  });
  
  it("return an array with merged params from current_path route", function(){
    $r = CupcakeRouter::getInstance();
    $r->connect("/blog/post", array("controller" => "article", "action" => "show"));
    $params = $r->current_path_params("/blog/post", array("controller" => "article", "action" => "show"));
    
    assert_equal($params[0], 0, "Failed to set the index for current path params");
    assert_equal($params[1]['controller'], "article");
    assert_equal($params[1]['action'], "show");
    $r->reset();
  });
  
  it("has controller and action for params", function(){
    $r = CupcakeRouter::getInstance();
    $r->connect("/blog/post", array("controller" => "article", "action" => "show"));
    $params = $r->current_path_params("/blog/post", array("controller" => "article", "action" => "show"));
   
    ensure(is_array($params[1]), "Params should be an array");
    assert_equal($params[1]['controller'], "article", "Controller params should be article");
    assert_equal($params[1]['action'], "show", "Action should be show");
    $r->reset();    
  });
  
  it("returns false when current_path not found in routes", function(){
    $r = CupcakeRouter::getInstance();
    $r->connect("/blog/post", array("controller" => "article", "action" => "show"));
    $params = $r->current_path_params("/blog/post-not-found", array("controller" => "article", "action" => "show"));
    
    assert_equal($params, false, "Failed to return false when current_path is not found in routes");
    $r->reset();  
  });
});


describe("Router -> to", function(){
  it("returns an instance of Router", function(){
    $r = CupcakeRouter::getInstance();
    $resp = $r->connect("/blog/post", array("controller" => "main", "action" => "show"));
    assert_equal(get_class($resp), "CupcakeRouter", "Should have returned an instance of router");    
    $r->reset();    
  });
  
  it("return array with path", function(){
    $r = CupcakeRouter::getInstance();
    $r->connect("/blog/post", array("controller" => "main", "action" => "show"));
    $resp = end($r->routes);
    assert_equal($resp['path'], "/blog/post", "Failed to have path /blog/post");
    $r->reset();     
  });
  
  it("return array with params merged", function(){
    $r = CupcakeRouter::getInstance();
    $r->connect("/blog/post", array("controller" => "main", "action" => "show"));
    $resp = end($r->routes);
    assert_equal($resp['params'], array("controller" => "main", "action" => "show"), "Failed to merge params");
    $r->reset();     
  });
});


describe("Router -> name", function(){
  it("sets name key in the routes array", function(){
    CupcakeRouter::map(function($r){
      $r->recent_books("/books/recent", array("controller" => "books", "action" => "recent"));
    });
    $r = CupcakeRouter::getInstance();
    $route = end($r->routes);
    assert_equal($route["name"], "recent_books", "Failed to return name of route");
    $r->reset();
  });
  
  it("returns false when routes is zero", function(){
    CupcakeRouter::map(function($r){
      $r->recent_books("/books/recent", array("controller" => "books", "action" => "recent"));
    });
    $r = CupcakeRouter::getInstance();
    $r->routes = array();
    assert_equal(count($r->routes), 0, "Named route should be zero / false when there no routes");
    $r->reset();
  });
});

describe("Router -> url", function(){
  it("returns a route path string", function() {
    CupcakeRouter::map(function($r){
      $r->recent_books("/books/recent", array("controller" => "books", "action" => "recent"));
    });
    $params = CupcakeRouter::url("recent_books");
    assert_equal($params, "/books/recent");
    CupcakeRouter::getInstance()->reset();
  });

  it("returns a route path string with query parameters", function() {
    CupcakeRouter::map(function($r){
      $r->recent_books("/books/recent", array("controller" => "books", "action" => "recent"));
    });
    $params = CupcakeRouter::url("recent_books", array("username" => "fernyb"));
    assert_equal($params, "/books/recent?username=fernyb");
    CupcakeRouter::getInstance()->reset();
  });
  
  it("returns a route with params in route path", function(){
    CupcakeRouter::map(function($r){
      $r->show_book("/book/show/:id", array("controller" => "books", "action" => "show"));
    });
    $params = CupcakeRouter::url("show_book", array("id" => "100", "sort" => "recent", "author" => "fernyb"));
    assert_equal($params, "/book/show/100?sort=recent&author=fernyb");
    CupcakeRouter::getInstance()->reset();
  });  
});


describe("Routerr -> prepare", function(){
  it("returns an instance of Router", function(){
	  $rsp = CupcakeRouter::map(function($r){ });
	  assert_equal(get_class($rsp), "CupcakeRouter", "Failed to return an instance of Router");
	  $rsp->reset();
  });
  
  it("creates build routes like merb", function(){
    CupcakeRouter::map(function($r){
      $r->connect("/", array("controller" => "public", "action" => "index"));
      $r->connect("/blog/post", array("controller" => "article", "action" => "show"));
    });
  
    $r = CupcakeRouter::getInstance();
    assert_equal(count($r->routes), 2, "It should have a count of 2 routes");
    $r->reset();
  });
  
  it("matches all paths", function(){
    CupcakeRouter::map(function($r){
      $r->connect("/", array("controller" => "application", "action" => "index"));  
      $r->connect("/book(/:id)", array("controller" => "book", "action" => "details_show"));
    });
    $router = CupcakeRouter::getInstance();
    $params = $router->find_route("/book/100");
    
    assert_equal($params["id"], "100");
    assert_equal($params["controller"], "book");
    assert_equal($params["action"], "details_show");
    $router->reset();
  });
});


describe("Router -> route_for", function(){
  it("returns an array with route index", function(){
    CupcakeRouter::map(function($r){
      $r->connect("/", array("controller" => "public", "action" => "index"));
      $r->connect("/blog/post", array("controller" => "article", "action" => "show"));
    });
    
    $r = CupcakeRouter::getInstance();
    $route = $r->route_for("/");
    assert_equal($route[0], 0, "It should have returned path: /");
    
    $route = $r->route_for("/blog/post");
    assert_equal($route[0], 1, "It should have returned path: /blog/post");
    $r->reset();
  });
  
  it("returns an array with params", function(){
    CupcakeRouter::map(function($r){
      $r->connect("/", array("controller" => "public", "action" => "index"));
      $r->connect("/blog/post", array("controller" => "article", "action" => "show"));
    });
    $r = CupcakeRouter::getInstance();
    $route = $r->route_for("/");
    
    $params = array("path" => "/", "params" => array("controller" => "public", "action" => "index"));
    assert_equal($route[1], $params, "It should return an array with params 1");
  
    $route = $r->route_for("/blog/post");  
    $params = array("path" => "/blog/post", "params" => array("controller" => "article", "action" => "show"));
    assert_equal($route[1], $params, "It should return an array with params 2");
    $r->reset();
  });  
});


/*
* Test All the possible paths to make sure they match
*/
describe("Router -> arrays_to_regexps", function(){
  it("returns a string", function(){
    $r = CupcakeRouter::getInstance();
    $pattern = $r->arrays_to_regexps(array("path" => "/music/artist/:id(/:artist_name)"));
    
    ensure(is_string($pattern));
    $r->reset();
  });
  
  it("should match /music/artist/:id(/:artist_name) to /music/artist/5/coldplay", function(){
    $r = CupcakeRouter::getInstance();
    $pattern = $r->arrays_to_regexps(array("path" => "/music/artist/:id(/:artist_name)"));
    $match   = preg_match("/". $pattern ."/", "/music/artist/5/coldplay", $matches);
    
    ensure($match, "Failed to match route: /music/artist/5/coldplay using path: /music/artist/:id(/:artist_name) with pattern: $pattern");
    assert_equal($matches[0], "/music/artist/5/coldplay", "Failed to match path");
    assert_equal($matches[1], "5", "Failed to match :id");
    assert_equal($matches[2], "coldplay", "Failed to match :artist_name");
    $r->reset();
  });

  it("should match /music/artist/:id(/:artist_name) to /music/artist/5", function(){
    $r = CupcakeRouter::getInstance();
    $pattern = $r->arrays_to_regexps(array("path" => "/music/artist/:id(/:artist_name)"));
    $match   = preg_match("/". $pattern ."/", "/music/artist/5", $matches);
    
    ensure($match, "Failed to match route: /music/artist/5 using path: /music/artist/:id(/:artist_name) with pattern: $pattern");
    assert_equal($matches[0], "/music/artist/5", "Failed to match path");
    assert_equal($matches[1], "5", "Failed to match :id");
    //assert_equal($matches[2], "", "Failure, Tried to over match paths");
    assert_equal(2, count($matches));
    $r->reset();
  });
  
  it("should match /book(/:id) to /book", function(){
    CupcakeRouter::map(function($r){
      $r->connect("/book(/:id)", array("controller" => "store", "action" => "books"));
    });
    $r = CupcakeRouter::getInstance();
    $pattern = $r->arrays_to_regexps(array("path" => "/book(/:id)"));
    $match   = preg_match("/". $pattern ."/", "/book", $matches);
    
    ensure($match, "Failed to match route /book with path: /book(/:id) using pattern: $pattern");
    $r->reset();
  });
  
  it("should match /book(/:id) to /book/100", function(){
    $r = CupcakeRouter::getInstance();
    $pattern = $r->arrays_to_regexps(array("path" => "/book(/:id)"));
    $match   = preg_match("/". $pattern ."/", "/book/100", $matches);
    
    ensure($match, "Failed to match route /book/100 with path: /book(/:id) using pattern: $pattern");
    $r->reset();
  });    
});

describe("Router -> param_keys_for_path", function(){
  it("should extract :controller, :action, :id for /:controller/:action/:id", function(){
    $r = CupcakeRouter::getInstance();
    $params = $r->param_keys_for_path("/:controller/:action/:id");
    
    assert_equal($params[0], ":controller", "Failed to match :controller");
    assert_equal($params[1], ":action",     "Failed to match :action");
    assert_equal($params[2], ":id",         "Failed to match :id");
    assert_equal(count($params), 3,         "Failed to have 3 keys");
    $r->reset();    
  });  
  
  it("should extract :controller, :action for /:controller/:action", function(){
    $r = CupcakeRouter::getInstance();
    $params = $r->param_keys_for_path("/:controller/:action");
    
    assert_equal($params[0], ":controller", "Failed to match :controller");
    assert_equal($params[1], ":action",     "Failed to match :action");
    assert_equal(count($params), 2,         "Failed to have 2 keys");
    $r->reset();    
  });  
  
  it("should extract :action for /controller/:action", function(){
    $r = CupcakeRouter::getInstance();
    $params = $r->param_keys_for_path("/controller/:action");
    
    assert_equal($params[0], ":action", "Failed to match :action");
    assert_equal(count($params), 1,     "Failed to have 1 keys");
    $r->reset();    
  }); 
    
  it("should extract :id for /book/:id", function(){
    $r = CupcakeRouter::getInstance();
    $params = $r->param_keys_for_path("/book/:id");
    
    assert_equal($params[0], ":id", "Failed to match :id");
    $r->reset();    
  });
  
  it("should extract :id, :artist for /music/:id(/:artist)", function(){
    $r = CupcakeRouter::getInstance();
    $params = $r->param_keys_for_path("/music/:id(/:artist)");
    
    assert_equal($params[0], ":id",     "Failed to match :id");
    assert_equal($params[1], ":artist", "Failed to match :artist");
    assert_equal(count($params), 2,     "Failed to have 2 keys");
    $r->reset();    
  }); 
  
  it("returns an empty array for /:action", function(){
    $r = CupcakeRouter::getInstance();
    $params = $r->param_keys_for_path("/:action");
    assert_equal($params[0], ":action", "Failed to match :action");
    assert_equal(count($params), 1,     "Failed to have 1 key");
    $r->reset();   
  });
  
  it("returns an empty array for (/:action)", function(){
    $r = CupcakeRouter::getInstance();
    $params = $r->param_keys_for_path("(/:action)");
    assert_equal($params[0], ":action", "Failed to match :action");
    assert_equal(count($params), 1,     "Failed to have 1 key");
    $r->reset();   
  });
        
  it("returns an empty array for /", function(){
    $r = CupcakeRouter::getInstance();
    $params = $r->param_keys_for_path("/");
    assert_equal(count($params), 0,     "Failed to have 0 keys");
    $r->reset();   
  });
    
  it("returns an empty array", function(){
    $r = CupcakeRouter::getInstance();
    $params = $r->param_keys_for_path("");
    assert_equal(count($params), 0,     "Failed to have 0 keys");
    $r->reset();   
  });
});



describe("Router -> map_route_to_params", function(){
  it("maps route to params", function(){
    CupcakeRouter::map(function($r){
      $r->connect("/book/:id", array("controller" => "catalog", "action" => "show"));
    });
    $r = CupcakeRouter::getInstance();
    $params = $r->map_route_to_params("/book/500");
    
    assert_equal($params["controller"], "catalog");
    assert_equal($params["action"], "show");
    assert_equal($params["id"], "500");
    $r->reset();
  });

  it("params from to take precedence over request params", function(){
    CupcakeRouter::map(function($r){
      $r->connect("/book/:id", array("controller" => "catalog", "action" => "show", "id" => "25"));
    });
    $r = CupcakeRouter::getInstance();
    $params = $r->map_route_to_params("/book/5");
    
    assert_equal($params["controller"], "catalog");
    assert_equal($params["action"], "show");
    assert_equal($params["id"], "25");
    $r->reset();
  });

  it("should match some params", function(){
    CupcakeRouter::map(function($r){
      $r->connect("/book/:id(/:name)", array("controller" => "catalog", "action" => "show_book"));
    });
    $r = CupcakeRouter::getInstance();
    $params = $r->map_route_to_params("/book/5/LearnToRead");
    
    assert_equal($params["name"], "LearnToRead");
    assert_equal($params["id"], "5");
    $r->reset();
  });    

  it("should match params when having dashes", function(){
    CupcakeRouter::map(function($r){
      $r->connect("/new/:id(/:name)", array("controller" => "catalog", "action" => "show_book"));
    });
    $r = CupcakeRouter::getInstance();
    $params = $r->map_route_to_params("/new/5/learn-to-read");
    
    assert_equal($params["name"], "learn-to-read");
    $r->reset();
  });    
  
  it("should match all params when having dashes", function(){
    CupcakeRouter::map(function($r){
      $r->connect("/new/:id(/:name)", array("controller" => "catalog", "action" => "show_book"));
    });
    $r = CupcakeRouter::getInstance();
    $params = $r->map_route_to_params("/new/new-release/learn-to-read");
    
    assert_equal($params["name"], "learn-to-read");
    assert_equal($params["id"],"new-release");
    $r->reset();
  });

  it("should match all optional params", function(){
    CupcakeRouter::map(function($r){
      $r->connect("/artist/:id(/:name(/:page(/:sort_order)))", array("controller" => "catalog", "action" => "show_book"));
    });
    $r = CupcakeRouter::getInstance();
    $params = $r->map_route_to_params("/artist/1234/coldplay/1/recent");
    
    assert_equal($params["id"], "1234");
    assert_equal($params["name"], "coldplay");
    assert_equal($params["page"], "1");
    assert_equal($params["sort_order"], "recent");
    $r->reset();
  });
  
  it("should match all params in path", function(){
    CupcakeRouter::map(function($r){
      $r->connect("/artist/:id/:name/:page/:sort_order", array("controller" => "catalog", "action" => "show_book"));
    });
    $r = CupcakeRouter::getInstance();
    $params = $r->map_route_to_params("/artist/1234/coldplay/1/recent");
    
    assert_equal($params["id"], "1234");
    assert_equal($params["name"], "coldplay");
    assert_equal($params["page"], "1");
    assert_equal($params["sort_order"], "recent");
    $r->reset();
  });         
});


describe("Router -> route_path_to_regexp", function(){
  it("should return a regular expression", function(){
    $r = CupcakeRouter::getInstance();
    $pattern = $r->route_path_to_regexp("/artist/:id/:name");
    assert_equal(preg_match("/{$pattern}/", "/artist/100/coldplay"), true, "It should return a regular expression");
  });
  
  it("should return a regular expression with optional", function(){
    $r = CupcakeRouter::getInstance();
    $pattern = $r->route_path_to_regexp("/artist/:id(/:name)");
    assert_equal(preg_match("/{$pattern}/", "/artist/100"), true, "It should return a regular expression");
  });  
});


describe("Router -> remove_parenthesis", function(){
  it("should remove parentheses", function(){
    $r = CupcakeRouter::getInstance();
    $response = $r->remove_parenthesis("(:id)");
    assert_equal($response, ":id", "Failed to remove parentheses");
  });
  
  it("should remove left parenthese", function(){
    $r = CupcakeRouter::getInstance();
    $response = $r->remove_parenthesis("(:id");
    assert_equal($response, ":id", "Failed to remove parentheses");
  });
  
  it("should remove right parenthese", function(){
    $r = CupcakeRouter::getInstance();
    $response = $r->remove_parenthesis(":id)");
    assert_equal($response, ":id", "Failed to remove parentheses");
  });  
  
  it("should remove all parentheses", function(){
    $r = CupcakeRouter::getInstance();
    $response = $r->remove_parenthesis("/:controller(/:id(/:name))");
    assert_equal($response, "/:controller/:id/:name", "Failed to remove parentheses");    
  });
});

describe("NewRotuer -> is_param_key", function(){
  it("returns true when string begins with colon", function(){
    $r = CupcakeRouter::getInstance();
    $response = $r->is_param_key(":name");
    assert_equal($response, true, "Should return true for, :name");
  });
  
  it("returns false when string does not have colon", function(){
    $r = CupcakeRouter::getInstance();
    $response = $r->is_param_key("name");
    assert_equal($response, false, "Should return false for, name");    
  });
  
  it("returns false for name:", function(){
    $r = CupcakeRouter::getInstance();
    $response = $r->is_param_key("name:");
    assert_equal($response, false);    
  });
  
  it("returns false for na:me", function(){
    $r = CupcakeRouter::getInstance();
    $response = $r->is_param_key("na:me");
    assert_equal($response, false);    
  });    
});

describe("Creates an Array of Routes", function(){
  before(function(){
    CupcakeRouter::map(function($r){
      $r->connect("/", array("controller" => "application", "action" => "show"));
      $r->show_books("/", array("controller" => "application", "action" => "view_books"));
    });
    return CupcakeRouter::getInstance();
  });
  
  it("has two routes", function($router){
    assert_equal(count($router->routes), 2);
  });
  
  it("has one named route", function($r){
    $route = array();
    foreach($r->routes as $routes) {
      if(!empty($routes["name"])) {
        $route = $routes;
        break;
      }
    }
    
    assert_equal("show_books", $route["name"]);
    assert_equal("application", $route["params"]["controller"]);
    assert_equal("view_books", $route["params"]["action"]);
    assert_equal("/", $route["path"]);
  });
});


?>