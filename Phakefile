<?php

class ReleasePackage {
  public static $env;
  
  public static function package() {
    $current_path = dirname(__FILE__);
  
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
    
    
    /*
    // zip the directory
    exec("cd {$current_path}/vendors; ditto -c -k --keepParent -rsrc Cupcake Cupcake.zip");
    
    // Move zip file to tmp directory
    if(file_exists("{$current_path}/vendors//Cupcake.zip")) {
      exec("mv {$current_path}/vendors/Cupcake.zip {$current_path}/tmp/Cupcake.zip");
    }
    */  
    
  }
}


group("build", function() {
  desc("Build For Distribution Release");
  task("release", "package", function() {
    ReleasePackage::$env = "release";
  });
  
  task("package", function() {    
    ReleasePackage::package();
  });
});
