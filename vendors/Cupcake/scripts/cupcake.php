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


ini_set("include_path", ini_get("include_path") . DIRECTORY_SEPARATOR . ":/Users/fernyb/php/cupcake/vendors");

$include_paths = preg_split("/:/", ini_get("include_path"));
$cupcake_path = null;
foreach($include_paths as $path) {
  if(file_exists($path . "/Cupcake") && $path !== ".") {
    $cupcake_path = realpath($path . "/Cupcake");
    break;
  }
}
define("CUPCAKE_PATH", $cupcake_path);
if(CUPCAKE_PATH === null) {
  echo "\nCupcake Not Found\nMake sure to set the include_path\n";
  exit;
}

# Load any commands extended by the application from /cli
if(file_exists("cli") && is_dir("cli")) {
  foreach(glob("cli/*.php") as $cli_file) {
    include_once $cli_file;
  }
}


try {
  $result = $command->parse();
} catch (Exception $exc) {
  $command->displayError($exc->getMessage());
  exit;
}

$options = $result->options;

foreach($options as $k => $name) {
  if(!empty($name)) {
    $terminal = $command->options[$k];
    $callback = $terminal->callback;
    if(is_string($callback) && strlen($callback) > 0) {
      $callback($options, $terminal);
    }
  }
}



function application_path($opts=array()) {
  $path = empty($opts['path']) === true ? "." : $opts['path'];
  $path = substr($path, -1) === "/" ? substr($path, 0, -1) : $path;
  return $path;
}

# Create an Application 
function cupcake_generate_application($opts=array()) {
  $path      = application_path($opts);
  $app_name  = $opts['application'];
  $app_path  = $path ."/". $app_name;
  
  $directories = array(
    'app'     => array('controllers', 'helpers', 'models', 'views'),
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
      if(mkdir($app, 0775, true)) {
        echo "    [CREATE] {$app}\n";
      } else {
        echo "    [FAILED] {$app}\n";
      }
      foreach($directories[$k] as $index => $subdir) {
        $subdir = "{$app}/{$subdir}";
        if(!file_exists($subdir)) {
          if(mkdir($subdir, 0775, true)) {
            echo "    [CREATE] {$subdir}\n";
          } else {
            echo "    [FAILED] {$subdir}\n";
          }
        } else {
          echo "    [EXISTS] {$subdir}\n";
        }
      }
    } else {
      echo "    [EXISTS] {$app}\n";
    }
  }
  
  # Generate Assets For Public Directory
  cupcake_generate_assets($opts);
  
} # end of generate application

if(!empty($options["application"])) {
  cupcake_generate_application($options);
  exit;
}


#
# Generate a controller, helper, model, view
#
function cupcake_generate($dest_dir, $dest_file, $content) {
  if(!file_exists($dest_dir)) {
    if(mkdir($dest_dir, 0775, true)) {
      echo "    [CREATE] {$dest_dir}\n";
    } else {
      echo "    [FAILED] {$dest_dir}\n";
    }
  } else {
    echo "    [EXISTS] {$dest_dir}\n";
  }
  $controller = "{$dest_dir}/{$dest_file}";
  if(!file_exists($controller)) {
    if(file_put_contents($controller, $content) !== false) {
      echo "    [CREATE] {$controller}\n";
    } else {
      echo "    [FAILED] {$controller}\n";
    }
  } else {
    echo "    [EXISTS] {$controller}\n";
  }
}


