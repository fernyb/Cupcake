<?php

function __to_attributes($attr=array()) {
  $attributes = array();  
  foreach($attr as $k => $v) {
    array_push($attributes, "{$k}=\"{$v}\"");
  }
  return $attributes;
}


function content_tag($tag_name, $content=nil, $attr=array()) {
  $attributes = __to_attributes($attr);
  if($content == nil) {
    $html = "<{$tag_name} " . join(" ", $attributes) . " />";
  } else {
    $tag_options = " " . join(" ", $attributes);
    if(count($attributes) == 0) {
      $tag_options = trim($tag_options);
    }
    $html = "<{$tag_name}{$tag_options}>{$content}</{$tag_name}>";
  }
  return $html;
}


function stylesheet_link_tag($stylesheet, $options=array()) {
  $default = array("href" => "", "media" => "screen", "rel" => "stylesheet", "type" => "text/css");
  $default = array_merge($default, array("href" => "/stylesheets/" . $stylesheet . ".css"));
  $attributes = array_merge($default, $options);
  return content_tag("link", nil, $attributes);
} 


function javascript_include_tag($javascript_file) {
   $attributes = array("type" => "text/javascript", "src" => "/javascripts/". $javascript_file .".js");
  return content_tag("script", "",  $attributes);
}


?>