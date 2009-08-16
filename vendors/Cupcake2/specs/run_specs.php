<?php
require_once dirname(__FILE__) . "/spec_test.php";

$runner = new SpecRunner();
$runner->require_all(dirname(__FILE__) . "/tests");
$runner->setDescriptiveOutput(false);
$runner->run();

?>