#
# Generate A Controller
# * Assumes you are in the root directory of the application
#
function cupcake_generate_controller($opts=array()) {
  $controller_name = $opts['controller'];
  $controller_class_name = camelize($controller_name);
  $helper_class_name = camelize($controller_name ."_helper");
  
  $controller_content = "<?php\n\n";
  $controller_content .= "class {$controller_class_name} extends Application {\n";
  $controller_content .= "\n";
  $controller_content .= "}\n\n";
  $controller_content .= "?>";
  cupcake_generate("app/controllers", "{$controller_name}.php", $controller_content);
  
  $helper_content = "<?php\n\n";
  $helper_content .= "class {$helper_class_name} {\n";
  $helper_content .= "\n";
  $helper_content .= "}\n\n";
  $helper_content .= "?>";
  cupcake_generate("app/helpers", "{$controller_name}.php", $helper_content);
  
  $test_content = "<?php\n\n";
  $test_content .= "describe(\"{$controller_name}\", function(\$request){\n";
  $test_content .= "  before(function(){\n";
  $test_content .= "    \$request = new Request();\n";
  $test_content .= "  });\n";
  $test_content .= "\n";
  $test_content .= "  it(\"asserts true\", function(){\n";
  $test_content .= "    assert_equal(true, true); \n";
  $test_content .= "  });\n";
  $test_content .= "});";
  cupcake_generate("specs/functional", "{$controller_name}_spec.php", $test_content);
  
  $test_helper_content = "<?php\n";
  $test_helper_content .= "define(\"CUPCAKE_ENV\", \"test\");\n\n";
  $test_helper_content .= "define(\"ROOT_PATH\",          realpath(dirname(__FILE__).\"/../\"));\n";
  $test_helper_content .= "define(\"CONFIG_DIR\",         ROOT_PATH   .\"/config\");\n";
  $test_helper_content .= "define(\"APP_DIR\",            ROOT_PATH   .\"/app\");\n";
  $test_helper_content .= "define(\"CONTROLLER_DIR\",     APP_DIR     .\"/controllers\");\n";
  $test_helper_content .= "define(\"VIEW_DIR\",           APP_DIR     .\"/views\");\n";
  $test_helper_content .= "define(\"HELPER_DIR\",         APP_DIR     .\"/helpers\");\n";
  $test_helper_content .= "define(\"PUBLIC_DIR\",         ROOT_PATH   .\"/public\");\n";
  $test_helper_content .= "define(\"CONFIG_DIR\",         ROOT_PATH   .\"/config\");\n";
  $test_helper_content .= "define(\"LOG_DIR\",            ROOT_PATH   .\"/log\");\n";
  $test_helper_content .= "define(\"STYLESHEETS_DIR\",    PUBLIC_DIR  .\"/stylesheets\");\n";
  $test_helper_content .= "define(\"JAVASCRIPTS_DIR\",    PUBLIC_DIR  .\"/javascripts\");\n";
  $test_helper_content .= "define(\"VENDORS_DIR\",        ROOT_PATH   .\"/vendors\");\n";
  $test_helper_content .= "define(\"VENDOR_CUPCAKE_DIR\", VENDORS_DIR .\"/Cupcake\");\n\n";
  $test_helper_content .= "require \"Cupcake/Cupcake.php\";\n\n";
  $test_helper_content .= "?>";
  cupcake_generate("specs", "spec_helper.php", $test_helper_content);
  
  
  if(!file_exists("app/views/{$controller_name}")) {
    if(mkdir("app/views/{$controller_name}")) {
      echo "    [CREATE] views/{$controller_name}\n";
    } else {
      echo "    [FAILED] views/{$controller_name}\n";
    }
  } else {
    echo "    [EXISTS] views/{$controller_name}\n";
  }
  
} # end of generate controller

if(!empty($options["controller"])) {
  cupcake_generate_controller($options);
}



