<?php
xdebug_start_code_coverage(XDEBUG_CC_DEAD_CODE);

require_once dirname(__FILE__) . "/ztest.php";



$runner = new SpecRunner();
$runner->require_all(dirname(__FILE__) . "/tests");
$runner->setDescriptiveOutput(true);
$runner->run();

$coverage = xdebug_get_code_coverage();
xdebug_stop_code_coverage();

foreach($coverage as $k => $v) {
   if(!preg_match("/vendors\/cupcake\/specs\/inc\/.*/", $k) && 
      !preg_match("/vendors\/cupcake\/tests\/.*.php/", $k) && 
      !preg_match("/vendors\/cupcake\/specs\/.*.php/", $k)) {
      
      $file = new SpecCoverage($k);
      $lines = $file->get_lines(array_keys($v));
      echo "\n" . $k ."\n";
   }
}

?>