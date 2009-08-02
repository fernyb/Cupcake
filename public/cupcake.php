<?php
if(!defined("ROOT_PATH")) {
  define("ROOT_PATH", realpath(dirname(__FILE__) . "/../"));
}

define("APP_DIR",            ROOT_PATH   ."/app");
define("CONTROLLER_DIR",     APP_DIR     ."/controllers");
define("VIEW_DIR",           APP_DIR     ."/views");
define("HELPER_DIR",         APP_DIR     ."/helpers");
define("PUBLIC_DIR",         ROOT_PATH   ."/public");
define("STYLESHEETS_DIR",    PUBLIC_DIR  ."/stylesheets");
define("JAVASCRIPTS_DIR",    PUBLIC_DIR  ."/javascripts");
define("VENDORS_DIR",        ROOT_PATH   ."/vendors");
define("VENDOR_CUPCAKE_DIR", VENDORS_DIR . "/cupcake");


require_once VENDOR_CUPCAKE_DIR . "/basics.php";
require_once VENDOR_CUPCAKE_DIR . "/inflector.php";
require_once VENDOR_CUPCAKE_DIR . "/import.php";
require_once VENDOR_CUPCAKE_DIR . "/dispatcher.php";
require_once VENDOR_CUPCAKE_DIR . "/helpers.php";
require_once VENDOR_CUPCAKE_DIR . "/controller.php";
require_once VENDOR_CUPCAKE_DIR . "/controller_exception.php";
require_once VENDOR_CUPCAKE_DIR . "/view.php";
require_once VENDOR_CUPCAKE_DIR . "/router.php";


?>