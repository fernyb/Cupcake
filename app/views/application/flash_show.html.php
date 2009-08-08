<h2>Should Display the flash message</h2>

<div style="padding:10px; border:1px solid #f09;">
<?= var_dump(flash("notice")) ?>
</div>

<br />
<p>
Clicking on Refresh Page should not display the flash message above<br />
<?= link_to("Reresh Page", url("flash_show")) ?>
</p>

<br /><br />
<?= link_to("Go Back", url("flash_example")) ?>

