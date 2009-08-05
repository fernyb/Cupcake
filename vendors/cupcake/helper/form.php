<?php

class HelperForm {
  public function form_for($name, $object, $url, $block) {
  }
  
  public function hidden_field() {
  }
  
  public function label() {
  }

  public function checkbox() {
  }
  
  public function fields_for() {
  }
  
  public function file_field() {
  }
  
  public function password_field() {
  }
  
  public function radio_button() {
  }
   
  public function text_area() {
  } 
  
  public function text_field($field_name) {
  }
}

function submit_tag($name="", $options=array()) {
  $defaults = array("name" => "commit", "type" => "submit", "value" => $name);
  $attributes = array_merge($defaults, $options);
  return content_tag("input", null, $attributes);
}

?>