#!/usr/bin/env php
<?php
date_default_timezone_set("America/Los_Angeles");

echo 'rm Cupcake-0.8.0.tgz';


$package_file = "vendors/package.xml";

$output = `pear package-validate {$package_file}`;  

echo $output;

if(!preg_match("/Validation: 0 error/", $output)) {
  exit;
}

echo "\n****************************************\n\n";

echo `pear package {$package_file}`;
  
?>