<?php

function flash($key, $value=null) {
  if($value === null) {
    setcookie("flash_{$key}", "", time() - (5 * DAY), "/");
    return $_COOKIE["flash_{$key}"];  
  } else {
    Logger::info("Flash: flash_{$key} => {$value}");
    setcookie("flash_{$key}", $value, time() + (1 * DAY), "/");
  }
}


?>