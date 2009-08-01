<?php
require_once 'PHPUnit/Framework.php';
require_once "../router.php";

class RouterTest extends PHPUnit_Framework_TestCase {
  protected $router;
  
  public function setUp() {
    $this->router = Router::getInstance();
  }
  
  public function tearDown() {
    $this->router->reset();
  }
  
  /** Testing match method **/
  public function test_match_adds_to_routes() {
    $r = $this->router;
    $r->match("/new/router");
    $this->assertGreaterThan(0, count($r->routes));
  }
  
  public function test_build_more_than_one_route() {
    $r = $this->router;
    $r->match("/new/router");
    $r->match("/another/route");
    $this->assertEquals(count($r->routes), 2);
  }
  
  public function test_returns_an_instance_of_router() {
    $resp = $this->router->match("/new/current_path");
    $this->assertEquals(get_class($resp), "Router");
  }
}


?>