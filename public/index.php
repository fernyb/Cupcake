<?php
define('DIR_PATH', dirname(__FILE__));

if(!defined("MVC_ROOT")) {
  define('MVC_ROOT', DIR_PATH . "/../app/");
}

define("CONFIGS", dirname(__FILE__) . "/../config/");
define("APP_BASE_URL", dirname(__FILE__) . "/../app");
define("DS", DIRECTORY_SEPARATOR);
define("APP_DIR", "app");
define("WEBROOT_DIR", "public");
define("VENDOR_CUPCAKE_DIR", realpath(dirname(__FILE__) . "/../vendors/cupcake"));
define("STYLESHEETS_PATH", realpath(dirname(__FILE__) . "/stylesheets"));
define("TEMP_DIR_PATH", realpath(dirname(__FILE__) . "/../tmp"));


require_once VENDOR_CUPCAKE_DIR . "/basics.php";    
require_once VENDOR_CUPCAKE_DIR . "/inflector.php";
require_once VENDOR_CUPCAKE_DIR . "/helpers.php";
require_once VENDOR_CUPCAKE_DIR . "/view.php";

require_once VENDOR_CUPCAKE_DIR . "/dispatcher.php";
require_once VENDOR_CUPCAKE_DIR . "/router.php";

/**
* Almost completed replacing the router and dispatcher
*/

require_once VENDOR_CUPCAKE_DIR . "/new_dispatcher.php";
require_once VENDOR_CUPCAKE_DIR . "/new_router.php";

#$Dispatcher = new Dispatcher();
#$Dispatcher->dispatch($_SERVER['REQUEST_URI']);


NewRouter::prepare(function($r){
  $r->match("/")->to(array("controller" => "application", "action" => "index"));
  
  $r->match("/book")->to(array("controller" => "book", "action" => "main_show"));
  $r->match("/book/:id")->to(array("controller" => "book", "action" => "details_show"));
  
  $r->match("/public/:action/:page/:name/:id")->to(array("controller" => "application", "action" => "show_action"));
  $r->match("/profile/:user/:feature/:id")->to(array("controller" => "user", "action" => "profile"));
  $r->match("/public/page/:action")->to(array("controller" => "public", "action" => "page_show"));
});


$new_dispatcher = new NewDispatcher(NewRouter::getInstance());
$new_dispatcher->dispatch($_SERVER);


?>