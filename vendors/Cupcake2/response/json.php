<?php

class JSONResponse {
  private $data;
  
  public function __construct($data) {
    $this->data = $data;
  }
  
  public function to_json() {
    return json_encode($this->data, JSON_FORCE_OBJECT);
  }
}

?>