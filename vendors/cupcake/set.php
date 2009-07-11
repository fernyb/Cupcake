<?php

class Set {
  
/**
 * Computes the difference between a Set and an array, two Sets, or two arrays
 *
 * @param mixed $val1 First value
 * @param mixed $val2 Second value
 * @return array Computed difference
 * @access public
 * @static
 */
	function diff($val1, $val2 = null) {
		if (empty($val1)) {
			return (array)$val2;
		}
		if (empty($val2)) {
			return (array)$val1;
		}
		$out = array();

		foreach ($val1 as $key => $val) {
			$exists = array_key_exists($key, $val2);

			if ($exists && $val2[$key] != $val) {
				$out[$key] = $val;
			} elseif (!$exists) {
				$out[$key] = $val;
			}
			unset($val2[$key]);
		}

		foreach ($val2 as $key => $val) {
			if (!array_key_exists($key, $out)) {
				$out[$key] = $val;
			}
		}
		return $out;
	}
	  
}

?>