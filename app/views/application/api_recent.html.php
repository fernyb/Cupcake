<h2>API Recent</h2>

<p>
<strong>Request URI:</strong>  &nbsp; <strong><?= $request_uri ?></strong>
</p>


<table>
<tr>
  <td>Controller:</td>
  <td><?= $params["controller"] ?></td>
</tr>
<tr>
  <td>Action:</td>
  <td><?= $params["action"] ?></td>
</tr>
<tr>
  <td>Format:</td>
  <td><?= $params["format"] ?></td>
</tr>
</table>

<br />

<?= link_to("Home", url("root")) ?>
