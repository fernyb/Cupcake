<?php

function remote_form_for($name, $object=null, $url="", $options=null, $block=null) {
  if(is_closure($options)) {
    $block = $options;
    $options = array();
  }
  
  $options = array_merge(array("onsubmit" => "remoteFormSubmit(this); return false;"), $options);
  form_for($name, $object, $url, $options, $block);
}

?>