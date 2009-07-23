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
