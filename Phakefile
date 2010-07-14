<?php
require "vendors/Cupcake/inflector.php";

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

    $this->mkdir("app/controllers");
    $this->mkdir("app/helpers");
    $this->mkdir("app/views");
    $this->mkdir("config/environments");
    $this->mkdir("public/javascripts");
    $this->mkdir("public/stylesheets");
    $this->mkdir("scripts");
    $this->mkdir("vendors");
    
    exec("cp -r {$current_path}/vendors/* {$current_path}/tmp/template/vendors/");
    
    $this->generate_controller("Application");
    $this->generate_helper("Application");
    $this->generate_layout("Application");
    
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
  
  public function generate_layout($name) {
    $name = strtolower($name);
    $this->mkdir("app/views/{$name}");
    $this->mkdir("app/views/layouts");
    $this->generate_file("layout", "{$name}.html", false);
  }
  
  public function mkdir($path) {
    $current_path = $this->current_path();
    exec("mkdir -p {$current_path}/tmp/template/{$path}");
  }
  
  private function file_path($current_path, $type, $name) {
    $type = Inflector::pluralize($type);
    return "{$current_path}/tmp/template/app/{$type}/". strtolower($name) .".php";
  }
  
  private function generate_file($type, $name, $eval=true) {
    $name = ucfirst($name);
    $current_path = $this->current_path();
    $filename = Inflector::singularize($type);
    $file_path = "./vendors/Cupcake/templates/{$filename}.php";
    if($eval) {
      ob_start();
      include $file_path;
      $code = ob_get_contents();
      ob_end_clean();
    } else {
      $code = file_get_contents($file_path);
    }
    
    $type = ($type == "layout" ? "views/layouts" : $type);
    $filename = $this->file_path($current_path, $type, $name);
    
    $f = fopen($filename, "w+");
    ( $eval ? fwrite($f, "<?php\n\n{$code}\n\n?>") : fwrite($f, $code) );
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
