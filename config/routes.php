<?php

/*
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
*/

Router::map(function($r){
  $r->root("/", array("controller" => "application", "action" => "show"));
  
  $r->the_name_format("/the_name/:action.:format", array("controller" => "application"));
  
  $r->api_recent("/api/recent.:format", array("controller" => "application", "action" => "api_recent"));
  
  $r->the_name("/the_name", array("controller" => "application", "action" => "the_name"));
  
  $r->session_set("/artist/session_set", array("controller" => "application", "action" => "session_set"));


  $r->redirect("/artist/redirect", array("controller" => "application", "action" => "redirect"));
  
  $r->flash_example("/artist/flash_example", array("controller" => "application", "action" => "flash_example"));
  
  $r->flash_show("/artist/flash_show", array("controller" => "application", "action" => "flash_show"));
  
  $r->my_layout("/artist/my_layout", array("controller" => "application", "action" => "my_layout"));
  
  
  $r->html_form("/artist/html_form", array("controller" => "application", "action" => "html_form"));
  
  $r->artist("/artist/:artist", array("controller" => "application", "action" => "show"));
  
  $r->show_book("/book(/:id)", array("controller" => "application", "action" => "show"));
  
  $r->user_profile("/user/profile", array("controller" => "application", "action" => "profile"));
  
  $r->app("/app/:action", array("controller" => "application"));
  
  $r->connect("/app/:action.:format", array("controller" => "application"));
  
  $r->connect("/:controller/:action(/:id)");
});


?>