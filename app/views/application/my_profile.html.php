<h2>My Profile</h2>
<p>
<strong>Rendering</strong><br /><br />
<strong>Layout:</strong> &nbsp;&nbsp; <?= $view->layout ?><br /><br />
<strong>Template:</strong> <?= $view->template ?>
</p>

<p>
This is the layout specified in the action. <br />
<pre>
    $this->render_action("my_profile", array("layout" => "my_layout"));
</pre>
</p>

<br />

<?= link_to("Home", url("root")) ?>
