<?php

describe("Session, CookieStore", function(){
  before(function(){
    $cookie = new CookieStore();
    $cookie->initialize(array(
        "session_key" => "_cupcake_session",
        "secret" => "819b03089487407e44177a2bbec6ee270cfc2964785558d63b489e4cf4d3c9dfefe27b2fc188d4beb96471e1d5c68478dfbbbab43df51cef3e7450236d38d746"
      ));
    return array($cookie);
  });
  
  it("stores session in a cookie", function($args){
    $cookie = $args[0];
    $cookie->set("id", "100");
    $cookie->set("name", "fernyb");
    $cookie->save();
    
    $session_data = $cookie->session_data;
    $session = $cookie->verify($session_data);
    
    assert_equal("100", $session["id"]);
    assert_equal("fernyb", $session["name"]);
    assert_array_has_key($session, "session_id");
  });
  
  it("sets session key", function($args){
    $cookie = $args[0];
    assert_equal("_cupcake_session", $cookie->key);
  });
  
  it("sets a session variable", function($args){
    $cookie = $args[0];
    $cookie->set("name", "fernyb");
    $cookie->set("id", "24");
    
    assert_equal($cookie->params["name"], "fernyb");
    assert_equal($cookie->params["id"], "24");
  });

  it("gets a session variable", function($args){
    $cookie = $args[0];
    $cookie->set("name", "fernyb");
    $cookie->set("id", "24");
    
    assert_equal($cookie->get("name"), "fernyb");
    assert_equal($cookie->get("id"), "24");
  });
  
  it("sets the session path", function($args){
    $cookie = $args[0];;
    
    assert_equal("/", $cookie->default_options["path"]);  
  });
  
  it("sets cookie", function($args){
    $cookie = $args[0];
    $time = time();
    $krumb = $cookie->set_cookie(array(
        "name"     => "cupcake_session",
        "value"    => "abc",
        "domain"   => ".cupcake-framework.com",
        "path"     => "/",
        "expires"  => $time,
        "secure"   => true,
        "httponly" => true
      ));
    
    assert_equal("cupcake_session", $krumb["name"]);
    assert_equal("abc", $krumb["value"]);
    assert_equal(".cupcake-framework.com", $krumb["domain"]);
    assert_equal("/",    $krumb["path"]);
    assert_equal($time,  $krumb["expires"]);
    assert_equal(true,   $krumb["secure"]);
    assert_equal(true,   $krumb["httponly"]);
  });
  
  it("sets the secret key", function($args){
    $cookie = $args[0];
    
    assert_equal("819b03089487407e44177a2bbec6ee270cfc2964785558d63b489e4cf4d3c9dfefe27b2fc188d4beb96471e1d5c68478dfbbbab43df51cef3e7450236d38d746",
    $cookie->secret);
  });
  
  it("returns values stored in session for a new request", function($args){
    $cookie = $args[0];
    $cookie->set("name", "fernyb");
    $cookie->set("age", "24");
    $cookie->set("id", "100");
    $cookie->save();
    $session_data = $cookie->session_data;
    
    $new_request = new CookieStore();
    $new_request->initialize(array(
        "session_key" => $cookie->key,
        "secret"      => $cookie->secret,
      ));
    
    $new_request->load_session($session_data);  
    
    assert_equal("fernyb", $new_request->params["name"]);
    assert_equal("24",     $new_request->params["age"]);
    assert_equal("100",    $new_request->params["id"]);
  });
  
  it("does not load the session when its tampered", function($a){
    $cookie = $a[0];
    $cookie->set("name", "fernyb");
    $cookie->set("id",   "100");
    $cookie->save();
    $session_data = $cookie->session_data;
    
    list($data, $signature) = explode("--", $session_data, 2);
    $new_data = base64_decode($data);
    $new_data = unserialize($new_data);
    
    # make changes to the data
    $new_data["name"] = "Michael Scott";    
    $new_data["id"]   = "200";
    $new_data = base64_encode(serialize($new_data));
    
    # Since we don't know how the signature is generated 
    # We just assume is a sha1 hash and because we don't 
    # the servers secret key it should not be allowed to load.
    $new_sig = sha1($new_data);
    
    # This will be sent back to the server 
    $tampered_session_data = "{$new_data}--{$new_sig}";
    
    $new_request = new CookieStore(array(
        "session_key" => $cookie->key,
        "secret"      => $cookie->secret
      ));
    
    #  
    # The data will attempt to load the session_data if tampered with
    # It will not load and just return an empty string. 
    #
    # The only to determine if the data was changed is 
    # by the sha1 hash that uses are secret key
    #
    $loaded_data = $new_request->load_session($tampered_session_data);
    
    assert_equal($loaded_data, "");
    assert_equal(0, count($new_request->params));
  });
});


?>