#
# After the application generates all directories then we can add assets
#
function cupcake_generate_assets($opts) {
  if(empty($opts['application'])) {
    echo "\n* Failed to generate cupcake assets\n";
    return;
  }
  $app_name = $opts['application'];
  $app_path = application_path($opts) ."/". $app_name;
  
  $htaccess = "<IfModule mod_rewrite.c>\n";
  $htaccess .= "  RewriteEngine on\n";
  $htaccess .= "  RewriteRule  ^$ public/     [L]\n";
  $htaccess .= "  RewriteRule  (.*) public/$1 [L]\n";
  $htaccess .= "</IfModule>\n";
  
  cupcake_generate("{$app_path}", ".htaccess", $htaccess);
  
  $public = array('.htaccess', '404.html', '422.html', '500.html', 'cupcake.php', 
                  'favicon.ico', 'index.php', 'robots.txt');
  
  $dest_path   = $app_path    ."/public";
  $public_path = CUPCAKE_PATH ."/public";
  
  foreach($public as $asset) {
    $source_file = $public_path ."/". $asset;
    $dest_file   = $dest_path   ."/". $asset;
    if(!file_exists($dest_file)) {
      if(copy($source_file, $dest_file)) {
        echo "    [CREATE] {$dest_file}\n";
      } else {
        echo "    [FAILED] {$dest_file}\n";
      }
    } else {
      echo "    [EXISTS] {$dest_file}\n";
    }
  }
  
  $dir_assets = array(
      'javascripts' => array('jquery.js'),
      'stylesheets' => array('master.css')
    );
  foreach($dir_assets as $dir => $assets) {
    foreach($assets as $file) {
      $source_file = $public_path ."/{$dir}/{$file}";
      $dest_file   = $dest_path   ."/{$dir}/{$file}";
      if(!file_exists($dest_file)) {
        if(copy($source_file, $dest_file)) {
          echo "    [CREATE] {$dest_file}\n";
        } else {
          echo "    [FAILED] {$dest_file}\n";
        }
      } else {
        echo "    [EXISTS] {$dest_file}\n";
      }
    }
  } #

  
  # Generate Sample Command Line File
  $cli_content = "<?php\n\n";
  $cli_content .= "#\n";
  $cli_content .= "# Add your own custom comand line code here\n";
  $cli_content .= "#\n\n";
  $cli_content .= "#function sample_method(\$opts=array(), \$command) {\n";
  $cli_content .= "#  print_r(\$opts);\n";
  $cli_content .= "#  print_r(\$command);\n";
  $cli_content .= "#}\n\n";
  $cli_content .= '#$command->addOption(\'sample\', array(';
  $cli_content .= "\n";
  $cli_content .= "#  'short_name'  =>  '-s',\n";
  $cli_content .= "#  'long_name'   => '--sample',\n";
  $cli_content .= "#  'action'      => 'StoreString',\n";
  $cli_content .= "#  'description' => 'a sample cli argument',\n";
  $cli_content .= "#  'callback'    =>  'sample_method'\n";
  $cli_content .= "# ));\n\n";
  $cli_content .= "?>";
  
  cupcake_generate("{$app_path}/cli", "application.php", $cli_content);
  
  # Generate Run Specs Command Option
  $runspec_content = "<?php\n\n";
  $runspec_content .= "function run_specs(\$opts=array(), \$command) {\n";
  $runspec_content .= "  include_once dirname(__FILE__).\"/../specs/spec_helper.php\";\n";
  $runspec_content .= "  include_once \"Spectest/spec_test.php\";\n\n";
  $runspec_content .= "  \$controllers_path = realpath(dirname(__FILE__).\"/../app/controllers\");\n";
  $runspec_content .= "  foreach(glob(\"{\$controllers_path}/*.php\") as \$file) {\n";
  $runspec_content .= "    include_once \$file;\n";
  $runspec_content .= "  }\n\n";
  $runspec_content .= "\$runner = new SpecRunner();\n";
  $runspec_content .= "\$runner->require_all(dirname(__FILE__) . \"/../specs/functional\");\n";
  $runspec_content .= "\$runner->setDescriptiveOutput(false);\n";
  $runspec_content .= "\$runner->run();\n";
  $runspec_content .= "}\n\n";
  $runspec_content .= "\$command->addOption('run_specs', array(\n";
  $runspec_content .= "  'short_name'  =>  '-r',\n";
  $runspec_content .= "  'long_name'   => '--run-specs',\n";
  $runspec_content .= "  'action'      => 'StoreTrue',\n";
  $runspec_content .= "  'description' => 'Run Specs',\n";
  $runspec_content .= "  'callback'    =>  'run_specs'\n";
  $runspec_content .= "));\n\n";
  $runspec_content .= "?>";
  
  cupcake_generate("{$app_path}/cli", "run_specs.php", $runspec_content);
  
  
  # Genetate Environment File
  $environment_content = file_get_contents(CUPCAKE_PATH ."/structure/config/environment.php");
  $environment_content = preg_replace("/@app_name@/", $app_name, $environment_content);
  $environment_content = preg_replace("/@secret@/",   random('alpha', 80), $environment_content);
  $environment_file = $app_path ."/config/environment.php";
  
  cupcake_generate("{$app_path}/config", "environment.php", $environment_content);
  
  
  # Generate The Development Environment Files
  $environment_content = "<?php\n";
  $environment_content .= "# Set config values for development here\n";
  $environment_content .= "Config::set(\"debug\", true);\n";
  $environment_content .= "?>";
 
  cupcake_generate("{$app_path}/config/environments", "development.php", $environment_content);
  
  
  # Generate The Test Environment File
  $environment_content = "<?php\n";
  $environment_content .= "# Set config values for test here\n";
  $environment_content .= "Config::set(\"debug\", false);\n";
  $environment_content .= "?>";
 
  cupcake_generate("{$app_path}/config/environments", "test.php", $environment_content);
  
    
  # Generate The Production Environment File
  $environment_content = "<?php\n";
  $environment_content .= "# Set config values for production here\n";
  $environment_content .= "Config::set(\"debug\", false);\n";
  $environment_content .= "?>";

  cupcake_generate("{$app_path}/config/environments", "production.php", $environment_content);

  
  # Generate Routes File
  $route_content = "<?php\n";
  $route_content .= "#\n";
  $route_content .= "# The priority is based upon order of creation: first created -> highest priority.\n";
  $route_content .= "#\n";
  $route_content .= "Router::prepare(function(\$r){\n";
  $route_content .= '  $r->match("/")->to(array("controller" => "application", "action" => "show"))->name("root");';
  $route_content .= "\n";
  $route_content .= "});\n\n";
  $route_content .= "?>";

  cupcake_generate("{$app_path}/config", "routes.php", $route_content);

  
  # Generate Application Controller
  $controller_content = "<?php\n";
  $controller_content .= "\n";
  $controller_content .= "class Application extends Controller {\n";
  $controller_content .= "\n";
  $controller_content .= "}\n";
  $controller_content .= "\n";
  $controller_content .= "?>";

  cupcake_generate("{$app_path}/app/controllers", "application.php", $controller_content);

  
  # Generate Application Helper
  $helper_content = "<?php\n";
  $helper_content .= "\n";
  $helper_content .= "class ApplicationHelper {\n";
  $helper_content .= "\n";
  $helper_content .= "}\n";
  $helper_content .= "\n";
  $helper_content .= "?>";

  cupcake_generate("{$app_path}/app/helpers", "application.php", $helper_content);

  # Generate Mime Type config
  $mime_content = "<?php\n";
  $mime_content .= "\n";
  $mime_content .= "#\n";
  $mime_content .= "# Set any Extra Mime Types here\n";
  $mime_content .= "#\n";
  $mime_content .= "# MimeType::register(\"text/html\", \"html\");\n";
  $mime_content .= "#\n";
  $mime_content .= "?>";
  
  cupcake_generate("{$app_path}/config", "mime_types.php", $mime_content);
  

  # Generate Application Layout (copy)
  $source_layout_file = CUPCAKE_PATH ."/structure/views/layouts/application.html.php";
  $layout_dir = $app_path ."/app/views/layouts";
  $dest_layout_file = $layout_dir ."/application.html.php";
  if(!file_exists($layout_dir)) {
    if(mkdir($layout_dir, 0775, true)) {
      echo "    [CREATE] {$layout_dir}\n";
    } else {
      echo "    [FAILED] {$layout_dir}\n";
    }
  } else {
    echo "    [EXISTS] {$layout_dir}\n";
  }
  
  if(copy($source_layout_file, $dest_layout_file)) {
    echo "    [CREATE] {$dest_layout_file}\n";
  } else {
    echo "    [FAILED] {$dest_layout_file}\n";  
  }
} # end of function cupcake_generate_assets()


#
# Taken from:
# http://www.imanpage.com/code/simple-php-function-generate-random-string-based-alpha-numeric-nozero-md5-and-sha1-type
#
function random ($type='sha1', $len=20) {
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


function camelize($word) {
  if(preg_match_all('/\/(.?)/',$word,$got)) {
    foreach ($got[1] as $k=>$v){
      $got[1][$k] = '::'.strtoupper($v);
    }
    $word = str_replace($got[0],$got[1],$word);
  }
  return str_replace(' ','',ucwords(preg_replace('/[^A-Z^a-z^0-9^:]+/',' ',$word)));
}



?>