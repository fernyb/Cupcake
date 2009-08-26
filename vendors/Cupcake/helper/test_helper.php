<?php

function assert_select($html, $selector, $opts=array()) {
   $selected_html = select_elements($selector, $html);
   if($opts["count"]) {
     assert_equal(true, (count($selected_html) === $opts['count']), "Failed to match count");
   }
   if($opts["minimum"]) {
     assert_equal(true, (count($selected_html) >= $opts['minimum']), "Failed minimum count");
   }
   if($opts["maximum"]) {
     assert_equal(true, (count($selected_html) <= $opts['maximum']), "Failed maximum count");
   }
   if($opts["text"]) {
     $text_found = false;
     foreach($selected_html as $element) {
       if($element["text"] === $opts["text"]) {
        $text_found = true;
        assert_equal($element["text"], $opts["text"], "Failed to match text");
        break;
       }
     }
     if($text_found === false) {
       fail("Failed to match text");
     }
   }
   
  if($opts["match"]) {
    $text_match_found = false;
    foreach($selected_html as $element) {
      if(preg_match($opts["match"], $element["text"])) {
        $text_match_found = true;     
        assert_equal(true, preg_match($opts["match"], $element["text"]), "Failed to match regular expression");
        break;
      }
    }
    if($text_match_found === false) {
      fail("Failed to match regular expression");
    }
  }
  
}

?>