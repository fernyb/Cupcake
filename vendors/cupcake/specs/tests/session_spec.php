<?php

describe("Session Management", function(){
  before(function(){
    $session = Session::getInstance();
    $session->session_store = new CookieStore();
    return array($session);
  });
  
  it("sets the session store", function($args){
    $session = $args[0];
    assert_match("/CookieStore/", get_class($session->session_store));
  });
  
  it("sets value to session", function($args){
    $session = $args[0];
    Session::set("id", "100");
    assert_equal(Session::get("id"), "100");
  });
  
  it("clears session", function($args){
    $session = $args[0];
    Session::set("id", "100");
    Session::clear();
    assert_null(Session::get('id'));
  });
});

?>