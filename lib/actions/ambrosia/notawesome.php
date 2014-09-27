<?php
if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_ANOTHER = true;

delete_inventory_byid($this_inventory['idnum']);

if($user['is_a_whale'] == 'yes')
{
  $command = 'UPDATE monster_users SET is_a_whale=\'no\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'itemaction.php');

  echo 'You drink the potion, restoring your regular appearance.';
}
else
  echo 'You drink the potion.  It tastes like honeysuckle, which is nice, but nothing out of the ordinary seems to happen.';
?>
