<?php

function flash($key, $value=null) {
  if($value === null) {
    return $_COOKIE["flash_{$key}"];  
  } else {
    setcookie("flash_{$key}", $value);
  }
}


?>