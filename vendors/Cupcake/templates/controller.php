class <?= $name ?> extends CupcakeController {
  public function show() {
    $this->set("current_date", date("F m, Y - g:i:s A", time()));
    // the action show will render the show template
  }
}
