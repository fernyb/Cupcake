<?php
require_once "cupcake.php";

Router::prepare(function($r){
  $r->match("/")->to(array("controller" => "application", "action" => "show"));  
  
  $r->match("/artist/:artist")->to(array("controller" => "application", "action" => "show"))->name("artist");
  
  $r->match("/book(/:id)")->to(array("controller" => "application", "action" => "show"))->name("show_book");
  
  $r->match("/:controller/:action(/:id)")->to();
});


Dispatcher::run();

?>