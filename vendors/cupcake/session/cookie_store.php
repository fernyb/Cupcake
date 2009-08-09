<?php

class CookieStore {
  const MAX = 4096;
  public $params = array();
  public $secret_min_length = 30; # characters
  public $default_options = array(
      "key"          => "_session_id",
      "domain"       => null,
      "path"         => "/",
      "expire_after" => null,
      "httponly"     => true
    );
  public $verifier; 
  public $secret;
  public $key;
  public $session_data;
  
  public function __construct($options=array()) {
    $this->initialize($options);
  }
  
  public function initialize($options=array()) {
    if(count($options) === 0) {
      return;
    }
    if(array_search("session_path", array_keys($options)) !== false) {
      $options["path"] = $options["session_path"];
      unset($options["session_path"]);
    }
    if(array_search("session_key", array_keys($options)) !== false) {
      $options["key"] = $options["session_key"];
      unset($options["session_key"]);
    }
    if(array_search("session_http_only", array_keys($options)) !== false) {
      $options["httponly"] = $options["session_http_only"];
      unset($options["session_http_only"]);
    }

    # The session_key option is required.
    $this->ensure_option_key($options["key"]);
    $this->key = $options["key"];

    # The secret option is required.
    $this->ensure_option_key($options["secret"]);
    $this->secret = $options["secret"];
  
    $this->default_options = array_merge($this->default_options, $options);
  }
  
  public function save() {
    $session_data = $this->marshal($this->params);
    $this->session_data = $session_data;
    
    if(strlen($session_data) > self::MAX) {
      throw new CookieOverflow("Cookie length Overflow: " . strlen($session_data) . " is greater than ". self::MAX);
    }
    
    $expire_after = $this->default_options["expire_after"];
    if(empty($expire_after)) {
      $this->default_options["expire_after"] = $expire_after = 5 * DAY;
    }
    
    $cookie = array();
    $cookie["value"] = $session_data;
    $cookie["expires"] = ( time() + $expire_after );
    
    $cookie = $this->build_cookie($this->key, array_merge($cookie, $this->default_options));
    return $this->set_cookie($cookie);
  }
  
  public function set_cookie($cookie) {
    if(!empty($cookie)) {
      $cookie = "Set-Cookie: {$cookie}";
      if(CUPCAKE_ENV !== "test") {
        header($cookie);
      }
    }
    return $cookie;
  }
  
  public function build_cookie($key, $value) {
    if(!empty($value["domain"])) {
      $domain = "; domain=".$value["domain"];
    }
    if(!empty($value["path"])) {
      $path = "; path=".$value["path"];
    }
    if(!empty($value["expires"])) {
      $expires = "; expires=". gmdate("D, d-M-Y H:i:s", $value["expires"]) ." GMT";
    }
    if(!empty($value["secure"])) {
      $secure = "; secure";
    }
    if(!empty($value["httponly"])) {
      $httponly = "; HttpOnly";
    }
    $value = $value["value"];
  
    if(!is_array($value)) {
      $value = array($value);
    }
    
    $cookie = rawurlencode($key) ."=". join("&", array_map(function($v){
      return rawurlencode($v);
    }, $value)) . "{$domain}{$path}{$expires}{$secure}{$httponly}";
    
    return $cookie;
  }
  
  public function load_session($session_data="") {
    try {
      $data = $this->verify($session_data);
      $this->params = $data;
    } catch(InvalidSignature $e) {
      Logger::info($e);
      $this->params = array();
      $data = "";
    }
    return $data;
  }
  
  public function verify($signed_message="") {
    list($data, $digest) = explode("--", $signed_message, 2);
    $new_digest = $this->generate_digest($data);
    if($digest !== $new_digest) {
      throw new InvalidSignature("Digest does not match: {$digest} != {$new_digest}");
    } else {
      $new_data = unserialize(base64_decode($data));
      return $new_data;
    }
  }
  
  private function ensure_option_key($value=null) {
    if(empty($value)) {
      throw new Exception("Missing Key");
    }
    return true;
  }
  
  private function marshal($session=array()) {
    $session_id = $this->persistent_session_id($session);
    $value = $this->generate($session_id);
    return $value;
  }
  
  private function generate($value) {
    $data = base64_encode(serialize($value));
    $value = "{$data}--". $this->generate_digest($data);
    return $value;
  }
  
  private function generate_digest($data) {
    $data = sha1("sha1". $data . $this->secret);
    return $data;
  }
  
  private function generate_id() {
    mt_srand(time() * rand());
    $id = substr(mt_rand() . mt_rand(), 0, 16);
    return $id;
  }
  
  private function persistent_session_id($data=array()) {
    $id = array_merge($data, $this->inject_persistent_session_id($data));
    return $id;
  }
  
  private function inject_persistent_session_id($data=array()) {
    $session = $this->requires_session_id($data) ? array("session_id" => $this->generate_id()) : array();
    return $session;
  }
  
  private function requires_session_id($data=array()) {
    $keys = array_keys($data);
    if(array_search("session_id", $keys)) {
      return false;
    }
    return true;
  }
  
  public function set($key, $value=null) {
    $this->params[$key] = $value;
  }
  
  public function get($key) {
    return $this->params[$key];
  }
  
  public function clear() {
    $this->params = array();
  }
}

class CookieOverflow extends Exception { }
class InvalidSignature extends Exception { }

?>