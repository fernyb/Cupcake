<?php

function content_tag($tag_name, $content=nil, $attributes=array()) {
  if($content == nil) {
    $html = "<{$tag_name} " . join(" ", $attributes) . " />";
  } else {
      if(count($attributes) > 0) {
       $tag = "<{$tag_name} ". join(" ", $attributes) .">";
      } else {
        $tag = "<{$tag_name}>";
      }
    $html = $tag . $content ."</{$tag_name}>";
  }
  return $html;
}

function stylesheet_link_tag($stylesheet, $options=array()) {
  $default = array("href" => "", "media" => "screen", "rel" => "stylesheet", "type" => "text/css");
  $default = array_merge($default, array("href" => "/stylesheets/" . $stylesheet . ".css"));
  $defaults = array_merge($default, $options);
  $attributes = array();
  foreach($default as $k => $v) {
    array_push($attributes, "{$k}=\"{$v}\"");
  }
  return content_tag("link", nil, $attributes);
} 



?>