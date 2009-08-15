#!/usr/bin/env php
<?php
require_once 'Console/CommandLine.php';

$command = new Console_CommandLine(array(
    "description" => "The 'cupcake' command create a new Cupcake application with a default directory structure and configuration at the path you specify.",
    "version" => "0.8.0"
  ));
  
$command->addOption('quiet', array(
    'short_name'  => '-q',
    'long_name'   => '--quiet',
    'description' => 'turn off verbose output',
    'action'      => 'StoreTrue'
   ));

$command->addOption('application', array(
    'short_name'  =>  '-a',
    'long_name'   => '--application',
    'action'      => 'StoreString',
    'description' => 'generate cupcake application'
  ));

$command->addOption('path', array(
    'short_name'  =>  '-p',
    'long_name'   => '--path',
    'action'      => 'StoreString',
    'description' => 'path to generate app'
  ));
  

$command->addOption('controller', array(
    'short_name'  =>  '-c',
    'long_name'   => '--controller',
    'action'      => 'StoreString',
    'description' => 'generate controller'
  ));

# Load any command extended by the application


try {
  $result = $command->parse();
} catch (Exception $exc) {
  $command->displayError($exc->getMessage());
  exit;
}

$options = $result->options;

# Create an Application 
function generate_application($opts=array()) {
  $path      = empty($opts['path']) === true ? "." : $opts['path'];
  $path      = substr($path, -1) === "/" ? substr($path, 0, -1) : $path;
  $app_name  = $opts['application'];
  $app_path  = $path ."/". $app_name;
  
  $directories = array(
    'app'     => array('controllers'),
    'views'   => array('application'),
    'config'  => array('environments'),
    'public'  => array('images', 'javascripts', 'stylesheets'),
    'specs'   => array(),
    'log'     => array(),
    'vendors' => array(),
    'cli'     => array()
    );
    
  echo "\n";
  foreach($directories as $k => $v) {
    $app = "{$app_path}/{$k}";  
    if(!file_exists($app)) {
      if(mkdir($app, 0700, true)) {
        echo "    [CREATE] {$app}\n";
      } else {
        echo "    [EXISTS] {$app}\n";
      }
      foreach($directories[$k] as $index => $subdir) {
        $subdir = "{$app}/{$subdir}";
        if(!file_exists($subdir)) {
          if(mkdir($subdir, 0700, true)) {
            echo "    [CREATE] {$subdir}\n";
          }
        } else {
          echo "    [EXISTS] {$subdir}\n";
        }
      }
    }
  }
} # end of generate application

if(!empty($options["application"])) {
  generate_application($options);
  exit;
}

function generate_controller($opts=array()) {
  $controller_name = $opts['controller'];
  $dir = "app/controllers/{$controller_name}.php";
  if(!file_exists($dir)) {
    if(mkdir($dir, 0700, true)) {
      echo "    [CREATE] {$dir}";
    } else {
      echo "    [EXISTS] {$dir}";
    }
  }
} # end of generate controller

if(!empty($options["controller"])) {
  generate_controller($options);
}



?>