<?php
require_once "spec_helper.php";

$dependencies = array(
    'spec',
    'spec_runner',
    'assertions'
);

foreach ($dependencies as $dependency) {
    require_once dirname(__FILE__) . "/inc/{$dependency}.php";
}

unset($dependencies);
	
?>

