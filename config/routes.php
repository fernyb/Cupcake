<?php


Router::prepare(function($r){
  $r->match("/")->to(array("controller" => "application", "action" => "show"))->name("root");  
  
  $r->match("/the_name/:action.:format")->to(array("controller" => "application"))->name("the_name_format");
  
  $r->match("/api/recent.:format")->to(array("controller" => "application", "action" => "api_recent"))->name("api_recent");
  
  $r->match("/the_name")->to(array("controller" => "application", "action" => "the_name"))->name("the_name");
  
  $r->match("/artist/session_set")->to(array("controller" => "application", "action" => "session_set"))->name("session_set");
  
  $r->match("/artist/session_values")->to(array("controller" => "application", "action" => "session_values"))->name("session_values");
    
  $r->match("/artist/redirect")->to(array("controller" => "application", "action" => "redirect"))->name("redirect");
  
  $r->match("/artist/flash_example")->to(array("controller" => "application", "action" => "flash_example"))->name("flash_example");
  
  $r->match("/artist/flash_show")->to(array("controller" => "application", "action" => "flash_show"))->name("flash_show");
  
  $r->match("/artist/my_layout")->to(array("controller" => "application", "action" => "my_layout"))->name("my_layout");
  
  $r->match("/artist/html_form")->to(array("controller" => "application", "action" => "html_form"))->name("html_form");
  
  $r->match("/artist/:artist")->to(array("controller" => "application", "action" => "show"))->name("artist");
  
  $r->match("/book(/:id)")->to(array("controller" => "application", "action" => "show"))->name("show_book");
  
  $r->match("/user/profile")->to(array("controller" => "application", "action" => "profile"))->name("user_profile");
  
  $r->match("/app/:action")->to(array("controller" => "application"))->name("app");
  $r->match("/app/:action.:format")->to(array("controller" => "application"));
  
  $r->match("/:controller/:action(/:id)")->to();
});



?>