<?php

class ControllerException extends Controller {
  
  public function not_found() {
    $this->set("name", "John Doe");
  }
}

?>