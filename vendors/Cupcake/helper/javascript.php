<?php

function remote_form_for($name, $object=null, $url="", $options=null, $block=null) {
  if(is_closure($options)) {
    $block = $options;
    $options = array();
  }
  
  $options = array_merge(array("onsubmit" => "jQuery.cupcake.remoteFormSubmit(this); return false;"), $options);
  form_for($name, $object, $url, $options, $block);
}

function link_to_remote($name, $url, $options=array()) {
  return link_to($name, $url, array_merge(
      array("onclick" => "jQuery.cupcake.linkToRemote(this); return false;"),
      $options
    ));
}

?>