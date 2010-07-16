CupcakeRouter::map(function($r){
  $r->root("/", array("controller" => "application", "action" => "show"));
  $r->connect("/:controller/:action(/:id)");
});
