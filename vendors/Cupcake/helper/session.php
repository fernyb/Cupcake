<?php

function session_set($key, $value) {
  CupcakeSession::getInstance()->set($key, $value);
}

function session_get($key) {
  return CupcakeSession::getInstance()->get($key);
}

?>