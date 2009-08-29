<?php

class Application extends Controller {
  public $before_filter = array(
      array("set_some_text_only", "only" => "html_form"),
      array("set_some_text",      "skip" => "show")
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
    $this->render_action("user_profile");
  }
  
  function flash_example() {
    flash("notice", "Hello World");
  }
  
  function flash_show() {
    // should display flash message on screen
    // when page is refreshed it should not display the message
  }
  
  function api_recent() {
    switch($this->params["format"]) {
      case "xml" :
        $link = link_to("Try With JSON", url("the_name_format", array("action" => "api_recent", "format" => "json")));
        $this->render_text("Response for XML Format: {$link}");
      break;
      case "json" :
        $link = link_to("Try With XML", url("the_name_format", array("action" => "api_recent", "format" => "xml")));
        $this->render_text("Response for JSON Response: {$link}");
      break;
    }
  }
  
  function session_set() {
    session_set("name", "fernyb");
    session_set("id", "250");
  }
  
  function redirect() {
    $this->redirect_to(url("root"));
  }
  
  function my_layout() {
    $this->render_action("my_profile", array("layout" => "my_layout"));
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
  
  function ajax() {
    if($this->xhr()) {
      $this->set("ajax_request", true);
    } else {
      $this->set("ajax_request", false);
    }
    $this->set("varname", "Value from Controller Method!");
    $this->set("format", $this->params["format"]);
    
    $user = $this->params["user"];
    $this->set("introduce", "Hello, ". $user["name"] ." have a good day!");
    
    #if($this->params["format"] === "json" || $this->params["format"] === "iphone") {
    #  $this->render_template("ajax.html.php");
    #}
  }
}

?>