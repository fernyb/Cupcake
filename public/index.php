<?php
define('DIR_PATH', dirname(__FILE__));
define("VENDOR_CUPCAKE_DIR", realpath(dirname(__FILE__) . "/../vendors/cupcake"));
define("STYLESHEETS_PATH",   realpath(dirname(__FILE__) . "/stylesheets"));
define("TEMP_DIR_PATH",      realpath(dirname(__FILE__) . "/../tmp"));


require_once VENDOR_CUPCAKE_DIR . "/new_dispatcher.php";
require_once VENDOR_CUPCAKE_DIR . "/router.php";


NewRouter::prepare(function($r){
  $r->match("/")->to(array("controller" => "application", "action" => "index"));  
  $r->match("/book(/:id)")->to(array("controller" => "book", "action" => "details_show"));
  $r->match("/public/:action/:page/:name/:id")->to(array("controller" => "application", "action" => "show_action"));
  $r->match("/profile/:user/:feature/:id")->to(array("controller" => "user", "action" => "profile"));
  $r->match("/public/page/:action")->to(array("controller" => "public", "action" => "page_show"));
});

$new_dispatcher = new NewDispatcher(NewRouter::getInstance());
$new_dispatcher->dispatch($_SERVER);


?>