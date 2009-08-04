<?php

class Application extends Controller {
  
  function show() {
  }
  
  function profile() {
    $this->set("name",  "fernyb");
    $this->set("age",   "24");
    $this->set("city",  "Pico Rivera");
    $this->set("state", "California");
    $this->render(array("action" => "user_profile"));
  }
}

?>