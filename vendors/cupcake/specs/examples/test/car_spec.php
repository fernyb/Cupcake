<?php

describe("Car", function(){
  it("cost over 5k", function(){
    $cost_of_car = 12000;
    assert_equal(($cost_of_car > 5), true);
  });
  
  it("doesn't gets flat tire after someone buys it", function(){
    ensure(false);
  });
});

?>