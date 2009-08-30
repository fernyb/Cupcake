$("html").attr("style", "background-color: #f4f4f4;");
$("body").css("background-color", "#fff");
$("body").css("padding", "15px");
$("body").css("border", "12px solid #ccc");
$("body").css("min-height", "200px");

<?php if($error === true) { ?>
  //$("#box .errors").remove();
  //$("#box").append("<strong class='errors' style='color:red;'><br /><div>Please fix the error and try again</div></strong>");
<? } else { ?>
  //$("#box").html("<strong style='color:green'>Thanks for your name</strong>");
<?php } ?>

$("#box").hide("slow");

$("#go-back-link").remove();
$("#box").parent().append('<div id="go-back-link"><br /><?= link_to("Go Home", url("root")) ?></div>');
