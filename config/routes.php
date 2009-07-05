<?php

Router::connect("/", array("controller" => "main", "action" => "show"));
Router::connect("/users", array("controller" => "main", "action" => "users"));


?>