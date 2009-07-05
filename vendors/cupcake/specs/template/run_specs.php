<?php
require_once dirname(__FILE__) . "/ztest.php";

$runner = new SpecRunner();
$runner->require_all(dirname(__FILE__) . "/test");
$runner->setDescriptiveOutput(true);
$runner->run();

?>