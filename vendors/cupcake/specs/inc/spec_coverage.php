<?php

class SpecCoverage {
  private $file_name = NULL;
  
  function __construct($filename) {
    $this->file_name = $filename;
  }
  
  public function get_lines($line_number) {
    if($handle = file($this->file_name)) {
      $content = array();
      if(is_array($line_number)) {
        foreach($line_number as $num) {
          $content[$num] = $handle[$num];
        }
      } else {
        $content[$num] = $handle[$line_number];      
      }

      $coverage_dir = realpath(dirname(__FILE__) . "/../coverage");
      
      $code_diff = "<link rel=\"stylesheet\" href=\"{$coverage_dir}/coverage.css\" type=\"text/css\" media=\"screen\" />";
      $code_diff .= "<div><strong>". $this->file_name ."</strong></div><br />";
      $code_diff .= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">";
      foreach($handle as $line => $code) {
        // If the line exists in the content array then that
        // line is considered tested.
        if(!array_key_exists($line, $content)) {
          $code_diff .= "<tr><td class=\"line-number\">{$line}</td><td class=\"code green\"><pre>". $handle[$line] ."</pre></td></tr>";
        } else {
          $code_diff .= "<tr><td class=\"line-number\">{$line}</td><td class=\"code red\"><pre>". $handle[$line] ."</pre></td></tr>";
        }
      }
      $code_diff .= "</table>";
      
      $file = $coverage_dir . "/" . basename($this->file_name, ".php") .".html";
      
      $fp = fopen($file, "w+");
      fwrite($fp, $code_diff);
      fclose($fp);
      
      return $content;
    }
    return false;
  }
}


?>