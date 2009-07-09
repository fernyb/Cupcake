<?php
require_once dirname(__FILE__) . "/ztest.php";

xdebug_start_code_coverage();

$runner = new SpecRunner();
$runner->require_all(dirname(__FILE__) . "/tests");
$runner->setDescriptiveOutput(true);
$runner->run();

$coverage = xdebug_get_code_coverage();

foreach($coverage as $k => $v) {
   if(!preg_match("/vendors\/cupcake\/specs\/inc\/.*/", $k) && 
      !preg_match("/vendors\/cupcake\/tests\/.*.php/", $k) && 
      !preg_match("/vendors\/cupcake\/specs\/.*.php/", $k)) {
        
     echo $k . "\n";
     print_r($v);
     echo "\n";
     
   }
}


?>