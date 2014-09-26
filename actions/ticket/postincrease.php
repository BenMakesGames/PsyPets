<?php
if($okay_to_be_here !== true)
  exit();

if($_GET['confirm'] == 'yes')
{
  $command = 'UPDATE monster_users SET postsize=postsize+400 WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'post office box increaser');

  delete_inventory_byid($this_inventory['idnum']);

  echo 'Your mailbox size has been permanently increased by 400.';
}
else
{
?>
<p>Using this ticket will permanently increase your mailbox size by 400.</p>
<ul>
 <li><a href="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>&confirm=yes">Use the ticket!</a></li>
</ul>
<?php
}
?>
