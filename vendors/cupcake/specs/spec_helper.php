<?php
define("CONFIGS", dirname(__FILE__) . "/config/");
define("APP_BASE_URL", dirname(__FILE__) . "/app");
define("DS", DIRECTORY_SEPARATOR);
define("APP_DIR", "app");
define("WEBROOT_DIR", "public");

define("VENDOR_CUPCAKE_DIR", realpath(dirname(__FILE__) . "/../../"));
define("STYLESHEETS_PATH", realpath(dirname(__FILE__) . "/../../../public/stylesheets"));


require_once dirname(__FILE__) . "/../basics.php";
require_once dirname(__FILE__) . "/../set.php";
require_once dirname(__FILE__) . "/../inflector.php";  
require_once dirname(__FILE__) . "/../helpers.php";  
require_once dirname(__FILE__) . "/../view.php";    
require_once dirname(__FILE__) . "/../dispatcher.php";
require_once dirname(__FILE__) . "/../router.php";
require_once dirname(__FILE__) . "/../new_router.php";


?>