<?php

describe("Session Management", function(){
  before(function(){
    $cookie_store = new CookieStore(array(
        "session_key"  =>  "_session_management",
        "secret"       =>  "abcdef"
      ));
    
    $session = new CupcakeSession($cookie_store);
    
    return array($session);
  });
  
  it("sets the session store", function($args){
    $session = $args[0];
    assert_match("/CookieStore/", get_class($session->session_store));
  });
  
  it("sets value to session", function($args){
    $session = $args[0];
    $session->set("id", "100");
    assert_equal($session->get("id"), "100");
  });
  
  it("clears session", function($args){
    $session = $args[0];
    $session->set("id", "100");
    $session->clear();
    assert_null($session->get('id'));
  });
  
  it("saves and verifies data", function($args){
    $session = $args[0];
    $session->set("id", "100");
    $session->set("name", "fernyb");
    $session->save();
    
    $data = $session->session_store->session_data;
    $session_data = $session->session_store->verify($data);
    
    assert_equal($session_data["id"], "100");
    assert_equal($session_data["name"], "fernyb");
    assert_array_has_key($session_data, "session_id");
  });
  
  it("gets data after its saved", function($args){
    $session = $args[0];
    $session->set("id", "100");
    $session->set("name", "fernyb");
    $session->save();
    
    assert_equal($session->get("id"), "100");
    assert_equal($session->get("name"), "fernyb");
  });
  
  it("does not save data when it overflows", function($args){
    $session = $args[0];
    for($i=0; $i<=94; $i++) {
      $session->set("svar_{$i}", "number: {$i}");
    }
    assert_throws(CookieOverflow, function() use($session) {
      $session->save();
    }, "Failed to throw CookieOverflow");
    
  });
});

?>