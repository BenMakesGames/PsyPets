<?php
if($okay_to_be_here !== true)
  exit();

if($_GET['action'] == 'avatar')
{
  delete_inventory_byid($this_inventory['idnum']);

  $database->FetchNone('UPDATE monster_users SET graphic=\'special-secret/bubbles.png\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1');

  echo '<p><i>(Your avatar has been changed!)</i></p>';
}
else if($_GET['action'] == 'title')
{
  $command = 'UPDATE monster_users SET title=\'Bubble Buff\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'updating resident\'s title');

  echo '<p><i>(Your title has been changed!)</i></p>';

  delete_inventory_byid($this_inventory['idnum']);
}
else
{
  echo '
    <p>Before opening it up, you peer through the thick plastic, and see...</p>
    <ul>
     <li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&action=avatar">An avatar!  Luckyyyyy~!</a></li>
     <li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&action=title"><b><em>Yes!</em></b>  It\'s a title!</a></li>
    </ul>
  ';
}
?>
