<?php

class SpecCoverage {
  private $file_name = NULL;
  
  function __construct($filename) {
    $this->file_name = $filename;
  }
  
  public function get_lines($line_number) {
    if($handle = file($this->file_name)) {
      $content = $handle[$line_number];
      return $handle;
      return $content;
    }
    return false;
  }
}

$coverage = new SpecCoverage("/Users/fernyb/php/cupcake/vendors/cupcake/router.php");
$lines = $coverage->get_lines(26);

print_r($lines);

?>