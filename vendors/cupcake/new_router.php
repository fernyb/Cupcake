<?php

class NewRouter {
  
  const SEGMENT_REGEXP               = "/(:([a-z](_?[a-z0-9])*))/";
  const OPTIONAL_SEGMENT_REGEX       = "/^.*?([\(\)])/i";
  const SEGMENT_REGEXP_WITH_BRACKETS = "/(:[a-z_]+)(\[(\d+)\])?/";
  const JUST_BRACKETS                = "/\[(\d+)\]/";
  const SEGMENT_CHARACTERS           = "[^\/.,;?]";
  
  private $conditions = array();
  private $params = array();
  private $segments;      
  
  static $instance = false;
  public $routes = array();
  public $current_path = false;
  
  static function &getInstance() {
    if(!self::$instance) {
      self::$instance = new self();
    }
    return self::$instance;
  }
  
  static function &prepare($block) {
    $_this = self::getInstance();
    $block($_this);
    return $_this;
  }
  
  public function &match($path) {
    $this->routes[][$path] = array("path" => $path);
    $this->current_path = $path;
    return $this;
  }
  
  public function to($params=array()) {
    if($this->current_path === false) return false;
    
    if(list($index, $new_params) = $this->current_path_params($params)) {
      $this->params[] = $params;
      $old_params = $this->routes[$index][$this->current_path];
      $this->routes[$index][$this->current_path] = array_merge($old_params, $new_params);
      $this->conditions[] = array("path" => $this->current_path);
      return $this->routes[$index][$this->current_path];
    }
    return false;
  }
  
  public function current_path_params($params=array()) {
    if($path = $this->route_for($this->current_path)) {
      $route_path = array_keys($path[1]);
      $new_params = array_merge($this->routes[$path[0]][$route_path[0]], array("params" => $params));
      return array($path[0], $new_params);
    }
    return false;
  }
  
  public function route_for($path) {
    foreach($this->routes as $i => $r) {
      if(array_key_exists($path, $this->routes[$i])) {
        return array($i, $this->routes[$i]);
      }
    }
    return false;
  }
  
  #
  # TODO: Code below needs to be better tested
  #  
  public function compiled_statement() {
    $routes = array();
    foreach($this->routes as $k => $v) {
      $keys = array_keys($v);
      $route_path = $keys[0];
      $routes[] = $route_path;
    }
    //
    $condition_keys = array();
    $if_statements = array();
    
    foreach($routes as $k => $route) {
      foreach(array_keys($this->conditions[$k]) as $key) {
        $condition_keys[] = $key;
        $condition_keys = array_unique($condition_keys);
      }
      $first = ($k == 0) ? true : false;
      $if_statements[] = $this->route_compiled_statement($first, $k);
    }
    
    $code = join("\n", $if_statements);
    return $code;
  }
  
  public function route_compiled_statement($first, $index) {
    $els_if = $first ? " if " : " else if ";
    $code = "";
    $code .= $els_if ."( ". join(" && ", $this->condition_statements($index)) . " ) { \n";

    $params_as_string = $this->params_as_string($index);
    $code .= "    return array({$index}, {$params_as_string});" . "\n } ";
     
    return $code;
  }
  
  public function params_as_string($index) {
    $elements = array();
    foreach($this->params[$index] as $k => $v) {
      $elements[] = "\"{$k}\" => \"{$v}\"";
    }
    $params = "array(". join(",", $elements) .")";
    return $params;
  }
  
  public function condition_statements($index) {
    $statements = array();
    foreach($this->conditions as $i => $v) {
      switch($v) {
        case is_array($v) :
        foreach($v as $key => $value) {
          $statements[] = "preg_match(\"/". $this->arrays_to_regexps($v) ."/\", \$cached_{$key})";
        }
        break;
        default:
          $statements[] = "\$cached_{$i} == {$v}";
        break;
      }
    }
    $uniq_statements = array_unique($statements);
    $statement = $uniq_statements[$index];
  
    return array($statement);
  }
  
  private function arrays_to_regexps($condition) {
    if(!is_array($condition)) return $condition;
    
    $delimiter = "/";
    $source = array();
    foreach($condition as $i => $value) {

      preg_match_all("/([^\/.,;?]+)/", $value, $matches);
      $rgs = array();
      $first_param = false;
      
      foreach($matches[1] as $k => $v) {
        if(strpos($v, ":") === 0) {
          if($first_param === false) {
            $rgs[] = "([^\/.,;?]+)";
            $first_param = true;
          } else {
            $rgs[] = "(?:\/([^\/.,;?]+))";
          }
        } else {
          $rgs[] = $v ."\/";
        }
      }
      
      if(count($rgs) === 1) {
        $rgs[0] = substr($rgs[0], 0, strlen($rgs[0]) - 2);
      }
            
      $route_path = join("", $rgs);
      
      $source[] = "^\/" . $route_path ."$";
    }
    
    $source = array_unique($source);
    return join("|", $source);
  }
  
  public function find_route($request_path=null) {
    if($request_path === null) return false;
    if(list($index, $params) = $this->match_path($request_path)) {
      return array($index, $params);
    }
    return false;
  }
  
  private function match_path($path) {
     if($this->load_matcher()) {
       return __match($path);
    }
    return false;
  }
  
  
  /*
  * We should generate and reload the matcher every single time during development
  * In production and integration we can only generate and load it once.
  */
  private function load_matcher() {
    $file = TEMP_DIR_PATH . "/route_match.tmp.php";
    if(!file_exists(TEMP_DIR_PATH)) {
      mkdir(TEMP_DIR_PATH, 0700);
    }
   # if(file_exists($file)) {
  #    if(!function_exists("__match")) {
  #      include_once $file;
  #    }
  #  } else {
      // We should generate the file
      $code = $this->compiled_statement();
      if($code) {
        $out = array();
        $out[] = "<?php ";
        $out[] = "  function __match(\$cached_path) { ";
        $out[] = $code;
        $out[] = "    return false; ";
        $out[] = "  } ";
        $out[] = "?>";
        $out = join("\n", $out);
        if($fp = fopen($file, "w")) {
          fwrite($fp, $out, strlen($out));
          fclose($fp);
          if(!function_exists("__match")) {
            include_once $file;
          }
        }
      }
    #}
    if(function_exists("__match")) return true;
    return false;
  }
  
  
  public function reset() {
    $this->routes = array();
    $this->current_path = false;
    $this->conditions = array();
    $this->params = array();
  }
}

?>