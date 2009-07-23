<?php

define('DIR_PATH', dirname(__FILE__));

if(!defined("MVC_ROOT")) {
  define('MVC_ROOT', DIR_PATH . "/../app/");
}

define("CONFIGS", dirname(__FILE__) . "/../config/");
define("APP_BASE_URL", dirname(__FILE__) . "/../app");
define("DS", DIRECTORY_SEPARATOR);
define("APP_DIR", "app");
define("WEBROOT_DIR", "public");
define("VENDOR_CUPCAKE_DIR", realpath(dirname(__FILE__) . "/../vendors/cupcake"));
define("STYLESHEETS_PATH", realpath(dirname(__FILE__) . "/stylesheets"));


require_once VENDOR_CUPCAKE_DIR . "/basics.php";    
require_once VENDOR_CUPCAKE_DIR . "/inflector.php";
require_once VENDOR_CUPCAKE_DIR . "/helpers.php";
require_once VENDOR_CUPCAKE_DIR . "/view.php";
require_once VENDOR_CUPCAKE_DIR . "/dispatcher.php";
require_once VENDOR_CUPCAKE_DIR . "/router.php";


$Dispatcher = new Dispatcher();
$Dispatcher->dispatch($_REQUEST['url']);

?>