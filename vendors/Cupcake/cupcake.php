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

define("DS",                 DIRECTORY_SEPARATOR);
define("APP_DIR",            ROOT_PATH   . DS . "app");
define("CONTROLLER_DIR",     APP_DIR     . DS . "controllers");
define("VIEW_DIR",           APP_DIR     . DS . "views");
define("HELPER_DIR",         APP_DIR     . DS . "helpers");
define("PUBLIC_DIR",         ROOT_PATH   . DS . "public");
define("CONFIG_DIR",         ROOT_PATH   . DS . "config");
define("LOG_DIR",            ROOT_PATH   . DS . "log");
define("STYLESHEETS_DIR",    PUBLIC_DIR  . DS . "stylesheets");
define("JAVASCRIPTS_DIR",    PUBLIC_DIR  . DS . "javascripts");
define("VENDORS_DIR",        ROOT_PATH   . DS . "vendors");
define("VENDOR_CUPCAKE_DIR", VENDORS_DIR . DS . "Cupcake");


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
  "cupcake_view",
  "router",
  "cupcake_dispatcher_test",
  "cupcake_request"
);


foreach($dependencies as $file) {
  if(file_exists(VENDOR_CUPCAKE_DIR ."/". $file .".php")) {
    $file = VENDOR_CUPCAKE_DIR . DS . $file .".php";
  } else {
    $file = CUPCAKE_FRAMEWORK_PATH . DS . $file .".php";
  }
  if(file_exists($file)) {
    require_once $file;
  } else {
    throw new Exception("Cupcake Core File Not Found: {$file}");
  }
}

?>