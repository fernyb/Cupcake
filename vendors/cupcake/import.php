<?php

class Import {
  static function controller($name) {
    $file = CONTROLLER_DIR . "/" . $name .".php";
    if(self::file_exists($file)) {
      @include_once $file;
      return true;
    }
    return false;
  }
  
  static function helper($name) {
    $file = HELPER_DIR ."/". $name .".php";
    if(self::file_exists($file)) {
      @include_once $file;
      return true;
    }
    return false;
  }
  
  static function view($__name, $__ext, $__options=array()) {
    $__file = VIEW_DIR ."/". $__name .".". $__ext;
    if(self::file_exists($__file)) {
      extract($__options, EXTR_SKIP);
      include $__file;
      return true;
    }
    return false;
  }
  
  static function file_exists($file_path) {
    return file_exists($file_path);
  }
}

?>