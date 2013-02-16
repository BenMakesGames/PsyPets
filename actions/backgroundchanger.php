<?php
if($_GET['ala'] == 'kazam')
{
  delete_inventory_byid($this_inventory['idnum']);
?>
<p>It is done!!</p>
<?php
}
else if($user['style_background'] != $action_info[2])
{
?>
<p>Using this item will change the site background!  (And destroy the <?= $this_inventory['itemname'] ?>!)</p>
<ul>
 <li><a href="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>&amp;ala=kazam">Let's do it!</a></li>
</ul>
<?php
}
else
{
  echo '<p>Using this item <em>would</em> change the site background, except you\'re already using the very same background!</p>';
}
?>
