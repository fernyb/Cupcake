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
  
  
  <p>
    <?= submit_tag("Submit")   ?>
  </p>
<? }); ?>


<?= form_tag("/posts", array("method" => "get", "id" => "hello")) ?>
<br />
<?= form_tag("/posts", array("method" => "post", "id" => "hello", "class" => "post_form")) ?>
<br />
<?= form_tag("/posts", array("multipart" => true, "id" => "hello")) ?>
<br />


<br />
<p>
<?= link_to("Home", url("root")) ?>
</p>