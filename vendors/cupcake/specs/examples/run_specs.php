<?php
require_once dirname(__FILE__) . "/ztest.php";

/*
* It runs all test under /test that match <filename>_spec.php
* It must have _spec.php and the end of the file name for the tests to run
* Otherwise they don't run.
*
* You can setDescritiveOutput to true or false to display a descritive output or
* just the minimum, Red mean failures, Green mean Pass
*
*/

$runner = new SpecRunner();
$runner->require_all(dirname(__FILE__) . "/test");
$runner->setDescriptiveOutput(true);
$runner->run();

?>