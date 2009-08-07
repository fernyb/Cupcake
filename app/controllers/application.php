<?php

class Application extends Controller {
  public $before_filter = array(
      array("set_some_text"),
      array("set_some_text_only", "only" => "html_form")
    );
  
  public $after_filter = array(
      array("set_after_filter_text")
    );
  
  function show() {
  }
  
  function profile() {
    $this->set("name",  "fernyb");
    $this->set("age",   "24");
    $this->set("city",  "Pico Rivera");
    $this->set("state", "California");
    $this->render(array("action" => "user_profile"));
  }
  
  function redirect() {
    $this->redirect_to(url("root"));
  }
  
  function my_layout() {
    $this->render(array("action" => "my_profile", "layout" => "my_layout"));
  }
  
  function html_form() {
  }
  
  function set_some_text() {
    $this->set("set_some_text", "yes");
  }
  
  function set_some_text_only() {
    $this->set("set_some_text_only", "yes");
    $this->set("artist_name", "Coldplay");
  }
  
  function set_after_filter_text() {
    $this->set("set_after_filter", "yes");
    $this->set("album_name", "X&Y");
  }
}

?>