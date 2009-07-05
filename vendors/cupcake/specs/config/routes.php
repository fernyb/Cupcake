<?php

Router::connect("/", array("controller" => "main", "action" => "show"));
Router::connect("/users", array("controller" => "main", "action" => "users"));
Router::connect("/users/profile", array("controller" => "main", "action" => "users_profile"));
Router::connect("/cars", array("controller" => "cars"));

?>