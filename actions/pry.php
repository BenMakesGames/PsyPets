<?php
if($okay_to_be_here !== true)
  exit();

if($user['cornergraphic'] == 'support.png')
{
  if($_GET['step'] == 2)
  {
    $command = 'UPDATE monster_users SET cornergraphic=\'\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'item: ' . $this_inventory['itemname']);

    echo '<p>You pry that wooden support off of the corner.</p><p>...</p><p>Nothing is collapsing so far, anyway...</p>';
    
    add_inventory($user['user'], '', 'Wood', '', $this_inventory['location']);
  }
  else
  {
?>
<p>Really pry the wooden support from the corner of your house?  I mean, it must be there for a reason, right?</p>
<ul>
 <li><a href="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>&step=2">Psh, whatever.  What's the worst that could happen?</a></li>
</ul>
<?php
  }
}
else
  echo '<p>You think about prying up the floorboards, but it occurs to you that they might be better off where they are.</p>';
?>
