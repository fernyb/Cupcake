<?php

class ReleasePackage {
  public static $env;
  private static $_instance;
  
  public static function sharedInstance() {
    if(!self::$_instance) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }
  
  public function current_path() {
    return dirname(__FILE__);
  }
  
  public function package() {
    $current_path = $this->current_path();
  
    if(!file_exists("{$current_path}/tmp")) {
      exec("mkdir -p {$current_path}/tmp");
    }
    exec("rm -rf {$current_path}/tmp/*");
    
    // remove the zip file if any from the vendors directory
    if(file_exists("{$current_path}/vendors/Cupcake.zip")) {
      exec("rm {$current_path}/vendors/Cupcake.zip");
    }
    
    exec("mkdir -p {$current_path}/tmp/template/app");
    exec("mkdir -p {$current_path}/tmp/template/app/controllers");
    exec("mkdir -p {$current_path}/tmp/template/app/helpers");
    exec("mkdir -p {$current_path}/tmp/template/app/views");
    
    exec("mkdir -p {$current_path}/tmp/template/config/environments");
    
    exec("mkdir -p {$current_path}/tmp/template/public");
    exec("mkdir -p {$current_path}/tmp/template/public/javascripts");
    exec("mkdir -p {$current_path}/tmp/template/public/stylesheets");
    
    exec("mkdir -p {$current_path}/tmp/template/scripts");
    exec("mkdir -p {$current_path}/tmp/template/vendors");
    
    exec("cp -r {$current_path}/vendors/* {$current_path}/tmp/template/vendors/");
    
    $this->generate_controller("Application");
    $this->generate_helper("Application");
    
    /*
    // zip the directory
    exec("cd {$current_path}/vendors; ditto -c -k --keepParent -rsrc Cupcake Cupcake.zip");
    
    // Move zip file to tmp directory
    if(file_exists("{$current_path}/vendors//Cupcake.zip")) {
      exec("mv {$current_path}/vendors/Cupcake.zip {$current_path}/tmp/Cupcake.zip");
    }
    */  
  }
  
  public function generate_controller($name) {
    $this->generate_file("controller", $name);
  }
  
  public function generate_helper($name) {
    $this->generate_file("helper", $name);
  }
  
  private function file_path($current_path, $type, $name) {
    return "{$current_path}/tmp/template/app/{$type}s/". strtolower($name) .".php";
  }
  
  private function generate_file($type, $name) {
    $name = ucfirst($name);
    $current_path = $this->current_path();
    ob_start();
    include "./vendors/Cupcake/templates/{$type}.php";
    $code = ob_get_contents();
    ob_end_clean();
    $f = fopen($this->file_path($current_path, $type, $name), "a");
    fwrite($f, "<?php\n\n{$code}\n\n?>");
    fclose($f); 
  }
}


group("build", function() {
  desc("Build For Distribution Release");
  task("release", "package", function() {
    ReleasePackage::$env = "release";
  });
  
  task("package", function() {    
    ReleasePackage::sharedInstance()->package();
  });
});
