<h2>Session Values</h2>
<p>
  <?= session_get("id") ?>
</p>

<p>
  <?= session_get("name") ?>
</p>


<br /><br />
<?= link_to("Back", url("session_set")) ?>
