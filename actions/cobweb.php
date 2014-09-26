<?php
if($okay_to_be_here !== true)
  exit();

if($user['cornergraphic'] == '')
{
  $command = 'UPDATE monster_users SET cornergraphic=\'cobweb.png\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'cobwebbing profile');

  echo 'You decorate the house with cobweb.</p><p>Spooky.';

  delete_inventory_byid($this_inventory['idnum']);
}
else
  echo 'You\'ve already got something hanging in the corner.';
?>
