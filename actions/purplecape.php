<?php
if($okay_to_be_here !== true)
  exit();

if($_GET['step'] == 2)
{
  delete_inventory_byid($this_inventory['idnum']);

  $command = 'UPDATE monster_users SET title=\'Wearing Purple\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'updating resident\'s title');

  echo '<p><i>(Your title has been changed to "Wearing Purple".)</i></p>';
}
else
{
?>
<p>Really?  Doing so will consume the <?= $this_inventory['itemname'] ?>.
<p>Oh, and change your title to "Wearing Purple".</p>
<ul>
<?php
  if(user_age($user) >= 20)
  {
?>
 <li><a href="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>&step=2">Are you kidding?  I've been wanting to do this since I was a-twenty!</a></li>
<?php
  }
  else
  {
?>
 <li class="dim">Are you kidding?  I've been wanting to do this since I was a-twenty!</li>
 <li><a href="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>&step=2">I'm not even twenty, so that first option doesn't even make sense.  Well, it doesn't really makes sense, even if I was...  Look, whatever!  Just use the item, already!</a></li>
<?php
  }

  echo '</ul>';
}
?>
