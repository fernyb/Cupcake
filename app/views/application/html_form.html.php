<h2>HTML Forms</h2>
<hr />

<?= form_for("user", $user, url("html_form"), function($f){ ?>
  <?= $f->hidden_field("secret") ?> <br />
  <?= $f->check_box("validated") ?> <br />
  <?= $f->label("name", "User Name") ?> <br />
  <?= $f->text_field("name") ?> <br />
  <?= $f->label("password", "Password") ?> <br />
  <?= $f->password_field("password") ?> <br />
  <?= $f->text_area("comment") ?> <br />
  <?= $f->radio_button("color", "Blue") ?> <br />
  <?= $f->radio_button("color", "Green") ?> <br />
  <?= $f->file_field("avatar", array("class" => "image_upload")) ?> <br />
  
  <br />
  
  <div style="border:1px solid #ccc; padding:10px;">
    <strong>Fields For:</strong><br />
    <?= fields_for("customer[addresses][]", array(array("name" => "Michael Scott", "number" => "562-699-1234", "street" => "1234 Main St")), function($f, $customer){ ?>
      <?= $f->label("number", "Address For Person: <strong>". $customer["name"] ."</strong>") ?><br />
      <?= $f->text_field("number") ?><br />
      <?= $f->text_field("street") ?>
    <? }) ?>
  </div>
  
  <br />
  
  <div>
    <?= submit_tag("Submit") ?>
  </div>
<? }); ?>


<hr />
<h2>Before Filters</h2>
<? if(isset($set_some_text_only)): ?>
<div style="border:2px solid #ccc;padding:10px;">
  <div><strong>Before Filter Works!</strong></div>
  <strong><?= $artist_name ?></strong>
</div>
<? endif; ?>


<br />
<br />
<p>
<?= link_to("Home", url("root")) ?>
</p>