<?php 
  function __match($cached_path) { 
 if ( preg_match("/^\/$/", $cached_path) ) { 
    return array(0, array("controller" => "application","action" => "index"));
 } 
 else if ( preg_match("/^\/public\/([^\/.,;?]+)$/", $cached_path) ) { 
    return array(1, array("controller" => "application","action" => "show"));
 } 
 else if ( preg_match("/^\/publicprofile\/([^\/.,;?]+)(?:\/([^\/.,;?]+))(?:\/([^\/.,;?]+))$/", $cached_path) ) { 
    return array(2, array("controller" => "user","action" => "profile"));
 } 
 else if ( preg_match("/^\/publicpage\/([^\/.,;?]+)$/", $cached_path) ) { 
    return array(3, array("controller" => "public","action" => "page_show"));
 } 
    return false; 
  } 
?>