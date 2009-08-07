<?php

describe("Session, CookieStore", function(){
  before(function(){
    $cookie = new CookieStore();
    return array($cookie);
  });
  
  it("stores session in a cookie", function($args=array()){
    $cookie = $args[0];
  });
  
  it("sets a value", function(){
    // sets value
  });
  
  it("retrieves value", function(){
    // retrieves value
  });
});


?>