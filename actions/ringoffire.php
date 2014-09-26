<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/debris.php';

$AGAIN_WITH_ANOTHER = true;
$RECOUNT_INVENTORY = true;

$command = 'UPDATE monster_inventory SET health=0,itemname=\'Gold Ring\' WHERE idnum=' . $this_inventory['idnum'] . ' LIMIT 1';
$database->FetchNone($command, 'reverting to ordinary Gold Ring');

if(mt_rand(1, 2) == 2)
{
  echo '<p>The flames flicker and dance, create a bit of smoke, and vanish, leaving an ordinary Gold Ring.</p>';
  add_inventory($user['user'], '', 'Smoke', 'Created by a ' . $this_item['itemname'], $this_inventory['location']);
}
else
{
  echo '<p>The flames flicker and dance before gathering together.  The ring begins to tremble until Debris erupts from its center, and the ring reverts to an ordinary Gold Ring.</p>';
  add_inventory($user['user'], '', 'Debris', 'Created by a ' . $this_item['itemname'], $this_inventory['location']);
}
?>
