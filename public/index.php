<?php
define('DIR_PATH', dirname(__FILE__));
define("VENDOR_CUPCAKE_DIR", realpath(dirname(__FILE__) . "/../vendors/cupcake"));
define("STYLESHEETS_PATH",   realpath(dirname(__FILE__) . "/stylesheets"));
define("TEMP_DIR_PATH",      realpath(dirname(__FILE__) . "/../tmp"));


require_once VENDOR_CUPCAKE_DIR . "/router.php";


Router::prepare(function($r){
  $r->match("/")->to(array("controller" => "application", "action" => "index"));  
  $r->match("/book(/:id)")->to(array("controller" => "book", "action" => "details_show"));
  $r->match("/:controller/:action(/:id)")->to();
});


function base_path($uri) {
 if(strpos($uri, "?") > 0) {
   return substr($uri, 0, strpos($uri, "?"));
  }
  return $uri;
}

$router = Router::getInstance();

$uri = base_path($_SERVER['REQUEST_URI']);  

$params = $router->find_route($uri);

echo "<h3>Request URI:</h3>";
echo $uri ."<br />";

var_dump($params);



?>