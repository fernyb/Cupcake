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


require_once dirname(__FILE__) . "/../vendors/cupcake/basics.php";    
require_once dirname(__FILE__) . "/../vendors/cupcake/inflector.php";
require_once dirname(__FILE__) . "/../vendors/cupcake/dispatcher.php";
require_once dirname(__FILE__) . "/../vendors/cupcake/router.php";


$Dispatcher = new Dispatcher();
$Dispatcher->dispatch($_REQUEST['url']);

echo "<pre>";
print_r($Dispatcher);
echo "</pre>";


?>