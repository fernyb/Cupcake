<?php
//
// Assertions are written as normal functions and we use a static property on
// Test_Base to keep track of state. The reason: it's less to type. No one likes
// writing $this->assert() over and over.

function pass() {
	ensure(true);
}

function fail($msg = "") {
	ensure(false, $msg);
}

/**
 * Assert
 * 
 * @param $v value to be checked for truthiness
 * @param $msg message to report on failure
 */
function ensure($v, $msg = "") {
	if(!$v) {
	  Spec::assert_fail($msg);
	} else {
	  Spec::assert_pass();
	}
}

function assert_pending() {
  Spec::assert_pending();
}

function assert_each($iterable, $test, $msg = "") {
    foreach ($iterable as $i) {
        ensure($test($i));
    }
}

function assert_object($v, $msg = "") {
	ensure(is_object($v), $msg);	
}

function assert_array_has_keys($l, $r, $msg = "") {
  foreach($r as $k => $v) {
    if(empty($msg)) {
      ob_start();
      echo "\nExpected $v to be a key in array:\n";
      var_dump($l);
      $msg = ob_get_contents();
      ob_clean();
    }
    ensure(array_key_exists($v, $l), $msg);
  }
}

function assert_array_has_values($l, $r, $msg = "") {
  foreach($r as $k => $v) {
    if(empty($msg)) {
      ob_start();
      echo "\nExpected $v to be in array:\n";
      var_dump(array_flip(array_values($l)));
      $msg = ob_get_contents();
      ob_clean();
    }
    if(preg_match("/^([0-9]+)$/", $v)) {
      $v = (string) $v;
    }
   ensure( array_key_exists($v, array_flip(array_values($l))), $msg );
  }
}

function assert_array_has_key($l, $r, $msg = "") {
  ensure(array_key_exists($r, $l), $msg);
}

function assert_array($v, $msg = "") {
	ensure(is_array($v), $msg);	
}

function assert_scalar($v, $msg = "") {
    ensure(is_scalar($v), $msg);
}

function assert_not_equal($l, $r, $msg = "") {
  if(empty($msg)) {
    $msg = "\nExpected $l to not be $r but was $r\n";
  }   
  ensure($l != $r, $msg);
}

function assert_equal($l, $r, $msg = "") {
  if(empty($msg)) {
    $msg = "\nExpected $l to be $r but was $r\n";
  }  
  ensure($l == $r, $msg);
}

function assert_identical($l, $r, $msg = "") {
	ensure($l === $r, $msg);	
}

function assert_equal_strings($l, $r, $msg = "") {
	ensure(strcmp($l, $r) === 0);	
}

function assert_match($regex, $r, $msg = "") {
	ensure(preg_match($regex, $r), $msg);	
}

function assert_null($v, $msg = "") {
	ensure($v === null, $msg);
}

function assert_not_null($v, $msg = "") {
	ensure($v !== null, $msg);	
}

// NOTE: this assertion swallows all exceptions
function assert_throws($exception_class, $lambda, $msg = '') {
    try {
        $lambda();
        fail($msg);
    } catch (Exception $e) {
        if (is_a($e, $exception_class)) {
            pass();
        } else {
            fail($msg);
        }
    }
}

/**
 * Wraps the common pattern of having a map of input => expected output that you
 * wish to check against some function.
 * 
 * @param $data_array map of input value => expected output
 * @param $lambda each input value will be passed to this function and compared
 *        against expected output.
 */
function assert_output($data_array, $lambda) {
	foreach ($data_array as $input => $expected_output) {
		assert_equal($expected_output, $lambda($input));
	}
}

/**
 * This one's a bit dubious - it tests that an assertion made by the supplied
 * lambda fails. It primarily exists for self-testing the ztest library, and
 * using it causes the displayed assertion stats to be incorrect.
 */
function assert_fails($lambda, $msg = '') {
    $caught = false;
    try {
        $lambda();
    } catch (ztest\AssertionFailed $e) {
        $caught = true;
        pass();
    }
    if (!$caught) {
        Spec::assert_fail();
        //throw new ztest\AssertionFailed($msg);
    }
}

?>