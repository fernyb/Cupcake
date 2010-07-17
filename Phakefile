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
    $this->mkdir("log");
    
    $this->cp(".htaccess", ".htaccess");
    $this->cp("public/.htaccess",    "public/.htaccess");
    $this->cp("public/index.php",    "public/index.php");
    $this->cp("public/cupcake.php",  "public/cupcake.php");
    $this->cp("public/favicon.ico",  "public/favicon.ico");
    $this->cp("public/404.html",     "public/404.html");
    $this->cp("public/500.html",     "public/500.html");
    $this->cp("public/favicon.ico",  "public/favicon.ico");
    $this->cp("public/robots.txt",   "public/robots.txt");
    $this->cp("public/javascripts/jquery.js",   "public/javascripts/jquery.js");
    $this->cp("scripts/phake",   "scripts/phake");
    
    $this->generate_javascript("application");
    $this->generate_css("master");
    $this->generate_phake("Phakefile");
     
    exec("cp -r {$current_path}/vendors/* {$current_path}/tmp/template/vendors/");
    
    $this->generate_controller("Application");
    $this->generate_helper("Application");
    $this->generate_layout("Application");
    $this->generate_view("application/show.html.php", "application", "show");
    
    $this->generate_config("environment");
    $this->generate_config("mime_types");
    $this->generate_config("routes");
    $this->generate_config("environments/test");
    $this->generate_config("environments/development");
    $this->generate_config("environments/production");
    
    $this->zip();  
  }
  
  public function generate_phake($name) {
    $current_path = $this->current_path();
    $fp = fopen("{$current_path}/tmp/template/{$name}", "w+");
    fwrite($fp, '<?php' ."\n");
    fwrite($fp, 'desc("Run tests");'. "\n");
    fwrite($fp, 'task("test", function(){' ."\n");
    fwrite($fp, "  echo 'TODO: run the test code....';\n");
    fwrite($fp, "});\n");
    fwrite($fp, "\n?>");
    fclose($fp);    
  }
  
  public function generate_javascript($name) {
    $current_path = $this->current_path();
    $name = strtolower($name);
    $fp = fopen("{$current_path}/tmp/template/public/javascripts/{$name}.js", "w+");
    fclose($fp);
  }
  
  public function generate_css($name) {
    $current_path = $this->current_path();
    $name = strtolower($name);
    $fp = fopen("{$current_path}/tmp/template/public/stylesheets/{$name}.css", "w+");
    fclose($fp);
  }
  
  public function zip() {
    $current_path = $this->current_path();
    exec("mv {$current_path}/tmp/template {$current_path}/tmp/cupcake");
    exec("cd {$current_path}/tmp; ditto -c -k --keepParent -rsrc cupcake Cupcake.zip");
    if(file_exists("{$current_path}/tmp/Cupcake.zip")) {
      exec("rm -rf {$current_path}/tmp/cupcake");
    }
  }
  
  public function generate_view($filepath, $controller, $action) {
    $current_path = $this->current_path();
    $fp = fopen("{$current_path}/tmp/template/app/views/{$filepath}", "w+");
    fwrite($fp, "<h2>". ucfirst($controller)." -> ". strtolower($action)."</h2>\n");
    fwrite($fp, '<p>The current date and time is: <strong><?= $current_date ?></strong></p>');
    fclose($fp);
  }
  
  public function generate_config($name) {
    $vars = array("name" => $name, "secret_key" => $this->random('alpha', 80));
    
    $this->generate_file("config", $name, "config", $vars);
  }
  
  public function generate_controller($name) {
    $vars = array("name" => $name);
    $this->generate_file("controller", "controller", "controller", $vars);
  }
  
  public function generate_helper($name) {
    $vars = array("name" => $name);
    $this->generate_file("helper", "helper", "helper", $vars);
  }
  
  public function generate_layout($name) {
    $vars = array("name" => "{$name}.html");
    
    $name = strtolower($name);
    $this->mkdir("app/views/{$name}");
    $this->mkdir("app/views/layouts");
    $this->generate_file("layout", "layout", "views/layout", $vars, false);
  }
  
  public function cp($from, $to) {
    $current_path = $this->current_path();
    exec("cp {$current_path}/{$from} {$current_path}/tmp/template/{$to}");
  }
  
  public function mkdir($path) {
    $current_path = $this->current_path();
    exec("mkdir -p {$current_path}/tmp/template/{$path}");
  }
  
  private function file_path($current_path, $dir, $type, $name) {
    $base = "{$current_path}/tmp/template";
    if($type == "controller" || $type == "helper" || $type == "view" || $type == "views/layout") {
      $type = Inflector::pluralize($type);
      $type = "app/{$type}";
      $base .= "/{$type}/". strtolower($name) .".php";
    } 
    else if($dir == "config") {
      $base .= "/config/". strtolower($name) .".php";
    }
    
    return $base;
  }
  
  private function generate_file($dir, $template_name, $type, $vars, $eval=true) {
    extract($vars);
    $name = ucfirst($name);    
    $current_path = $this->current_path();
    
    $file_path = "vendors/Cupcake/templates/". strtolower($template_name) .".php";
    if($eval) {
      ob_start();
      include $file_path;
      $code = ob_get_contents();
      ob_end_clean();
    } else {
      $code = file_get_contents($file_path);
    }
    
    $type = $this->file_location($type);
    $filename = $this->file_path($current_path, $dir, $type, $name);
    
    $f = fopen($filename, "w+");
    ( $eval ? fwrite($f, "<?php\n\n{$code}\n\n?>") : fwrite($f, $code) );
    fclose($f); 
  }
  
  public function file_location($type) {
    if($type == "layout") {
      return "views/layouts";
    }
    if($type == "environment") {
      return "config";
    }
    
    return $type;
  }
  
  #
  # Taken from:
  # http://www.imanpage.com/code/simple-php-function-generate-random-string-based-alpha-numeric-nozero-md5-and-sha1-type
  #
  public function random($type='sha1', $len=20) {
      mt_srand(time());
      switch ($type) {
          case 'basic':
              return mt_rand();
              break;
          case 'alpha':
          case 'numeric':
          case 'nozero':
              switch ($type) {
                  case 'alpha':
                      $param = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                      break;
                  case 'numeric':
                      $param = '0123456789';
                      break;
                  case 'nozero':
                      $param = '123456789';
                      break;
              }
              $str = '';
              for ($i = 0; $i < $len; $i ++) {
                  $str .= substr($param, mt_rand(0, strlen($param) - 1), 1);
              }
              return $str;
              break;
          case 'md5':
              return md5(uniqid(mt_rand(), TRUE));
              break;
          case 'sha1':
              return sha1(uniqid(mt_rand(), TRUE));
              break;
      }
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
