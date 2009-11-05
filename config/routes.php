<?php

CupcakeRouter::map(function($r){
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