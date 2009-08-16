<?php

describe("JSON Response", function(){
  before(function(){
    $response = new JSONResponse(array(
        "id" => "100",
        array("location" => array(
            "city"  => "Pico Rivera",
            "state" => "CA"
          ))
      ));
    return $response;  
  });
  
  it("generates json response", function($response){  
    $data = '{"id":"100","0":{"location":{"city":"Pico Rivera","state":"CA"}}}';
    assert_equal($data, $response->to_json());
  });
  
  it("retrieves values", function($response){
    $json = json_decode($response->to_json(), true);
    
    assert_equal($json["id"], "100");
    assert_array($json[0]);
    assert_array($json[0]["location"]);
    assert_equal($json[0]["location"]["city"], "Pico Rivera");
    assert_equal($json[0]["location"]["state"], "CA");
  });
});

?>