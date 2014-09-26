<?php
if($okay_to_be_here !== true)
  exit();

if($_GET['step'] == 2)
{
  delete_inventory_byid($this_inventory['idnum']);

  $command = 'UPDATE monster_users SET profile_wall=\'' . $action_info[2] . '\',profile_wall_repeat=\'' . $action_info[3] . '\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'applying background');
  
  echo '<p>Your profile background has been changed!</p>';
}
else
{
  echo '<p>You can use this item to change your profile background.</p>' .
       '<ul><li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&step=2">Sounds like a good use.</a></li></ul>';
}
