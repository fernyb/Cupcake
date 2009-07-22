<?php

class ApplicationController extends Controller {
  
  function show() {
    // Set any variables you may need in the view
    $this->set("welcome", "Welcome to Cupcake");
    
    $description = "<h3>Cupcake is a small mvc framework..</h3>" . 
    "<p>Cupcake is a slice of CakePHP.<br />
     What the means is that Cupcake uses a " . 
    "few files taken from CakePHP and modified to form a lighter framework</p>";
  
    $description .= "
    Files from CakePHP:
     <ul>
      <li>basic.php</li>
      <li>dispatcher.php</li>
      <li>inflector.php</li>
      <li>router.php</li>
      <li>view.php</li>
      <li>controller.php</li>
      </ul>
    ";
    
    $this->set("description", $description);
  }
}

?>