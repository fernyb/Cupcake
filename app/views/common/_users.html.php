<h2>Users Partial</h2>

<? if(!empty($name)) { ?>
<div style="border:1px solid #f09; padding:5px;">
<p>Users partial with local variables</p>
<strong>Name: </strong><?= $name ?><br />
<strong>Age:</strong><?= $age ?><br />
</div>
<? } ?>
