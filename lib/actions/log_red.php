<?php
if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_ANOTHER = true;

delete_inventory_byid($this_inventory['idnum']);

add_inventory($user["user"], 'u:' . $user['idnum'], 'Red Wood', "", $this_inventory["location"]);
add_inventory($user["user"], 'u:' . $user['idnum'], 'Red Wood', "", $this_inventory["location"]);
if(rand(1, 2) == 1)
{
  add_inventory($user["user"], 'u:' . $user['idnum'], 'Paper', "", $this_inventory["location"]);
  $items = "two Red Wood planks.  The leftovers are processed to make some Paper";
}
else
{
  add_inventory($user["user"], 'u:' . $user['idnum'], 'Red Wood', "", $this_inventory["location"]);
  $items = "three Red Wood planks";
}

if(rand(1, 100) == 1)
{
  add_inventory($user["user"], 'u:' . $user['idnum'], "Amber", "", $this_inventory["location"]);
  $items = "-- what's this?!  There's a bit of Amber on this Log!</p><p>You carefully put it aside, and continue to saw, yielding $items";
}
?>
<p>You saw the Log into <?= $items ?>.</p>
