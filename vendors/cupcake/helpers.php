<?php

function is_closure($object) {
  return (is_callable($object) && is_object($object));
}

function to_attributes($attr=array()) {
  $attributes = array();  
  foreach($attr as $k => $v) {
    array_push($attributes, "{$k}=\"{$v}\"");
  }
  return $attributes;
}

function content_tag($tag_name, $content=nil, $attr=array()) {
  $attributes = to_attributes($attr);
  if($content === nil || $content === null) {
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

function image_tag($image_name, $options=array()) {
  $suffix = explode(".", $image_name);
  $suffix = end(array_values($suffix));
  $attributes = array("alt" => basename($image_name, ".{$suffix}"), "src" => "/images/" . $image_name);
  $attributes = array_merge($attributes, $options);
  return content_tag("img", nil, $attributes);
}

function truncate($string, $length=30, $truncate_string="...") {
  if(strlen($string) < $length) return $string;
  $length = $length - count(preg_split("//", $truncate_string, null, PREG_SPLIT_NO_EMPTY));
  return substr($string, 0, $length) . $truncate_string;
}

function url($name, $options=array()) {
  return Router::url($name, $options);
}

function link_to($name, $link, $options=array()) {
  $attributes = array_merge(array("href" => $link), $options);
  return content_tag("a", $name, $attributes);
}


?>