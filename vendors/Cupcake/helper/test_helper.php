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
     assert_equal($selected_html[0]["text"], $opts["text"], "Failed to match text");
   }
}

?>