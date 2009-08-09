<?php

class Spec {
  public static $colors = array(
      'red'       => "\033[31m",
      'green'     => "\033[32m",
      'blue'      => "\033[34m",
      'yellow'    => "\033[33m",
      'default'   => "\033[0m"
  );
  
  public static $describe_specs = array();
  public static $results = array("pass" => 0, "fail" => 0, "pending" => 0);
  public static $descriptive = true;
  public static $total_results = array("pass" => 0, "fail" => 0, "pending" => 0, "total" => 0);
  public static $pending_messages = array();
  
  public static function setDescribeOutput($bool=false) {
    self::$descriptive = ($bool ? true : false);
  }
  
  public static function setSpec($name, $fn) {
    self::$describe_specs[$name] = $fn;
  }
  
  public static function write($text, $isTest=false, $color="green") {
    $out = function($txt, $c){
      return Spec::$colors[$c] . $txt . Spec::$colors['default'];
    };
    if(self::$descriptive) {
      echo $out($text, $color);
    } else if($isTest == true) {
      echo $out($text, $color);
    }
  }
  
  public static function run() {
    foreach(self::$describe_specs as $name => $fn) {
      self::write("\n* Describe: ". $name);
      $fn($name);
      self::write("\n");
    }
    self::report_results();
    self::clear();
  }
  
  public static function clear() {
    self::$describe_specs = array();
    self::$results = array("pass" => 0, "fail" => 0, "pending" => 0);
  }
  
  public static function assert_pass() {
    if(!self::$descriptive) { 
      self::write(".", true, "green");
    }
    self::$results['pass'] += 1;
  }
  
  public static function assert_fail($msg="") {
    if(!self::$descriptive) {      
      self::write("F", true, "red");
    }
    self::$results['fail'] += 1;
    if(isset($msg)) {
      echo "\n" . $msg . "\n\n";
      debug_print_backtrace();
      echo "\n\n";
    }
  }
  
  public static function assert_pending() {
    if(!self::$descriptive) {      
      self::write("P", true, "yellow");
    }
    self::$results['pending'] += 1;
  }
  
  public static function report_results() {
    $passed  = self::$results['pass'];
    $failure = self::$results['fail'];
    $pending = self::$results['pending'];
    $total   = $passed + $failure + $pending;
    self::$total_results['pass'] += (int) $passed;
    self::$total_results['fail'] += (int) $failure;
    self::$total_results['total'] += (int) $total;
    self::$total_results['pending'] += (int)$pending;
  }
  
  public static function results() {
    $passed  = self::$total_results['pass'];
    $failure = self::$total_results['fail'];
    $total   = self::$total_results['total'];
    $pending = self::$total_results['pending'];
    
    echo "\n";
    foreach(self::$pending_messages as $k => $message) {
      self::write("\n Peding: ". $message, true, "yellow");
    }
    
    self::write("\n\nPass: {$passed}, Failure: ". Spec::$colors['red'] ."{$failure}". Spec::$colors['green'] .", Pending: {$pending}, Test: {$total}\n", true);
    
    self::$total_results['pass']    = 0;
    self::$total_results['failure'] = 0;
    self::$total_results['pass']    = 0;
    self::$total_results['pending'] = 0;
    self::$pending_messages = array();    
  }
}


function describe($name, $fn) {  
  Spec::setSpec($name, $fn);
}

$before_closure = function(){
};

function before($closure) {
  global $before_closure;
  $before_closure = $closure;  
}

function it($name, $fn) {
  global $before_closure;
  
  $before_objects = $before_closure();
  if(empty($before_objects)) {
    $before_objects = array();
  }
  
  $failure_count = Spec::$results['fail'];
  $passed_count = Spec::$results['pass'];
  $pending_count = Spec::$results['pending'];
  
  $fn($before_objects);
  
  if(Spec::$results['pending'] > $pending_count || ($failure_count === Spec::$results['fail'] && $passed_count === Spec::$results['pass'])) {
    Spec::assert_pending();
    array_push(Spec::$pending_messages, $name);
  }
  
  if(Spec::$descriptive) {
    if($failure_count < Spec::$results['fail']) {
      Spec::write("\n  " . $name, false, "red");
    } else {
      Spec::write("\n  " . $name, false, "green");
    }
  }
}

?>