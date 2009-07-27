<?php
/**
* Add your routes here
*/

Router::connect("/public/:action", array("controller" => "application", "action" => "show"));
Router::connect("/public/page/:action", array("controller" => "application", "action" => "show"));
Router::connect("/", array("controller" => "application", "action" => "show"));


?>