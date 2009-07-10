<?php
define('ZTEST_VERSION', '0.0.2');

require_once dirname(__FILE__) . "/inc/spec.php";
require_once dirname(__FILE__) . "/inc/spec_runner.php";

// Dependencies
$ztest_manifest = array(
    'exceptions',
    'TestCase',
    'UnitTestCase',
    'TestSuite',
    'Reporter',
    'ConsoleReporter',
    'test_invokers',
    'assertions',
    'mocking',
    'spec_coverage'
);

foreach ($ztest_manifest as $ztest_file) {
    require_once dirname(__FILE__) . "/inc/{$ztest_file}.php";
}

unset($ztest_manifest);
unset($ztest_file);
	
?>

