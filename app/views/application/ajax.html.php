<script type="text/javascript">
  $(document).bind("cupcake.linkToRemoteBefore", function(){
    for(var i=0; i<arguments.length; i++) {
      console.log(arguments[i]);
    }
  });
  
  $(document).bind("cupcake.linkToRemoteComplete", function(object, method, element, status, response){
    for(var i=0; i<arguments.length; i++) {
      console.log(arguments[i]);
    }
    $("body").append("<div><strong>Response Complete!</strong></div>");
  });
</script>

<h2>Display Ajax Here!</h2>

<div style="margin-top:10px; border: 1px solid #f09; padding:10px;" id="box">
  
<?= remote_form_for("user", array(), url("app", array("action" => "ajax_response")), function($f){ ?>
  <p style="float:left">
    <?= $f->label("name", "Your Name") ?>
    <?= $f->text_field("name") ?>
  </p>
  
  <p style="float:left; margin-left:10px;">
    <?= submit_tag("Submit") ?>
  </p>
<? }); ?>

<br />
<div class="clear"></div>
</div>

<p></p>

<div>
  <p>link_to_remote</p>
  <p>Link to remote is a link that will execute an ajax request to the destination url from the href value.</p>
  <p><?= link_to_remote("Link To Remote", url("app", array("action" => "ajax_response"))) ?></p>
</div>

<br />