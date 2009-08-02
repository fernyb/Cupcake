<?php
define('DIR_PATH', dirname(__FILE__));
define("VENDOR_CUPCAKE_DIR", realpath(dirname(__FILE__) . "/../vendors/cupcake"));
define("STYLESHEETS_PATH",   realpath(dirname(__FILE__) . "/stylesheets"));
define("TEMP_DIR_PATH",      realpath(dirname(__FILE__) . "/../tmp"));

define("ROOT_PATH", realpath(dirname(__FILE__) . "/../"));
define("APP_DIR",        ROOT_PATH ."/app");
define("CONTROLLER_DIR", APP_DIR   ."/controllers");
define("VIEW_DIR",       APP_DIR   ."/views");
define("HELPER_DIR",     APP_DIR   ."/helpers");


require_once VENDOR_CUPCAKE_DIR . "/inflector.php";
require_once VENDOR_CUPCAKE_DIR . "/import.php";
require_once VENDOR_CUPCAKE_DIR . "/dispatcher.php";
require_once VENDOR_CUPCAKE_DIR . "/controller.php";
require_once VENDOR_CUPCAKE_DIR . "/controller_exception.php";
require_once VENDOR_CUPCAKE_DIR . "/view.php";
require_once VENDOR_CUPCAKE_DIR . "/router.php";


Router::prepare(function($r){
  $r->match("/")->to(array("controller" => "application", "action" => "show"));  
  $r->match("/book(/:id)")->to(array("controller" => "book", "action" => "details_show"));
  $r->match("/:controller/:action(/:id)")->to();
});

Dispatcher::handle($_SERVER);

?>