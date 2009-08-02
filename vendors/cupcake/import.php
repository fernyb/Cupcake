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
  
  static function view($__name, $__ext, $options=array()) {
    $__file = VIEW_DIR ."/". $__name .".". $__ext;
    if(self::file_exists($__file)) {
      extract($options, EXTR_SKIP);
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