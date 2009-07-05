<?php

describe("Assertions", function(){
  it("equals 1 = 1", function(){
    assert_equal(1, 1);
  });
  
  it("should have the same name", function(){
    assert_equal("John", "JohnDoe");
  });
  
  it("should have the same age", function(){
    $age = 25;
    assert_equal($age, 25);
  });
  
  it("test match", function() {
    assert_match('/[a-z]{3}/', 'abc');
   assert_fails(function() { assert_match('/[a-z]{3}/', '111'); });
  });
  
});

/*
* You can also have more than one describe in a file
* It does not support nested describes
*/  
describe("Another Describe in one file", function(){
  it("it doesn't do nested describes", function(){
    assert_equal("Nested", "Nested");
  });
  
  it("Doesn't display on screen correctly!", function() {
    assert_equal("Cool!", "Cool!");
  });
  
  it("fails when I say so", function() {
    assert_equal("fail", "pass");
  });
});



?>