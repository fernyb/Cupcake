<?php
define('DIR_PATH', dirname(__FILE__));
define("VENDOR_CUPCAKE_DIR", realpath(dirname(__FILE__) . "/../vendors/cupcake"));
define("STYLESHEETS_PATH",   realpath(dirname(__FILE__) . "/stylesheets"));
define("TEMP_DIR_PATH",      realpath(dirname(__FILE__) . "/../tmp"));


require_once VENDOR_CUPCAKE_DIR . "/router.php";


Router::prepare(function($r){
  $r->match("/")->to(array("controller" => "application", "action" => "index"));  
  #$r->match("/book(/:id)")->to(array("controller" => "book", "action" => "details_show"));
});

function base_path($uri) {
 if(strpos($uri, "?") > 0) {
   return substr($uri, 0, strpos($uri, "?"));
  }
  return $uri;
}

$uri = $_SERVER['REQUEST_URI'];  

$router = Router::getInstance();

$params = $router->find_route($uri);

var_dump($params);


?>