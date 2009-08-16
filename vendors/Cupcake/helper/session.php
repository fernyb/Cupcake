<?php

function session_set($key, $value) {
  Session::getInstance()->set($key, $value);
}

function session_get($key) {
  return Session::getInstance()->get($key);
}

?>