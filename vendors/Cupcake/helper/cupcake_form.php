<?php

class CupcakeHelperForm {
  public static $instance = false;
  public $content = "";
  public $object_name;
  public $object;
  
  public static function &getInstance() {
    if(!self::$instance) {
      self::$instance = new self();
    }
    return self::$instance;
  }
  
  public function form_for($name, $object=null, $url="", $options=null, $block=null) {
    if(is_closure($options)) {
      $block   = $options;
      $options = array();
    }
    $this->object      = $object;    
    $this->object_name = $name;
    echo form_tag($url, array_merge($options, array("name" => $name)));
    echo $block($this);
    echo "</form>";
  }
  
  private function value_for($method) {
    $value = "";
    if(is_array($this->object) && array_key_exists($method, $this->object)) {
      $value = $this->object[$method];
    } else if(is_object($this->object) && 
    array_key_exists($method, get_class_methods(array_flip($this->object)) )) {
      $value = $this->object->{$method}();
    }
    return $value;
  }
  
  private function object_method($method) {
    $method      = strtolower($method);
    $object_name = strtolower($this->object_name);
    $object_name = preg_replace("/\[\]/", "", $object_name);
    $object_name = preg_replace("/\[/", "_", $object_name);
    $object_name = preg_replace("/\]/", "", $object_name);
    return array($object_name, $method);
  } 
  
  public function hidden_field($method, $options=array()) {
    $value = $this->value_for($method);
    list($object_name, $method) = $this->object_method($method);
    $defaults = array("type" => "hidden", "id" => "{$object_name}_{$method}", "name" => "{$object_name}[$method]", "value" => "");
    return content_tag("input", null, array_merge($defaults, $options));
  }
  
  public function label($method, $text=null, $attributes=array()) {
    list($object_name, $method) = $this->object_method($method);
    $defaults = array("for" => "{$object_name}_{$method}");
    if(empty($text)) {
      $text = ucfirst($method);
    }
    return content_tag("label", $text, array_merge($defaults, $attributes));
  }

  public function check_box($method, $options=array(), $checked_value="1", $unchecked_value="0") {
    $value = $this->value_for($method);
    list($object_name, $method) = $this->object_method($method);
    $defaults = array("name" => "{$object_name}[{$method}]", "value" => "{$checked_value}");
    
    $checkbox_defaults = array_merge($defaults, array("type" => "checkbox", "id" => "{$object_name}_{$method}"));
    $tag = content_tag("input", null, $checkbox_defaults);
    
    $hidden_defaults = array_merge($defaults, array("type" => "hidden", "value" => "{$unchecked_value}"));
    $hidden_tag = content_tag("input", null, $hidden_defaults);
    
    $html_tags = $tag ."\n". $hidden_tag;
    return $html_tags;
  }
  
  public function file_field($method, $options=array()) {
    list($object_name, $method) = $this->object_method($method);
    $defaults = array("type" => "file", "id" => "{$object_name}_{$method}", "name" => "{$object_name}[{$method}]");
    $attributes = array_merge($defaults, $options);
    return content_tag("input", null, $attributes);
  }

  public function radio_button($method, $tag_value=null, $options=array()) {
    $value = $this->value_for($method);
    list($object_name, $method) = $this->object_method($method);
    $defaults = array("type" => "radio", "id" => "{$object_name}_{$method}", "name" => "{$object_name}[{$method}]", "value" => "{$value}");
    if($value === $tag_value) {
      $defaults = array_merge($defaults, array("checked" => "checked"));
    }
    $attributes = array_merge($defaults, $options);
    return content_tag("input", null, $attributes);
  }
   
  public function text_area($method, $options=array()) {
    $value = $this->value_for($method);
    list($object_name, $method) = $this->object_method($method);
    $defaults = array("id" => "{$object_name}_{$method}", "name" => "{$object_name}[{$method}]", "rows" => "8", "cols" => "40");
    $attributes = array_merge($defaults, $options);
    return content_tag("textarea", $value, $attributes);
  } 
  
  public function password_field($method, $options=array()) {
    return $this->text_field($method, array_merge($options, array("type" => "password")));
  }
    
  public function text_field($method, $options=array()) {
    $value = $this->value_for($method);
    list($object_name, $method) = $this->object_method($method);
    $defaults = array("type" => "text", "id" => "{$object_name}_{$method}", "name" => "{$object_name}[{$method}]", "value" => "{$value}");
    $attributes = array_merge($defaults, $options);
    return content_tag("input", null, $attributes);
  }
}


function form_tag($url, $options=array()) {
  if(array_key_exists("multipart", $options) && $options["multipart"] === true) {
    if(empty($options["method"])) {
      $options["method"] = "post";
    }
    $options["enctype"] = "multipart/form-data";
  } else {
    $options["method"] = "get";
  }
  $new_options = array();
  foreach($options as $k => $v) {
    if($k !== "multipart") {
      $new_options[$k] = $v;
    }
  }
  $options = $new_options;
  unset($new_options);
  $attributes = join(" ", to_attributes($options));
  
  $tag = "<form action=\"{$url}\"";
  if(strlen($attributes) > 0) {
    $tag .= " {$attributes}";
  }
  $tag .= ">";
    
  return $tag;
}


function form_for($name, $object, $url, $options=array(), $block=nil) {
  $h = CupcakeHelperForm::getInstance();
  if(is_closure($options)) {
    $block = $options;
    $options = array();
  }
  return $h->form_for($name, $object, $url, $options, $block);
}

function fields_for($record_or_name_of_array, $options=array(), $block=null) {
  if(is_closure($options)) {
    $block = $options;
    $options = array();
  }
  $h = new CupcakeHelperForm();
  foreach($options as $index => $object) {
    $h->object      = $object;    
    $h->object_name = $record_or_name_of_array;
    $block($h, $object);
  }
}

function submit_tag($name="", $options=array()) {
  $defaults = array("name" => "commit", "type" => "submit", "value" => $name);
  $attributes = array_merge($defaults, $options);
  return content_tag("input", null, $attributes);
}


?>