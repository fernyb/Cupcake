<?php
if(!defined("ROOT_PATH")) {
  define("ROOT_PATH", realpath(dirname(__FILE__) . "/../../"));
}

define("APP_DIR",            ROOT_PATH   ."/app");
define("CONTROLLER_DIR",     APP_DIR     ."/controllers");
define("VIEW_DIR",           APP_DIR     ."/views");
define("HELPER_DIR",         APP_DIR     ."/helpers");
define("PUBLIC_DIR",         ROOT_PATH   ."/public");
define("CONFIG_DIR",         ROOT_PATH   ."/config");
define("LOG_DIR",            ROOT_PATH   ."/log");
define("STYLESHEETS_DIR",    PUBLIC_DIR  ."/stylesheets");
define("JAVASCRIPTS_DIR",    PUBLIC_DIR  ."/javascripts");
define("VENDORS_DIR",        ROOT_PATH   ."/vendors");
define("VENDOR_CUPCAKE_DIR", VENDORS_DIR . "/cupcake");

$dependencies = array(
  "config",
  "logger",
  "basics",
  "inflector",
  "import",
  "dispatcher",
  "helpers",
  "helper/form",
  "helper/flash",
  "helper/session",
  "response/xml",
  "session",
  "session/cookie_store",
  "controller",
  "controller_exception",
  "view",
  "router"
);

foreach($dependencies as $file) {
  require_once VENDOR_CUPCAKE_DIR ."/". $file .".php";
}

?>