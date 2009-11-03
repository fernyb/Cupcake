<?php
#
# ROOT_PATH is the base path to the web application
#
if(!defined("ROOT_PATH")) {
  define("ROOT_PATH", realpath(dirname(__FILE__)));
}

#
# CUPCAKE_FRAMEWORK_PATH is the path to the framework
#
if(!defined("CUPCAKE_FRAMEWORK_PATH")) {
  define("CUPCAKE_FRAMEWORK_PATH", dirname(__FILE__));
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
define("VENDOR_CUPCAKE_DIR", VENDORS_DIR ."/Cupcake");


$dependencies = array(
  "cupcake_config",
  "cupcake_logger",
  "basics",
  "inflector",
  "cupcake_import",
  "http/mime_types",
  "http/header",  
  "cupcake_dispatcher",
  "helpers",
  "helper/cupcake_form",
  "helper/flash",
  "helper/session",
  "helper/css_selector",
  "helper/test_helper",
  "response/xml",
  "response/json",
  "cupcake_session",
  "session/cookie_store",
  "cupcake_controller",
  "view",
  "router",
  "cupcake_dispatcher_test",
  "cupcake_request"
);


foreach($dependencies as $file) {
  if(file_exists(VENDOR_CUPCAKE_DIR ."/". $file .".php")) {
    $file = VENDOR_CUPCAKE_DIR ."/". $file .".php";
  } else {
    $file = CUPCAKE_FRAMEWORK_PATH ."/". $file .".php";
  }
  if(file_exists($file)) {
    require_once $file;
  } else {
    throw new Exception("Cupcake Core File Not Found: {$file}");
  }
}

?>