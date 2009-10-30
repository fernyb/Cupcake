<h2>app/views/application/show.html.php</h2>
<h3><?= $welcome ?></h3>
<?= $description ?>


<br />
<h2>Helpers:</h2>

<p>
  <strong>stylesheet_link_tag</strong><br />
  Include any stylesheets from the stylesheets directory:<br />
  Example: <strong>stylesheet_link_tag("master")</strong> <br />
  # => <?= htmlentities(stylesheet_link_tag("master")) ?>
</p>

<p>
  <strong>content_tag</strong><br />
 Print an html tag:<br />
 Exmaple: <strong>content_for("p", "Hello World")</strong> <br />
 # => <?= htmlentities(content_tag("p", "Hello World")) ?>
</p>

<p>
  <strong>image_tag</strong><br />
 Generates an img tag:<br />
 Exmaple: <strong>image_tag("main.png")</strong> <br />
 # => <?= htmlentities(image_tag("main.png")) ?>
</p>

<p>
  <strong>truncate</strong><br />
 Truncates a string to a limit length<br />
 Exmaple: <strong>truncate("Hello World", 5)</strong> <br />
 # => <?= htmlentities(truncate("Hello World", 5)) ?>
</p>

<?= link_to("Artist - Coldplay", url("artist", array("artist" => "coldplay", "year" => 2009))) ?>
<br />
<br />

<?= link_to("User Profile", url("user_profile")) ?>

<br />
<br />
<?= link_to("My Layout", url("my_layout")) ?>

<br />
<br />
<?= link_to("HTML Forms", url("html_form")) ?>

<br />
<br />
<?= link_to("Flash Message Example", url("flash_example")) ?>

<br />
<br />
<?= link_to("Sessions", url("session_set")) ?>

<br />
<br />
<?= link_to("The Name", url("the_name")) ?>

<br />
<br />
<?= link_to("API Recent", url("api_recent", array("format" => "xml"))) ?>

<br />
<br />
<?= link_to("API Recent with action", url("the_name_format", array("action" => "api_recent", "format" => "xml"))) ?>


<br />
<br />

<strong>Environment &nbsp; => &nbsp; <?= CUPCAKE_ENV ?></strong>
<br />

<p>
Debug: <?= CupcakeConfig::get("debug") ? "true" : "false" ?>
</p>

<?php if(!empty($set_some_text)) { ?>
  <h2>Failed The Skip Before Filter</h2>
  <?= var_dump($set_some_text) ?>
<? } ?>

<?= $helper->print_with_h2("Application Helper Method!") ?>


<br /><br />

<?= $view->render_partial("recent") ?>

<?= $view->render_partial("common/users") ?>

<? 
# An Example of how-to render a parial with local variables
?>
<?= $view->render_partial("common/locals", array("locals" => array(
    "name" => "john", "age" => 24
  ))) ?>
