<?php

describe("Basics", function(){
  it("escapes characters", function(){
    $text = h("The Fox Jumps & Runs!");
    assert_equal($text, "The Fox Jumps &amp; Runs!");
  });
  
  it("returns script name", function(){
    $name = env("SCRIPT_NAME");
    assert_equal($name, "run_specs.php");
  });
  
  it("strips slashes from all values in array", function(){
    $text = array("o\\ne\\/t\wo", "th\\ree\\/f\our", "f\ive\\/six");
    $text = stripslashes_deep($text);
    
    assert_equal("one/two",    $text[0]);
    assert_equal("three/four", $text[1]);
    assert_equal("five/six",   $text[2]);
  });
});

?>