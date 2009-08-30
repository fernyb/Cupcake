<h2>Display Ajax Here!</h2>

<div style="margin-top:10px; border: 1px solid #f09; padding:10px;" id="box">
  
<?= remote_form_for("user", $user, url("app", array("action" => "ajax_response")), function($f){ ?>
  <p style="float:left">
    <?= $f->label("name", "Your Name") ?>
    <?= $f->text_field("name") ?>
  </p>
  
  <p style="float:left; margin-left:10px;">
    <?= submit_tag("Submit") ?>
  </p>
<? }); ?>

<div class="clear"></div>
</div>