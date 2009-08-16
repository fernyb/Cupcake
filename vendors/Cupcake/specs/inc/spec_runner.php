<?php

class SpecRunner {
  private $files = array();
  private $descriptiveOutput = false;
   
  public function require_all($dir = 'test', $extensions = array('php')) {
    $ext_match = '/\.(' . implode('|', $extensions) . ')$/'; 
  	$stack = array($dir);
  	while (count($stack)) {
  		$dir = array_pop($stack);
  		$dh = opendir($dir);
  		if($dh){
    		while (($file = readdir($dh)) !== false) {
    			if ($file[0] == '.') continue;
    			$fqd = $dir . DIRECTORY_SEPARATOR . $file;
    			if (is_dir($fqd)) {
    				$stack[] = $fqd;
    			} elseif (preg_match($ext_match, $fqd)) {
    			  if(preg_match("/_spec\.php$/", $fqd)) {
    			   array_push($this->files, $fqd);
    			  }
    			}
    		}
    	  closedir($dh);	
  	  } // end if opendir
  	}
  }
  
  public function setDescriptiveOutput($bool=false) {
    $this->descriptiveOutput = $bool;
  }
  
  public function run() {
    Spec::write("\n", true);
    //xdebug_start_code_coverage(XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE);
    foreach($this->files as $file) {
      require $file;
      
      Spec::setDescribeOutput($this->descriptiveOutput);
      Spec::run();
    }
    //xdebug_get_code_coverage();
      
    Spec::results();
  }
}

?>