<?php
require_once "cupcake.php";

Router::prepare(function($r){
  $r->match("/")->to(array("controller" => "application", "action" => "show"));  
  $r->match("/book(/:id)")->to(array("controller" => "book", "action" => "details_show"));
  $r->match("/:controller/:action(/:id)")->to();
});

Dispatcher::handle($_SERVER);

?>