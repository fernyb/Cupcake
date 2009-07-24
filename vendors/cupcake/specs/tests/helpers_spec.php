<?php
require_once dirname(__FILE__) . "/../spec_helper.php";

describe("Helpers -> __to_attributes", function(){
  it("returns array with attributes", function(){
    $attr = __to_attributes(array("src" => "filename", "type" => "text/javascript"));
    assert_equal($attr, array('src="filename"', 'type="text/javascript"'));
  });
});


describe("Helpers -> content_tag", function(){
  it("returns html tag for DIV tag", function(){
    $html = content_tag("div", "Div Tag");
    assert_equal($html, "<div>Div Tag</div>");
  });
});


describe("Helpers -> stylesheet_link_tag", function(){
  it("returns css stylesheet html tag", function(){
    $tag = stylesheet_link_tag("master");
    assert_equal($tag, '<link href="/stylesheets/master.css" media="screen" rel="stylesheet" type="text/css" />');
  });
  
  it("merges attributes for stylesheet helper", function(){
    $tag = stylesheet_link_tag("master", array("media" => "print"));
    assert_equal($tag, '<link href="/stylesheets/master.css" media="print" rel="stylesheet" type="text/css" />');    
  });  
});


describe("Helpers -> javascript_include_tag", function(){
  it("create html tag for javascript tag", function(){
    $tag = javascript_include_tag("master");
    assert_equal($tag, '<script type="text/javascript" src="/javascripts/master.js"></script>');
  });
});

describe("Helpers -> image_tag", function(){
  it("returns html tag for image", function(){
    $tag = image_tag("cupcake.png");
    assert_equal($tag, '<img alt="cupcake" src="/images/cupcake.png" />');
  });
  
  it("it has image attributes", function(){
    $tag = image_tag("cupcake.png", array("class" => "main"));
    assert_match("/class=\"main\"/", $tag);
  });
});

?>