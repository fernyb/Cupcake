<?php
/**
* dispatcher.php
* 
* Handle the REQUEST_URI to determine which controller and method to call.
*
* @author Fernando Barajas <fernyb@fernyb.net>
* @version 1.0
* @package cupcake-core
*/

class Dispatcher {
  static $instance = false;
  
  public function &getInstance() {
    if(self::$instance === false) {
      self::$instance = new self();
    }
    return self::$instance;
  }
  
  static function handle($request_uri) {
    
  }
}

?>