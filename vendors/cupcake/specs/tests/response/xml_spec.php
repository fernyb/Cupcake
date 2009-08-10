<?php

describe("XML Response", function(){
  before(function(){
    $data = array("name" => "fernyb", "id" => "100", "location" => "California");
    $xml = new XMLResponse($data);
    return $xml;
  });
  
  it("returns xml", function($xml){
    $response = $xml->to_xml();

    $data = '<?xml version="1.0" encoding="UTF-8"?>
<root><name>fernyb</name><id>100</id><location>California</location></root>';
    assert_equal($data, $response);
  });
});

?>