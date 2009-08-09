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
    $krumb = $cookie->set_cookie("abc");
    
    assert_equal("Set-Cookie: abc", $krumb);
  });
  
  it("sets the secret key", function($args){
    $cookie = $args[0];
    
    assert_equal("819b03089487407e44177a2bbec6ee270cfc2964785558d63b489e4cf4d3c9dfefe27b2fc188d4beb96471e1d5c68478dfbbbab43df51cef3e7450236d38d746",
    $cookie->secret);
  });
});


?>