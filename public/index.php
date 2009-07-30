<?php
define('DIR_PATH', dirname(__FILE__));
define("VENDOR_CUPCAKE_DIR", realpath(dirname(__FILE__) . "/../vendors/cupcake"));
define("STYLESHEETS_PATH",   realpath(dirname(__FILE__) . "/stylesheets"));
define("TEMP_DIR_PATH",      realpath(dirname(__FILE__) . "/../tmp"));


require_once VENDOR_CUPCAKE_DIR . "/new_dispatcher.php";
require_once VENDOR_CUPCAKE_DIR . "/router.php";


Router::prepare(function($r){
  $r->match("/")->to(array("controller" => "application", "action" => "index"));  
  $r->match("/book(/:id)")->to(array("controller" => "book", "action" => "details_show"));
});

$new_dispatcher = new NewDispatcher(Router::getInstance());
$new_dispatcher->dispatch($_SERVER);


?>