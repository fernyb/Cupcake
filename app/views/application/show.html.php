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
<strong>Environment &nbsp; => &nbsp; <?= CUPCAKE_ENV ?></strong>
<br />

<p>
Debug: <?= Config::get("debug") ? "true" : "false" ?>
</p>

<?= $view->render_partial("recent") ?>

<?= $view->render_partial("common/users") ?>

<? 
# An Example of how-to render a parial with local variables
?>
<?= $view->render_partial("common/locals", array("locals" => array(
    "name" => "john", "age" => 24
  ))) ?>
